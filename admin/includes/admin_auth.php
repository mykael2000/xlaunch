<?php
/**
 * Admin Authentication Functions
 * X Token Presale Platform
 */

if (!defined('X_TOKEN_APP')) {
    define('X_TOKEN_APP', true);
}

/**
 * Require admin login - redirect if not logged in
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Check admin permission (for future role-based access)
 * @param string $permission
 * @return bool
 */
function hasAdminPermission($permission = '') {
    // For now, all admins have all permissions
    // Can be extended for role-based access control
    return isAdminLoggedIn();
}
