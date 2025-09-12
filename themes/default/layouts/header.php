<?php
/**
 * themes/default/layouts/header.php
 * ZPanel default theme page header
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
require_once __DIR__ . '/../../../init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($config['site']['name']) ?></title>

    <!-- Bootstrap -->
    <link href="<?= BASE_URL ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link href="<?= BASE_URL ?>assets/fontawesome/css/fontawesome.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/fontawesome/css/all.min.css" rel="stylesheet">

    <!-- Theme Styles -->
    <link href="<?= BASE_URL ?>themes/default/css/style.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="<?= BASE_URL ?>themes/default/favicon.ico" type="image/x-icon">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>dashboard">
			<img src="<?= BASE_URL ?>themes/default/images/logo.png" height="30px" />
            <?= e($config['site']['name']) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (Session::isLoggedIn()): ?>
					<!-- Additional link samples (will show on top right prior the username dropdown)
                    Example 1: Link with fontawesome icon
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-tachometer-alt"></i> Linkwithicon
                        </a>
                    </li>

                    Example 2: Link with no icon
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            Linkwithnoicon
                        </a>
                    </li>
					-->

                    <!-- Username dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i> <?= e(Session::get('username')) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>settings">
                                    <i class="fas fa-cog me-2"></i> Settings
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>logout">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
					<!-- add custom menu items here on login page -->
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
