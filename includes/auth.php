<?php
/**
 * Authentication Functions
 * X Token Presale Platform
 */

if (!defined('X_TOKEN_APP')) {
    define('X_TOKEN_APP', true);
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

/**
 * Register new user
 * @param string $email
 * @param string $password
 * @param string $fullname
 * @return array
 */
function registerUser($email, $password, $fullname) {
    try {
        $pdo = getDB();
        
        // Validate input
        if (!isValidEmail($email)) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }
        
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        if (strlen($fullname) < 2) {
            return ['success' => false, 'message' => 'Full name is required'];
        }
        
        // Check if registration is enabled
        if (getSetting('registration_enabled', '1') !== '1') {
            return ['success' => false, 'message' => 'Registration is currently disabled'];
        }
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (email, password, fullname) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashedPassword, $fullname]);
        
        $userId = $pdo->lastInsertId();
        
        // Create initial balance record
        $stmt = $pdo->prepare("INSERT INTO balances (user_id) VALUES (?)");
        $stmt->execute([$userId]);
        
        // Create initial status record
        $stmt = $pdo->prepare("INSERT INTO user_status (user_id) VALUES (?)");
        $stmt->execute([$userId]);
        
        // Log activity
        logActivity('User Registration', "User registered: $email", null, $userId);
        
        return [
            'success' => true,
            'message' => 'Registration successful',
            'user_id' => $userId
        ];
        
    } catch (Exception $e) {
        error_log("Registration Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

/**
 * Login user
 * @param string $email
 * @param string $password
 * @return array
 */
function loginUser($email, $password) {
    try {
        $pdo = getDB();
        
        // Check rate limit
        if (!checkLoginRateLimit($email)) {
            return ['success' => false, 'message' => 'Too many login attempts. Please try again later.'];
        }
        
        // Get user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            incrementLoginAttempts($email);
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Check if user is active
        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account is suspended. Please contact support.'];
        }
        
        // Reset login attempts
        resetLoginAttempts($email);
        
        // Set session
        setUserSession($user);
        
        // Log activity
        logActivity('User Login', "User logged in: $email", null, $user['id']);
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => $user
        ];
        
    } catch (Exception $e) {
        error_log("Login Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Login failed. Please try again.'];
    }
}

/**
 * Login admin
 * @param string $username
 * @param string $password
 * @return array
 */
function loginAdmin($username, $password) {
    try {
        $pdo = getDB();
        
        // Get admin
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if (!$admin || !password_verify($password, $admin['password'])) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        // Update last login
        $stmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$admin['id']]);
        
        // Set session
        setAdminSession($admin);
        
        // Log activity
        logActivity('Admin Login', "Admin logged in: $username", $admin['id']);
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'admin' => $admin
        ];
        
    } catch (Exception $e) {
        error_log("Admin Login Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Login failed. Please try again.'];
    }
}

/**
 * Update user password
 * @param int $userId
 * @param string $currentPassword
 * @param string $newPassword
 * @return array
 */
function updateUserPassword($userId, $currentPassword, $newPassword) {
    try {
        $pdo = getDB();
        
        // Get user
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'New password must be at least 6 characters'];
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        // Update password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);
        
        // Log activity
        logActivity('Password Changed', null, null, $userId);
        
        return ['success' => true, 'message' => 'Password updated successfully'];
        
    } catch (Exception $e) {
        error_log("Password Update Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to update password'];
    }
}

/**
 * Update user profile
 * @param int $userId
 * @param array $data
 * @return array
 */
function updateUserProfile($userId, $data) {
    try {
        $pdo = getDB();
        
        $updates = [];
        $params = [];
        
        if (isset($data['fullname']) && !empty($data['fullname'])) {
            $updates[] = "fullname = ?";
            $params[] = $data['fullname'];
        }
        
        if (isset($data['wallet_address'])) {
            $updates[] = "wallet_address = ?";
            $params[] = $data['wallet_address'];
        }
        
        if (empty($updates)) {
            return ['success' => false, 'message' => 'No data to update'];
        }
        
        $params[] = $userId;
        
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Log activity
        logActivity('Profile Updated', null, null, $userId);
        
        return ['success' => true, 'message' => 'Profile updated successfully'];
        
    } catch (Exception $e) {
        error_log("Profile Update Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to update profile'];
    }
}

/**
 * Get user by ID
 * @param int $userId
 * @return array|null
 */
function getUserById($userId) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, email, fullname, wallet_address, created_at, status FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}
