<?php
/**
 * init.php
 * ZPanel entry point triggers
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */

// -----------------------------
// Load Configuration
// -----------------------------
$config = require __DIR__ . '/config.php';

// -----------------------------
// Error Reporting (dev mode) [to enable, check config.php]
// -----------------------------
if (!empty($config['debug']) && $config['debug'] === true) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// -----------------------------
// Define Constants
// -----------------------------
define('SITE_NAME', $config['site']['name']);
$siteUrlClean = rtrim($config['site']['url'] ?? '', '/');
$rootPathRaw  = $config['site']['root_path'] ?? '';
$rootPathTrim = trim($rootPathRaw, '/');

$baseUrl = $siteUrlClean;
if ($rootPathTrim !== '') {
    $baseUrl .= '/' . $rootPathTrim;
}
// Ensure trailing slash so existing concatenations work
$baseUrl = rtrim($baseUrl, '/') . '/';

define('BASE_URL',  $baseUrl);
define('ROOT_PATH', __DIR__);


// -----------------------------
// Start Session
// -----------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------
// Database Connection (PDO)
// -----------------------------
try {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $config['db']['host'],
        $config['db']['name'],
        $config['db']['charset'] ?? 'utf8mb4'
    );

    $db = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    // Expose $db globally for functions.php and modules
    $GLOBALS['db'] = $db;
    $pdo = $db; // optional alias for modules using $pdo
    $GLOBALS['pdo'] = $pdo;

} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// -----------------------------
// Load Helpers & Classes
// -----------------------------
require_once ROOT_PATH . '/functions.php';
if (file_exists(ROOT_PATH . '/lib/csrf.php')) {
    require_once ROOT_PATH . '/lib/csrf.php';
}
require_once ROOT_PATH . '/lib/session.php';

// -----------------------------
// CSRF Protection Toggle
// -----------------------------
if (!empty($config['security']['csrf_protection']) && $config['security']['csrf_protection'] === true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!csrf_verify($token)) {
            die('CSRF validation failed.');
        }
    }
}

// -----------------------------
// Redirect after login support
// -----------------------------
if (!Session::isLoggedIn() && !preg_match('#/(login|register|resendve)#', $_SERVER['REQUEST_URI'])) {
    // Save intended destination (but skip login, register & resendve pages)
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
}

