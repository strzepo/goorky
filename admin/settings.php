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

// Tworzenie tabeli settings, jeśli nie istnieje
try {
    $tableExists = $pdo->query("SHOW TABLES LIKE 'settings'")->rowCount() > 0;
    
    if (!$tableExists) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT,
            setting_type VARCHAR(20) DEFAULT 'text',
            setting_group VARCHAR(50) DEFAULT 'general',
            setting_description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Dodanie domyślnych ustawień
        $defaultSettings = [
            ['site_name', 'ToolsOnline', 'text', 'general', 'Nazwa strony wyświetlana w nagłówku i tytule'],
            ['site_description', 'Darmowe narzędzia online: kalkulatory, konwertery i downloadery.', 'textarea', 'general', 'Krótki opis strony używany w znacznikach meta'],
            ['contact_email', 'contact@example.com', 'email', 'general', 'Adres email do kontaktu'],
            ['google_analytics', '', 'textarea', 'integrations', 'Kod śledzenia Google Analytics'],
            ['show_ads', '1', 'boolean', 'ads', 'Włącz/wyłącz wyświetlanie reklam'],
            ['ad_header', '', 'textarea', 'ads', 'Kod reklamy dla górnego bannera'],
            ['ad_footer', '', 'textarea', 'ads', 'Kod reklamy dla dolnego bannera'],
            ['ad_sidebar', '', 'textarea', 'ads', 'Kod reklamy dla paska bocznego'],
            ['copyright_text', '&copy; ' . date('Y') . ' ToolsOnline. Wszelkie prawa zastrzeżone.', 'textarea', 'general', 'Tekst praw autorskich w stopce'],
            ['maintenance_mode', '0', 'boolean', 'system', 'Tryb konserwacji strony'],
            ['maintenance_message', 'Strona jest chwilowo niedostępna z powodu prac konserwacyjnych. Przepraszamy za utrudnienia.', 'textarea', 'system', 'Komunikat w trybie konserwacji'],
            ['default_language', 'en', 'select', 'localization', 'Domyślny język strony'],
            ['enable_multilingual', '1', 'boolean', 'localization', 'Włącz obsługę wielu języków'],
            ['date_format', 'd.m.Y', 'text', 'localization', 'Format daty'],
            ['time_format', 'H:i', 'text', 'localization', 'Format czasu'],
            ['timezone', 'Europe/Warsaw', 'select', 'localization', 'Strefa czasowa']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, setting_description) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
        
        $success = "Tabela ustawień została utworzona i wypełniona domyślnymi danymi.";
    }
} catch (PDOException $e) {
    $error = "Błąd podczas tworzenia tabeli ustawień: " . $e->getMessage();
}

// Funkcja do pobierania ustawień
function getSettings($group = null) {
    global $pdo;
    
    try {
        if ($group) {
            $stmt = $pdo->prepare("SELECT * FROM settings WHERE setting_group = ? ORDER BY id");
            $stmt->execute([$group]);
        } else {
            $stmt = $pdo->query("SELECT * FROM settings ORDER BY setting_group, id");
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Funkcja do pobierania grup ustawień
function getSettingGroups() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT DISTINCT setting_group FROM settings ORDER BY setting_group");
        $groups = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Nazwy grup w czytelnej formie
        $groupNames = [
            'general' => 'Ogólne',
            'integrations' => 'Integracje',
            'ads' => 'Reklamy',
            'system' => 'System',
            'localization' => 'Lokalizacja'
        ];
        
        $result = [];
        foreach ($groups as $group) {
            $result[$group] = $groupNames[$group] ?? ucfirst($group);
        }
        $result = [];
        foreach ($groups as $group) {
            $result[$group] = $groupNames[$group] ?? ucfirst($group);
        }
        
        return $result;
    } catch (Exception $e) {
        return [];
    }
}

// Obsługa aktualizacji ustawień
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_settings') {
    try {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        
        foreach ($_POST['settings'] as $key => $value) {
            $stmt->execute([$value, $key]);
        }
        
        $success = "Ustawienia zostały pomyślnie zaktualizowane.";
    } catch (Exception $e) {
        $error = "Błąd podczas aktualizacji ustawień: " . $e->getMessage();
    }
}

// Tytuł strony
$pageTitle = "Panel Administracyjny - Ustawienia Systemu";
include_once 'includes/admin_header.php';
?>

