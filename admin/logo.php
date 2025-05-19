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

// Inicjalizacja zmiennych
$error = null;
$success = null;

// Obsługa uploadu logo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_logo'])) {
    // Upewniamy się, że katalog uploads/logos istnieje
    $uploadDir = __DIR__ . '/../uploads/logos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Obsługa header logo
    if (!empty($_FILES['header_logo']['name'])) {
        $fileExtension = pathinfo($_FILES['header_logo']['name'], PATHINFO_EXTENSION);
        $fileName = 'header_logo_' . time() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['header_logo']['tmp_name'], $uploadFile)) {
            $logoPath = '/uploads/logos/' . $fileName;
            
            // Sprawdź czy istnieje wpis w tabeli settings
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'header_logo'");
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                // Aktualizuj istniejący wpis
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'header_logo'");
                $stmt->execute([$logoPath]);
            } else {
                // Dodaj nowy wpis
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, setting_description) VALUES (?, ?, 'image', 'appearance', 'Header logo image')");
                $stmt->execute(['header_logo', $logoPath]);
            }
            
            $success = $lang['logo_header_updated'] ?? 'Header logo has been updated successfully.';
        } else {
            $error = $lang['logo_upload_error'] ?? 'Error uploading header logo.';
        }
    }
    
    // Obsługa footer logo
    if (!empty($_FILES['footer_logo']['name'])) {
        $fileExtension = pathinfo($_FILES['footer_logo']['name'], PATHINFO_EXTENSION);
        $fileName = 'footer_logo_' . time() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['footer_logo']['tmp_name'], $uploadFile)) {
            $logoPath = '/uploads/logos/' . $fileName;
            
            // Sprawdź czy istnieje wpis w tabeli settings
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'footer_logo'");
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                // Aktualizuj istniejący wpis
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'footer_logo'");
                $stmt->execute([$logoPath]);
            } else {
                // Dodaj nowy wpis
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, setting_description) VALUES (?, ?, 'image', 'appearance', 'Footer logo image')");
                $stmt->execute(['footer_logo', $logoPath]);
            }
            
            $success = isset($success) ? $success . ' ' . ($lang['logo_footer_updated'] ?? 'Footer logo has been updated successfully.') : ($lang['logo_footer_updated'] ?? 'Footer logo has been updated successfully.');
        } else {
            $error = isset($error) ? $error . ' ' . ($lang['logo_footer_upload_error'] ?? 'Error uploading footer logo.') : ($lang['logo_footer_upload_error'] ?? 'Error uploading footer logo.');
        }
    }
    
    // Obsługa favicon
    if (!empty($_FILES['favicon']['name'])) {
        $fileExtension = pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION);
        $fileName = 'favicon.' . $fileExtension;
        $uploadFile = __DIR__ . '/../assets/images/' . $fileName;
        
        if (move_uploaded_file($_FILES['favicon']['tmp_name'], $uploadFile)) {
            $faviconPath = '/assets/images/' . $fileName;
            
            // Sprawdź czy istnieje wpis w tabeli settings
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'favicon'");
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                // Aktualizuj istniejący wpis
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'favicon'");
                $stmt->execute([$faviconPath]);
            } else {
                // Dodaj nowy wpis
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, setting_description) VALUES (?, ?, 'image', 'appearance', 'Website favicon')");
                $stmt->execute(['favicon', $faviconPath]);
            }
            
            $success = isset($success) ? $success . ' ' . ($lang['favicon_updated'] ?? 'Favicon has been updated successfully.') : ($lang['favicon_updated'] ?? 'Favicon has been updated successfully.');
        } else {
            $error = isset($error) ? $error . ' ' . ($lang['favicon_upload_error'] ?? 'Error uploading favicon.') : ($lang['favicon_upload_error'] ?? 'Error uploading favicon.');
        }
    }
    
    // Obsługa social logo/image
    if (!empty($_FILES['social_image']['name'])) {
        $fileExtension = pathinfo($_FILES['social_image']['name'], PATHINFO_EXTENSION);
        $fileName = 'social_image_' . time() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['social_image']['tmp_name'], $uploadFile)) {
            $imagePath = '/uploads/logos/' . $fileName;
            
            // Sprawdź czy istnieje wpis w tabeli settings
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'social_image'");
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                // Aktualizuj istniejący wpis
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'social_image'");
                $stmt->execute([$imagePath]);
            } else {
                // Dodaj nowy wpis
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, setting_description) VALUES (?, ?, 'image', 'seo', 'Social sharing image')");
                $stmt->execute(['social_image', $imagePath]);
            }
            
            $success = isset($success) ? $success . ' ' . ($lang['social_image_updated'] ?? 'Social media image has been updated successfully.') : ($lang['social_image_updated'] ?? 'Social media image has been updated successfully.');
        } else {
            $error = isset($error) ? $error . ' ' . ($lang['social_image_upload_error'] ?? 'Error uploading social media image.') : ($lang['social_image_upload_error'] ?? 'Error uploading social media image.');
        }
    }
}

