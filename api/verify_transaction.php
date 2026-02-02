<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$orderId = $_GET['order_id'] ?? '';

if (empty($orderId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID required']);
    exit;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, order_id, amount, crypto_type, crypto_amount, network, status, created_at, admin_verified FROM transactions WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $transaction = $stmt->fetch();
    
    if (!$transaction) {
        http_response_code(404);
        echo json_encode(['error' => 'Transaction not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'transaction' => [
            'order_id' => $transaction['order_id'],
            'amount' => (float)$transaction['amount'],
            'crypto_type' => $transaction['crypto_type'],
            'crypto_amount' => (float)$transaction['crypto_amount'],
            'network' => $transaction['network'],
            'status' => $transaction['status'],
            'verified' => (bool)$transaction['admin_verified'],
            'created_at' => $transaction['created_at']
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to verify transaction']);
}
