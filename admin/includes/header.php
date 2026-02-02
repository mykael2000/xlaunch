<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Panel' ?> - X Token</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #0b0f1a 0%, #1a1f35 100%);
            min-height: 100vh;
            color: #fff;
        }
        
        .admin-header {
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 600;
        }
        
        .admin-logo svg {
            width: 32px;
            height: 32px;
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 16px;
            color: #9aa4bf;
            font-size: 14px;
        }
        
        .admin-user a {
            color: #ef4444;
            text-decoration: none;
        }
        
        .admin-user a:hover {
            color: #dc2626;
        }
        
        .admin-layout {
            display: flex;
            min-height: calc(100vh - 64px);
        }
        
        .admin-sidebar {
            width: 260px;
            background: rgba(255, 255, 255, 0.03);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 24px 0;
        }
        
        .sidebar-nav a {
            display: block;
            padding: 12px 24px;
            color: #9aa4bf;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }
        
        .sidebar-nav a.active {
            background: rgba(79, 140, 255, 0.1);
            color: #4f8cff;
            border-left-color: #4f8cff;
        }
        
        .admin-content {
            flex: 1;
            padding: 32px;
        }
        
        .page-header {
            margin-bottom: 32px;
        }
        
        .page-title {
            font-size: 32px;
            margin-bottom: 8px;
        }
        
        .page-subtitle {
            color: #9aa4bf;
            font-size: 14px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 24px;
        }
        
        .stat-label {
            color: #9aa4bf;
            font-size: 13px;
            margin-bottom: 8px;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .stat-change {
            font-size: 12px;
            color: #22c55e;
        }
        
        .stat-change.negative {
            color: #ef4444;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 600;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        th {
            color: #9aa4bf;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        td {
            font-size: 14px;
        }
        
        tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-pending {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
        }
        
        .badge-approved {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }
        
        .badge-rejected {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
        
        .badge-completed {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }
        
        .badge-active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }
        
        .badge-suspended {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #4f8cff 0%, #6366f1 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #cbd5e1;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
        }
        
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #4f8cff;
            background: rgba(255, 255, 255, 0.08);
        }
        
        .pagination {
            display: flex;
            gap: 8px;
            margin-top: 20px;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
        }
        
        .pagination a:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .pagination .active {
            background: #4f8cff;
            border-color: #4f8cff;
        }
        
        .filters {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="admin-logo">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path>
            </svg>
            <span>Admin Panel</span>
        </div>
        <div class="admin-user">
            <span>Welcome, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="admin-layout">
        <div class="admin-sidebar">
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="<?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">📊 Dashboard</a>
                <a href="users.php" class="<?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>">👥 Users</a>
                <a href="transactions.php" class="<?= ($currentPage ?? '') === 'transactions' ? 'active' : '' ?>">💰 Transactions</a>
                <a href="withdrawals.php" class="<?= ($currentPage ?? '') === 'withdrawals' ? 'active' : '' ?>">💸 Withdrawals</a>
                <a href="support.php" class="<?= ($currentPage ?? '') === 'support' ? 'active' : '' ?>">💬 Support</a>
                <a href="settings.php" class="<?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">⚙️ Settings</a>
                <a href="email_users.php" class="<?= ($currentPage ?? '') === 'email' ? 'active' : '' ?>">📧 Email Users</a>
            </nav>
        </div>
        
        <div class="admin-content">
