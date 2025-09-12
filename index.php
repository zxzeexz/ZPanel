<?php
/**
 * index.php
 * ZPanel root page
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
 
// Entry point
require_once __DIR__ . '/init.php';

// -----------------------------
// Parse request URI
// -----------------------------
$basePath   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$requestUri = str_replace($basePath, '', $_SERVER['REQUEST_URI']);
$requestUri = strtok($requestUri, '?');
$requestUri = trim($requestUri, '/');

// Default module
if (empty($requestUri)) {
    $requestUri = Session::isLoggedIn() ? 'dashboard' : 'login';
}

// Break URI into parts (module/action/etc.)
$parts  = explode('/', $requestUri);
$module = strtolower($parts[0] ?? 'login');

// -----------------------------
// Resolve module file
// -----------------------------
$moduleFile = __DIR__ . "/modules/{$module}/{$module}.php";

if (!file_exists($moduleFile)) {
    http_response_code(404);
    $moduleFile = __DIR__ . '/modules/errors/404.php';
}

// -----------------------------
// Load theme layout
// -----------------------------
$theme     = $config['theme']['default'];
$themePath = __DIR__ . "/themes/{$theme}/layouts";

// Include header
include $themePath . '/header.php';

// Load module
include $moduleFile;

// Include footer
include $themePath . '/footer.php';
