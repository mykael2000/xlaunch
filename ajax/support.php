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

// Handle GET request - Load messages
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM support_messages WHERE user_id = ? ORDER BY created_at ASC");
        $stmt->execute([$userId]);
        $messages = $stmt->fetchAll();
        
        // Mark messages as read
        $stmt = $pdo->prepare("UPDATE support_messages SET is_read = 1 WHERE user_id = ? AND sender = 'admin' AND is_read = 0");
        $stmt->execute([$userId]);
        
        echo json_encode($messages);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to load messages']);
    }
    exit;
}

// Handle POST request - Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $sender = $_POST['sender'] ?? 'user';
    
    if (empty($message)) {
        echo json_encode(['error' => 'Message cannot be empty']);
        exit;
    }
    
    // Only allow 'user' sender from user side
    $sender = 'user';
    
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("INSERT INTO support_messages (user_id, message, sender) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $message, $sender]);
        
        echo json_encode(['success' => true, 'message' => 'Message sent']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to send message']);
    }
    exit;
}
