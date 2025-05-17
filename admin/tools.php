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

// Połączenie z bazą danych
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/language.php';

// Tworzenie tabeli tools, jeśli nie istnieje
try {
    $tableExists = $pdo->query("SHOW TABLES LIKE 'tools'")->rowCount() > 0;
    
    if (!$tableExists) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS tools (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            icon VARCHAR(100),
            enabled TINYINT(1) DEFAULT 1,
            display_order INT DEFAULT 0,
            category VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Dodanie domyślnych narzędzi
        $defaultTools = [
            ['BMI Calculator', 'bmi', 'Calculate your Body Mass Index.', 'chart-bar', 1, 1, 'calculators'],
            ['Calories Calculator', 'calories', 'Calculate your daily caloric needs.', 'fire', 1, 2, 'calculators'],
            ['Unit Converter', 'units', 'Convert between different units of measurement.', 'arrows-expand', 1, 3, 'calculators'],
            ['Date Calculator', 'dates', 'Calculate the difference between dates.', 'calendar', 1, 4, 'calculators'],
            ['Password Generator', 'password-generator', 'Generate secure random passwords.', 'key', 1, 5, 'tools'],
            ['YouTube Downloader', 'youtube', 'Download videos from YouTube.', 'film', 1, 6, 'downloaders'],
            ['Instagram Downloader', 'instagram', 'Download photos and videos from Instagram.', 'camera', 1, 7, 'downloaders'],
            ['Facebook Downloader', 'facebook', 'Download videos from Facebook.', 'thumbs-up', 1, 8, 'downloaders'],
            ['Vimeo Downloader', 'vimeo', 'Download videos from Vimeo.', 'video', 1, 9, 'downloaders']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO tools (name, slug, description, icon, enabled, display_order, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($defaultTools as $tool) {
            $stmt->execute($tool);
        }
        
        $success = "Tabela narzędzi została utworzona i wypełniona domyślnymi danymi.";
    }
} catch (PDOException $e) {
    $error = "Błąd podczas tworzenia tabeli narzędzi: " . $e->getMessage();
}

// Obsługa włączania/wyłączania narzędzia
if (isset($_GET['action']) && ($_GET['action'] === 'enable' || $_GET['action'] === 'disable') && isset($_GET['id'])) {
    $toolId = (int)$_GET['id'];
    $enabled = $_GET['action'] === 'enable' ? 1 : 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE tools SET enabled = ? WHERE id = ?");
        $stmt->execute([$enabled, $toolId]);
        
        $status = $enabled ? 'włączone' : 'wyłączone';
        $success = "Narzędzie zostało $status.";
        
        // Przekieruj, aby uniknąć ponownej zmiany przy odświeżeniu strony
        header("Location: tools.php?status=$status");
        exit;
    } catch (Exception $e) {
        $error = "Błąd podczas aktualizacji statusu narzędzia: " . $e->getMessage();
    }
}

// Obsługa zmiany kolejności
if (isset($_POST['action']) && $_POST['action'] === 'update_order') {
    try {
        $stmt = $pdo->prepare("UPDATE tools SET display_order = ? WHERE id = ?");
        
        foreach ($_POST['order'] as $id => $order) {
            $stmt->execute([(int)$order, (int)$id]);
        }
        
        $success = "Kolejność narzędzi została zaktualizowana.";
        
        // Przekieruj, aby uniknąć ponownej aktualizacji przy odświeżeniu strony
        header("Location: tools.php?order_updated=1");
        exit;
    } catch (Exception $e) {
        $error = "Błąd podczas aktualizacji kolejności narzędzi: " . $e->getMessage();
    }
}

// Obsługa dodawania/edycji narzędzia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && ($_POST['action'] === 'add' || $_POST['action'] === 'edit')) {
    // Pobranie i walidacja danych
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $enabled = isset($_POST['enabled']) ? 1 : 0;
    
    // Jeśli slug jest pusty, wygeneruj go z nazwy
    if (empty($slug) && !empty($name)) {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
    }
    
    // Podstawowa walidacja
    if (empty($name) || empty($slug)) {
        $error = "Nazwa i slug są wymagane.";
    } else {
        try {
            if ($_POST['action'] === 'add') {
                // Sprawdź, czy slug już istnieje
                $stmt = $pdo->prepare("SELECT * FROM tools WHERE slug = ?");
                $stmt->execute([$slug]);
                
                if ($stmt->rowCount() > 0) {
                    $error = "Narzędzie o podanym slugu już istnieje.";
                } else {
                    // Znajdź najwyższy display_order
                    $maxOrder = $pdo->query("SELECT MAX(display_order) FROM tools")->fetchColumn();
                    $displayOrder = $maxOrder ? $maxOrder + 1 : 1;
                    
                    // Dodaj nowe narzędzie
                    $stmt = $pdo->prepare("INSERT INTO tools (name, slug, description, icon, enabled, display_order, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $slug, $description, $icon, $enabled, $displayOrder, $category]);
                    
                    $success = "Narzędzie zostało pomyślnie dodane.";
                }
            } elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
                $toolId = (int)$_POST['id'];
                
                // Sprawdź, czy slug już istnieje (z wyłączeniem edytowanego narzędzia)
                $stmt = $pdo->prepare("SELECT * FROM tools WHERE slug = ? AND id != ?");
                $stmt->execute([$slug, $toolId]);
                
                if ($stmt->rowCount() > 0) {
                    $error = "Narzędzie o podanym slugu już istnieje.";
                } else {
                    // Aktualizuj narzędzie
                    $stmt = $pdo->prepare("UPDATE tools SET name = ?, slug = ?, description = ?, icon = ?, enabled = ?, category = ? WHERE id = ?");
                    $stmt->execute([$name, $slug, $description, $icon, $enabled, $category, $toolId]);
                    
                    $success = "Narzędzie zostało pomyślnie zaktualizowane.";
                }
            }
        } catch (Exception $e) {
            $error = "Błąd podczas zapisywania narzędzia: " . $e->getMessage();
        }
    }
}

