<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    $pdo = getDB();
    
    // Get total users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
    $totalUsers = $stmt->fetch()['total'];
    
    // Get total transactions
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM transactions WHERE status = 'approved'");
    $totalTransactions = $stmt->fetch()['total'];
    
    // Get settings
    $tokenPrice = (float)getSetting('x_token_price', 5.44);
    $currentStage = (int)getSetting('current_stage', 3);
    $tokensSold = (float)getSetting('tokens_sold', 5317977);
    $totalTokens = (float)getSetting('total_tokens', 6475000);
    
    // Calculate total raised
    $totalRaised = $tokensSold * $tokenPrice;
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_users' => (int)$totalUsers,
            'total_transactions' => (int)$totalTransactions,
            'tokens_sold' => $tokensSold,
            'total_tokens' => $totalTokens,
            'progress_percent' => ($tokensSold / $totalTokens) * 100,
            'total_raised_usd' => $totalRaised,
            'token_price' => $tokenPrice,
            'current_stage' => $currentStage
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to get stats']);
}
