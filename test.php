<?php
echo "<h1>Strona testowa</h1>";
echo "<p>Ta strona działa!</p>";
echo "<h2>Informacje o ścieżkach:</h2>";
echo "<ul>";
echo "<li>Katalog główny: " . __DIR__ . "</li>";
echo "<li>Pełna ścieżka do tego pliku: " . __FILE__ . "</li>";
echo "</ul>";
echo "<h2>Sprawdzenie pliku 'pages/bmi.php':</h2>";
$bmiPath = __DIR__ . '/pages/bmi.php';
if (file_exists($bmiPath)) {
    echo "<p style='color: green;'>✅ Plik 'pages/bmi.php' istnieje!</p>";
} else {
    echo "<p style='color: red;'>❌ Plik 'pages/bmi.php' NIE istnieje!</p>";
}
echo "<p>Sprawdzana ścieżka: $bmiPath</p>";
echo "<h2>Zawartość katalogu 'pages':</h2>";
$pagesDir = __DIR__ . '/pages';
if (is_dir($pagesDir)) {
    $files = scandir($pagesDir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ Katalog 'pages' NIE istnieje!</p>";
}
?>