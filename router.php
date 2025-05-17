<?php
// router.php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // obsłuż plik statyczny (np. obraz, css)
}

include __DIR__ . '/index.php';