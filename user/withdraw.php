<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$userId = getUserId();
$user = getUserById($userId);
$balance = getUserBalance($userId);

$withdrawalsEnabled = (bool)getSetting('withdrawals_enabled', 0);
$minWithdrawal = (float)getSetting('min_withdrawal', 100);

$error = '';
$success = '';

// Handle withdrawal request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_withdrawal'])) {
    $amount = floatval($_POST['amount'] ?? 0);
    $walletAddress = sanitize($_POST['wallet_address'] ?? '');
    
    if (!$withdrawalsEnabled) {
        $error = 'Withdrawals are currently disabled.';
    } elseif ($amount < $minWithdrawal) {
        $error = 'Minimum withdrawal amount is ' . formatNumber($minWithdrawal, 2) . ' X.';
    } elseif ($amount > $balance['x_token_balance']) {
        $error = 'Insufficient balance.';
    } elseif (empty($walletAddress)) {
        $error = 'Please provide a wallet address.';
    } else {
        try {
            $pdo = getDB();
            
            // Start transaction
            $pdo->beginTransaction();
            
            try {
                // Create withdrawal request
                $stmt = $pdo->prepare("INSERT INTO withdrawals (user_id, amount, wallet_address, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
                $stmt->execute([$userId, $amount, $walletAddress]);
                
                // Deduct from balance (will be refunded if rejected)
                $stmt = $pdo->prepare("UPDATE balances SET x_token_balance = x_token_balance - ? WHERE user_id = ?");
                $stmt->execute([$amount, $userId]);
                
                // Commit transaction
                $pdo->commit();
                
                $success = 'Withdrawal request submitted successfully. Awaiting admin approval.';
                
                // Refresh balance
                $balance = getUserBalance($userId);
            } catch (Exception $e) {
                // Rollback on error
                $pdo->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            $error = 'Failed to submit withdrawal request.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Withdraw</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            background: #0b0e11;
            color: #e7e9ea;
            font-family: Inter, system-ui, sans-serif;
        }

        .container {
            max-width: 480px;
            margin: 120px auto;
            padding: 32px;
            background: #111827;
            border-radius: 16px;
            border: 1px solid #1f2937;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .5);
        }

        h1 {
            text-align: center;
            font-size: 26px;
            margin-bottom: 6px;
        }

        .sub {
            text-align: center;
            color: #9ca3af;
            font-size: 14px;
            margin-bottom: 28px;
        }

        .balance-box {
            background: #0b1220;
            border: 1px solid #1f2937;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            margin-bottom: 24px;
        }

        .balance-box strong {
            display: block;
            font-size: 28px;
            margin-top: 6px;
        }

        .notice {
            background: rgba(234, 179, 8, 0.08);
            border: 1px solid rgba(234, 179, 8, 0.25);
            color: #fde68a;
            padding: 14px;
            border-radius: 10px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 24px;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }

        .btn.primary {
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            color: white;
        }

        .btn.alt {
            margin-top: 12px;
            background: #1f2937;
            color: #e5e7eb;
        }

        .btn.disabled {
            opacity: .5;
            cursor: not-allowed;
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>Withdraw</h1>
        <p class="sub">Manage your token withdrawals</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom:20px; padding:14px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:8px; color:#fca5a5; text-align:center;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom:20px; padding:14px; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; color:#86efac; text-align:center;">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="balance-box">
            <span>Your Balance</span>
            <strong><?= formatNumber($balance['x_token_balance'], 2) ?> X</strong>
        </div>

        <?php if (!$withdrawalsEnabled): ?>
            <div class="notice">
                Withdrawals are currently disabled.<br>
                Please check back later.
            </div>

            <a href="dashboard.php">
                <button class="btn primary">Back to Dashboard</button>
            </a>
        <?php elseif ($balance['x_token_balance'] < $minWithdrawal): ?>
            <div class="notice">
                Your balance is below the minimum withdrawal amount of <?= formatNumber($minWithdrawal, 2) ?> X.<br>
                Buy more tokens to meet the requirement.
            </div>

            <a href="buy.php">
                <button class="btn primary">Buy Tokens</button>
            </a>
        <?php else: ?>
            <form method="POST" style="margin-top:20px;">
                <label style="display:block; margin-bottom:8px; color:#e7e9ea;">Withdrawal Amount</label>
                <input type="number" 
                    name="amount" 
                    step="0.01" 
                    min="<?= $minWithdrawal ?>" 
                    max="<?= $balance['x_token_balance'] ?>"
                    style="width:100%; padding:12px; background:#0b1220; border:1px solid #1f2937; border-radius:8px; color:#e7e9ea; margin-bottom:16px;"
                    required>
                
                <label style="display:block; margin-bottom:8px; color:#e7e9ea;">Wallet Address</label>
                <input type="text" 
                    name="wallet_address" 
                    placeholder="Enter your wallet address"
                    style="width:100%; padding:12px; background:#0b1220; border:1px solid #1f2937; border-radius:8px; color:#e7e9ea; margin-bottom:16px;"
                    required>
                
                <p class="muted" style="font-size:13px; margin-bottom:20px;">
                    Minimum withdrawal: <?= formatNumber($minWithdrawal, 2) ?> X<br>
                    Processing time: 24-48 hours
                </p>
                
                <button type="submit" name="submit_withdrawal" class="btn primary">
                    Submit Withdrawal Request
                </button>
            </form>
        <?php endif; ?>
        <a href="dashboard.php">
            <button class="btn alt">Back to Dashboard</button>
        </a>

    </div>

    <script>
        function lockedNotice() {
            alert("Withdrawals will be enabled after the presale ends.");
        }
    </script>

    <!-- =======================
     SUPPORT WIDGET
======================= -->
<div id="support-wrapper">

    <!-- CHAT WINDOW -->
    <div id="support-window" class="support-window">
        <div class="support-header">
            <div class="header-info">
                <div class="support-avatar">X</div>
                <div>
                    <strong>X Live Support</strong>
                    <p class="status-online">Online • Usually replies in minutes</p>
                </div>
            </div>
            <button class="close-support" onclick="toggleSupport()">&times;</button>
        </div>

        <div id="support-chat-content" class="support-chat-content">
            <p class="muted-text">Welcome! How can we help you today?</p>
        </div>

        <div class="support-input-area">
            <input
                type="text"
                id="user-support-msg"
                placeholder="Write a message..."
                inputmode="text"
            >
            <button class="send-btn" onclick="sendUserMsg()">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- FLOATING TRIGGER -->
    <button id="support-trigger" class="support-trigger" onclick="toggleSupport()">
        <span id="notif-badge" class="notification-badge">1</span>
        <svg viewBox="0 0 24 24" width="28" height="28" fill="white">
            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4z"/>
        </svg>
    </button>

</div>

<!-- =======================
     STYLES
======================= -->
<style>
/* ===== BASE ===== */
.support-window {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 360px;
    height: 420px;
    background: #15181c;
    border-radius: 18px;
    border: 1px solid #333;
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 10001;
    box-shadow: 0 10px 40px rgba(0,0,0,.6);
}

.support-window.active {
    display: flex;
}

/* ===== HEADER ===== */
.support-header {
    background: #1D9BF0;
    padding: 14px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.support-avatar {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.status-online {
    margin: 0;
    font-size: 11px;
    opacity: .9;
}

.close-support {
    background: none;
    border: none;
    color: #fff;
    font-size: 26px;
    cursor: pointer;
}

/* ===== CHAT ===== */
.support-chat-content {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #0b0e11;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.msg-bubble {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 14px;
    font-size: 14px;
    color: #fff;
}

.user-side {
    align-self: flex-end;
    background: #1D9BF0;
    border-bottom-right-radius: 4px;
}

.admin-side {
    align-self: flex-start;
    background: #2f3336;
    border-bottom-left-radius: 4px;
}

/* ===== INPUT ===== */
.support-input-area {
    display: flex;
    gap: 10px;
    padding: 12px;
    background: #15181c;
    border-top: 1px solid #333;
}

.support-input-area input {
    flex: 1;
    background: #202327;
    border: 1px solid #333;
    border-radius: 20px;
    padding: 10px 14px;
    color: #fff;
    font-size: 16px;
    outline: none;
}

.send-btn {
    background: none;
    border: none;
    color: #1D9BF0;
    cursor: pointer;
    display: flex;
    align-items: center;
}

/* ===== TRIGGER ===== */
.support-trigger {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #1D9BF0;
    border: none;
    cursor: pointer;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 18px rgba(29,155,240,.4);
}

.notification-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #f4212e;
    color: #fff;
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 12px;
    border: 2px solid #15181c;
}

/* ===== MOBILE FIX ===== */
@media (max-width: 480px) {

    .support-window {
        inset: 0;
        width: 100%;
        height: 100%;
        border-radius: 0;
        z-index: 10001;
    }

    .support-chat-content {
        padding-bottom: 90px;
    }

    .support-input-area {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        z-index: 10002;
    }

    .support-window.active ~ .support-trigger {
        display: none !important;
    }
}
</style>

<!-- =======================
     SCRIPT
======================= -->
<script>
function toggleSupport() {
    const win = document.getElementById('support-window');
    const badge = document.getElementById('notif-badge');
    const isOpen = !win.classList.contains('active');

    win.classList.toggle('active', isOpen);

    if (isOpen) {
        badge.style.display = 'none';
        loadUserMessages();
        setTimeout(() => {
            document.getElementById('user-support-msg').focus();
        }, 150);
    }
}

async function loadUserMessages() {
    try {
        const res = await fetch('./ajax/support.php');
        if (!res.ok) return;

        const data = await res.json();
        const box = document.getElementById('support-chat-content');

        box.innerHTML = data.map(m => `
            <div class="msg-bubble ${m.sender === 'user' ? 'user-side' : 'admin-side'}">
                ${m.message}
            </div>
        `).join('');

        box.scrollTop = box.scrollHeight;
    } catch (e) {
        console.error(e);
    }
}

async function sendUserMsg() {
    const input = document.getElementById('user-support-msg');
    const msg = input.value.trim();
    if (!msg) return;

    const fd = new FormData();
    fd.append('message', msg);
    fd.append('sender', 'user');

    await fetch('./ajax/support.php', { method: 'POST', body: fd });
    input.value = '';
    loadUserMessages();
}

document.getElementById('user-support-msg')
    .addEventListener('keydown', e => {
        if (e.key === 'Enter') sendUserMsg();
    });

setInterval(() => {
    const win = document.getElementById('support-window');
    if (win.classList.contains('active')) loadUserMessages();
}, 4000);
</script></body>

</html>