<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <?php include_once 'includes/admin_sidebar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 overflow-auto">
        <main class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-semibold text-gray-800">Ustawienia Systemu</h1>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Settings form -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST" action="settings.php">
                    <input type="hidden" name="action" value="update_settings">
                    
                    <div class="mb-6">
                        <div class="sm:hidden">
                            <label for="setting_group" class="sr-only">Wybierz grupę ustawień</label>
                            <select id="setting_group" name="setting_group" class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <?php foreach (getSettingGroups() as $key => $name): ?>
                                    <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="hidden sm:block">
                            <nav class="flex space-x-4" aria-label="Tabs">
                                <?php foreach (getSettingGroups() as $key => $name): ?>
                                    <a href="#<?php echo $key; ?>" class="setting-tab px-3 py-2 font-medium text-sm rounded-md" data-tab="<?php echo $key; ?>">
                                        <?php echo htmlspecialchars($name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </nav>
                        </div>
                    </div>
                    
                    <?php foreach (getSettingGroups() as $group => $groupName): ?>
                        <div id="tab-<?php echo $group; ?>" class="setting-content space-y-6" style="display: none;">
                            <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($groupName); ?></h3>
                            
                            <?php foreach (getSettings($group) as $setting): ?>
                                <div class="mb-4">
                                    <label for="settings-<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700">
                                        <?php echo htmlspecialchars($setting['setting_description'] ?? $setting['setting_key']); ?>
                                    </label>
                                    
                                    <?php if ($setting['setting_type'] === 'textarea'): ?>
                                        <textarea id="settings-<?php echo $setting['setting_key']; ?>" name="settings[<?php echo $setting['setting_key']; ?>]" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                    
                                    <?php elseif ($setting['setting_type'] === 'boolean'): ?>
                                        <div class="mt-1">
                                            <select id="settings-<?php echo $setting['setting_key']; ?>" name="settings[<?php echo $setting['setting_key']; ?>]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                <option value="1" <?php echo $setting['setting_value'] === '1' ? 'selected' : ''; ?>>Włączone</option>
                                                <option value="0" <?php echo $setting['setting_value'] === '0' ? 'selected' : ''; ?>>Wyłączone</option>
                                            </select>
                                        </div>
                                    
                                    <?php elseif ($setting['setting_type'] === 'select' && $setting['setting_key'] === 'default_language'): ?>
                                        <div class="mt-1">
                                            <select id="settings-<?php echo $setting['setting_key']; ?>" name="settings[<?php echo $setting['setting_key']; ?>]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                <option value="en" <?php echo $setting['setting_value'] === 'en' ? 'selected' : ''; ?>>English</option>
                                                <option value="pl" <?php echo $setting['setting_value'] === 'pl' ? 'selected' : ''; ?>>Polski</option>
                                            </select>
                                        </div>
                                    
                                    <?php elseif ($setting['setting_type'] === 'select' && $setting['setting_key'] === 'timezone'): ?>
                                        <div class="mt-1">
                                            <select id="settings-<?php echo $setting['setting_key']; ?>" name="settings[<?php echo $setting['setting_key']; ?>]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                <?php
                                                $timezones = [
                                                    'Europe/Warsaw' => 'Europe/Warsaw',
                                                    'Europe/London' => 'Europe/London',
                                                    'Europe/Paris' => 'Europe/Paris',
                                                    'Europe/Berlin' => 'Europe/Berlin',
                                                    'America/New_York' => 'America/New_York',
                                                    'America/Chicago' => 'America/Chicago',
                                                    'America/Los_Angeles' => 'America/Los_Angeles',
                                                    'Asia/Tokyo' => 'Asia/Tokyo',
                                                    'Asia/Dubai' => 'Asia/Dubai',
                                                    'Australia/Sydney' => 'Australia/Sydney'
                                                ];
                                                foreach ($timezones as $value => $label):
                                                ?>
                                                    <option value="<?php echo $value; ?>" <?php echo $setting['setting_value'] === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    
                                    <?php else: ?>
                                        <input type="text" id="settings-<?php echo $setting['setting_key']; ?>" name="settings[<?php echo $setting['setting_key']; ?>]" value="<?php echo htmlspecialchars($setting['setting_value']); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <?php endif; ?>
                                    
                                    <?php if ($setting['setting_type'] === 'textarea' && in_array($setting['setting_key'], ['ad_header', 'ad_footer', 'ad_sidebar'])): ?>
                                        <p class="mt-1 text-xs text-gray-500">Wprowadź kod HTML reklamy AdSense lub innej reklamy.</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Zapisz ustawienia
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
    // Tabs functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Show first tab by default
        const firstTab = document.querySelector('.setting-tab');
        const firstTabId = firstTab.getAttribute('data-tab');
        document.getElementById('tab-' + firstTabId).style.display = 'block';
        firstTab.classList.add('bg-blue-100', 'text-blue-700');
        
        // Tab switching
        const tabs = document.querySelectorAll('.setting-tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                const tabId = this.getAttribute('data-tab');
                
                // Hide all tabs
                document.querySelectorAll('.setting-content').forEach(content => {
                    content.style.display = 'none';
                });
                
                // Remove active class from all tabs
                tabs.forEach(t => {
                    t.classList.remove('bg-blue-100', 'text-blue-700');
                    t.classList.add('text-gray-500', 'hover:text-gray-700');
                });
                
                // Show selected tab
                document.getElementById('tab-' + tabId).style.display = 'block';
                
                // Add active class to selected tab
                this.classList.add('bg-blue-100', 'text-blue-700');
                this.classList.remove('text-gray-500', 'hover:text-gray-700');
            });
        });
        
        // Mobile dropdown
        const mobileDropdown = document.getElementById('setting_group');
        if (mobileDropdown) {
            mobileDropdown.addEventListener('change', function() {
                const tabId = this.value;
                
                // Hide all tabs
                document.querySelectorAll('.setting-content').forEach(content => {
                    content.style.display = 'none';
                });
                
                // Show selected tab
                document.getElementById('tab-' + tabId).style.display = 'block';
            });
        }
    });
</script>

<?php
include_once 'includes/admin_footer.php';
?>