<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/admin_auth.php';

requireAdminLogin();

$currentPage = 'dashboard';
$pageTitle = 'Dashboard';

$pdo = getDB();

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM transactions WHERE status = 'pending'");
$pendingTransactions = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM transactions WHERE status = 'approved'");
$approvedTransactions = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(amount) as total FROM transactions WHERE status = 'approved'");
$totalTokensSold = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM support_messages WHERE is_read = 0 AND sender = 'user'");
$unreadMessages = $stmt->fetch()['total'];

// Get recent transactions
$stmt = $pdo->prepare("
    SELECT t.*, u.email, u.fullname 
    FROM transactions t 
    JOIN users u ON t.user_id = u.id 
    ORDER BY t.created_at DESC 
    LIMIT 10
");
$stmt->execute();
$recentTransactions = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Overview of X Token presale platform</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Users</div>
        <div class="stat-value"><?= number_format($totalUsers) ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Pending Transactions</div>
        <div class="stat-value"><?= number_format($pendingTransactions) ?></div>
        <?php if ($pendingTransactions > 0): ?>
            <div class="stat-change">Requires attention</div>
        <?php endif; ?>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Approved Transactions</div>
        <div class="stat-value"><?= number_format($approvedTransactions) ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Total Tokens Sold</div>
        <div class="stat-value"><?= number_format($totalTokensSold, 0) ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Recent Transactions</h2>
        <a href="transactions.php" class="btn btn-sm">View All</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recentTransactions)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #9aa4bf;">No transactions yet</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recentTransactions as $tx): ?>
                    <tr>
                        <td><?= htmlspecialchars($tx['order_id']) ?></td>
                        <td><?= htmlspecialchars($tx['fullname']) ?></td>
                        <td><?= number_format($tx['amount'], 2) ?> X</td>
                        <td><?= number_format($tx['crypto_amount'], 6) ?> <?= htmlspecialchars($tx['crypto_type']) ?></td>
                        <td>
                            <span class="badge badge-<?= $tx['status'] ?>">
                                <?= ucfirst($tx['status']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($tx['created_at'])) ?></td>
                        <td>
                            <a href="transactions.php?id=<?= $tx['id'] ?>" class="btn btn-sm btn-secondary">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Unread Support Messages</div>
        <div class="stat-value"><?= number_format($unreadMessages) ?></div>
        <?php if ($unreadMessages > 0): ?>
            <div style="margin-top: 12px;">
                <a href="support.php" class="btn btn-sm">View Messages</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
