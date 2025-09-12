<?php
/**
 * modules/register/verify.php
 * ZPanel register module - verification handler
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
require_once __DIR__ . '/../../init.php';

$code = isset($_GET['code']) ? trim((string)$_GET['code']) : '';
$username = isset($_GET['user']) ? trim((string)$_GET['user']) : '';

$status  = 'error';
$message = 'Invalid or expired verification link.';

if ($code !== '' && $username !== '') {
    $row = db_fetch(
        "SELECT account_id, username, verified 
         FROM cp_accounts 
         WHERE username = :username AND activation_code = :code 
         LIMIT 1",
        [':username' => $username, ':code' => $code]
    );

    if ($row) {
        $isVerified = (int)($row['verified'] ?? 0);
        if ($isVerified === 1) {
            $status  = 'info';
            $message = 'This account has already been verified. You may log in.';
        } else {
            $ok = db_execute(
                "UPDATE cp_accounts 
                 SET verified = 1, activation_code = NULL 
                 WHERE account_id = :id",
                [':id' => $row['account_id']]
            );
            if ($ok) {
				// Fetch full account info for inserting into login
				$account = db_fetch(
					"SELECT username, email, password, sex, birthdate
					FROM cp_accounts WHERE account_id = :id LIMIT 1",
					[':id' => $row['account_id']]
				);

				if ($account) {
					// Insert into login table
					$sqlLogin = "INSERT INTO `login` (userid, user_pass, sex, email, group_id, birthdate) VALUES (:userid, :user_pass, :sex, :email, 0, :birthdate)";
					db_execute($sqlLogin, [
						':userid'    => $account['username'],
						':user_pass' => $account['password'],
						':sex'       => $account['sex'],
						':email'     => $account['email'],
						':birthdate' => $account['birthdate'],
					]);
				}
                $status  = 'success';
                $message = 'Your account has been verified! You can now log in.';
            } else {
                $status  = 'error';
                $message = 'Failed to verify account due to a server error. Please contact admin.';
            }
        }
    } else {
        $status  = 'error';
        $message = 'Verification failed. The link is invalid or the account does not exist.';
    }
} else {
    $status  = 'error';
    $message = 'Invalid verification link.';
}
// Render just the inner content header/footer are included by index.php
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <?php if ($status === 'success'): ?>
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h3 class="mb-2">Verified</h3>
                        <p class="mb-3"><?= e($message) ?></p>
                        <a href="<?= BASE_URL ?>login" class="btn btn-primary">Go to Login</a>
                    <?php elseif ($status === 'info'): ?>
                        <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                        <h3 class="mb-2">Already Verified</h3>
                        <p class="mb-3"><?= e($message) ?></p>
                        <a href="<?= BASE_URL ?>login" class="btn btn-primary">Go to Login</a>
                    <?php else: ?>
                        <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                        <h3 class="mb-2">Verification Failed</h3>
                        <p class="mb-3"><?= e($message) ?></p>
                        <a href="<?= BASE_URL ?>register" class="btn btn-secondary">Back to Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
