<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/includes/admin_auth.php';

requireAdminLogin();

$currentPage = 'settings';
$pageTitle = 'Settings';

$pdo = getDB();
$adminId = getAdminId();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token';
    } else {
        try {
            // Update all settings
            $settings = [
                'x_token_price',
                'current_stage',
                'tokens_sold',
                'total_tokens',
                'min_purchase',
                'max_purchase',
                'btc_wallet',
                'eth_wallet',
                'usdt_wallet',
                'usdc_wallet',
                'doge_wallet',
                'bnb_wallet',
                'trx_wallet',
                'sol_wallet',
                'xrp_wallet'
            ];
            
            foreach ($settings as $key) {
                if (isset($_POST[$key])) {
                    updateSetting($key, $_POST[$key]);
                }
            }
            
            // Handle checkbox (will be unset if unchecked)
            updateSetting('registration_enabled', isset($_POST['registration_enabled']) ? '1' : '0');
            
            logActivity('Settings Updated', 'System settings updated', $adminId);
            $success = 'Settings updated successfully!';
            
        } catch (Exception $e) {
            $error = 'Failed to update settings: ' . $e->getMessage();
        }
    }
}

// Get current settings
$currentSettings = [];
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
while ($row = $stmt->fetch()) {
    $currentSettings[$row['key']] = $row['value'];
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">System Settings</h1>
    <p class="page-subtitle">Configure X Token presale platform</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
    
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Token Settings</h2>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label>X Token Price (USD)</label>
                <input type="number" step="0.01" name="x_token_price" value="<?= htmlspecialchars($currentSettings['x_token_price'] ?? '5.44') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Current Stage</label>
                <input type="number" name="current_stage" value="<?= htmlspecialchars($currentSettings['current_stage'] ?? '3') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Tokens Sold</label>
                <input type="number" name="tokens_sold" value="<?= htmlspecialchars($currentSettings['tokens_sold'] ?? '5317977') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Total Tokens (This Stage)</label>
                <input type="number" name="total_tokens" value="<?= htmlspecialchars($currentSettings['total_tokens'] ?? '6475000') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Min Purchase (X Tokens)</label>
                <input type="number" name="min_purchase" value="<?= htmlspecialchars($currentSettings['min_purchase'] ?? '50') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Max Purchase (X Tokens)</label>
                <input type="number" name="max_purchase" value="<?= htmlspecialchars($currentSettings['max_purchase'] ?? '1000000') ?>" required>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Wallet Addresses</h2>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label>Bitcoin (BTC) Wallet</label>
                <input type="text" name="btc_wallet" value="<?= htmlspecialchars($currentSettings['btc_wallet'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Ethereum (ETH) Wallet</label>
                <input type="text" name="eth_wallet" value="<?= htmlspecialchars($currentSettings['eth_wallet'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>USDT Wallet</label>
                <input type="text" name="usdt_wallet" value="<?= htmlspecialchars($currentSettings['usdt_wallet'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>USDC Wallet</label>
                <input type="text" name="usdc_wallet" value="<?= htmlspecialchars($currentSettings['usdc_wallet'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Dogecoin (DOGE) Wallet</label>
                <input type="text" name="doge_wallet" value="<?= htmlspecialchars($currentSettings['doge_wallet'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>BNB Wallet</label>
                <input type="text" name="bnb_wallet" value="<?= htmlspecialchars($currentSettings['bnb_wallet'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>TRON (TRX) Wallet</label>
                <input type="text" name="trx_wallet" value="<?= htmlspecialchars($currentSettings['trx_wallet'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Solana (SOL) Wallet</label>
                <input type="text" name="sol_wallet" value="<?= htmlspecialchars($currentSettings['sol_wallet'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>XRP Wallet</label>
                <input type="text" name="xrp_wallet" value="<?= htmlspecialchars($currentSettings['xrp_wallet'] ?? '') ?>" required>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">System Settings</h2>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="registration_enabled" value="1" <?= ($currentSettings['registration_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                Enable User Registration
            </label>
        </div>
    </div>
    
    <div style="margin-top: 24px;">
        <button type="submit" class="btn">Save Settings</button>
    </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
