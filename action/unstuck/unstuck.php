<?php
// action/unstuck/unstuck.php - Centralized unstuck handler
require_once __DIR__ . '/../../init.php';  // Loads DB, config, session, etc.

// Only allow logged-in users
if (!Session::isLoggedIn()) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$accountId = Session::getAccountId();
$unstuckConfig = $config['unstuck'];

// Handle POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['unstuck_char_id'])) {
    // Invalid access - redirect to dashboard or 404
    header('Location: ' . BASE_URL . 'dashboard');
    exit;
}

$token = $_POST['csrf_token'];
if (!csrf_verify($token)) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['msg']['form_csrferror']];
} else {
    $charId = (int) $_POST['unstuck_char_id'];

    // Fetch char for this account
    $char = db_fetch(
        "SELECT char_id, online FROM `char` WHERE char_id = :cid AND account_id = :aid LIMIT 1",
        [':cid' => $charId, ':aid' => $accountId]
    );

    if (!$char) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['msg']['action_unotfnd']];
    } elseif ((int) $char['online'] === 1) {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['msg']['action_unisonl']];
    } elseif ($unstuckConfig['enabled']) {
        $ok = db_execute(
            "UPDATE `char` 
             SET last_map = :map, last_x = :x, last_y = :y 
             WHERE char_id = :cid",
            [
                ':map' => $unstuckConfig['town'],
                ':x'   => $unstuckConfig['x'],
                ':y'   => $unstuckConfig['y'],
                ':cid' => $charId,
            ]
        );

        if ($ok) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => $config['msg']['action_unsucce']];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['msg']['action_unerror']];
        }
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => $config['msg']['action_undisab']];
    }
}

// Redirect back (use POSTed redirect if set, else default to dashboard)
$redirect = $_POST['redirect'];
header('Location: ' . $redirect);
exit;