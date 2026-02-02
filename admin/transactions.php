<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/includes/admin_auth.php';

requireAdminLogin();

$currentPage = 'transactions';
$pageTitle = 'Transactions';

$pdo = getDB();
$adminId = getAdminId();

$success = '';
$error = '';

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token';
    } else {
        $transactionId = $_POST['transaction_id'] ?? 0;
        $action = $_POST['action'];
        
        // Get transaction details
        $stmt = $pdo->prepare("SELECT t.*, u.email, u.fullname FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
        $stmt->execute([$transactionId]);
        $transaction = $stmt->fetch();
        
        if ($transaction) {
            if ($action === 'approve') {
                // Begin transaction
                $pdo->beginTransaction();
                
                try {
                    // Update transaction status
                    $stmt = $pdo->prepare("UPDATE transactions SET status = 'approved', admin_verified = 1 WHERE id = ?");
                    $stmt->execute([$transactionId]);
                    
                    // Update user balance
                    updateUserBalance($transaction['user_id'], $transaction['amount'], $transaction['usd_amount']);
                    
                    // Update user status/tier
                    updateUserStatus($transaction['user_id'], $transaction['usd_amount']);
                    
                    // Commit transaction
                    $pdo->commit();
                    
                    // Send email
                    sendTransactionApprovedEmail($transaction['email'], $transaction);
                    
                    // Log activity
                    logActivity('Transaction Approved', "Approved transaction {$transaction['order_id']} for user {$transaction['email']}", $adminId, $transaction['user_id']);
                    
                    $success = "Transaction approved successfully!";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Failed to approve transaction: " . $e->getMessage();
                }
                
            } elseif ($action === 'reject') {
                $reason = $_POST['reason'] ?? '';
                
                // Update transaction status
                $stmt = $pdo->prepare("UPDATE transactions SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$transactionId]);
                
                // Send email
                sendTransactionRejectedEmail($transaction['email'], $transaction, $reason);
                
                // Log activity
                logActivity('Transaction Rejected', "Rejected transaction {$transaction['order_id']} for user {$transaction['email']}. Reason: $reason", $adminId, $transaction['user_id']);
                
                $success = "Transaction rejected successfully!";
            }
        } else {
            $error = "Transaction not found";
        }
    }
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? '';
$searchQuery = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$where = [];
$params = [];

if ($statusFilter) {
    $where[] = "t.status = ?";
    $params[] = $statusFilter;
}

if ($searchQuery) {
    $where[] = "(t.order_id LIKE ? OR u.email LIKE ? OR u.fullname LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM transactions t JOIN users u ON t.user_id = u.id $whereClause");
$stmt->execute($params);
$totalTransactions = $stmt->fetch()['total'];
$totalPages = ceil($totalTransactions / $perPage);

// Get transactions
$stmt = $pdo->prepare("
    SELECT t.*, u.email, u.fullname 
    FROM transactions t 
    JOIN users u ON t.user_id = u.id 
    $whereClause
    ORDER BY t.created_at DESC 
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$transactions = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Transaction Management</h1>
    <p class="page-subtitle">Review and manage all transactions</p>
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
                    <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Search</label>
                <input type="text" name="search" placeholder="Order ID, email, or name..." value="<?= htmlspecialchars($searchQuery) ?>">
            </div>
            
            <div class="filter-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn">Filter</button>
            </div>
        </form>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>X Tokens</th>
                <th>Payment</th>
                <th>Network</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #9aa4bf; padding: 40px;">No transactions found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $tx): ?>
                    <tr>
                        <td><?= htmlspecialchars($tx['order_id']) ?></td>
                        <td>
                            <div><?= htmlspecialchars($tx['fullname']) ?></div>
                            <div style="font-size: 12px; color: #9aa4bf;"><?= htmlspecialchars($tx['email']) ?></div>
                        </td>
                        <td>
                            <div><?= number_format($tx['amount'], 2) ?> X</div>
                            <div style="font-size: 12px; color: #9aa4bf;">$<?= number_format($tx['usd_amount'], 2) ?></div>
                        </td>
                        <td>
                            <div><?= number_format($tx['crypto_amount'], 6) ?> <?= htmlspecialchars(strtoupper($tx['crypto_type'])) ?></div>
                            <?php if ($tx['tx_hash']): ?>
                                <div style="font-size: 11px; color: #9aa4bf; max-width: 150px; overflow: hidden; text-overflow: ellipsis;">
                                    <?= htmlspecialchars(substr($tx['tx_hash'], 0, 16)) ?>...
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($tx['network'] ?? 'N/A') ?></td>
                        <td>
                            <span class="badge badge-<?= $tx['status'] ?>">
                                <?= ucfirst($tx['status']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y H:i', strtotime($tx['created_at'])) ?></td>
                        <td>
                            <?php if ($tx['status'] === 'pending'): ?>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to approve this transaction?');">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <input type="hidden" name="transaction_id" value="<?= $tx['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                
                                <button type="button" class="btn btn-sm btn-danger" onclick="showRejectForm(<?= $tx['id'] ?>, '<?= htmlspecialchars($tx['order_id']) ?>')">Reject</button>
                            <?php else: ?>
                                <span style="color: #9aa4bf; font-size: 12px;">No action</span>
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
                <a href="?page=<?= $page - 1 ?><?= $statusFilter ? "&status=$statusFilter" : '' ?><?= $searchQuery ? "&search=$searchQuery" : '' ?>">← Previous</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?><?= $statusFilter ? "&status=$statusFilter" : '' ?><?= $searchQuery ? "&search=$searchQuery" : '' ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?><?= $statusFilter ? "&status=$statusFilter" : '' ?><?= $searchQuery ? "&search=$searchQuery" : '' ?>">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #1a1f35; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 style="margin-bottom: 20px;">Reject Transaction</h3>
        <form method="POST" id="rejectForm">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="transaction_id" id="rejectTransactionId">
            <input type="hidden" name="action" value="reject">
            
            <div class="form-group">
                <label>Reason for rejection:</label>
                <textarea name="reason" rows="4" placeholder="Enter reason..." required></textarea>
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-danger">Reject Transaction</button>
                <button type="button" class="btn btn-secondary" onclick="hideRejectForm()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectForm(transactionId, orderId) {
    document.getElementById('rejectTransactionId').value = transactionId;
    document.getElementById('rejectModal').style.display = 'flex';
}

function hideRejectForm() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
