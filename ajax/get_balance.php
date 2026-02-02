<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Set JSON header
header('Content-Type: application/json');

// Require login
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$userId = getUserId();

try {
    $balance = getUserBalance($userId);
    $tokenPrice = (float)getSetting('x_token_price', 5.44);
    $usdEquivalent = $balance['x_token_balance'] * $tokenPrice;
    
    echo json_encode([
        'success' => true,
        'x_token_balance' => (float)$balance['x_token_balance'],
        'usd_balance' => (float)$balance['usd_balance'],
        'usd_equivalent' => $usdEquivalent,
        'token_price' => $tokenPrice
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to get balance']);
}
