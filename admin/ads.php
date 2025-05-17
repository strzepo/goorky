<?php
// Włącz wyświetlanie błędów
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Rozpocznij sesję
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Sprawdź, czy użytkownik ma uprawnienia administratora
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Połączenie z bazą danych
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/language.php';

// Tworzenie tabeli ads, jeśli nie istnieje
try {
    $tableExists = $pdo->query("SHOW TABLES LIKE 'ads'")->rowCount() > 0;
    
    if (!$tableExists) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS ads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            ad_code TEXT NOT NULL,
            position VARCHAR(50) NOT NULL,
            page VARCHAR(50) NOT NULL DEFAULT 'all',
            enabled TINYINT(1) DEFAULT 1,
            start_date DATE DEFAULT NULL,
            end_date DATE DEFAULT NULL,
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Dodanie przykładowych reklam
        $sampleAds = [
            ['Header Banner', '<div class="text-center py-4 bg-gray-100"><!-- Google AdSense Code Here --><div class="text-gray-500">Miejsce na reklamę (Header)</div></div>', 'header', 'all', 1, NULL, NULL, 1],
            ['Footer Banner', '<div class="text-center py-4 bg-gray-100"><!-- Google AdSense Code Here --><div class="text-gray-500">Miejsce na reklamę (Footer)</div></div>', 'footer', 'all', 1, NULL, NULL, 2],
            ['Sidebar Banner', '<div class="text-center py-4 bg-gray-100"><!-- Google AdSense Code Here --><div class="text-gray-500">Miejsce na reklamę (Sidebar)</div></div>', 'sidebar', 'all', 1, NULL, NULL, 3],
            ['Content Banner', '<div class="text-center py-4 my-4 bg-gray-100"><!-- Google AdSense Code Here --><div class="text-gray-500">Miejsce na reklamę (Content)</div></div>', 'content', 'all', 1, NULL, NULL, 4]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO ads (name, ad_code, position, page, enabled, start_date, end_date, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($sampleAds as $ad) {
            $stmt->execute($ad);
        }
        
        $success = "Tabela reklam została utworzona i wypełniona przykładowymi danymi.";
    }
} catch (PDOException $e) {
    $error = "Błąd podczas tworzenia tabeli reklam: " . $e->getMessage();
}

// Obsługa włączania/wyłączania reklamy
if (isset($_GET['action']) && ($_GET['action'] === 'enable' || $_GET['action'] === 'disable') && isset($_GET['id'])) {
    $adId = (int)$_GET['id'];
    $enabled = $_GET['action'] === 'enable' ? 1 : 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE ads SET enabled = ? WHERE id = ?");
        $stmt->execute([$enabled, $adId]);
        
        $status = $enabled ? 'włączona' : 'wyłączona';
        $success = "Reklama została $status.";
        
        // Przekieruj, aby uniknąć ponownej zmiany przy odświeżeniu strony
        header("Location: ads.php?status=$status");
        exit;
    } catch (Exception $e) {
        $error = "Błąd podczas aktualizacji statusu reklamy: " . $e->getMessage();
    }
}

// Obsługa usuwania reklamy
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $adId = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM ads WHERE id = ?");
        $stmt->execute([$adId]);
        
        $success = "Reklama została usunięta.";
        
        // Przekieruj, aby uniknąć ponownego usunięcia przy odświeżeniu strony
        header("Location: ads.php?deleted=1");
        exit;
    } catch (Exception $e) {
        $error = "Błąd podczas usuwania reklamy: " . $e->getMessage();
    }
}

