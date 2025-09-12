<?php
/**
 * modules/login/login.php
 * ZPanel login module
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
require_once __DIR__ . '/../../init.php';

// If already logged in, redirect
if (Session::isLoggedIn()) {
    redirect(BASE_URL . 'dashboard');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF: use procedural helper loaded by init.php
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_verify($token)) {
        $error = "CSRF validation failed. Please reload the page and try again.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = "Please enter both username and password.";
        } else {
            // Fetch account from `login` table
            $sql = "SELECT account_id, userid, user_pass FROM `login` WHERE userid = :u LIMIT 1";
            $row = db_fetch($sql, [':u' => $username]);

            if ($row) {
                $dbPass = $row['user_pass'];
                $isValid = false;

                // bcrypt / password_hash
                if (password_verify($password, $dbPass)) {
                    $isValid = true;
                }
                // plain text
                elseif ($password === $dbPass) {
                    $isValid = true;
                }
                // legacy md5
                elseif (md5($password) === $dbPass) {
                    $isValid = true;
                }

                if ($isValid) {
                    // Create session with real game account_id and username
                    // Ensure Session::create expects (int $accountId, string $username)
                    Session::create((int)$row['account_id'], $row['userid']);

                    $redirectTo = $_SESSION['redirect_after_login'] ?? (BASE_URL . 'dashboard');
                    unset($_SESSION['redirect_after_login']);

                    redirect($redirectTo);
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "Account not found.";
            }
        }
    }
}
// Determine whether registration requires verification
$requiresVerification = false;
if (isset($config['registration']['email_verification'])) {
	$requiresVerification = (bool)$config['registration']['email_verification'];
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-sign-in-alt"></i> Login</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <!-- CSRF token (procedural) -->
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username"
                                   class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password"
                                   class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <small>
                        <a href="<?= BASE_URL ?>register">Create an account</a>
						<?php if ($requiresVerification): ?>
                            | <a href="<?= BASE_URL ?>resendve">Resend confirmation email</a>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
