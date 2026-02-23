<?php
/**
 * themes/default/layouts/sidebar.php
 * ZPanel sidebar layout
 * Revision 1 [2-23-2026]
 * Zee ^_~
 */

// Only load if logged in (redundant but safe)
if (!Session::isLoggedIn()) {
    return;
}
?>
<!-- Sidebar (offcanvas for mobile, fixed for desktop) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a href="<?= BASE_URL ?>dashboard" class="nav-link"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
            </li>
            <li class="list-group-item">
                <a href="<?= BASE_URL ?>charview" class="nav-link"><i class="fas fa-users me-2"></i> Characters</a>
            </li>
            <li class="list-group-item">
                <a href="<?= BASE_URL ?>unstuck" class="nav-link"><i class="fas fa-map-marker-alt me-2"></i> Unstuck</a>
            </li>
            <li class="list-group-item">
                <a href="<?= BASE_URL ?>settings" class="nav-link"><i class="fas fa-cog me-2"></i> Settings</a>
            </li>
            <li class="list-group-item">
                <a href="<?= BASE_URL ?>logout" class="nav-link"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </li>
        </ul>
    </div>
</div>