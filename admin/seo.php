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

$error = null;
$success = null;

// Sprawdź, czy tabela settings istnieje
try {
    $tableExists = $pdo->query("SHOW TABLES LIKE 'settings'")->rowCount() > 0;
    
    if (!$tableExists) {
        // Utworzenie tabeli settings
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
        
        // Dodanie ustawień SEO
        $seoSettings = [
            ['meta_title', 'ToolsOnline - Free Online Tools', 'text', 'seo', 'Default meta title'],
            ['meta_description', 'Free online tools: calculators, converters and downloaders all in one place.', 'textarea', 'seo', 'Default meta description'],
            ['meta_keywords', 'online tools, calculators, converters, downloaders', 'text', 'seo', 'Meta keywords (comma separated)'],
            ['google_site_verification', '', 'text', 'seo', 'Google site verification code'],
            ['bing_site_verification', '', 'text', 'seo', 'Bing site verification code'],
            ['robots_txt', 'User-agent: *\nAllow: /', 'textarea', 'seo', 'Robots.txt content'],
            ['social_image', '', 'image', 'seo', 'Social media sharing image'],
            ['social_title', '', 'text', 'seo', 'Social media sharing title'],
            ['social_description', '', 'textarea', 'seo', 'Social media sharing description'],
            ['twitter_site', '', 'text', 'seo', 'Twitter site handle (without @)']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, setting_description) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($seoSettings as $setting) {
            $stmt->execute($setting);
        }
        
        $success = "Tabela ustawień SEO została utworzona i wypełniona domyślnymi danymi.";
    }
} catch (PDOException $e) {
    $error = "Błąd podczas tworzenia tabeli ustawień SEO: " . $e->getMessage();
}

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_seo'])) {
    try {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        
        foreach ($_POST['settings'] as $key => $value) {
            $stmt->execute([$value, $key]);
        }
        
        // Obsługa przesłanego obrazu
        if (!empty($_FILES['social_image']['name'])) {
            $uploadDir = __DIR__ . '/../assets/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = 'social-image.' . pathinfo($_FILES['social_image']['name'], PATHINFO_EXTENSION);
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['social_image']['tmp_name'], $uploadFile)) {
                $imagePath = '/assets/images/' . $fileName;
                $stmt->execute([$imagePath, 'social_image']);
            }
        }
        
        $success = $lang['settings_updated'] ?? "Ustawienia SEO zostały pomyślnie zaktualizowane.";
    } catch (Exception $e) {
        $error = "Błąd podczas aktualizacji ustawień SEO: " . $e->getMessage();
    }
}

// Pobieranie ustawień SEO
try {
    $stmt = $pdo->query("SELECT * FROM settings WHERE setting_group = 'seo' ORDER BY id");
    $seoSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Błąd podczas pobierania ustawień SEO: " . $e->getMessage();
    $seoSettings = [];
}

// Tytuł strony
$pageTitle = $lang['seo_page_title'] ?? "Panel Administracyjny - Ustawienia SEO";
include_once 'includes/admin_header.php';
?>

