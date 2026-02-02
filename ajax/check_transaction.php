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

$orderId = $_GET['order_id'] ?? '';

if (empty($orderId)) {
    echo json_encode(['error' => 'Order ID required']);
    exit;
}

$userId = getUserId();

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, order_id, amount, crypto_type, crypto_amount, network, status, created_at, admin_verified FROM transactions WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$orderId, $userId]);
    $transaction = $stmt->fetch();
    
    if (!$transaction) {
        echo json_encode(['error' => 'Transaction not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'transaction' => $transaction
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to check transaction']);
}
