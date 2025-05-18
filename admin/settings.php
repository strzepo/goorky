<?php
// Enable error display
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Check if user has admin privileges
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Database connection
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/language.php';

// Create settings table if it doesn't exist
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
        
        // Add default settings
        $defaultSettings = [
            ['site_name', 'ToolsOnline', 'text', 'general', 'Site name displayed in header and title'],
            ['site_description', 'Free online tools: calculators, converters and downloaders.', 'textarea', 'general', 'Short site description used in meta tags'],
            ['contact_email', 'contact@example.com', 'email', 'general', 'Contact email address'],
            ['google_analytics', '', 'textarea', 'integrations', 'Google Analytics tracking code'],
            ['show_ads', '1', 'boolean', 'ads', 'Enable/disable ad display'],
            ['header_logo', '', 'image', 'appearance', 'Header logo image'],
            ['footer_logo', '', 'image', 'appearance', 'Footer logo image'],
            ['copyright_text', '&copy; ' . date('Y') . ' ToolsOnline. All rights reserved.', 'textarea', 'general', 'Copyright text in footer'],
            ['maintenance_mode', '0', 'boolean', 'system', 'Site maintenance mode'],
            ['maintenance_message', 'The site is temporarily unavailable due to maintenance. We apologize for the inconvenience.', 'textarea', 'system', 'Maintenance mode message'],
            ['default_language', 'en', 'select', 'localization', 'Default site language'],
            ['enable_multilingual', '1', 'boolean', 'localization', 'Enable multilingual support'],
            ['enable_registration', '1', 'boolean', 'users', 'Enable user registration'],
            ['date_format', 'd.m.Y', 'text', 'localization', 'Date format'],
            ['time_format', 'H:i', 'text', 'localization', 'Time format'],
            ['timezone', 'Europe/Warsaw', 'select', 'localization', 'Timezone'],
            ['meta_title', 'ToolsOnline - Free Online Tools', 'text', 'seo', 'Default meta title'],
            ['meta_description', 'Free online tools: calculators, converters and downloaders all in one place.', 'textarea', 'seo', 'Default meta description'],
            ['meta_keywords', 'online tools, calculators, converters, downloaders', 'text', 'seo', 'Meta keywords (comma separated)'],
            ['google_site_verification', '', 'text', 'seo', 'Google site verification code'],
            ['bing_site_verification', '', 'text', 'seo', 'Bing site verification code'],
            ['robots_txt', 'User-agent: *\nAllow: /', 'textarea', 'seo', 'Robots.txt content']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, setting_description) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
        
        $success = "Settings table was created and filled with default data.";
    }
} catch (PDOException $e) {
    $error = "Error creating settings table: " . $e->getMessage();
}

// Function to get settings
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

// Function to get setting groups
function getSettingGroups() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT DISTINCT setting_group FROM settings ORDER BY setting_group");
        $groups = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Group names in readable form
        $groupNames = [
            'general' => 'General',
            'integrations' => 'Integrations',
            'ads' => 'Advertisements',
            'system' => 'System',
            'localization' => 'Localization',
            'appearance' => 'Appearance',
            'users' => 'Users',
            'seo' => 'SEO & Meta'
        ];
        
        $result = [];
        foreach ($groups as $group) {
            $result[$group] = $groupNames[$group] ?? ucfirst($group);
        }
        
        return $result;
    } catch (Exception $e) {
        return [];
    }
}

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_settings') {
    try {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        
        foreach ($_POST['settings'] as $key => $value) {
            $stmt->execute([$value, $key]);
        }
        
        // Handle file uploads for logo images
        if (!empty($_FILES['header_logo']['name'])) {
            $uploadDir = __DIR__ . '/../assets/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = 'header_logo.' . pathinfo($_FILES['header_logo']['name'], PATHINFO_EXTENSION);
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['header_logo']['tmp_name'], $uploadFile)) {
                $logoPath = '/assets/images/' . $fileName;
                $stmt->execute([$logoPath, 'header_logo']);
            }
        }
        
        if (!empty($_FILES['footer_logo']['name'])) {
            $uploadDir = __DIR__ . '/../assets/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = 'footer_logo.' . pathinfo($_FILES['footer_logo']['name'], PATHINFO_EXTENSION);
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['footer_logo']['tmp_name'], $uploadFile)) {
                $logoPath = '/assets/images/' . $fileName;
                $stmt->execute([$logoPath, 'footer_logo']);
            }
        }
        
        $success = "Settings were successfully updated.";
    } catch (Exception $e) {
        $error = "Error updating settings: " . $e->getMessage();
    }
}

// Page title
$pageTitle = "Admin Panel - System Settings";
include_once 'includes/admin_header.php';
?>

<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <?php include_once 'includes/admin_sidebar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 overflow-auto">
        <main class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-semibold text-gray-800">System Settings</h1>
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
                <form method="POST" action="settings.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_settings">
                    
                    <div class="mb-6">
                        <div class="sm:hidden">
                            <label for="setting_group" class="sr-only">Select settings group</label>
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
                                                <option value="1" <?php echo $setting['setting_value'] === '1' ? 'selected' : ''; ?>>Enabled</option>
                                                <option value="0" <?php echo $setting['setting_value'] === '0' ? 'selected' : ''; ?>>Disabled</option>
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
                                    
                                    <?php elseif ($setting['setting_type'] === 'image'): ?>
                                        <div class="mt-1">
                                            <?php if (!empty($setting['setting_value'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo htmlspecialchars($setting['setting_value']); ?>" alt="Current <?php echo htmlspecialchars($setting['setting_key']); ?>" class="h-16 object-contain">
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                            <input type="hidden" name="settings[<?php echo $setting['setting_key']; ?>]" value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                        </div>
                                    
                                    <?php else: ?>
                                        <input type="text" id="settings-<?php echo $setting['setting_key']; ?>" name="settings[<?php echo $setting['setting_key']; ?>]" value="<?php echo htmlspecialchars($setting['setting_value']); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <?php endif; ?>
                                    
                                    <?php if ($setting['setting_type'] === 'textarea' && in_array($setting['setting_key'], ['ad_header', 'ad_footer', 'ad_sidebar'])): ?>
                                        <p class="mt-1 text-xs text-gray-500">Enter AdSense code or other ad HTML.</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Save Settings
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