<?php
// config/db.php
$host = '127.0.0.1';
$db   = 'hotel_app';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo 'Error DB: ' . $e->getMessage();
    exit;
}

// Helper escape
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
