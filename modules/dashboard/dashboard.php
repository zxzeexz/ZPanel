<?php
/**
 * modules/dashboard/dashboard.php
 * ZPanel dashboard module
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../functions.php';

// Ensure logged in
if (!Session::isLoggedIn()) {
    $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? (BASE_URL . 'dashboard');
    redirect(BASE_URL . 'login');
}

$accountId = Session::getAccountId();
$username  = Session::get('username');

// Load unstuck config
$unstuckConfig = $config['unstuck'] ?? [
    'enabled' => false,
    'town'    => 'prontera',
    'x'       => 150,
    'y'       => 180,
];

// Handle Unstuck action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unstuck_char_id'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_verify($token)) {
        echo '<div class="alert alert-danger">CSRF validation failed. Please reload and try again.</div>';
    } else {
        $charId = (int) $_POST['unstuck_char_id'];

        // Fetch char for this account
        $char = db_fetch(
            "SELECT char_id, online FROM `char` WHERE char_id = :cid AND account_id = :aid LIMIT 1",
            [':cid' => $charId, ':aid' => $accountId]
        );

        if (!$char) {
            echo '<div class="alert alert-danger">Character not found.</div>';
        } elseif ((int) $char['online'] === 1) {
            echo '<div class="alert alert-danger">Character is online. Please logout first and try again.</div>';
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
                echo '<div class="alert alert-success">Character unstuck successfully!</div>';
            } else {
                echo '<div class="alert alert-danger">Failed to update character.</div>';
            }
        }
    }
}

// Fetch characters
$characters = db_fetch_all(
    "SELECT char_id, name, class, base_level, job_level, online
     FROM `char`
     WHERE account_id = :id
     ORDER BY char_id ASC",
    [':id' => $accountId]
);
?>
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-3">
                Welcome, <span class="text-primary"><?= e($username ?? 'Unknown') ?></span>!
            </h2>
    </div>

    <div class="row">
        <div class="col">
            <?php if (empty($characters)): ?>
                <div class="alert alert-info">
                    You don’t have any characters yet.
                </div>
            <?php else: ?>
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-users"></i> Your Characters
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 120px;"></th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Base Lv</th>
                                        <th>Job Lv</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($characters as $char): ?>
                                        <tr>
                                            <td>
                                                <?php if ((int)$char['online'] === 1): ?>
                                                    <span class="badge bg-success">Online</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Offline</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= e($char['name']) ?></td>
                                            <td><?= e(get_job_name((int)$char['class'])) ?></td>
                                            <td><?= e($char['base_level']) ?></td>
                                            <td><?= e($char['job_level']) ?></td>
                                            <td class="text-center">
                                                <!-- Secure: charview.php will validate account ownership -->
                                                <a href="<?= BASE_URL ?>charview?char_id=<?= (int)$char['char_id'] ?>" 
                                                   class="btn btn-view btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <?php if ($unstuckConfig['enabled']): ?>
                                                    <!-- Button trigger modal -->
                                                    <button type="button" 
                                                            class="btn btn-warning btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#unstuckModal<?= $char['char_id'] ?>">
                                                        <i class="fas fa-life-ring"></i> Unstuck
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="unstuckModal<?= $char['char_id'] ?>" tabindex="-1" aria-hidden="true">
                                                      <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                          <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Unstuck</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                          </div>
                                                          <div class="modal-body">
                                                            Are you sure you want to warp 
                                                            <strong><?= e($char['name']) ?></strong> 
                                                            to <strong><?= e($unstuckConfig['town']) ?></strong>?
                                                          </div>
                                                          <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <form method="post" class="d-inline">
                                                                <input type="hidden" name="unstuck_char_id" value="<?= $char['char_id'] ?>">
                                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                                <button type="submit" class="btn btn-warning">
                                                                    Yes, Unstuck
                                                                </button>
                                                            </form>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
