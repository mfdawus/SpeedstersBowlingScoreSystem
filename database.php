<?php
// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'speedsters_bowling_test');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Test database connection
function testConnection() {
    try {
        $pdo = getDBConnection();
        echo "Database connection successful!";
        return true;
    } catch(PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
        return false;
    }
}
?>
