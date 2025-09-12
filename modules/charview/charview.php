<?php
/**
 * modules/charview/charview.php
 * ZPanel Character dashboard module
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
$charId    = isset($_GET['char_id']) ? (int) $_GET['char_id'] : 0;

// Load unstuck config
$unstuckConfig = $config['unstuck'] ?? [
    'enabled' => false,
    'town'    => 'prontera',
    'x'       => 150,
    'y'       => 180,
];

if ($charId <= 0) {
    echo '<div class="alert alert-danger">Invalid character ID.</div>';
    exit;
}

// Fetch character with ownership validation
$char = db_fetch(
    "SELECT c.char_id, c.account_id, c.name, c.class, c.base_level, c.job_level,
            c.str, c.agi, c.vit, c.`int`, c.dex, c.luk, c.zeny,
            c.last_map, c.last_x, c.last_y, c.online,
            g.name AS guild_name
     FROM `char` c
     LEFT JOIN `guild_member` gm ON gm.char_id = c.char_id
     LEFT JOIN `guild` g ON g.guild_id = gm.guild_id
     WHERE c.char_id = :cid AND c.account_id = :aid
     LIMIT 1",
    [':cid' => $charId, ':aid' => $accountId]
);

if (!$char) {
    echo '<div class="alert alert-danger">Error accessing this page.</div>';
    exit;
}

// Handle Unstuck action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unstuck_char_id'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_verify($token)) {
        echo '<div class="alert alert-danger">CSRF validation failed. Please reload and try again.</div>';
    } else {
        $unstuckCharId = (int) $_POST['unstuck_char_id'];

        // Verify again for safety
        $c = db_fetch(
            "SELECT char_id, online FROM `char` WHERE char_id = :cid AND account_id = :aid LIMIT 1",
            [':cid' => $unstuckCharId, ':aid' => $accountId]
        );

        if (!$c) {
            echo '<div class="alert alert-danger">Character not found.</div>';
        } elseif ((int) $c['online'] === 1) {
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
                    ':cid' => $unstuckCharId,
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
?>
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-3">
				<i class="fas fa-user"></i> Character: 
				<span class="text-primary"><?= e($char['name']) ?></span>
				<?php if ((int)$char['online'] === 1): ?>
					<span class="badge bg-success ms-2 small px-2 py-1">Online</span>
				<?php else: ?>
					<span class="badge bg-danger ms-2 small px-2 py-1">Offline</span>
				<?php endif; ?>
			</h2>
            <div class="btn-group" role="group">
                <a href="<?= BASE_URL ?>dashboard" class="btn btn-outline-primary">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Character Summary Card -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-id-card"></i> Character Summary
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Class:</strong> <?= e(get_job_name((int)$char['class'])) ?></p>
                    <p><strong>Base Level:</strong> <?= e($char['base_level']) ?></p>
                    <p><strong>Job Level:</strong> <?= e($char['job_level']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Last Map:</strong> <?= e($char['last_map']) ?> (<?= e($char['last_x']) ?>, <?= e($char['last_y']) ?>)</p>
                    <p><strong>Zeny:</strong> <?= number_format((int)$char['zeny']) ?></p>
                    <p><strong>Guild:</strong> <?= $char['guild_name'] ? e($char['guild_name']) : '<em>No Guild</em>' ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attributes Card -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-secondary text-white">
            <i class="fas fa-dumbbell"></i> Attributes
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-6 col-md-2"><strong>STR</strong><br><?= e($char['str']) ?></div>
                <div class="col-6 col-md-2"><strong>AGI</strong><br><?= e($char['agi']) ?></div>
                <div class="col-6 col-md-2"><strong>VIT</strong><br><?= e($char['vit']) ?></div>
                <div class="col-6 col-md-2"><strong>INT</strong><br><?= e($char['int']) ?></div>
                <div class="col-6 col-md-2"><strong>DEX</strong><br><?= e($char['dex']) ?></div>
                <div class="col-6 col-md-2"><strong>LUK</strong><br><?= e($char['luk']) ?></div>
            </div>
        </div>
    </div>

    <!-- Unstuck Button -->
    <?php if ($unstuckConfig['enabled']): ?>
    <div class="mt-4">
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#unstuckModal<?= $char['char_id'] ?>">
            <i class="fas fa-life-ring"></i> Unstuck
        </button>
    </div>

    <!-- Confirmation Modal -->
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
</div>
