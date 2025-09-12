<?php
/**
 * modules/settings/settings.php
 * ZPanel account settings module
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../lib/csrf.php';

if (!Session::isLoggedIn()) {
    redirect(BASE_URL . 'login');
}

$accountId = Session::getAccountId();
$username  = Session::get('username');

// Fetch account info from `login` table
$account = db_fetch(
    "SELECT userid AS username, email, sex, birthdate, user_pass
     FROM login
     WHERE userid = :u",
    [':u' => $username]
);

if (!$account) {
    die("Account not found.");
}

$success = $error = "";

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = "Invalid CSRF token.";
    } else {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Basic validation
        if ($newPassword !== $confirmPassword) {
            $error = "New passwords do not match.";
        } elseif (strlen($newPassword) < 6) {
            $error = "New password must be at least 6 characters.";
        } else {
            $config = include __DIR__ . '/../../config.php';
            $hashMethod = $config['security']['hash_method'] ?? 'md5';

            $currentHash = ($hashMethod === 'md5')
                ? md5($currentPassword)
                : $currentPassword;

            // Verify current password in `login`
            if ($account['user_pass'] !== $currentHash) {
                $error = "Current password is incorrect.";
            } else {
                $newHash = ($hashMethod === 'md5')
                    ? md5($newPassword)
                    : $newPassword;

                // Update in `login`
                db_execute(
                    "UPDATE login SET user_pass = :pass WHERE userid = :u",
                    [':pass' => $newHash, ':u' => $username]
                );

                // Update in `cp_accounts`
                db_execute(
                    "UPDATE cp_accounts SET password = :pass WHERE username = :u",
                    [':pass' => $newHash, ':u' => $username]
                );

                $success = "Password updated successfully!";
            }
        }
    }
}
?>

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