// Obsługa dodawania/edycji reklamy
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && ($_POST['action'] === 'add' || $_POST['action'] === 'edit')) {
    // Pobranie i walidacja danych
    $name = trim($_POST['name'] ?? '');
    $adCode = trim($_POST['ad_code'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $page = trim($_POST['page'] ?? 'all');
    $enabled = isset($_POST['enabled']) ? 1 : 0;
    $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $displayOrder = (int)($_POST['display_order'] ?? 0);
    
    // Podstawowa walidacja
    if (empty($name) || empty($adCode) || empty($position)) {
        $error = "Nazwa, kod reklamy i pozycja są wymagane.";
    } else {
        try {
            if ($_POST['action'] === 'add') {
                // Dodaj nową reklamę
                $stmt = $pdo->prepare("INSERT INTO ads (name, ad_code, position, page, enabled, start_date, end_date, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $adCode, $position, $page, $enabled, $startDate, $endDate, $displayOrder]);
                
                $success = "Reklama została pomyślnie dodana.";
            } elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
                $adId = (int)$_POST['id'];
                
                // Aktualizuj reklamę
                $stmt = $pdo->prepare("UPDATE ads SET name = ?, ad_code = ?, position = ?, page = ?, enabled = ?, start_date = ?, end_date = ?, display_order = ? WHERE id = ?");
                $stmt->execute([$name, $adCode, $position, $page, $enabled, $startDate, $endDate, $displayOrder, $adId]);
                
                $success = "Reklama została pomyślnie zaktualizowana.";
            }
        } catch (Exception $e) {
            $error = "Błąd podczas zapisywania reklamy: " . $e->getMessage();
        }
    }
}

// Pobieranie reklamy do edycji
$editAd = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $adId = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM ads WHERE id = ?");
        $stmt->execute([$adId]);
        $editAd = $stmt->fetch();
    } catch (Exception $e) {
        $error = "Błąd podczas pobierania danych reklamy: " . $e->getMessage();
    }
}

// Pobieranie listy reklam
try {
    $stmt = $pdo->query("SELECT * FROM ads ORDER BY position, display_order");
    $ads = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Błąd podczas pobierania listy reklam: " . $e->getMessage();
    $ads = [];
}

// Przygotowanie listy pozycji i stron
$positions = [
    'header' => 'Nagłówek (góra strony)',
    'footer' => 'Stopka (dół strony)',
    'sidebar' => 'Pasek boczny',
    'content' => 'Wewnątrz treści',
    'content_top' => 'Góra treści',
    'content_bottom' => 'Dół treści',
    'before_content' => 'Przed treścią',
    'after_content' => 'Po treści'
];

$pages = [
    'all' => 'Wszystkie strony',
    'home' => 'Strona główna',
    'bmi' => 'Kalkulator BMI',
    'calories' => 'Kalkulator kalorii',
    'units' => 'Konwerter jednostek',
    'dates' => 'Kalkulator dat',
    'password_generator' => 'Generator haseł',
    'youtube' => 'YouTube Downloader',
    'instagram' => 'Instagram Downloader',
    'facebook' => 'Facebook Downloader',
    'vimeo' => 'Vimeo Downloader'
];

// Tytuł strony
$pageTitle = "Panel Administracyjny - Zarządzanie Reklamami";
include_once 'includes/admin_header.php';
?>

