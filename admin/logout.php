<?php
session_start();

// Log the logout activity if user is logged in
if (isset($_SESSION['user']['id'])) {
    require_once __DIR__ . '/../includes/db.php';
    
    $userId = $_SESSION['user']['id'];
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, 'logout', ?)");
    $stmt->execute([$userId, $_SERVER['REMOTE_ADDR']]);
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;