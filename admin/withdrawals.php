<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/includes/admin_auth.php';

requireAdminLogin();

$currentPage = 'withdrawals';
$pageTitle = 'Withdrawals';

$pdo = getDB();
$adminId = getAdminId();

$success = '';
$error = '';

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token';
    } else {
        $withdrawalId = $_POST['withdrawal_id'] ?? 0;
        $action = $_POST['action'];
        
        // Get withdrawal details
        $stmt = $pdo->prepare("SELECT w.*, u.email, u.fullname FROM withdrawals w JOIN users u ON w.user_id = u.id WHERE w.id = ?");
        $stmt->execute([$withdrawalId]);
        $withdrawal = $stmt->fetch();
        
        if ($withdrawal) {
            if ($action === 'approve') {
                $pdo->beginTransaction();
                
                try {
                    // Check if user has sufficient balance
                    $balance = getUserBalance($withdrawal['user_id']);
                    
                    if ($balance['x_token_balance'] < $withdrawal['amount']) {
                        throw new Exception('Insufficient balance');
                    }
                    
                    // Deduct balance
                    updateUserBalance($withdrawal['user_id'], -$withdrawal['amount']);
                    
                    // Update withdrawal status
                    $stmt = $pdo->prepare("UPDATE withdrawals SET status = 'completed', processed_at = NOW() WHERE id = ?");
                    $stmt->execute([$withdrawalId]);
                    
                    $pdo->commit();
                    
                    // Send email
                    sendWithdrawalApprovedEmail($withdrawal['email'], $withdrawal);
                    
                    // Log activity
                    logActivity('Withdrawal Approved', "Approved withdrawal ID: $withdrawalId for {$withdrawal['amount']} X tokens", $adminId, $withdrawal['user_id']);
                    
                    $success = "Withdrawal approved successfully!";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Failed to approve withdrawal: " . $e->getMessage();
                }
                
            } elseif ($action === 'reject') {
                $notes = $_POST['admin_notes'] ?? '';
                
                // Update withdrawal status
                $stmt = $pdo->prepare("UPDATE withdrawals SET status = 'rejected', admin_notes = ?, processed_at = NOW() WHERE id = ?");
                $stmt->execute([$notes, $withdrawalId]);
                
                // Send email
                sendWithdrawalRejectedEmail($withdrawal['email'], $withdrawal, $notes);
                
                // Log activity
                logActivity('Withdrawal Rejected', "Rejected withdrawal ID: $withdrawalId. Reason: $notes", $adminId, $withdrawal['user_id']);
                
                $success = "Withdrawal rejected successfully!";
            }
        } else {
            $error = "Withdrawal not found";
        }
    }
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? 'pending';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$where = $statusFilter ? "WHERE w.status = ?" : '';
$params = $statusFilter ? [$statusFilter] : [];

// Get total count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM withdrawals w $where");
$stmt->execute($params);
$totalWithdrawals = $stmt->fetch()['total'];
$totalPages = ceil($totalWithdrawals / $perPage);

// Get withdrawals
$stmt = $pdo->prepare("
    SELECT w.*, u.email, u.fullname 
    FROM withdrawals w 
    JOIN users u ON w.user_id = u.id 
    $where
    ORDER BY w.created_at DESC 
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$withdrawals = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Withdrawal Management</h1>
    <p class="page-subtitle">Review and process withdrawal requests</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="filters">
        <form method="GET" style="display: contents;">
            <div class="filter-group">
                <label>Status Filter</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
        </form>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Amount</th>
                <th>Wallet Address</th>
                <th>Status</th>
                <th>Requested</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($withdrawals)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #9aa4bf; padding: 40px;">No withdrawals found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($withdrawals as $w): ?>
                    <tr>
                        <td>#<?= $w['id'] ?></td>
                        <td>
                            <div><?= htmlspecialchars($w['fullname']) ?></div>
                            <div style="font-size: 12px; color: #9aa4bf;"><?= htmlspecialchars($w['email']) ?></div>
                        </td>
                        <td><?= number_format($w['amount'], 2) ?> X</td>
                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; font-size: 12px;">
                            <?= htmlspecialchars($w['wallet_address']) ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $w['status'] ?>">
                                <?= ucfirst($w['status']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y H:i', strtotime($w['created_at'])) ?></td>
                        <td>
                            <?php if ($w['status'] === 'pending'): ?>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Approve this withdrawal?');">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <input type="hidden" name="withdrawal_id" value="<?= $w['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                
                                <button type="button" class="btn btn-sm btn-danger" onclick="showRejectForm(<?= $w['id'] ?>)">Reject</button>
                            <?php else: ?>
                                <?php if ($w['admin_notes']): ?>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="alert('<?= htmlspecialchars($w['admin_notes']) ?>')">View Notes</button>
                                <?php else: ?>
                                    <span style="color: #9aa4bf; font-size: 12px;">No action</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?><?= $statusFilter ? "&status=$statusFilter" : '' ?>">← Previous</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?><?= $statusFilter ? "&status=$statusFilter" : '' ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?><?= $statusFilter ? "&status=$statusFilter" : '' ?>">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #1a1f35; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 style="margin-bottom: 20px;">Reject Withdrawal</h3>
        <form method="POST" id="rejectForm">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="withdrawal_id" id="rejectWithdrawalId">
            <input type="hidden" name="action" value="reject">
            
            <div class="form-group">
                <label>Reason for rejection:</label>
                <textarea name="admin_notes" rows="4" placeholder="Enter reason..." required></textarea>
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-danger">Reject Withdrawal</button>
                <button type="button" class="btn btn-secondary" onclick="hideRejectForm()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectForm(withdrawalId) {
    document.getElementById('rejectWithdrawalId').value = withdrawalId;
    document.getElementById('rejectModal').style.display = 'flex';
}

function hideRejectForm() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
