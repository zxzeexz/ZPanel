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

// Determine type (modules or action)
$type = strtolower($parts[0] ?? 'modules');
if ($type === 'action') {
    $module = strtolower($parts[1] ?? '');
} else {
    $module = $type;  // For modules, the first part is the module name
    $type = 'modules';
}

// If module is empty for action, default or 404
if ($type === 'action' && empty($module)) {
    http_response_code(404);
    $type = 'modules';
    $module = 'errors/404';
}

// -----------------------------
// Resolve module/action file
// -----------------------------
$moduleFile = __DIR__ . "/{$type}/{$module}/{$module}.php";

if (!file_exists($moduleFile)) {
    http_response_code(404);
    $moduleFile = __DIR__ . '/modules/errors/404.php';
    $type = 'modules';  // Ensure 404 uses layout
}

// -----------------------------
// Load theme layout (only for modules, not actions)
// -----------------------------
if ($type === 'modules') {
    $theme     = $config['theme']['default'];
    $themePath = __DIR__ . "/themes/{$theme}/layouts";

    // Include header
    include $themePath . '/header.php';
}

// Load the file (action or module)
include $moduleFile;

// Include footer only for modules
if ($type === 'modules') {
    include $themePath . '/footer.php';
}