<?php
/**
 * modules/resendve/resendve.php
 * ZPanel Resend verification email module
 * Revision 2 [9-21-2025]
 * Zee ^_~
 */
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../lib/mailer.php';
require_once __DIR__ . '/../../lib/email_templates.php';

// Check if email verification is enabled in config
if (empty($config['registration']['email_verification'])): ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="alert alert-warning shadow-sm">
                    <i class="fas fa-exclamation-triangle"></i>
                    Email verification is currently disabled. This page is not available.
                </div>
            </div>
        </div>
    </div>
    <?php return; // Stop here if feature is disabled
endif;

// Page variables
$error = '';
$success = '';
$inputValue = '';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check (friendly message instead of abrupt die)
    if (!empty($config['security']['csrf_protection'])) {
        $token = $_POST['csrf_token'] ?? '';
        if (!csrf_verify($token)) {
            $error = 'Invalid CSRF token. Please reload the page and try again.';
        }
    }

    $identifier = trim((string)($_POST['username_or_email'] ?? ''));
    $inputValue = $identifier;

    if (!$error) {
        if ($identifier === '') {
            $error = 'Please enter your username or email.';
        } else {
            // Find account by username or email
            $account = db_fetch(
				"SELECT account_id, username, email, activation_code, verified
				FROM cp_accounts
				WHERE username = :u OR email = :e
				LIMIT 1",
				[
					':u' => $identifier,
					':e' => $identifier
				]
			);


            if (!$account) {
				//Real error for debugging.
                //$error = 'No account found with that username or email.';
				$error = 'No pending verification found under this username or email.';
            } elseif ((int)($account['verified'] ?? 0) === 1) {
				//Real notice for debugging.
                //$success = 'This account is already verified. You can log in.';
				$error = 'No pending verification found under this username or email.';
            } elseif (empty($account['activation_code'])) {
				//Real notice for debugging.
                //$error = 'No activation code on file for this account. Please contact support.';
				$error = 'No pending verification found under this username or email.';
            } else {
                // Build activation link that points to the register verification script
                $siteUrl = $config['site']['url'] ?? BASE_URL;
				$rootPath = $config['site']['root_path'];
                $activationLink = $siteUrl . $rootPath . '/modules/register/verify.php?user=' . urlencode($account['username']) . '&code=' . urlencode($account['activation_code']);

                $subject = $config['mail']['verification_email'] ?? 'Activate your account';
                $body = getVerifyEmailTemplate($account['username'], $activationLink, $config);

                // Use the helper alias (uses global $config internally)
                $sent = sendMail($account['email'], $subject, $body);

                if ($sent) {
					//Detailed confirmation for debugging.
                    //$success = 'A verification email has been sent to <strong>' . e($account['email']) . '</strong>.';
					$success = 'Verification email has been re-sent. Please make sure to check your spam/junk mail.';
                } else {
                    $error = 'Failed to send verification email. Please contact the administrator.';
                }
            }
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-envelope-open-text"></i> Resend Verification Email
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endif; ?>

                    <form method="post" action="">
                        <?php if (!empty($config['security']['csrf_protection'])): ?>
                            <?= csrf_field() ?>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="username_or_email" class="form-label">Username or Email</label>
                            <input type="text" id="username_or_email" name="username_or_email"
                                   class="form-control" required
                                   value="<?= e($inputValue) ?>"
                                   placeholder="Enter your username or email">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Resend Verification Email
                            </button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="<?= BASE_URL ?>login">Back to login</a>
                    </div>
                </div>
                <div class="card-footer text-white">
                    If you no longer have access to the email on file, contact the administrator for help.
                </div>
            </div>
        </div>
    </div>
</div>
