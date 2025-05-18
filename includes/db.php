<?php
$host = 'localhost';
$db   = 'goorky';  // Sprawdź czy nazwa bazy jest poprawna
$user = 'root';    // Sprawdź czy użytkownik ma dostęp
$pass = '';        // Sprawdź czy hasło jest poprawne
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log("Błąd połączenia z bazą danych: " . $e->getMessage());
    // Modyfikuj poniższą linię, aby wyświetlać błąd w bardziej przyjazny sposób
    echo "Przepraszamy, wystąpił problem z połączeniem z bazą danych. Spróbuj ponownie później.";
    exit;
}