// Pobieranie narzędzia do edycji
$editTool = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $toolId = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM tools WHERE id = ?");
        $stmt->execute([$toolId]);
        $editTool = $stmt->fetch();
    } catch (Exception $e) {
        $error = "Błąd podczas pobierania danych narzędzia: " . $e->getMessage();
    }
}

// Pobieranie listy narzędzi
try {
    $stmt = $pdo->query("SELECT * FROM tools ORDER BY display_order");
    $tools = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Błąd podczas pobierania listy narzędzi: " . $e->getMessage();
    $tools = [];
}

// Pobieranie listy kategorii
$categories = [];
foreach ($tools as $tool) {
    if (!empty($tool['category']) && !in_array($tool['category'], $categories)) {
        $categories[] = $tool['category'];
    }
}
sort($categories);

// Tytuł strony
$pageTitle = "Panel Administracyjny - Zarządzanie Narzędziami";
include_once 'includes/admin_header.php';
?>

<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <?php include_once 'includes/admin_sidebar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 overflow-auto">
        <main class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-semibold text-gray-800">Zarządzanie Narzędziami</h1>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success) || isset($_GET['status']) || isset($_GET['order_updated'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php 
                    if (isset($success)) {
                        echo htmlspecialchars($success);
                    } elseif (isset($_GET['status'])) {
                        echo "Narzędzie zostało " . htmlspecialchars($_GET['status']) . ".";
                    } elseif (isset($_GET['order_updated'])) {
                        echo "Kolejność narzędzi została zaktualizowana.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Form for adding/editing tools -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">
                    <?php echo isset($editTool) ? 'Edytuj Narzędzie' : 'Dodaj Nowe Narzędzie'; ?>
                </h2>
                
                <form method="POST" action="tools.php">
                    <input type="hidden" name="action" value="<?php echo isset($editTool) ? 'edit' : 'add'; ?>">
                    <?php if (isset($editTool)): ?>
                        <input type="hidden" name="id" value="<?php echo $editTool['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nazwa</label>
                            <input type="text" name="name" id="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editTool) ? htmlspecialchars($editTool['name']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                            <input type="text" name="slug" id="slug" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editTool) ? htmlspecialchars($editTool['slug']) : ''; ?>" placeholder="Zostanie wygenerowany automatycznie, jeśli nie podano">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Opis</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"><?php echo isset($editTool) ? htmlspecialchars($editTool['description']) : ''; ?></textarea>
                        </div>
                        
                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700">Ikona</label>
                            <input type="text" name="icon" id="icon" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editTool) ? htmlspecialchars($editTool['icon']) : ''; ?>">
                            <p class="mt-1 text-xs text-gray-500">Nazwa ikony z biblioteki (np. chart-bar, fire, calendar, itp.)</p>
                        </div>
                        
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Kategoria</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input list="categories" type="text" name="category" id="category" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editTool) ? htmlspecialchars($editTool['category']) : ''; ?>">
                                <datalist id="categories">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category); ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="enabled" id="enabled" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?php echo (isset($editTool) && $editTool['enabled']) || !isset($editTool) ? 'checked' : ''; ?>>
                            <label for="enabled" class="ml-2 block text-sm text-gray-900">Włączone</label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <?php if (isset($editTool)): ?>
                            <a href="tools.php" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md mr-2 hover:bg-gray-300 transition">Anuluj</a>
                        <?php endif; ?>
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                            <?php echo isset($editTool) ? 'Zapisz zmiany' : 'Dodaj narzędzie'; ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Reorder tools -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Sortowanie Narzędzi</h2>
                <p class="mb-4 text-gray-600">Ustaw kolejność wyświetlania narzędzi na stronie głównej.</p>
                
                <form method="POST" action="tools.php">
                    <input type="hidden" name="action" value="update_order">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kolejność</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategoria</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($tools as $tool): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" name="order[<?php echo $tool['id']; ?>]" value="<?php echo $tool['display_order']; ?>" min="1" class="w-16 px-2 py-1 border border-gray-300 rounded">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($tool['name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($tool['slug']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($tool['category'] ?? '-'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php if ($tool['enabled']): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Włączone</span>
                                            <?php else: ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Wyłączone</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($tools)): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nie znaleziono narzędzi.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                            Zapisz kolejność
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Tools list -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Lista Narzędzi</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategoria</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kolejność</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($tools as $tool): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $tool['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($tool['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($tool['slug']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($tool['category'] ?? '-'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $tool['display_order']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($tool['enabled']): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Włączone</span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Wyłączone</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="tools.php?action=edit&id=<?php echo $tool['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edytuj</a>
                                        <?php if ($tool['enabled']): ?>
                                            <a href="tools.php?action=disable&id=<?php echo $tool['id']; ?>" class="text-yellow-600 hover:text-yellow-900 mr-3">Wyłącz</a>
                                        <?php else: ?>
                                            <a href="tools.php?action=enable&id=<?php echo $tool['id']; ?>" class="text-green-600 hover:text-green-900 mr-3">Włącz</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($tools)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Nie znaleziono narzędzi.</td>
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