<?php
/**
 * modules/register/register.php
 * ZPanel register module
 * Revision 2 [9-21-2025]
 * Zee ^_~
 */
require_once __DIR__ . '/../../init.php';           // loads $config, session, functions (db helpers), etc.
require_once __DIR__ . '/../../lib/mailer.php';     // sendMail(...)
require_once __DIR__ . '/../../lib/email_templates.php'; // getVerifyEmailTemplate(...)

if (Session::isLoggedIn()) {
    redirect(BASE_URL . 'dashboard');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check (if enabled)
    if (!empty($config['security']['csrf_protection'])) {
        $token = $_POST['csrf_token'] ?? '';
        if (!csrf_verify($token)) {
            $error = "Invalid session token. Please try again.";
        }
    }

    // Sanitize & collect input
    $username  = trim((string)($_POST['username'] ?? ''));
    $email     = trim((string)($_POST['email'] ?? ''));
    $password  = (string)($_POST['password'] ?? '');
    $password2 = (string)($_POST['password2'] ?? '');
    $sex       = strtoupper(trim((string)($_POST['sex'] ?? 'M')));
    $birthdate = trim((string)($_POST['birthdate'] ?? ''));
    $ip        = getUserIp();

    // Basic validation
    if (!$error) {
        if ($username === '' || $email === '' || $password === '' || $password2 === '' || $sex === '' || $birthdate === '') {
            $error = "All fields are required.";
        } elseif ($password !== $password2) {
            $error = "Passwords do not match.";
        } elseif (!in_array($sex, ['M','F'], true)) {
            $error = "Invalid sex selection.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email address.";
        } else {
            $dt = DateTime::createFromFormat('Y-m-d', $birthdate);
            if (!$dt || $dt->format('Y-m-d') !== $birthdate) {
                $error = "Invalid birthdate format. Use YYYY-MM-DD.";
            }
        }
    }

    // Proceed if no validation errors
    if (!$error) {
        // Check username/email uniqueness
        $rowUser  = db_fetch("SELECT account_id FROM cp_accounts WHERE username = :u LIMIT 1", [':u' => $username]);
        $rowEmail = db_fetch("SELECT account_id FROM cp_accounts WHERE email = :e LIMIT 1",    [':e' => $email]);

        if ($rowUser) {
            $error = "Username already taken.";
        } elseif ($rowEmail) {
            $error = "Email already registered.";
        }
    }

    // Enforce max accounts per IP (if configured)
    if (!$error) {
        $limit = (int)($config['registration']['max_accounts_per_ip'] ?? 0);
        if ($limit > 0) {
            $row = db_fetch("SELECT COUNT(*) AS cnt FROM cp_accounts WHERE reg_ip = :ip", [':ip' => $ip]);
            $count = (int)($row['cnt'] ?? 0);
            if ($count >= $limit) {
                $error = "Maximum number of accounts reached for your IP address.";
            }
        }
    }

    // Insert account
    if (!$error) {
        // Hash password based on config
        $hashMethod = strtolower($config['security']['hash_method'] ?? 'md5');
        if ($hashMethod === 'md5') {
            $passwordHash = md5($password);
        } elseif ($hashMethod === 'plain') {
            $passwordHash = $password; // not recommended
        } else {
            // default to PHP password_hash (bcrypt)
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        }

        // Determine whether registration requires verification
        $requiresVerification = false;
        if (isset($config['registration']['email_verification'])) {
            $requiresVerification = (bool)$config['registration']['email_verification'];
        }

        $verified = $requiresVerification ? 0 : 1;
        $activationCode = $requiresVerification ? bin2hex(random_bytes(16)) : null;

        // Insert prepared
        $sql = "INSERT INTO cp_accounts
                (username, email, password, sex, birthdate, reg_ip, verified, activation_code, created_at)
                VALUES (:username, :email, :password, :sex, :birthdate, :reg_ip, :verified, :activation_code, NOW())";

        $ok = db_execute($sql, [
            ':username'        => $username,
            ':email'           => $email,
            ':password'        => $passwordHash,
            ':sex'             => $sex,
            ':birthdate'       => $birthdate,
            ':reg_ip'          => $ip,
            ':verified'        => $verified,
            ':activation_code' => $activationCode,
        ]);

        if ($ok) {
            // Send verification email if needed
            if ($requiresVerification) {
                // Build activation link (direct to modules/register/verify.php)
                $activationLink = rtrim($config['site']['url'], '/') 
				. $config['site']['root_path']
				. '/modules/register/verify.php'
				. '?user=' . urlencode($username) 
				. '&code=' . urlencode($activationCode);

                // Build email body using template function
                $subject = $config['mail']['verification_email'] ?? 'Activate your account';
                $body = getVerifyEmailTemplate($username, $activationLink, $config);

                $sent = sendMail($email, $subject, $body);

                if ($sent) {
                    $success = "Account created! Please check your email for the verification link.";
                } else {
                    // Email failed. keep account unverified, inform admin
                    $error = "Account created, but failed to send verification email. Contact admin.";
                }
            } else {
				// Directly verified → also insert into login table
				$sqlLogin = "INSERT INTO `login` (userid, user_pass, sex, email, group_id, birthdate) VALUES (:userid, :user_pass, :sex, :email, 0, :birthdate)";
				db_execute($sqlLogin, [
					':userid'    => $username,
					':user_pass' => $passwordHash,
					':sex'       => $sex,
					':email'     => $email,
					':birthdate' => $birthdate,
				]);
                $success = "Account created successfully. You may now log in.";
            }
        } else {
            $error = "Failed to create account. Please try again later.";
        }
    }
}

// Render only the inner content header/footer are included by index.php
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h3 class="mb-4">Register</h3>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success"><?= e($success) ?></div>
                    <?php endif; ?>

                    <form id="registerForm" method="post" action="">
    <?php if (!empty($config['security']['csrf_protection'])): ?>
        <?= csrf_field() ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required value="<?= e($_POST['username'] ?? '') ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Re-type Password</label>
            <input type="password" id="password2" name="password2" class="form-control" required>
            <div id="passwordError" class="text-danger small mt-1" style="display:none;">
                Passwords do not match.
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <label class="form-label">Sex</label>
            <select name="sex" class="form-select" required>
                <option value="M" <?= (($_POST['sex'] ?? 'M') === 'M') ? 'selected' : '' ?>>Male</option>
                <option value="F" <?= (($_POST['sex'] ?? '') === 'F') ? 'selected' : '' ?>>Female</option>
            </select>
        </div>

        <div class="col-md-3 mb-3">
            <label class="form-label">Birthdate</label>
            <input type="date" name="birthdate" class="form-control" required value="<?= e($_POST['birthdate'] ?? '') ?>">
        </div>
    </div>

    <div class="d-grid mt-3">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-user-plus"></i> Register
        </button>
    </div>
</form>

<script>
document.getElementById('registerForm').addEventListener('submit', function (e) {
    const pass1 = document.getElementById('password').value;
    const pass2 = document.getElementById('password2').value;
    const errorDiv = document.getElementById('passwordError');

    if (pass1 !== pass2) {
        e.preventDefault(); // stop form submission
        errorDiv.style.display = 'block';
    } else {
        errorDiv.style.display = 'none';
    }
});
</script>

                    <div class="mt-3 text-center">
                        <a href="<?= BASE_URL ?>login">Already have an account? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
