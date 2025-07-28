<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'stagemanager_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        )
    );
    // Test connection
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage() . "<br>Vérifiez que MySQL est démarré et que la base de données 'stagemanager_db' existe.");
}
?>
