<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/includes/admin_auth.php';

requireAdminLogin();

$currentPage = 'email';
$pageTitle = 'Email Users';

$pdo = getDB();
$adminId = getAdminId();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token';
    } else {
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $recipient_type = $_POST['recipient_type'] ?? 'all';
        
        if (empty($subject) || empty($message)) {
            $error = 'Subject and message are required';
        } else {
            try {
                // Get recipients
                $where = '';
                if ($recipient_type === 'active') {
                    $where = "WHERE status = 'active'";
                } elseif ($recipient_type === 'suspended') {
                    $where = "WHERE status = 'suspended'";
                }
                
                $stmt = $pdo->query("SELECT email, fullname FROM users $where");
                $users = $stmt->fetchAll();
                
                $sentCount = 0;
                $failCount = 0;
                
                foreach ($users as $user) {
                    $body = "
                        <html>
                        <body style='font-family: Arial, sans-serif; background-color: #0b0f1a; color: #fff; padding: 20px;'>
                            <div style='max-width: 600px; margin: 0 auto; background: linear-gradient(180deg, rgba(79, 140, 255, 0.15), rgba(0, 0, 0, 0.4)); border-radius: 10px; padding: 30px;'>
                                <h1 style='color: #4f8cff;'>{$subject}</h1>
                                <p>Hi {$user['fullname']},</p>
                                " . nl2br(htmlspecialchars($message)) . "
                                <p style='margin-top: 30px; font-size: 12px; color: #cbd5e1;'>
                                    This email was sent by X Token Admin Team.<br>
                                    If you have any questions, please contact support.
                                </p>
                            </div>
                        </body>
                        </html>
                    ";
                    
                    if (sendEmail($user['email'], $subject, $body)) {
                        $sentCount++;
                    } else {
                        $failCount++;
                    }
                }
                
                logActivity('Bulk Email Sent', "Sent to $sentCount users. Subject: $subject", $adminId);
                $success = "Email sent to $sentCount users successfully!" . ($failCount > 0 ? " ($failCount failed)" : '');
                
            } catch (Exception $e) {
                $error = 'Failed to send emails: ' . $e->getMessage();
            }
        }
    }
}

// Get user counts
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
$activeUsers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE status = 'suspended'");
$suspendedUsers = $stmt->fetch()['total'];

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Email Users</h1>
    <p class="page-subtitle">Send bulk emails to users</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="stats-grid" style="margin-bottom: 32px;">
    <div class="stat-card">
        <div class="stat-label">Total Users</div>
        <div class="stat-value"><?= number_format($totalUsers) ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Active Users</div>
        <div class="stat-value"><?= number_format($activeUsers) ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Suspended Users</div>
        <div class="stat-value"><?= number_format($suspendedUsers) ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Send Email</h2>
    </div>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="form-group">
            <label>Recipients</label>
            <select name="recipient_type" required>
                <option value="all">All Users (<?= number_format($totalUsers) ?>)</option>
                <option value="active">Active Users Only (<?= number_format($activeUsers) ?>)</option>
                <option value="suspended">Suspended Users Only (<?= number_format($suspendedUsers) ?>)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject" placeholder="Email subject" required>
        </div>
        
        <div class="form-group">
            <label>Message</label>
            <textarea name="message" rows="10" placeholder="Enter your message here..." required></textarea>
            <small style="color: #9aa4bf; font-size: 12px;">Plain text will be converted to formatted email</small>
        </div>
        
        <button type="submit" class="btn" onclick="return confirm('Send email to all selected users?')">Send Email</button>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
