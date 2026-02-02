<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

// Destroy session
destroyUserSession();

// Redirect to login
redirect(SITE_URL . '/login.php');
