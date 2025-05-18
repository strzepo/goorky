<?php
// router.php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Obsługa plików statycznych
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Przekazanie ścieżki do index.php
$_GET['page'] = trim($uri, '/');
if (empty($_GET['page'])) {
    $_GET['page'] = 'home';
}

include __DIR__ . '/index.php';