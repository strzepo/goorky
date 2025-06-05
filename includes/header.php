<?php
// Sprawdź czy zmienna $pdo już istnieje, jeśli nie - zaimportuj plik db.php
if (!isset($pdo)) {
    require_once __DIR__ . '/db.php';
}
// Załaduj plik z funkcjami językowymi
require_once __DIR__ . '/language.php';

?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language'] ?? 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // Get site settings for SEO
    $settings = [];
try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
} catch (Exception $e) {
    // Silently fail if settings table doesn't exist yet
    error_log("Błąd przy pobieraniu ustawień: " . $e->getMessage());
}
    
    // Use settings for SEO if available, otherwise use defaults
    $meta_title = $settings['meta_title'] ?? $pageTitle;
    $meta_description = $settings['meta_description'] ?? $pageDescription;
    $meta_keywords = $settings['meta_keywords'] ?? '';
    $google_site_verification = $settings['google_site_verification'] ?? '';
    $bing_site_verification = $settings['bing_site_verification'] ?? '';
    
    // Output SEO meta tags
    echo "<title>" . htmlspecialchars($meta_title) . "</title>\n";
    echo "<meta name=\"description\" content=\"" . htmlspecialchars($meta_description) . "\">\n";
    
    if (!empty($meta_keywords)) {
        echo "<meta name=\"keywords\" content=\"" . htmlspecialchars($meta_keywords) . "\">\n";
    }
    
    // Site verification codes
    if (!empty($google_site_verification)) {
        echo "<meta name=\"google-site-verification\" content=\"" . htmlspecialchars($google_site_verification) . "\">\n";
    }
    
    if (!empty($bing_site_verification)) {
        echo "<meta name=\"msvalidate.01\" content=\"" . htmlspecialchars($bing_site_verification) . "\">\n";
    }
    
    // Canonical URL
    $canonicalUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    echo "<link rel=\"canonical\" href=\"" . htmlspecialchars($canonicalUrl) . "\">\n";
    
    // Open Graph tags
    echo "<meta property=\"og:title\" content=\"" . htmlspecialchars($meta_title) . "\">\n";
    echo "<meta property=\"og:description\" content=\"" . htmlspecialchars($meta_description) . "\">\n";
    echo "<meta property=\"og:type\" content=\"website\">\n";
    echo "<meta property=\"og:url\" content=\"" . htmlspecialchars($canonicalUrl) . "\">\n";
    echo "<meta property=\"og:image\" content=\"" . (!empty($settings['social_image']) ? htmlspecialchars($settings['social_image']) : 'https://goorky.com/assets/images/social-cover.jpg') . "\">\n";
    echo "<meta property=\"og:image:width\" content=\"1200\">\n";
    echo "<meta property=\"og:image:height\" content=\"630\">\n";
    echo "<meta property=\"og:locale\" content=\"" . ($_SESSION['language'] ?? 'en') . "\">\n";
    echo "<meta property=\"og:site_name\" content=\"" . htmlspecialchars($settings['site_name'] ?? 'ToolsOnline') . "\">\n";
    
    // Twitter Card tags
    echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    echo "<meta name=\"twitter:title\" content=\"" . htmlspecialchars($meta_title) . "\">\n";
    echo "<meta name=\"twitter:description\" content=\"" . htmlspecialchars($meta_description) . "\">\n";
    echo "<meta name=\"twitter:image\" content=\"" . (!empty($settings['social_image']) ? htmlspecialchars($settings['social_image']) : 'https://goorky.com/assets/images/social-cover.jpg') . "\">\n";
    
    if (!empty($settings['twitter_site'])) {
        echo "<meta name=\"twitter:site\" content=\"@" . htmlspecialchars($settings['twitter_site']) . "\">\n";
    }
    ?>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.3/dist/cdn.min.js" defer></script>
    
    <!-- Custom CSS -->
    <link href="/assets/css/styles.css" rel="stylesheet">
    <link href="/assets/css/popup.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Additional scripts -->
    <script src="/assets/js/main.js" defer></script>
    <!-- <script src="/assets/js/popup.js" defer></script> -->
    
    <!-- Google Analytics -->
    <?php if (!empty($settings['google_analytics'])): ?>
        <?php echo $settings['google_analytics']; ?>
    <?php endif; ?>

    <style>
    /* Mobile menu styling */
    .mobile-dropdown {
        transform-origin: top;
        transition: transform 0.2s ease-in-out, opacity 0.2s ease-in-out;
    }
    .mobile-dropdown.closed {
        transform: scaleY(0);
        opacity: 0;
    }
    .mobile-dropdown.open {
        transform: scaleY(1);
        opacity: 1;
    }
    .mobile-menu-container {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 50;
        background-color: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
</style>

    <script>
    // Initialize Alpine.js for mobile menu
    document.addEventListener('alpine:init', () => {
        Alpine.data('mobileNavigation', () => ({
            mobileMenuOpen: false,
            calculatorsOpen: false,
            downloadersOpen: false,
            toggleMenu() {
                this.mobileMenuOpen = !this.mobileMenuOpen;
            },
            toggleCalculators() {
                this.calculatorsOpen = !this.calculatorsOpen;
            },
            toggleDownloaders() {
                this.downloadersOpen = !this.downloadersOpen;
            }
        }));
    });
</script>

<!-- Google AdSense -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7818948548485078"
     crossorigin="anonymous"></script>

<meta name="google-adsense-account" content="ca-pub-7818948548485078">

</head>
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="/" class="flex items-center">
                    <?php if (!empty($settings['header_logo'])): ?>
                        <img src="<?php echo htmlspecialchars($settings['header_logo']); ?>" alt="<?php echo htmlspecialchars($settings['site_name'] ?? 'ToolsOnline'); ?>" class="h-8 w-auto">
                    <?php else: ?>
                        <span class="text-2xl font-bold text-blue-600"><?php echo htmlspecialchars($settings['site_name'] ?? 'ToolsOnline'); ?></span>
                    <?php endif; ?>
                </a>

                <!-- Mobile menu -->
<div x-data="{ mobileMenuOpen: false }" class="md:hidden">
    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-500 focus:outline-none">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
    
    <!-- Mobile menu dropdown -->
    <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="absolute top-full left-0 right-0 z-50 bg-white shadow-md mt-2 py-2 px-4">
        <a href="/" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_home'] ?? 'Home'; ?></a>
        
        <div x-data="{ calculatorsOpen: false }">
            <button @click="calculatorsOpen = !calculatorsOpen" class="flex w-full py-2 text-gray-600 hover:text-blue-600 justify-between items-center">
                <span><?php echo $lang['menu_calculators'] ?? 'Calculators'; ?></span>
                <svg class="ml-1 h-4 w-4 transition-transform" :class="{'rotate-180': calculatorsOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="calculatorsOpen" class="pl-4 space-y-2">
                <a href="/bmi" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_bmi'] ?? 'BMI Calculator'; ?></a>
                <a href="/calories" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_calories'] ?? 'Calorie Calculator'; ?></a>
                <a href="/units" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_units'] ?? 'Unit Converter'; ?></a>
                <a href="/dates" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_dates'] ?? 'Date Calculator'; ?></a>
            </div>
        </div>
        
        <a href="/password-generator" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_password'] ?? 'Password Generator'; ?></a>
        
        <div x-data="{ downloadersOpen: false }">
            <button @click="downloadersOpen = !downloadersOpen" class="flex w-full py-2 text-gray-600 hover:text-blue-600 justify-between items-center">
                <span><?php echo $lang['menu_downloaders'] ?? 'Downloaders'; ?></span>
                <svg class="ml-1 h-4 w-4 transition-transform" :class="{'rotate-180': downloadersOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="downloadersOpen" class="pl-4 space-y-2">
                <a href="/youtube" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_youtube'] ?? 'YouTube'; ?></a>
                <a href="/instagram" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_instagram'] ?? 'Instagram'; ?></a>
                <a href="/facebook" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_facebook'] ?? 'Facebook'; ?></a>
                <a href="/vimeo" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_vimeo'] ?? 'Vimeo'; ?></a>
            </div>
        </div>
        
        <div class="flex items-center justify-between pt-4 border-t mt-2">
            <?php echo languageSwitcher(); ?>
            
            <?php if (!isset($settings['enable_registration']) || $settings['enable_registration'] == '1'): ?>
            <a href="/admin/login.php" title="<?php echo $lang['login'] ?? 'Login'; ?>" class="text-gray-600 hover:text-blue-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
                
                <!-- Desktop menu -->
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="/" class="text-gray-600 hover:text-blue-600 <?php echo isActivePage('home'); ?>">
                        <?php echo $lang['menu_home'] ?? 'Home'; ?>
                    </a>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-gray-600 hover:text-blue-600 flex items-center focus:outline-none">
                            <?php echo $lang['menu_calculators'] ?? 'Calculators'; ?>
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                            <a href="/bmi" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('bmi'); ?>"><?php echo $lang['menu_bmi'] ?? 'BMI Calculator'; ?></a>
                            <a href="/calories" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('calories'); ?>"><?php echo $lang['menu_calories'] ?? 'Calorie Calculator'; ?></a>
                            <a href="/units" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('units'); ?>"><?php echo $lang['menu_units'] ?? 'Unit Converter'; ?></a>
                            <a href="/dates" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('dates'); ?>"><?php echo $lang['menu_dates'] ?? 'Date Calculator'; ?></a>
                        </div>
                    </div>
                    <a href="/password-generator" class="text-gray-600 hover:text-blue-600 <?php echo isActivePage('password_generator'); ?>"><?php echo $lang['menu_password'] ?? 'Password Generator'; ?></a>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-gray-600 hover:text-blue-600 flex items-center focus:outline-none">
                            <?php echo $lang['menu_downloaders'] ?? 'Downloaders'; ?>
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                            <a href="/youtube" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('downloaders_youtube'); ?>"><?php echo $lang['menu_youtube'] ?? 'YouTube'; ?></a>
                            <a href="/instagram" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('downloaders_instagram'); ?>"><?php echo $lang['menu_instagram'] ?? 'Instagram'; ?></a>
                            <a href="/facebook" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('downloaders_facebook'); ?>"><?php echo $lang['menu_facebook'] ?? 'Facebook'; ?></a>
                            <a href="/vimeo" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('downloaders_vimeo'); ?>"><?php echo $lang['menu_vimeo'] ?? 'Vimeo'; ?></a>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-xl">
                        <!-- Language switch -->
                        <?php echo languageSwitcher(); ?>
                        
<!-- Login link - only show if registration is enabled -->
<?php if (($settings['enable_registration'] ?? '1') == '1'): ?>
<a href="/admin/login.php" title="<?php echo $lang['login'] ?? 'Login'; ?>" class="text-gray-600 hover:text-blue-600">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
    </svg>
</a>
<?php endif; ?>
                    </div>
                </nav>
            </div>
            
            <!-- Mobile menu -->
            <div x-data="{ mobileMenuOpen: false }" x-show="mobileMenuOpen" class="md:hidden mt-2 py-2 border-t border-gray-200">
                <a href="/" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_home'] ?? 'Home'; ?></a>
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center w-full py-2 text-gray-600 hover:text-blue-600">
                        <?php echo $lang['menu_calculators'] ?? 'Calculators'; ?>
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-4">
                        <a href="/bmi" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_bmi'] ?? 'BMI Calculator'; ?></a>
                        <a href="/calories" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_calories'] ?? 'Calorie Calculator'; ?></a>
                        <a href="/units" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_units'] ?? 'Unit Converter'; ?></a>
                        <a href="/dates" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_dates'] ?? 'Date Calculator'; ?></a>
                    </div>
                </div>
                <a href="/password-generator" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_password'] ?? 'Password Generator'; ?></a>
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center w-full py-2 text-gray-600 hover:text-blue-600">
                        <?php echo $lang['menu_downloaders'] ?? 'Downloaders'; ?>
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-4">
                        <a href="/youtube" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_youtube'] ?? 'YouTube'; ?></a>
                        <a href="/instagram" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_instagram'] ?? 'Instagram'; ?></a>
                        <a href="/facebook" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_facebook'] ?? 'Facebook'; ?></a>
                        <a href="/vimeo" class="block py-2 text-gray-600 hover:text-blue-600"><?php echo $lang['menu_vimeo'] ?? 'Vimeo'; ?></a>
                    </div>
                </div>
                <div class="flex items-center space-x-4 pt-4 px-2 text-xl">
                    <!-- Language switch -->
                    <?php echo languageSwitcher(); ?>
                    
                    <!-- Login link - only show if registration is enabled -->
                    <?php if (!isset($settings['enable_registration']) || $settings['enable_registration'] == '1'): ?>
                    <a href="/admin/login.php" title="<?php echo $lang['login'] ?? 'Login'; ?>" class="text-gray-600 hover:text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main content -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <!-- Banner AdSense - top of page -->
        <?php if ((!isset($settings['show_ads']) || $settings['show_ads'] == '1') && !empty($settings['ad_header'])): ?>
        <div class="w-full bg-gray-100 text-center py-4 mb-8">
            <!-- Google AdSense Code -->
            <?php echo $settings['ad_header']; ?>
        </div>
        <?php else: ?>
        <div class="w-full bg-gray-100 text-center py-4 mb-8">
            <!-- Placeholder for ad -->
            <div class="text-gray-500"><?php echo $lang['ad_placeholder'] ?? 'Advertisement space'; ?></div>
        </div>
        <?php endif; ?>