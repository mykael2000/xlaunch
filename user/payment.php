<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$userId = getUserId();
$user = getUserById($userId);

$orderId = sanitize($_GET['order'] ?? '');
$error = '';
$success = '';

if (empty($orderId)) {
    header('Location: buy.php');
    exit;
}

// Get transaction details
try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$orderId, $userId]);
    $transaction = $stmt->fetch();
    
    if (!$transaction) {
        header('Location: buy.php');
        exit;
    }
} catch (Exception $e) {
    header('Location: buy.php');
    exit;
}

// Handle payment confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    try {
        $stmt = $pdo->prepare("UPDATE transactions SET status = 'approved' WHERE id = ?");
        $stmt->execute([$transaction['id']]);
        
        $success = 'Payment confirmation submitted. Awaiting admin verification.';
        
        // Refresh transaction data
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
        $stmt->execute([$transaction['id']]);
        $transaction = $stmt->fetch();
    } catch (Exception $e) {
        $error = 'Failed to submit payment confirmation.';
    }
}

// Get payment wallet address
$walletAddress = getWalletAddress($transaction['crypto_type'], $transaction['network']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link rel="stylesheet" href="assets/style.css?v=2">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <div class="container" style="max-width:520px; margin-top:90px;">

        <div class="card" style="padding:32px;">
        
            <?php if ($error): ?>
                <div class="alert alert-error" style="margin-bottom:20px; padding:14px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:8px; color:#fca5a5;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" style="margin-bottom:20px; padding:14px; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; color:#86efac;">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <h1 style="text-align:center; margin-bottom:6px;">
                <?php if ($transaction['status'] === 'approved' || $transaction['status'] === 'completed'): ?>
                    Payment Submitted
                <?php else: ?>
                    Waiting for Payment
                <?php endif; ?>
            </h1>

            <p class="muted" style="text-align:center; margin-bottom:28px;">
                <?php if ($transaction['status'] === 'approved' || $transaction['status'] === 'completed'): ?>
                    Your payment is being verified by our team
                <?php else: ?>
                    Complete the payment using the details below
                <?php endif; ?>
            </p>

            <div class="tx-box">
                <div class="tx-row">
                    <span>Order</span>
                    <strong>#<?= htmlspecialchars($transaction['order_id']) ?></strong>
                </div>
            </div>

            <div class="tx-box">
                <span class="muted">Token Amount</span>
                <div class="copy-box">
                    <input type="text" id="copyAmount"
                        value="<?= formatNumber($transaction['amount'], 2) ?> X Tokens" readonly>
                    <button type="button" onclick="copyText('copyAmount', this)">Copy</button>
                </div>
            </div>

            <div class="tx-box">
                <div class="tx-row">
                    <span>Payment method</span>
                    <strong><?= htmlspecialchars($transaction['crypto_type']) ?> · <?= htmlspecialchars($transaction['network']) ?></strong>
                </div>
            </div>

            <div class="tx-box">
                <span class="muted">Send to address</span>
                <div class="copy-box">
                    <input type="text" id="copyAddress"
                        value="<?= htmlspecialchars($walletAddress ?: 'Not available') ?>" readonly>
                    <button type="button" onclick="copyText('copyAddress', this)">Copy</button>
                </div>
            </div>

            <div class="tx-box">
                <div class="tx-row">
                    <span>USD Amount</span>
                    <strong>$<?= formatNumber($transaction['usd_amount'], 2) ?></strong>
                </div>
            </div>

            <ul class="muted" style="font-size:13px; margin-top:18px;">
                <li>Send only on the selected network</li>
                <li>Wrong network may result in loss of funds</li>
                <li>Your tokens will be credited after confirmation</li>
            </ul>

            <?php if ($transaction['status'] === 'pending'): ?>
            <div style="display:flex; gap:14px; margin-top:30px;">
                <form method="post" style="width:100%;">
                    <button type="submit" name="confirm_payment" class="btn" style="width:100%;">
                        I Have Made the Payment
                    </button>
                </form>

                <a href="buy.php" class="btn alt" style="flex:1;">
                    Cancel Order
                </a>
            </div>
            <?php else: ?>
            <div style="margin-top:30px;">
                <a href="dashboard.php" class="btn" style="width:100%; display:block; text-align:center;">
                    Back to Dashboard
                </a>
            </div>
            <?php endif; ?>

        </div>
    </div>

    
    <script>
        function copyText(id, btn) {
            const input = document.getElementById(id);
            input.select();
            document.execCommand("copy");

            const original = btn.innerText;
            btn.innerText = "Copied";
            btn.disabled = true;

            setTimeout(() => {
                btn.innerText = original;
                btn.disabled = false;
            }, 1200);
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