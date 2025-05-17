<?php
// Set default language to English
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';
}

// Handle language switching
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'pl'])) {
    $_SESSION['language'] = $_GET['lang'];
    
    // If the user is logged in, update their language preference
    if (isset($_SESSION['user']['id'])) {
        require_once __DIR__ . '/../../includes/db.php'; // Poprawna ścieżka do połączenia z bazą danych
        
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
$langFile = __DIR__ . '/lang/' . $_SESSION['language'] . '.php';

if (file_exists($langFile)) {
    require_once $langFile;
} else {
    // Fallback to English
    require_once __DIR__ . '/lang/en.php';
}

// Language Switcher Function
function languageSwitcher() {
    global $lang;
    $currentLang = $_SESSION['language'];
    $currentUrl = strtok($_SERVER['REQUEST_URI'], '?');
    
    $html = '<div class="relative inline-block text-left">';
    $html .= '<div>';
    $html .= '<button type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="language-menu" aria-expanded="true" aria-haspopup="true">';
    $html .= $lang['current_language'];
    $html .= '<svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">';
    $html .= '<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />';
    $html .= '</svg>';
    $html .= '</button>';
    $html .= '</div>';
    
    $html .= '<div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="language-menu" style="display: none;">';
    $html .= '<div class="py-1" role="none">';
    
    // English option
    $html .= '<a href="' . $currentUrl . '?lang=en" class="' . ($currentLang == 'en' ? 'bg-gray-100 text-gray-900' : 'text-gray-700') . ' block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">English</a>';
    
    // Polish option
    $html .= '<a href="' . $currentUrl . '?lang=pl" class="' . ($currentLang == 'pl' ? 'bg-gray-100 text-gray-900' : 'text-gray-700') . ' block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">Polski</a>';
    
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}