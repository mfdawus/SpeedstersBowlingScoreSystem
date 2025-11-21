<?php
// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'speedsters_bowling_test');
define('DB_USER', 'root');
define('DB_PASS', '');

// Auto-detect base URL for the application
// Works on both localhost subdirectory and production root domain
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Get the base path - find the root of the application
    // This file is in the root, so use its directory as the reference
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $scriptDir = __DIR__; // Directory where this database.php file is located
    
    // Calculate the base path by removing document root from script directory
    if ($documentRoot && strpos($scriptDir, $documentRoot) === 0) {
        $basePath = substr($scriptDir, strlen($documentRoot));
        $basePath = str_replace('\\', '/', $basePath); // Normalize Windows paths
    } else {
        // Fallback: extract from SCRIPT_NAME but only take first segment
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
        $pathParts = explode('/', trim($scriptPath, '/'));
        $basePath = !empty($pathParts[0]) ? '/' . $pathParts[0] : '';
    }
    
    // Ensure empty string if at root
    if ($basePath === '/' || $basePath === '\\') {
        $basePath = '';
    }
    
    define('BASE_URL', $protocol . '://' . $host . $basePath);
    define('BASE_PATH', $basePath); // Just the path portion, e.g., '/VipersVenomsBowlingSystem' or ''
}

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
