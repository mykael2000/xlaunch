<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';

// Destroy admin session
destroyAdminSession();

// Redirect to login
header('Location: login.php');
exit;
