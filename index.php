<?php
/**
 * Główny plik aplikacji odpowiedzialny za routing
 * @author ToolsOnline
 * @version 1.0
 */

// Włączenie pełnego logowania błędów
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Konfiguracja
define('BASE_PATH', __DIR__);
define('INCLUDE_PATH', BASE_PATH . '/includes');
define('PAGES_PATH', BASE_PATH . '/pages');

// Dołączenie pliku z funkcjami
require_once INCLUDE_PATH . '/functions.php';

// Popraw adres URL jeśli zawiera index.php
if (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false) {
    $redirectUrl = str_replace('index.php', '', $_SERVER['REQUEST_URI']);
    header('Location: ' . $redirectUrl);
    exit;
}

// Pobieranie nazwy strony z URL
$page = isset($_GET['page']) ? sanitizeInput($_GET['page']) : 'home';

// Zabezpieczenie przed atakami typu path traversal
$page = str_replace(['..', '/', '\\'], '', $page);

// Debugowanie - można później usunąć
error_log("Ładowana strona: " . $page);

// Domyślny tytuł i opis strony
$pageTitle = 'Narzędzia Online - Kalkulatory, Konwertery i Downloadery';
$pageDescription = 'Darmowe narzędzia online: kalkulatory BMI i kalorii, konwertery jednostek, generatory haseł oraz downloadery wideo.';

// Określenie ścieżki do pliku strony
$pagePath = '';

// Sprawdzanie, czy strona znajduje się w podkatalogu downloaders
if (strpos($page, 'downloaders_') === 0) {
    $downloaderPage = str_replace('downloaders_', '', $page);
    if (file_exists(PAGES_PATH . '/downloaders/' . $downloaderPage . '.php')) {
        $pagePath = PAGES_PATH . '/downloaders/' . $downloaderPage . '.php';
    }
} else {
    if (file_exists(PAGES_PATH . '/' . $page . '.php')) {
        $pagePath = PAGES_PATH . '/' . $page . '.php';
    }
}

// Dołączenie nagłówka strony
require_once INCLUDE_PATH . '/header.php';

// Sprawdzenie czy plik strony istnieje
if (!empty($pagePath) && file_exists($pagePath)) {
    require_once $pagePath;
} else {
    // Strona 404 - nie znaleziono
    $pageTitle = '404 - Nie znaleziono strony | Narzędzia Online';
    $pageDescription = 'Strona, której szukasz, nie została znaleziona. Sprawdź nasze inne narzędzia online.';
    require_once PAGES_PATH . '/404.php';
}

// Dołączenie stopki strony
require_once INCLUDE_PATH . '/footer.php';
?>