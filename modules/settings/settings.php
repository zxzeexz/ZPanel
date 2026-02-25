<?php
/**
 * modules/settings/settings.php
 * ZPanel account settings module
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
require_once __DIR__ . '/../../init.php';

// Ensure logged in
if (!Session::isLoggedIn()) {
    Session::cleanupExpired();  // Run cleanup here — after validation set $last_error
    $lastError = Session::getLastError();
    $redirect = BASE_URL . 'login?logout=2';
    $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? (BASE_URL . 'dashboard');
    redirect($redirect);
}

$accountId = Session::getAccountId();
$username  = Session::get('username');

// Fetch account info from `login` table
$account = db_fetch(
    "SELECT username, email, sex, birthdate, password
     FROM cp_accounts
     WHERE username = :u",
    [':u' => $username]
);

if (!$account) {
    die("Account not found.");
}

$success = $error = "";

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['new_password'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = $config['msg']['form_csrferror'];
    } else {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Basic validation
        if ($newPassword !== $confirmPassword) {
            $error = $config['msg']['settng_inpmism'];
        } elseif (strlen($newPassword) < 6) {
            $error = $config['msg']['settng_inpulen'];
        } else {
            $hashMethod = strtolower($config['security']['hash_method'] ?? 'md5');

            $dbPass = $account['user_pass'];
            $isValid = false;

            if ($hashMethod === 'bcrypt') {
                $isValid = password_verify($currentPassword, $dbPass);
            } elseif ($hashMethod === 'plain') {
                $isValid = $currentPassword === $dbPass;
            } elseif ($hashMethod === 'md5') {
                $isValid = md5($currentPassword) === $dbPass;
            }

            if (!$isValid) {
                $error = $config['msg']['settng_xcurpas'];
            } else {
                // Hash new password
                if ($hashMethod === 'bcrypt') {
                    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
                } elseif ($hashMethod === 'plain') {
                    $newHash = $newPassword;
                } elseif ($hashMethod === 'md5') {
                    $newHash = md5($newPassword);
                } else {
                    $newHash = $newPassword; // fallback
                }

                // Update in `login`
                $stmt1 = $GLOBALS['db']->prepare("UPDATE login SET user_pass = :pass WHERE userid = :u");
                $ok1 = $stmt1->execute([':pass' => $newHash, ':u' => $username]);
                $rows1 = $stmt1->rowCount();

                // Update in `cp_accounts`
                $stmt2 = $GLOBALS['db']->prepare("UPDATE cp_accounts SET password = :pass WHERE username = :u");
                $ok2 = $stmt2->execute([':pass' => $newHash, ':u' => $username]);
                $rows2 = $stmt2->rowCount();

                if ($ok1 && $ok2 && ($rows1 > 0 || $rows2 > 0)) {
                    $success = $config['msg']['settng_success'];
                } else {
                    $error = 'Failed to update password in database. Please try again or contact admin.';
                }
            }
        }
    }
}?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-3">
                <i class="fas fa-cog"></i> Account Settings
            </h2>
            <div class="btn-group" role="group">
                <a href="<?= BASE_URL ?>dashboard" class="btn btn-outline-primary">
                    <i class="fas fa-home"></i> Return to dashboard
                </a>
            </div>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-secondary text-white">
            <i class="fas fa-user"></i> Account Information
        </div>
        <div class="card-body">
            <p><strong>Username:</strong> <?= e($account['username']) ?></p>
            <p><strong>Email:</strong> <?= e($account['email']) ?></p>
            <p><strong>Sex:</strong> <?= e($account['sex']) ?></p>
            <p><strong>Birthdate:</strong> <?= e($account['birthdate']) ?></p>
        </div>
    </div>

    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-key"></i> Change Password
        </div>
        <div class="card-body">
            <form method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </form>
        </div>
    </div>
</div>