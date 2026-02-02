<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Set JSON header
header('Content-Type: application/json');

try {
    $tokenPrice = (float)getSetting('x_token_price', 5.44);
    $currentStage = (int)getSetting('current_stage', 3);
    $tokensSold = (float)getSetting('tokens_sold', 5317977);
    $totalTokens = (float)getSetting('total_tokens', 6475000);
    
    echo json_encode([
        'success' => true,
        'token_price' => $tokenPrice,
        'current_stage' => $currentStage,
        'tokens_sold' => $tokensSold,
        'total_tokens' => $totalTokens,
        'progress_percent' => ($tokensSold / $totalTokens) * 100
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to get exchange rate']);
}
