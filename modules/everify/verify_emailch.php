<?php
/**
 * modules/settings/verify_email.php
 * Email change verification (no login required)
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
require_once __DIR__ . '/../../init.php';

$token = trim($_GET['token'] ?? '');
$user = trim($_GET['user'] ?? '');

$status = 'error';
$message = $config['msg']['settng_email_badlink'];

if ($token && $user) {
    $request = db_fetch(
        "SELECT new_email FROM cp_emailchange WHERE userid = :user AND token = :token AND expires_at > :now LIMIT 1",
        [':user' => $user, ':token' => $token, ':now' => time()]
    );

    if ($request) {
        $ok1 = db_execute("UPDATE cp_accounts SET email = :new_email WHERE username = :user", [':new_email' => $request['new_email'], ':user' => $user]);
        $ok2 = db_execute("UPDATE login SET email = :new_email WHERE userid = :user", [':new_email' => $request['new_email'], ':user' => $user]);

        if ($ok1 && $ok2) {
            db_execute("DELETE FROM cp_emailchange WHERE userid = :user", [':user' => $user]);
            $status = 'success';
            $message = $config['msg']['settng_email_changed'];
        } else {
            $message = $config['msg']['settng_email_db_error'];
        }
    }
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <?php if ($status === 'success'): ?>
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h3>Email Changed</h3>
                        <p><?= e($message) ?></p>
                    <?php else: ?>
                        <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                        <h3>Verification Failed</h3>
                        <p><?= e($message) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>