<?php
/**
 * action/change_email/change_email.php
 * Handle email change request
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
require_once __DIR__ . '/../../init.php';

// Only allow logged-in users
if (!Session::isLoggedIn()) {
    Session::cleanupExpired();  // Run cleanup here — after validation set $last_error
    $lastError = Session::getLastError();
    $redirect = BASE_URL . 'login?logout=2';
    $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? (BASE_URL . 'dashboard');
    redirect($redirect);
}

// Handle POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$config['email_change']['enabled']) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['email_change']['enabled'] ? 'Invalid request.' : $config['msg']['settng_email_disabled']];
    header('Location: ' . BASE_URL . 'settings');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!csrf_verify($token)) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['msg']['form_csrferror']];
    header('Location: ' . BASE_URL . 'settings');
    exit;
}

$username = Session::get('username');

$account = db_fetch("SELECT * FROM cp_accounts WHERE username = :username LIMIT 1", [':username' => $username]);
if (!$account) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['msg']['settng_email_db_error']];
    header('Location: ' . BASE_URL . 'settings');
    exit;
}

$new_email = trim($_POST['new_email'] ?? '');

if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['msg']['settng_email_invalid']];
} elseif ($new_email === $account['email']) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['msg']['settng_email_same']];
} else {
    $exists = db_fetch("SELECT COUNT(*) AS cnt FROM cp_accounts WHERE email = :email", [':email' => $new_email]);
    if ((int)$exists['cnt'] > 0) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['msg']['settng_email_exists']];
    } else {
        // Clean expired
        db_execute("DELETE FROM cp_emailchange WHERE expires_at < :now", [':now' => time()]);

        // Generate token
        $token = bin2hex(random_bytes(32));
        $expiry = time() + $config['email_change']['expiry'];

        // Insert/overwrite with distinct placeholders for UPDATE
        $ok = db_execute(
            "INSERT INTO cp_emailchange (userid, token, new_email, expires_at) 
             VALUES (:userid, :token, :new_email, :expiry) 
             ON DUPLICATE KEY UPDATE token = :token_upd, new_email = :new_email_upd, expires_at = :expiry_upd",
            [
                ':userid' => $username,
                ':token' => $token,
                ':new_email' => $new_email,
                ':expiry' => $expiry,
                ':token_upd' => $token,
                ':new_email_upd' => $new_email,
                ':expiry_upd' => $expiry
            ]
        );

        if ($ok) {
            $verify_link = BASE_URL . 'modules/everify/verify_emailch.php?user=' . urlencode($username) . '&token=' . urlencode($token);

            require_once __DIR__ . '/../../lib/email_templates.php';
            $body = getChangeEmailTemplate($username, $new_email, $verify_link, $config);

            require_once __DIR__ . '/../../lib/mailer.php';
            $subject = $config['mail']['account_change_email'];
            $sent = sendMail($new_email, $subject, $body);

            $_SESSION['flash'] = ['type' => $sent ? 'success' : 'danger', 'msg' => $sent ? $config['msg']['settng_email_sent'] : 'Failed to send email.'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['msg']['settng_email_db_error']];
        }
    }
}

// Redirect back (use POSTed redirect if set, else default to settings)
$redirect = $_POST['redirect'] ?? BASE_URL . 'settings';
if (strpos($redirect, BASE_URL) !== 0) {
    $redirect = BASE_URL . 'settings';
}
header('Location: ' . $redirect);
exit;