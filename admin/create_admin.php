<?php
// Włącz wyświetlanie błędów
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Połączenie z bazą danych
require_once __DIR__ . '/../includes/db.php';

// Dane nowego administratora
$username = 'admin';
$email = 'admin@example.com';
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$role = 'admin';
$language = 'pl';

try {
    // Sprawdź, czy tabela users istnieje
    $tableExists = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    
    if (!$tableExists) {
        // Utwórz tabelę users jeśli nie istnieje
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(20) DEFAULT 'user',
            language VARCHAR(10) DEFAULT 'en',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        echo "Tabela users została utworzona.<br>";
    }
    
    // Sprawdź, czy tabela activity_logs istnieje
    $tableExists = $pdo->query("SHOW TABLES LIKE 'activity_logs'")->rowCount() > 0;
    
    if (!$tableExists) {
        // Utwórz tabelę activity_logs jeśli nie istnieje
        $pdo->exec("CREATE TABLE IF NOT EXISTS activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            action VARCHAR(100) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        echo "Tabela activity_logs została utworzona.<br>";
    }
    
    // Sprawdź, czy tabela password_resets istnieje
    $tableExists = $pdo->query("SHOW TABLES LIKE 'password_resets'")->rowCount() > 0;
    
    if (!$tableExists) {
        // Utwórz tabelę password_resets jeśli nie istnieje
        $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(255) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        echo "Tabela password_resets została utworzona.<br>";
    }
    
    // Sprawdź, czy użytkownik o podanej nazwie już istnieje
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        // Aktualizuj hasło dla istniejącego użytkownika
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hashedPassword, $username]);
        
        echo "Hasło użytkownika admin zostało zaktualizowane.<br>";
    } else {
        // Dodaj nowego administratora
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, language) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword, $role, $language]);
        
        echo "Administrator został pomyślnie utworzony.<br>";
    }
    
    echo "<div style='background-color: #d1e7dd; color: #0f5132; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<strong>Dane logowania:</strong><br>";
    echo "Login: <strong>" . htmlspecialchars($username) . "</strong><br>";
    echo "Hasło: <strong>" . htmlspecialchars($password) . "</strong><br>";
    echo "</div>";
    
    echo "<p style='margin-top: 20px;'><a href='login.php' style='background-color: #0d6efd; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Przejdź do strony logowania</a></p>";

} catch (PDOException $e) {
    echo "<div style='background-color: #f8d7da; color: #842029; padding: 15px; border-radius: 5px;'>";
    echo "<strong>Błąd bazy danych:</strong> " . $e->getMessage();
    echo "</div>";
}