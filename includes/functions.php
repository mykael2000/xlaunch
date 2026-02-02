<?php
/**
 * Helper Functions
 * X Token Presale Platform
 */

if (!defined('X_TOKEN_APP')) {
    define('X_TOKEN_APP', true);
}

/**
 * Sanitize input
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate unique order ID
 * @return string
 */
function generateOrderId() {
    return 'ORD-' . strtoupper(bin2hex(random_bytes(5)));
}

/**
 * Get setting value
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function getSetting($key, $default = null) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Update setting value
 * @param string $key
 * @param mixed $value
 * @return bool
 */
function updateSetting($key, $value) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("UPDATE settings SET `value` = ? WHERE `key` = ?");
        return $stmt->execute([$value, $key]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get user balance
 * @param int $userId
 * @return array
 */
function getUserBalance($userId) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM balances WHERE user_id = ?");
        $stmt->execute([$userId]);
        $balance = $stmt->fetch();
        
        if (!$balance) {
            // Create balance record if doesn't exist
            $stmt = $pdo->prepare("INSERT INTO balances (user_id) VALUES (?)");
            $stmt->execute([$userId]);
            return ['x_token_balance' => 0, 'usd_balance' => 0];
        }
        
        return $balance;
    } catch (Exception $e) {
        return ['x_token_balance' => 0, 'usd_balance' => 0];
    }
}

/**
 * Update user balance
 * @param int $userId
 * @param float $xTokenAmount
 * @param float $usdAmount
 * @return bool
 */
function updateUserBalance($userId, $xTokenAmount, $usdAmount = null) {
    try {
        $pdo = getDB();
        
        // Get current balance
        $balance = getUserBalance($userId);
        
        $newXTokenBalance = $balance['x_token_balance'] + $xTokenAmount;
        
        if ($usdAmount !== null) {
            $newUsdBalance = $balance['usd_balance'] + $usdAmount;
            $stmt = $pdo->prepare("UPDATE balances SET x_token_balance = ?, usd_balance = ? WHERE user_id = ?");
            return $stmt->execute([$newXTokenBalance, $newUsdBalance, $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE balances SET x_token_balance = ? WHERE user_id = ?");
            return $stmt->execute([$newXTokenBalance, $userId]);
        }
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get user status
 * @param int $userId
 * @return array
 */
function getUserStatus($userId) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM user_status WHERE user_id = ?");
        $stmt->execute([$userId]);
        $status = $stmt->fetch();
        
        if (!$status) {
            // Create status record if doesn't exist
            $stmt = $pdo->prepare("INSERT INTO user_status (user_id) VALUES (?)");
            $stmt->execute([$userId]);
            return ['status_level' => 'Basic', 'contribution_amount' => 0];
        }
        
        return $status;
    } catch (Exception $e) {
        return ['status_level' => 'Basic', 'contribution_amount' => 0];
    }
}

/**
 * Calculate user tier based on contribution
 * @param float $contribution
 * @return string
 */
function calculateTier($contribution) {
    if ($contribution >= 50000) return 'VIP';
    if ($contribution >= 10000) return 'Platinum';
    if ($contribution >= 5000) return 'Gold';
    if ($contribution >= 1000) return 'Silver';
    return 'Basic';
}

/**
 * Update user status
 * @param int $userId
 * @param float $amount
 * @return bool
 */
function updateUserStatus($userId, $amount) {
    try {
        $pdo = getDB();
        
        // Get current status
        $status = getUserStatus($userId);
        $newContribution = $status['contribution_amount'] + $amount;
        $newTier = calculateTier($newContribution);
        
        $stmt = $pdo->prepare("UPDATE user_status SET status_level = ?, contribution_amount = ? WHERE user_id = ?");
        return $stmt->execute([$newTier, $newContribution, $userId]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Format number with commas
 * @param float $number
 * @param int $decimals
 * @return string
 */
function formatNumber($number, $decimals = 2) {
    return number_format($number, $decimals);
}

/**
 * Log activity
 * @param string $action
 * @param string $details
 * @param int $adminId
 * @param int $userId
 */
function logActivity($action, $details = null, $adminId = null, $userId = null) {
    try {
        $pdo = getDB();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        
        $stmt = $pdo->prepare("INSERT INTO activity_log (admin_id, user_id, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$adminId, $userId, $action, $details, $ipAddress]);
    } catch (Exception $e) {
        // Silently fail
    }
}

/**
 * Check rate limit for login attempts
 * @param string $email
 * @return bool
 */
function checkLoginRateLimit($email) {
    $key = 'login_attempts_' . md5($email);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'time' => time()];
    }
    
    // Reset if lockout time has passed
    if (time() - $_SESSION[$key]['time'] > LOGIN_LOCKOUT_TIME) {
        $_SESSION[$key] = ['count' => 0, 'time' => time()];
    }
    
    // Check if locked out
    if ($_SESSION[$key]['count'] >= MAX_LOGIN_ATTEMPTS) {
        return false;
    }
    
    return true;
}

/**
 * Increment login attempts
 * @param string $email
 */
function incrementLoginAttempts($email) {
    $key = 'login_attempts_' . md5($email);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'time' => time()];
    }
    
    $_SESSION[$key]['count']++;
}

/**
 * Reset login attempts
 * @param string $email
 */
function resetLoginAttempts($email) {
    $key = 'login_attempts_' . md5($email);
    unset($_SESSION[$key]);
}

/**
 * Get wallet address for cryptocurrency
 * @param string $crypto
 * @param string $network
 * @return string|null
 */
function getWalletAddress($crypto, $network = null) {
    $crypto = strtolower($crypto);
    
    // For USDT and USDC, use network-specific wallet
    if (in_array($crypto, ['usdt', 'usdc']) && $network) {
        if ($network === 'ERC20') {
            return getSetting('eth_wallet');
        } elseif ($network === 'TRC20') {
            return getSetting('trx_wallet');
        } elseif ($network === 'BEP20') {
            return getSetting('bnb_wallet');
        }
    }
    
    // Direct wallet mapping
    $walletKey = $crypto . '_wallet';
    return getSetting($walletKey);
}

/**
 * Get time ago format
 * @param string $datetime
 * @return string
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return $diff . ' seconds ago';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
    if ($diff < 31536000) return floor($diff / 2592000) . ' months ago';
    return floor($diff / 31536000) . ' years ago';
}

/**
 * Redirect to URL
 * @param string $url
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}
