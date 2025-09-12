<?php
/**
 * lib/csrf.php
 * ZPanel CSRF token helper functions
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */

/**
 * Generate or return the current CSRF token
 *
 * @return string
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate a hidden input field for forms
 *
 * @return string
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verify a submitted CSRF token
 *
 * @param string $token
 * @return bool
 */
function csrf_verify(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Optional: Regenerate CSRF token (call after successful form submission if desired)
 */
function csrf_regenerate(): void
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
