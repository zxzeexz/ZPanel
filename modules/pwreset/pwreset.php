<?php
/**
 * modules/pwreset/pwreset.php
 * ZPanel password reset module
 * Revision 1 [02-23-2026]
 * Zee ^_~
 */
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../lib/mailer.php';     // For sendMail(...)
require_once __DIR__ . '/../../lib/email_templates.php'; // Extend for reset template

// Toggle check
if (!($config['pwreset']['enabled'] ?? false)) {
    $error = $config['msg']['pwres_disable'];
} else {
    $error = '';
    $success = '';

    // Mode 1: Request reset (no GET params, show form)
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && empty($_GET['user']) && empty($_GET['token'])) {
        // Just show form
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_GET['user']) && empty($_GET['token'])) {
        // Process request
        if ($config['security']['csrf_protection'] ?? false) {
            $token = $_POST['csrf_token'] ?? '';
            if (!csrf_verify($token)) {
                $error = $config['msg']['form_csrferror'];
            }
        }

        $input = trim($_POST['input'] ?? '');
        if (!$error) {
            if ($input === '') {
                $error = $config['msg']['pwres_nullinp'];
            } else {
                // Find account by username or email (from cp_accounts table)
                $account = db_fetch(
                    "SELECT username FROM `cp_accounts` WHERE username = :username OR email = :email LIMIT 1",
                    [':username' => $input, ':email' => $input]
                );

                if (!$account) {
                    $error = $config['msg']['pwres_noaccnt'];
                } else {
                    $userid = $account['username'];

                    // Clean old requests
                    db_execute("DELETE FROM cp_pwreset WHERE expires_at < :now", [':now' => time()]);

                    // Generate token and expiry
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + ($config['pwreset']['expiry'] ?? 3600);

                    // Insert (replaces if unique on userid)
                    $ok = db_execute(
						"INSERT INTO `cp_pwreset` (userid, token, expires_at) 
						VALUES (:userid, :token, :expiry) 
						ON DUPLICATE KEY UPDATE token = :token_upd, expires_at = :expiry_upd",
						[
							':userid'     => $userid,
							':token'      => $token,
							':expiry'     => $expiry,
							':token_upd'  => $token,
							':expiry_upd' => $expiry
						]
					);

                    if ($ok) {
                        // Get email
                        $emailRow = db_fetch("SELECT email FROM `cp_accounts` WHERE username = :userid", [':userid' => $userid]);
                        $email = $emailRow['email'] ?? '';

                        // Build reset link
                        $resetLink = BASE_URL . 'pwreset?user=' . urlencode($userid) . '&token=' . urlencode($token);

                        // Email to player
                        $subject = $config['mail']['passwrdreset_email'] ?? 'YourRO - Password Reset';
                        $body = getResetEmailTemplate($userid, $resetLink, $config);

                        $sent = sendMail($email, $subject, $body);

                        if ($sent) {
                            $success = $config['msg']['pwres_reqsent'];
                        } else {
                            $error = $config['msg']['pwres_notsent'];
                        }
                    } else {
                        $error = $config['msg']['pwres_dberror'];
                    }
                }
            }
        }
    } elseif (!empty($_GET['user']) && !empty($_GET['token'])) {
        // Mode 2: Validate link and show/process reset form
        $userid = trim($_GET['user'] ?? '');
        $token = trim($_GET['token'] ?? '');

        $request = db_fetch(
            "SELECT * FROM cp_pwreset WHERE userid = :userid AND token = :token AND expires_at > :now LIMIT 1",
            [':userid' => $userid, ':token' => $token, ':now' => time()]
        );

        if (!$request) {
            $error = $config['msg']['pwres_badlink'];
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Process reset
                if ($config['security']['csrf_protection'] ?? false) {
                    $csrf = $_POST['csrf_token'] ?? '';
                    if (!csrf_verify($csrf)) {
                        $error = $config['msg']['form_csrferror'];
                    }
                }

                $newPass = $_POST['new_password'] ?? '';
                $confirm = $_POST['confirm_password'] ?? '';

                if (!$error) {
                    if ($newPass !== $confirm) {
                        $error = $config['msg']['pwres_inpmism'];
                    } elseif (strlen($newPass) < 6) {
                        $error = $config['msg']['pwres_inpulen'];
                    } else {
                        // Hash new password (consistent with register/login)
                        $hashMethod = strtolower($config['security']['hash_method'] ?? 'md5');
                        if ($hashMethod === 'md5') {
                            $hash = md5($newPass);
                        } elseif ($hashMethod === 'plain') {
                            $hash = $newPass;
                        } else {
                            $hash = password_hash($newPass, PASSWORD_BCRYPT);
                        }

                        // Update cp_accounts table first
                        $ok = db_execute(
                            "UPDATE `cp_accounts` SET password = :hash WHERE username = :userid",
                            [':hash' => $hash, ':userid' => $userid]
                        );
						// Then we update the game login table
						$ok2 = db_execute(
                            "UPDATE `login` SET user_pass = :hash WHERE userid = :userid",
                            [':hash' => $hash, ':userid' => $userid]
                        );

                        if ($ok && $ok2) {
                            // Clean the request
                            db_execute("DELETE FROM cp_pwreset WHERE userid = :userid", [':userid' => $userid]);
                            $success = $config['msg']['pwres_success'];
                        } else {
                            $error = $config['msg']['pwres_dberror'];
                        }
                    }
                }
            }
            // Else show reset form
        }
    }
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-key"></i> Password Reset</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= e($success) ?></div>
                    <?php endif; ?>

                    <?php if (empty($_GET['user']) && empty($_GET['token']) && !$success && !$error): ?>
                        <!-- Request Form (only show if no success/error yet) -->
                        <form method="post" action="">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="input" class="form-label">Username or Email</label>
                                <input type="text" name="input" id="input" class="form-control" required autofocus>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-envelope"></i> Send Reset Link
                            </button>
                        </form>
                    <?php elseif (!empty($_GET['user']) && !empty($_GET['token']) && !$success && !$error): ?>
                        <!-- Reset Form (only show if link valid and no success/error) -->
                        <form method="post" action="">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check"></i> Reset Password
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                    <small><a href="<?= BASE_URL ?>login">Back to Login</a></small>
                </div>
            </div>
        </div>
    </div>
</div>