<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <?php include_once 'includes/admin_sidebar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 overflow-auto">
        <main class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-semibold text-gray-800"><?php echo $lang['seo_heading'] ?? 'Ustawienia SEO'; ?></h1>
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

            <!-- SEO Form -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4"><?php echo $lang['seo_general_settings'] ?? 'Ogólne ustawienia SEO'; ?></h2>
                
                <form method="POST" action="seo.php" enctype="multipart/form-data">
                    <div class="space-y-6">
                        <?php foreach ($seoSettings as $setting): ?>
                            <div class="mb-4">
                                <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                                    <?php echo htmlspecialchars($setting['setting_description']); ?>
                                </label>
                                
                                <?php if ($setting['setting_type'] === 'textarea'): ?>
                                    <textarea 
                                        id="<?php echo $setting['setting_key']; ?>" 
                                        name="settings[<?php echo $setting['setting_key']; ?>]" 
                                        rows="3" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    ><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                <?php elseif ($setting['setting_type'] === 'image'): ?>
                                    <div class="mb-2">
                                        <?php if (!empty($setting['setting_value'])): ?>
                                            <img src="<?php echo htmlspecialchars($setting['setting_value']); ?>" alt="<?php echo htmlspecialchars($setting['setting_description']); ?>" class="h-32 object-contain mb-2">
                                        <?php endif; ?>
                                        <input 
                                            type="file" 
                                            id="<?php echo $setting['setting_key']; ?>" 
                                            name="<?php echo $setting['setting_key']; ?>" 
                                            accept="image/*"
                                            class="block w-full text-sm text-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        >
                                        <input type="hidden" name="settings[<?php echo $setting['setting_key']; ?>]" value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                        <p class="mt-1 text-sm text-gray-500"><?php echo $lang['social_image_rec_size'] ?? 'Rekomendowany rozmiar: 1200 × 630 pikseli'; ?></p>
                                    </div>
                                <?php else: ?>
                                    <input 
                                        type="text" 
                                        id="<?php echo $setting['setting_key']; ?>" 
                                        name="settings[<?php echo $setting['setting_key']; ?>]" 
                                        value="<?php echo htmlspecialchars($setting['setting_value']); ?>" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    >
                                <?php endif; ?>
                                
                                <?php if ($setting['setting_key'] === 'meta_title'): ?>
                                    <p class="mt-1 text-sm text-gray-500"><?php echo $lang['meta_title_tip'] ?? 'Tytuł strony wyświetlany w wynikach wyszukiwania (50-60 znaków)'; ?></p>
                                <?php elseif ($setting['setting_key'] === 'meta_description'): ?>
                                    <p class="mt-1 text-sm text-gray-500"><?php echo $lang['meta_description_tip'] ?? 'Opis strony wyświetlany w wynikach wyszukiwania (150-160 znaków)'; ?></p>
                                <?php elseif ($setting['setting_key'] === 'meta_keywords'): ?>
                                    <p class="mt-1 text-sm text-gray-500"><?php echo $lang['meta_keywords_tip'] ?? 'Słowa kluczowe oddzielone przecinkami'; ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="flex justify-end">
                            <button type="submit" name="update_seo" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                                <?php echo $lang['save_changes'] ?? 'Zapisz zmiany'; ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- SEO Tips -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4"><?php echo $lang['seo_tips_title'] ?? 'Wskazówki SEO'; ?></h2>
                
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="font-medium"><?php echo $lang['seo_title_tips'] ?? 'Tytuły stron (Meta Title)'; ?></h3>
                        <ul class="list-disc pl-5 mt-2 space-y-1 text-sm">
                            <li><?php echo $lang['seo_title_tip_1'] ?? 'Powinny mieć od 50 do 60 znaków'; ?></li>
                            <li><?php echo $lang['seo_title_tip_2'] ?? 'Zawierać najważniejsze słowa kluczowe na początku'; ?></li>
                            <li><?php echo $lang['seo_title_tip_3'] ?? 'Każda strona powinna mieć unikalny tytuł'; ?></li>
                        </ul>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="font-medium"><?php echo $lang['seo_desc_tips'] ?? 'Opisy stron (Meta Description)'; ?></h3>
                        <ul class="list-disc pl-5 mt-2 space-y-1 text-sm">
                            <li><?php echo $lang['seo_desc_tip_1'] ?? 'Powinny mieć od 150 do 160 znaków'; ?></li>
                            <li><?php echo $lang['seo_desc_tip_2'] ?? 'Zawierać wezwanie do działania (call to action)'; ?></li>
                            <li><?php echo $lang['seo_desc_tip_3'] ?? 'Zawierać słowa kluczowe, ale w naturalny sposób'; ?></li>
                        </ul>
                    </div>
                    
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h3 class="font-medium"><?php echo $lang['seo_social_tips'] ?? 'Udostępnianie w mediach społecznościowych'; ?></h3>
                        <ul class="list-disc pl-5 mt-2 space-y-1 text-sm">
                            <li><?php echo $lang['seo_social_tip_1'] ?? 'Obraz społecznościowy powinien mieć wymiary 1200 × 630 pikseli'; ?></li>
                            <li><?php echo $lang['seo_social_tip_2'] ?? 'Tytuł społecznościowy może być inny niż meta title, ale powinien być chwytliwy'; ?></li>
                            <li><?php echo $lang['seo_social_tip_3'] ?? 'Opis społecznościowy powinien zachęcać do kliknięcia linku'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
include_once 'includes/admin_footer.php';
?>