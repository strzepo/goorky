<?php
// Language handling mechanism
session_start();

// Default language
$default_lang = 'en';

// Available languages
$available_languages = ['pl', 'en'];

// Set default language to English if not set
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';
}

// Handle language switching
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'pl'])) {
    $_SESSION['language'] = $_GET['lang'];
    
    // If the user is logged in, update their language preference
    if (isset($_SESSION['user']['id'])) {
        require_once __DIR__ . '/db.php';
        
        $stmt = $pdo->prepare("UPDATE users SET language = ? WHERE id = ?");
        $stmt->execute([$_SESSION['language'], $_SESSION['user']['id']]);
        $_SESSION['user']['language'] = $_SESSION['language'];
    }
    
    // Redirect to remove the query string
    $redirect = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $redirect");
    exit;
}

// Load language file
$lang = [];
$language = $_SESSION['language'] ?? 'en';
$langFile = __DIR__ . '/lang/' . $language . '.php';

if (file_exists($langFile)) {
    require_once $langFile;
} else {
    // Fallback to English
    $enFile = __DIR__ . '/lang/en.php';
    if (file_exists($enFile)) {
        require_once $enFile;
    } else {
        // Minimalne tłumaczenia, jeśli nie ma plików językowych
        $lang = [
            'current_language' => 'English',
            'language_code' => 'en',
            'menu_home' => 'Home',
            'footer_rights' => 'All rights reserved.',
            'ad_placeholder' => 'Advertisement space'
        ];
        error_log("Uwaga: Brak plików językowych. Proszę utworzyć pliki w katalogu includes/lang/");
    }
}

// Language Switcher Function
function languageSwitcher() {
    global $lang;
    
    // Get site settings
    $settings = getSiteSettings();
    $currentLang = $_SESSION['language'] ?? 'en';
    $currentUrl = strtok($_SERVER['REQUEST_URI'], '?');
    
    $html = '<div class="relative inline-block text-left">';
    $html .= '<div>';
    $html .= '<a href="?lang='.($currentLang == 'en' ? 'pl' : 'en').'" class="inline-flex items-center text-gray-600 hover:text-blue-600">';
    
    // Show flag icon instead of the globe
    if ($currentLang == 'en') {
        $html .= '<img src="/assets/images/flags/pl.svg" alt="Polski" class="w-5 h-5 mr-1">';
        $html .= '<span class="hidden md:inline-block">Polski</span>';
    } else {
        $html .= '<img src="/assets/images/flags/gb.svg" alt="English" class="w-5 h-5 mr-1">';
        $html .= '<span class="hidden md:inline-block">English</span>';
    }
    
    $html .= '</a>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

// Function to get site settings
function getSiteSettings() {
    static $settings = null;
    
    if ($settings === null) {
        try {
            global $pdo;
            if (isset($pdo)) {
                $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
                $settingsData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                $settings = $settingsData;
            } else {
                $settings = [];
            }
        } catch (Exception $e) {
            $settings = [];
        }
    }
    
    return $settings;
}