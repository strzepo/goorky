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

// Tworzenie tabeli languages, jeśli nie istnieje
try {
    $tableExists = $pdo->query("SHOW TABLES LIKE 'languages'")->rowCount() > 0;
    
    if (!$tableExists) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS languages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(10) NOT NULL UNIQUE,
            name VARCHAR(100) NOT NULL,
            native_name VARCHAR(100) NOT NULL,
            flag VARCHAR(50),
            is_default TINYINT(1) DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Dodanie domyślnych języków
        $defaultLanguages = [
            ['en', 'English', 'English', 'gb', 1, 1],
            ['pl', 'Polish', 'Polski', 'pl', 0, 1]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO languages (code, name, native_name, flag, is_default, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($defaultLanguages as $language) {
            $stmt->execute($language);
        }
        
        $success = "Tabela języków została utworzona i wypełniona domyślnymi danymi.";
    }
} catch (PDOException $e) {
    $error = "Błąd podczas tworzenia tabeli języków: " . $e->getMessage();
}

// Obsługa ustawiania domyślnego języka
if (isset($_GET['action']) && $_GET['action'] === 'set_default' && isset($_GET['code'])) {
    $langCode = $_GET['code'];
    
    try {
        // Najpierw resetujemy domyślny język dla wszystkich wpisów
        $pdo->exec("UPDATE languages SET is_default = 0");
        
        // Ustawiamy wybrany język jako domyślny
        $stmt = $pdo->prepare("UPDATE languages SET is_default = 1 WHERE code = ?");
        $stmt->execute([$langCode]);
        
        $success = "Język został ustawiony jako domyślny.";
        
        // Przekieruj, aby uniknąć ponownej zmiany przy odświeżeniu strony
        header("Location: language.php?default_set=$langCode");
        exit;
    } catch (Exception $e) {
        $error = "Błąd podczas ustawiania domyślnego języka: " . $e->getMessage();
    }
}

// Obsługa włączania/wyłączania języka
if (isset($_GET['action']) && ($_GET['action'] === 'enable' || $_GET['action'] === 'disable') && isset($_GET['code'])) {
    $langCode = $_GET['code'];
    $active = $_GET['action'] === 'enable' ? 1 : 0;
    
    try {
        // Sprawdzamy, czy język jest domyślny
        $stmt = $pdo->prepare("SELECT is_default FROM languages WHERE code = ?");
        $stmt->execute([$langCode]);
        $isDefault = $stmt->fetchColumn();
        
        if ($isDefault && $active === 0) {
            $error = "Nie można wyłączyć domyślnego języka.";
        } else {
            $stmt = $pdo->prepare("UPDATE languages SET is_active = ? WHERE code = ?");
            $stmt->execute([$active, $langCode]);
            
            $status = $active ? 'włączony' : 'wyłączony';
            $success = "Język został $status.";
            
            // Przekieruj, aby uniknąć ponownej zmiany przy odświeżeniu strony
            header("Location: language.php?status=$status&code=$langCode");
            exit;
        }
    } catch (Exception $e) {
        $error = "Błąd podczas aktualizacji statusu języka: " . $e->getMessage();
    }
}

// Obsługa dodawania/edycji języka
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && ($_POST['action'] === 'add' || $_POST['action'] === 'edit')) {
    // Pobranie i walidacja danych
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $nativeName = trim($_POST['native_name'] ?? '');
    $flag = trim($_POST['flag'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    // Podstawowa walidacja
    if (empty($code) || empty($name) || empty($nativeName)) {
        $error = "Kod języka, nazwa i nazwa natywna są wymagane.";
    } else {
        try {
            if ($_POST['action'] === 'add') {
                // Sprawdź, czy kod języka już istnieje
                $stmt = $pdo->prepare("SELECT * FROM languages WHERE code = ?");
                $stmt->execute([$code]);
                
                if ($stmt->rowCount() > 0) {
                    $error = "Język o podanym kodzie już istnieje.";
                } else {
                    // Dodaj nowy język
                    $stmt = $pdo->prepare("INSERT INTO languages (code, name, native_name, flag, is_active) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$code, $name, $nativeName, $flag, $isActive]);
                    
                    $success = "Język został pomyślnie dodany.";
                }
            } elseif ($_POST['action'] === 'edit' && isset($_POST['original_code'])) {
                $originalCode = $_POST['original_code'];
                
                // Sprawdź, czy kod języka już istnieje (jeśli został zmieniony)
                if ($code !== $originalCode) {
                    $stmt = $pdo->prepare("SELECT * FROM languages WHERE code = ?");
                    $stmt->execute([$code]);
                    
                    if ($stmt->rowCount() > 0) {
                        $error = "Język o podanym kodzie już istnieje.";
                    } else {
                        // Sprawdź, czy edytowany język jest domyślny
                        $stmt = $pdo->prepare("SELECT is_default FROM languages WHERE code = ?");
                        $stmt->execute([$originalCode]);
                        $isDefault = $stmt->fetchColumn();
                        
                        // Aktualizuj język
                        $stmt = $pdo->prepare("UPDATE languages SET code = ?, name = ?, native_name = ?, flag = ?, is_active = ? WHERE code = ?");
                        $stmt->execute([$code, $name, $nativeName, $flag, $isActive, $originalCode]);
                        
                        // Jeśli język jest domyślny, zaktualizuj język sesji
                        if ($isDefault) {
                            $_SESSION['language'] = $code;
                        }
                        
                        // Jeśli bieżący język użytkownika to edytowany język, zaktualizuj go
                        if ($_SESSION['language'] === $originalCode) {
                            $_SESSION['language'] = $code;
                        }
                        
                        $success = "Język został pomyślnie zaktualizowany.";
                    }
                } else {
                    // Aktualizuj język bez zmiany kodu
                    $stmt = $pdo->prepare("UPDATE languages SET name = ?, native_name = ?, flag = ?, is_active = ? WHERE code = ?");
                    $stmt->execute([$name, $nativeName, $flag, $isActive, $code]);
                    
                    $success = "Język został pomyślnie zaktualizowany.";
                }
            }
        } catch (Exception $e) {
            $error = "Błąd podczas zapisywania języka: " . $e->getMessage();
        }
    }
}

// Pobieranie języka do edycji
$editLanguage = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['code'])) {
    $langCode = $_GET['code'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM languages WHERE code = ?");
        $stmt->execute([$langCode]);
        $editLanguage = $stmt->fetch();
    } catch (Exception $e) {
        $error = "Błąd podczas pobierania danych języka: " . $e->getMessage();
    }
}

// Pobieranie listy języków
try {
    $stmt = $pdo->query("SELECT * FROM languages ORDER BY is_default DESC, name");
    $languages = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Błąd podczas pobierania listy języków: " . $e->getMessage();
    $languages = [];
}

// Tytuł strony
$pageTitle = "Panel Administracyjny - Ustawienia Języków";
include_once 'includes/admin_header.php';
?>

<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <?php include_once 'includes/admin_sidebar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 overflow-auto">
        <main class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-semibold text-gray-800">Ustawienia Języków</h1>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success) || isset($_GET['default_set']) || isset($_GET['status'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php 
                    if (isset($success)) {
                        echo htmlspecialchars($success);
                    } elseif (isset($_GET['default_set'])) {
                        echo "Język " . htmlspecialchars($_GET['default_set']) . " został ustawiony jako domyślny.";
                    } elseif (isset($_GET['status'])) {
                        echo "Język " . htmlspecialchars($_GET['code']) . " został " . htmlspecialchars($_GET['status']) . ".";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Form for adding/editing languages -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">
                    <?php echo isset($editLanguage) ? 'Edytuj Język' : 'Dodaj Nowy Język'; ?>
                </h2>
                
                <form method="POST" action="language.php">
                    <input type="hidden" name="action" value="<?php echo isset($editLanguage) ? 'edit' : 'add'; ?>">
                    <?php if (isset($editLanguage)): ?>
                        <input type="hidden" name="original_code" value="<?php echo $editLanguage['code']; ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700">Kod języka</label>
                            <input type="text" name="code" id="code" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editLanguage) ? htmlspecialchars($editLanguage['code']) : ''; ?>" <?php echo isset($editLanguage) && $editLanguage['is_default'] ? 'readonly' : ''; ?> placeholder="np. en, pl, de, fr">
                        </div>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nazwa angielska</label>
                            <input type="text" name="name" id="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editLanguage) ? htmlspecialchars($editLanguage['name']) : ''; ?>" placeholder="np. English, Polish, German">
                        </div>
                        
                        <div>
                            <label for="native_name" class="block text-sm font-medium text-gray-700">Nazwa natywna</label>
                            <input type="text" name="native_name" id="native_name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editLanguage) ? htmlspecialchars($editLanguage['native_name']) : ''; ?>" placeholder="np. English, Polski, Deutsch">
                        </div>
                        
                        <div>
                            <label for="flag" class="block text-sm font-medium text-gray-700">Kod flagi</label>
                            <input type="text" name="flag" id="flag" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editLanguage) ? htmlspecialchars($editLanguage['flag']) : ''; ?>" placeholder="np. gb, pl, de, fr">
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?php echo (isset($editLanguage) && $editLanguage['is_active']) || !isset($editLanguage) ? 'checked' : ''; ?> <?php echo isset($editLanguage) && $editLanguage['is_default'] ? 'disabled' : ''; ?>>
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktywny</label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <?php if (isset($editLanguage)): ?>
                            <a href="language.php" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md mr-2 hover:bg-gray-300 transition">Anuluj</a>
                        <?php endif; ?>
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                            <?php echo isset($editLanguage) ? 'Zapisz zmiany' : 'Dodaj język'; ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Current language selection -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Bieżący język</h2>
                
                <div class="flex items-center space-x-4 mb-4">
                    <div class="flex items-center">
                        <img src="https://flagcdn.com/w40/<?php echo isset($_SESSION['language']) && $_SESSION['language'] === 'en' ? 'gb' : ($_SESSION['language'] ?? 'gb'); ?>.png" alt="<?php echo $_SESSION['language'] ?? 'en'; ?>" class="h-8 w-auto">
                        <span class="ml-2 font-medium"><?php echo $lang['current_language']; ?></span>
                    </div>
                    
                    <div>
                        <?php echo languageSwitcher(); ?>
                    </div>
                </div>
                
                <p class="text-gray-600">
                    Zmiana języka w tym miejscu wpłynie tylko na bieżącą sesję. Aby zmienić domyślny język dla całej witryny, użyj opcji "Ustaw jako domyślny" w tabeli poniżej.
                </p>
            </div>
            
            <!-- Languages list -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Lista Języków</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kod</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flaga</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa angielska</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa natywna</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domyślny</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($languages as $language): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($language['code']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (!empty($language['flag'])): ?>
                                            <img src="https://flagcdn.com/w40/<?php echo htmlspecialchars($language['flag']); ?>.png" alt="<?php echo htmlspecialchars($language['code']); ?>" class="h-6 w-auto">
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($language['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($language['native_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($language['is_active']): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktywny</span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Nieaktywny</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($language['is_default']): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Domyślny</span>
                                        <?php else: ?>
                                            <a href="language.php?action=set_default&code=<?php echo $language['code']; ?>" class="text-blue-600 hover:text-blue-900" onclick="return confirm('Czy na pewno chcesz ustawić język <?php echo htmlspecialchars($language['name']); ?> jako domyślny?')">Ustaw jako domyślny</a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="language.php?action=edit&code=<?php echo $language['code']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edytuj</a>
                                        <?php if (!$language['is_default']): ?>
                                            <?php if ($language['is_active']): ?>
                                                <a href="language.php?action=disable&code=<?php echo $language['code']; ?>" class="text-yellow-600 hover:text-yellow-900 mr-3">Wyłącz</a>
                                            <?php else: ?>
                                                <a href="language.php?action=enable&code=<?php echo $language['code']; ?>" class="text-green-600 hover:text-green-900 mr-3">Włącz</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($languages)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Nie znaleziono języków.</td>
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