<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <?php include_once 'includes/admin_sidebar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 overflow-auto">
        <main class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-semibold text-gray-800">Zarządzanie Reklamami</h1>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success) || isset($_GET['status']) || isset($_GET['deleted'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php 
                    if (isset($success)) {
                        echo htmlspecialchars($success);
                    } elseif (isset($_GET['status'])) {
                        echo "Reklama została " . htmlspecialchars($_GET['status']) . ".";
                    } elseif (isset($_GET['deleted'])) {
                        echo "Reklama została usunięta.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Form for adding/editing ads -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">
                    <?php echo isset($editAd) ? 'Edytuj Reklamę' : 'Dodaj Nową Reklamę'; ?>
                </h2>
                
                <form method="POST" action="ads.php">
                    <input type="hidden" name="action" value="<?php echo isset($editAd) ? 'edit' : 'add'; ?>">
                    <?php if (isset($editAd)): ?>
                        <input type="hidden" name="id" value="<?php echo $editAd['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nazwa</label>
                            <input type="text" name="name" id="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editAd) ? htmlspecialchars($editAd['name']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700">Pozycja</label>
                            <select name="position" id="position" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <?php foreach ($positions as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo (isset($editAd) && $editAd['position'] === $key) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="page" class="block text-sm font-medium text-gray-700">Strona</label>
                            <select name="page" id="page" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <?php foreach ($pages as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo (isset($editAd) && $editAd['page'] === $key) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700">Kolejność wyświetlania</label>
                            <input type="number" name="display_order" id="display_order" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editAd) ? htmlspecialchars($editAd['display_order']) : '0'; ?>" min="0">
                        </div>
                        
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Data początkowa (opcjonalnie)</label>
                            <input type="date" name="start_date" id="start_date" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editAd) && $editAd['start_date'] ? htmlspecialchars($editAd['start_date']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Data końcowa (opcjonalnie)</label>
                            <input type="date" name="end_date" id="end_date" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editAd) && $editAd['end_date'] ? htmlspecialchars($editAd['end_date']) : ''; ?>">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="ad_code" class="block text-sm font-medium text-gray-700">Kod reklamy</label>
                            <textarea name="ad_code" id="ad_code" rows="8" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"><?php echo isset($editAd) ? htmlspecialchars($editAd['ad_code']) : ''; ?></textarea>
                            <p class="mt-1 text-xs text-gray-500">Wprowadź kod JavaScript lub HTML reklamy Google AdSense.</p>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="enabled" id="enabled" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?php echo (isset($editAd) && $editAd['enabled']) || !isset($editAd) ? 'checked' : ''; ?>>
                            <label for="enabled" class="ml-2 block text-sm text-gray-900">Włączona</label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <?php if (isset($editAd)): ?>
                            <a href="ads.php" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md mr-2 hover:bg-gray-300 transition">Anuluj</a>
                        <?php endif; ?>
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                            <?php echo isset($editAd) ? 'Zapisz zmiany' : 'Dodaj reklamę'; ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Guidelines -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Wskazówki dotyczące reklam AdSense</h2>
                
                <div class="prose max-w-none">
                    <p>Oto kilka wskazówek dotyczących umieszczania reklam Google AdSense na stronie:</p>
                    
                    <ul class="list-disc pl-6 mt-2 space-y-2">
                        <li>Nie umieszczaj więcej niż 3 reklam AdSense na jednej stronie.</li>
                        <li>Umieszczaj reklamy w miejscach, gdzie będą widoczne, ale nie przeszkadzające w korzystaniu ze strony.</li>
                        <li>Reklamy są wyświetlane tylko wtedy, gdy są włączone i ich data ważności (jeśli określona) jest aktualna.</li>
                        <li>Kod AdSense należy wkleić bez modyfikacji z panelu AdSense Google.</li>
                        <li>Reklamy oznaczone jako "Wszystkie strony" będą wyświetlane na wszystkich stronach witryny.</li>
                        <li>Reklamy oznaczone dla konkretnej strony będą wyświetlane tylko na tej stronie.</li>
                    </ul>
                </div>
            </div>
            
            <!-- Ads list -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Lista Reklam</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pozycja</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Strona</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Okres</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($ads as $ad): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $ad['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($ad['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($positions[$ad['position']] ?? $ad['position']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($pages[$ad['page']] ?? $ad['page']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($ad['enabled']): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Włączona</span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Wyłączona</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($ad['start_date'] && $ad['end_date']): ?>
                                            <?php echo htmlspecialchars($ad['start_date'] . ' - ' . $ad['end_date']); ?>
                                        <?php elseif ($ad['start_date']): ?>
                                            Od <?php echo htmlspecialchars($ad['start_date']); ?>
                                        <?php elseif ($ad['end_date']): ?>
                                            Do <?php echo htmlspecialchars($ad['end_date']); ?>
                                        <?php else: ?>
                                            Bez ograniczeń
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="ads.php?action=edit&id=<?php echo $ad['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edytuj</a>
                                        <?php if ($ad['enabled']): ?>
                                            <a href="ads.php?action=disable&id=<?php echo $ad['id']; ?>" class="text-yellow-600 hover:text-yellow-900 mr-3">Wyłącz</a>
                                        <?php else: ?>
                                            <a href="ads.php?action=enable&id=<?php echo $ad['id']; ?>" class="text-green-600 hover:text-green-900 mr-3">Włącz</a>
                                        <?php endif; ?>
                                        <a href="ads.php?action=delete&id=<?php echo $ad['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Czy na pewno chcesz usunąć tę reklamę?')">Usuń</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($ads)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Nie znaleziono reklam.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
include_once 'includes/admin_footer.php';
?>