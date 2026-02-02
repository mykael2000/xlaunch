<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../includes/admin_auth.php';

requireAdminLogin();

$userId = intval($_GET['id'] ?? 0);
if (!$userId) {
    echo '<p>Invalid user ID</p>';
    exit;
}

$pdo = getDB();

// Get user details
$stmt = $pdo->prepare("
    SELECT u.*, 
           COALESCE(b.x_token_balance, 0) as x_token_balance,
           COALESCE(b.usd_balance, 0) as usd_balance,
           COALESCE(us.status_level, 'Basic') as status_level,
           COALESCE(us.contribution_amount, 0) as contribution_amount
    FROM users u
    LEFT JOIN balances b ON u.id = b.user_id
    LEFT JOIN user_status us ON u.id = us.user_id
    WHERE u.id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo '<p>User not found</p>';
    exit;
}

// Get user transactions
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$userId]);
$transactions = $stmt->fetchAll();

?>

<h3>User Details</h3>
<button onclick="hideUserModal()" style="float: right; background: transparent; border: none; color: #fff; font-size: 24px; cursor: pointer;">&times;</button>

<div style="margin-top: 20px;">
    <div style="margin-bottom: 16px;">
        <strong>Name:</strong> <?= htmlspecialchars($user['fullname']) ?>
    </div>
    <div style="margin-bottom: 16px;">
        <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?>
    </div>
    <div style="margin-bottom: 16px;">
        <strong>X Token Balance:</strong> <?= number_format($user['x_token_balance'], 2) ?> X
    </div>
    <div style="margin-bottom: 16px;">
        <strong>USD Balance:</strong> $<?= number_format($user['usd_balance'], 2) ?>
    </div>
    <div style="margin-bottom: 16px;">
        <strong>Status Level:</strong> <?= $user['status_level'] ?>
    </div>
    <div style="margin-bottom: 16px;">
        <strong>Contribution:</strong> $<?= number_format($user['contribution_amount'], 2) ?>
    </div>
    <div style="margin-bottom: 16px;">
        <strong>Account Status:</strong> 
        <span class="badge badge-<?= $user['status'] ?>"><?= ucfirst($user['status']) ?></span>
    </div>
    <div style="margin-bottom: 16px;">
        <strong>Wallet Address:</strong> 
        <?= $user['wallet_address'] ? htmlspecialchars($user['wallet_address']) : '<span style="color: #9aa4bf;">Not set</span>' ?>
    </div>
    <div style="margin-bottom: 16px;">
        <strong>Registered:</strong> <?= date('M d, Y H:i', strtotime($user['created_at'])) ?>
    </div>
</div>

<hr style="border: none; border-top: 1px solid rgba(255, 255, 255, 0.1); margin: 24px 0;">

<h4>Recent Transactions</h4>
<?php if (empty($transactions)): ?>
    <p style="color: #9aa4bf;">No transactions yet</p>
<?php else: ?>
    <table style="width: 100%; margin-top: 12px;">
        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <th style="text-align: left; padding: 8px; font-size: 12px;">Order ID</th>
            <th style="text-align: left; padding: 8px; font-size: 12px;">Amount</th>
            <th style="text-align: left; padding: 8px; font-size: 12px;">Status</th>
        </tr>
        <?php foreach ($transactions as $tx): ?>
            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                <td style="padding: 8px; font-size: 13px;"><?= htmlspecialchars($tx['order_id']) ?></td>
                <td style="padding: 8px; font-size: 13px;"><?= number_format($tx['amount'], 2) ?> X</td>
                <td style="padding: 8px;">
                    <span class="badge badge-<?= $tx['status'] ?>" style="font-size: 11px;">
                        <?= ucfirst($tx['status']) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<hr style="border: none; border-top: 1px solid rgba(255, 255, 255, 0.1); margin: 24px 0;">

<h4>Actions</h4>
<div style="margin-top: 16px; display: flex; gap: 12px; flex-wrap: wrap;">
    <?php if ($user['status'] === 'active'): ?>
        <form method="POST" action="users.php" style="display: inline;" onsubmit="return confirm('Suspend this user?');">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <input type="hidden" name="action" value="suspend">
            <button type="submit" class="btn btn-sm btn-danger">Suspend User</button>
        </form>
    <?php else: ?>
        <form method="POST" action="users.php" style="display: inline;">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <input type="hidden" name="action" value="activate">
            <button type="submit" class="btn btn-sm btn-success">Activate User</button>
        </form>
    <?php endif; ?>
    
    <button type="button" class="btn btn-sm btn-secondary" onclick="showEditWallet(<?= $user['id'] ?>, '<?= htmlspecialchars($user['wallet_address'] ?? '') ?>')">Edit Wallet</button>
</div>

<div id="editWalletForm_<?= $user['id'] ?>" style="display: none; margin-top: 20px;">
    <form method="POST" action="users.php">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
        <input type="hidden" name="action" value="update_wallet">
        <div class="form-group">
            <label>Wallet Address:</label>
            <input type="text" name="wallet_address" value="<?= htmlspecialchars($user['wallet_address'] ?? '') ?>" placeholder="Enter wallet address">
        </div>
        <button type="submit" class="btn btn-sm">Update Wallet</button>
    </form>
</div>

<script>
function showEditWallet(userId, currentWallet) {
    document.getElementById('editWalletForm_' + userId).style.display = 'block';
}
</script>
