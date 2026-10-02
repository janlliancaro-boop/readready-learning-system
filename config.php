<?php
// Start the session so we can keep login state across pages.
session_start();

// Set the default timezone to make date/time operations consistent.
date_default_timezone_set('Asia/Manila');

// Define the app name for reuse in templates.
if (!defined('APP_NAME')) {
    define('APP_NAME', 'ReadReady Dictionary and Nursery Learning System');
}

// Helper to read environment variables safely.
if (!function_exists('getEnv')) {
    function getEnv(string $key, $default = null) {
        $value = getenv($key);

        if ($value !== false && $value !== null) {
            return $value;
        }

        return $default;
    }
}

// Store database configuration.
$dbConfig = [
    'host' => getEnv('DB_HOST', 'localhost'),
    'port' => getEnv('DB_PORT', '3306'),
    'db'   => getEnv('DB_NAME', 'readready_db'),
    'user' => getEnv('DB_USER', 'root'),
    'pass' => getEnv('DB_PASS', ''),
    'charset' => 'utf8mb4',
];

// Database connection helper.
if (!function_exists('getPDO')) {
    function getPDO() {
        static $pdo = null;

        // Only create the database connection once.
        if ($pdo === null) {
            global $dbConfig;

            $dsn = 'mysql:host=' . $dbConfig['host']
                 . ';port=' . $dbConfig['port']
                 . ';dbname=' . $dbConfig['db']
                 . ';charset=' . $dbConfig['charset'];

            try {
                $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage() . '. Please create the database readready_db and update config.php.');
            }
        }

        return $pdo;
    }
}

// Include common helper functions used across the app ONLY ONCE.
if (!function_exists('redirect')) {
    require_once __DIR__ . '/includes/functions.php';
}
