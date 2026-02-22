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
$unstuckConfig = $config['unstuck'];

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
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?php echo e($_SESSION['flash']['type']); ?>"><?php echo e($_SESSION['flash']['msg']); ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-3">
                Welcome, <span class="text-primary"><?php echo e($username ?? 'Unknown'); ?></span>!
            </h2>
    </div>

    <div class="row">
        <div class="col">
            <?php if (empty($characters)): ?>
                <div class="alert alert-info">
                    <?php echo $config['msg']['dashbo_nochars']; ?>
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
                                            <td><?php echo e($char['name']); ?></td>
                                            <td><?php echo e(get_job_name((int)$char['class'])); ?></td>
                                            <td><?php echo e($char['base_level']); ?></td>
                                            <td><?php echo e($char['job_level']); ?></td>
                                            <td class="text-center">
                                                <!-- Secure: charview.php will validate account ownership -->
                                                <a href="<?php echo BASE_URL; ?>charview?char_id=<?php echo (int)$char['char_id']; ?>" 
                                                   class="btn btn-view btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <?php if ($unstuckConfig['enabled']): ?>
                                                    <!-- Button trigger modal -->
                                                    <button type="button" 
                                                            class="btn btn-warning btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#unstuckModal<?php echo $char['char_id']; ?>">
                                                        <i class="fas fa-life-ring"></i> Unstuck
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="unstuckModal<?php echo $char['char_id']; ?>" tabindex="-1" aria-hidden="true">
                                                      <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                          <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Unstuck</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                          </div>
                                                          <div class="modal-body">
                                                            Are you sure you want to warp 
                                                            <strong><?php echo e($char['name']); ?></strong> 
                                                            to <strong><?php echo e($unstuckConfig['town']); ?></strong>?
                                                          </div>
                                                          <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <form method="post" action="<?php echo BASE_URL; ?>action/unstuck/unstuck.php" class="d-inline">
                                                                <input type="hidden" name="unstuck_char_id" value="<?php echo $char['char_id']; ?>">
                                                                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                                                <input type="hidden" name="redirect" value="<?php echo BASE_URL; ?>dashboard">
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