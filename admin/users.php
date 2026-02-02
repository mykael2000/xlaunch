<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/admin_auth.php';

requireAdminLogin();

$currentPage = 'users';
$pageTitle = 'Users';

$pdo = getDB();
$adminId = getAdminId();

$success = '';
$error = '';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token';
    } else {
        $userId = $_POST['user_id'] ?? 0;
        $action = $_POST['action'];
        
        if ($action === 'suspend') {
            $stmt = $pdo->prepare("UPDATE users SET status = 'suspended' WHERE id = ?");
            $stmt->execute([$userId]);
            logActivity('User Suspended', "Suspended user ID: $userId", $adminId, $userId);
            $success = "User suspended successfully!";
            
        } elseif ($action === 'activate') {
            $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
            $stmt->execute([$userId]);
            logActivity('User Activated', "Activated user ID: $userId", $adminId, $userId);
            $success = "User activated successfully!";
            
        } elseif ($action === 'update_wallet') {
            $walletAddress = $_POST['wallet_address'] ?? '';
            $stmt = $pdo->prepare("UPDATE users SET wallet_address = ? WHERE id = ?");
            $stmt->execute([$walletAddress, $userId]);
            logActivity('Wallet Updated', "Updated wallet for user ID: $userId to $walletAddress", $adminId, $userId);
            $success = "Wallet address updated successfully!";
        }
    }
}

// Get filter parameters
$searchQuery = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$where = [];
$params = [];

if ($searchQuery) {
    $where[] = "(email LIKE ? OR fullname LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}

if ($statusFilter) {
    $where[] = "status = ?";
    $params[] = $statusFilter;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users $whereClause");
$stmt->execute($params);
$totalUsers = $stmt->fetch()['total'];
$totalPages = ceil($totalUsers / $perPage);

// Get users
$stmt = $pdo->prepare("
    SELECT u.*, 
           COALESCE(b.x_token_balance, 0) as x_token_balance,
           COALESCE(b.usd_balance, 0) as usd_balance,
           COALESCE(us.status_level, 'Basic') as status_level,
           COALESCE(us.contribution_amount, 0) as contribution_amount
    FROM users u
    LEFT JOIN balances b ON u.id = b.user_id
    LEFT JOIN user_status us ON u.id = us.user_id
    $whereClause
    ORDER BY u.created_at DESC 
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$users = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">User Management</h1>
    <p class="page-subtitle">Manage all registered users</p>
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
                <label>Search</label>
                <input type="text" name="search" placeholder="Email or name..." value="<?= htmlspecialchars($searchQuery) ?>">
            </div>
            
            <div class="filter-group">
                <label>Status</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="suspended" <?= $statusFilter === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            
            <div class="filter-group" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn">Search</button>
            </div>
        </form>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>X Token Balance</th>
                <th>Status Level</th>
                <th>Account Status</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #9aa4bf; padding: 40px;">No users found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td>
                            <div><?= htmlspecialchars($user['fullname']) ?></div>
                            <div style="font-size: 12px; color: #9aa4bf;"><?= htmlspecialchars($user['email']) ?></div>
                        </td>
                        <td>
                            <div><?= number_format($user['x_token_balance'], 2) ?> X</div>
                            <div style="font-size: 12px; color: #9aa4bf;">$<?= number_format($user['usd_balance'], 2) ?></div>
                        </td>
                        <td>
                            <span class="badge badge-<?= strtolower($user['status_level']) ?>" style="background: rgba(79, 140, 255, 0.2); color: #4f8cff;">
                                <?= $user['status_level'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= $user['status'] ?>">
                                <?= ucfirst($user['status']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="showUserDetails(<?= $user['id'] ?>)">View</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?><?= $searchQuery ? "&search=$searchQuery" : '' ?><?= $statusFilter ? "&status=$statusFilter" : '' ?>">← Previous</a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?><?= $searchQuery ? "&search=$searchQuery" : '' ?><?= $statusFilter ? "&status=$statusFilter" : '' ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?><?= $searchQuery ? "&search=$searchQuery" : '' ?><?= $statusFilter ? "&status=$statusFilter" : '' ?>">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- User Details Modal -->
<div id="userModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content: center; overflow-y: auto;">
    <div style="background: #1a1f35; padding: 30px; border-radius: 12px; max-width: 600px; width: 90%; margin: 20px;">
        <div id="userModalContent">Loading...</div>
    </div>
</div>

<script>
function showUserDetails(userId) {
    document.getElementById('userModal').style.display = 'flex';
    
    fetch('ajax/get_user_details.php?id=' + userId)
        .then(response => response.text())
        .then(html => {
            document.getElementById('userModalContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('userModalContent').innerHTML = '<p>Error loading user details</p>';
        });
}

function hideUserModal() {
    document.getElementById('userModal').style.display = 'none';
}

// Close modal on background click
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideUserModal();
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