// Pobranie aktualnych logo z bazy danych
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('header_logo', 'footer_logo', 'favicon', 'social_image')");
    $logos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    $error = "Error fetching logo settings: " . $e->getMessage();
    $logos = [];
}

// Tytuł strony
$pageTitle = $lang['logo_page_title'] ?? "Admin Panel - Logo Settings";
include_once 'includes/admin_header.php';
?>

<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <?php include_once 'includes/admin_sidebar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 overflow-auto">
        <main class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-semibold text-gray-800"><?php echo $lang['logo_settings'] ?? 'Logo Settings'; ?></h1>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Logo configuration form -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4"><?php echo $lang['logo_upload'] ?? 'Upload Logo Images'; ?></h2>
                
                <form method="POST" action="logo.php" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Header Logo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['header_logo'] ?? 'Header Logo'; ?></label>
                            <?php if (!empty($logos['header_logo'])): ?>
                                <div class="mb-4 p-4 bg-gray-100 rounded-lg">
                                    <img src="<?php echo htmlspecialchars($logos['header_logo']); ?>" alt="Header Logo" class="h-16 object-contain">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="header_logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500"><?php echo $lang['logo_recommended_size'] ?? 'Recommended size: 200px × 60px'; ?></p>
                        </div>
                        
                        <!-- Footer Logo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['footer_logo'] ?? 'Footer Logo'; ?></label>
                            <?php if (!empty($logos['footer_logo'])): ?>
                                <div class="mb-4 p-4 bg-gray-100 rounded-lg">
                                    <img src="<?php echo htmlspecialchars($logos['footer_logo']); ?>" alt="Footer Logo" class="h-16 object-contain">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="footer_logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500"><?php echo $lang['logo_recommended_size'] ?? 'Recommended size: 200px × 60px'; ?></p>
                        </div>
                        
                        <!-- Favicon -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['favicon'] ?? 'Favicon'; ?></label>
                            <?php if (!empty($logos['favicon'])): ?>
                                <div class="mb-4 p-4 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <img src="<?php echo htmlspecialchars($logos['favicon']); ?>" alt="Favicon" class="h-8 w-8 object-contain">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="favicon" accept="image/x-icon,image/png,image/jpeg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500"><?php echo $lang['favicon_recommended'] ?? 'Recommended formats: ICO, PNG (32x32px)'; ?></p>
                        </div>
                        
                        <!-- Social Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $lang['social_media_image'] ?? 'Social Media Image'; ?></label>
                            <?php if (!empty($logos['social_image'])): ?>
                                <div class="mb-4 p-4 bg-gray-100 rounded-lg">
                                    <img src="<?php echo htmlspecialchars($logos['social_image']); ?>" alt="Social Media Image" class="h-24 object-contain">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="social_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500"><?php echo $lang['social_image_recommended'] ?? 'Recommended size: 1200px × 630px for optimal sharing on social media'; ?></p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button type="submit" name="update_logo" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                            <?php echo $lang['save_changes'] ?? 'Save Changes'; ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Logo guidelines -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4"><?php echo $lang['logo_guidelines'] ?? 'Logo Guidelines'; ?></h2>
                
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="font-medium text-blue-700 mb-2"><?php echo $lang['logo_best_practices'] ?? 'Best Practices'; ?></h3>
                        <ul class="list-disc pl-5 space-y-1 text-blue-800">
                            <li><?php echo $lang['logo_transparent_bg'] ?? 'Use logos with transparent backgrounds (PNG format) for best results'; ?></li>
                            <li><?php echo $lang['logo_readable'] ?? 'Ensure that your logo is readable even at smaller sizes'; ?></li>
                            <li><?php echo $lang['logo_consistent'] ?? 'Maintain consistent branding across header and footer logos'; ?></li>
                            <li><?php echo $lang['logo_optimize'] ?? 'Optimize image size for faster page loading'; ?></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-medium mb-2"><?php echo $lang['logo_visibility'] ?? 'Logo Visibility'; ?></h3>
                        <p class="text-gray-600"><?php echo $lang['logo_visibility_desc'] ?? 'The header logo is displayed in the top navigation bar of your website. The footer logo appears in the footer section. Make sure both are clearly visible against their backgrounds.'; ?></p>
                    </div>
                    
                    <div>
                        <h3 class="font-medium mb-2"><?php echo $lang['social_sharing'] ?? 'Social Sharing'; ?></h3>
                        <p class="text-gray-600"><?php echo $lang['social_sharing_desc'] ?? 'The social media image is used when your website is shared on platforms like Facebook, Twitter, and LinkedIn. This image should be compelling and represent your brand well.'; ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
include_once 'includes/admin_footer.php';
?>