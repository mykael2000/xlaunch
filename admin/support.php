<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/admin_auth.php';

requireAdminLogin();

$currentPage = 'support';
$pageTitle = 'Support';

$pdo = getDB();
$adminId = getAdminId();

$success = '';
$error = '';

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token';
    } else {
        $userId = intval($_POST['user_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');
        
        if ($userId && $message) {
            try {
                // Insert admin reply
                $stmt = $pdo->prepare("INSERT INTO support_messages (user_id, message, sender) VALUES (?, ?, 'admin')");
                $stmt->execute([$userId, $message]);
                
                // Mark user messages as read
                $stmt = $pdo->prepare("UPDATE support_messages SET is_read = 1 WHERE user_id = ? AND sender = 'user'");
                $stmt->execute([$userId]);
                
                logActivity('Support Reply', "Replied to user ID: $userId", $adminId, $userId);
                $success = 'Reply sent successfully!';
                
            } catch (Exception $e) {
                $error = 'Failed to send reply: ' . $e->getMessage();
            }
        } else {
            $error = 'Invalid input';
        }
    }
}

// Mark as read
if (isset($_GET['mark_read'])) {
    $userId = intval($_GET['mark_read']);
    $stmt = $pdo->prepare("UPDATE support_messages SET is_read = 1 WHERE user_id = ? AND sender = 'user'");
    $stmt->execute([$userId]);
    header('Location: support.php');
    exit;
}

// Get conversations grouped by user
$stmt = $pdo->query("
    SELECT u.id, u.fullname, u.email,
           COUNT(CASE WHEN sm.is_read = 0 AND sm.sender = 'user' THEN 1 END) as unread_count,
           MAX(sm.created_at) as last_message_at
    FROM users u
    INNER JOIN support_messages sm ON u.id = sm.user_id
    GROUP BY u.id
    ORDER BY last_message_at DESC
");
$conversations = $stmt->fetchAll();

// Get selected conversation
$selectedUserId = intval($_GET['user'] ?? 0);
$messages = [];
$selectedUser = null;

if ($selectedUserId) {
    // Get user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$selectedUserId]);
    $selectedUser = $stmt->fetch();
    
    // Get messages
    $stmt = $pdo->prepare("SELECT * FROM support_messages WHERE user_id = ? ORDER BY created_at ASC");
    $stmt->execute([$selectedUserId]);
    $messages = $stmt->fetchAll();
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Support Messages</h1>
    <p class="page-subtitle">Manage user support conversations</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 24px; min-height: 600px;">
    <!-- Conversations List -->
    <div class="card" style="height: fit-content; max-height: 600px; overflow-y: auto;">
        <div class="card-header">
            <h3 class="card-title">Conversations</h3>
        </div>
        
        <?php if (empty($conversations)): ?>
            <p style="padding: 20px; color: #9aa4bf; text-align: center;">No messages yet</p>
        <?php else: ?>
            <div>
                <?php foreach ($conversations as $conv): ?>
                    <a href="?user=<?= $conv['id'] ?>" style="display: block; padding: 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); text-decoration: none; <?= $selectedUserId === $conv['id'] ? 'background: rgba(79, 140, 255, 0.1);' : '' ?>">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; margin-bottom: 4px;"><?= htmlspecialchars($conv['fullname']) ?></div>
                                <div style="font-size: 12px; color: #9aa4bf;"><?= htmlspecialchars($conv['email']) ?></div>
                            </div>
                            <?php if ($conv['unread_count'] > 0): ?>
                                <div style="background: #ef4444; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                                    <?= $conv['unread_count'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div style="font-size: 11px; color: #9aa4bf; margin-top: 4px;">
                            <?= date('M d, Y H:i', strtotime($conv['last_message_at'])) ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Messages -->
    <div class="card">
        <?php if ($selectedUser): ?>
            <div class="card-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <div>
                    <h3 class="card-title"><?= htmlspecialchars($selectedUser['fullname']) ?></h3>
                    <p style="font-size: 13px; color: #9aa4bf; margin-top: 4px;"><?= htmlspecialchars($selectedUser['email']) ?></p>
                </div>
            </div>
            
            <div style="padding: 24px; max-height: 400px; overflow-y: auto; background: rgba(0, 0, 0, 0.2);">
                <?php foreach ($messages as $msg): ?>
                    <div style="margin-bottom: 16px; display: flex; <?= $msg['sender'] === 'admin' ? 'justify-content: flex-end;' : '' ?>">
                        <div style="max-width: 70%; padding: 12px 16px; border-radius: 12px; <?= $msg['sender'] === 'admin' ? 'background: linear-gradient(135deg, #4f8cff 0%, #6366f1 100%);' : 'background: rgba(255, 255, 255, 0.08);' ?>">
                            <div style="margin-bottom: 4px;"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                            <div style="font-size: 11px; opacity: 0.7; text-align: right;">
                                <?= date('M d, H:i', strtotime($msg['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="padding: 24px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="reply">
                    <input type="hidden" name="user_id" value="<?= $selectedUserId ?>">
                    
                    <div class="form-group">
                        <textarea name="message" rows="3" placeholder="Type your reply..." required style="margin-bottom: 12px;"></textarea>
                    </div>
                    
                    <button type="submit" class="btn">Send Reply</button>
                </form>
            </div>
        <?php else: ?>
            <div style="display: flex; align-items: center; justify-content: center; height: 400px; color: #9aa4bf;">
                Select a conversation to view messages
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
