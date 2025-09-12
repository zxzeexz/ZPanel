<?php
/**
 * modules/logout/logout.php
 * ZPanel logout module
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
require_once __DIR__ . '/../../init.php';

Session::destroy();
redirect(BASE_URL . 'login');