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
$status = getUserStatus($userId);

$tokenPrice = (float)getSetting('x_token_price', 5.44);
$usdEquivalent = $balance['x_token_balance'] * $tokenPrice;

// Get transaction history
try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'buy' ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $transactions = $stmt->fetchAll();
    
    // Calculate stats
    $totalPurchased = 0;
    foreach ($transactions as $tx) {
        if ($tx['status'] === 'completed') {
            $totalPurchased += $tx['amount'];
        }
    }
    
} catch (Exception $e) {
    $transactions = [];
    $totalPurchased = 0;
    $bonusTokens = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My X Token</title>
    <link rel="stylesheet" href="assets/style.css?v=2">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <div class="top-nav">
        <div class="logo">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path>
            </svg>
        </div>

        <div class="menu-toggle" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="nav-links" id="navMenu">
            <a href="dashboard.php" class="">Dashboard</a>
            <a href="buy.php">Buy Token</a>
            <a href="profile.php">Profile</a>
            <a href="my-x-token.php" class="active">My X Token</a>
            <a href="status.php">Status</a>
            <a href="how-to-buy.php">How to Buy</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h1 class="page-title">My X Token</h1>

            <div style="display:flex; gap:14px;">
                <a href="buy.php" class="btn">+ Buy More Token</a>
                <a href="withdraw.php" class="btn">Withdraw</a>
            </div>
        </div>

        <!-- STATS -->
        <div class="cards">
            <div class="card">
                <div class="card-title">Token Balance</div>
                <div class="stat-box">
                    <span class="stat-number"><?= formatNumber($balance['x_token_balance'], 2) ?></span>
                    <span class="stat-unit">X</span>
                </div>
            </div>

            <div class="card">
                <div class="card-title">Equivalent in USD</div>
                <div class="stat-box">
                    <span class="stat-number">$<?= formatNumber($usdEquivalent, 2) ?></span>
                </div>
            </div>

            <div class="card">
                <div class="card-title">Purchased Tokens</div>
                <div class="stat-box">
                    <span class="stat-number"><?= formatNumber($totalPurchased, 2) ?></span>
                    <span class="stat-unit">X</span>
                </div>
            </div>

            <div class="card">
                <div class="card-title">Total Contributed</div>
                <div class="stat-box">
                    <span class="stat-number">$<?= formatNumber($status['contribution_amount'], 2) ?></span>
                </div>
            </div>

            <div class="card">
                <div class="card-title">Quick Actions</div>
                <button class="btn" style="width:100%; margin-bottom:12px;" onclick="toggleTxHistory()">
                    View Transaction History
                </button>
                <button class="btn alt" style="width:100%;">Generate Referral Link</button>
            </div>
        </div>

        <!-- TRANSACTION HISTORY -->
        <div id="tx-history" style="display:none; margin-top:50px;">
            <h2 style="font-size:18px; margin-bottom:20px;">Transaction History</h2>

            <div class="card" style="padding:0; overflow:hidden;">

                <div style="
                display:grid;
                grid-template-columns: 1.3fr 1fr 1fr 1fr 1fr;
                padding:14px 20px;
                font-size:13px;
                color:#9aa4bf;
                background:rgba(255,255,255,.04);
            ">
                    <div>Date</div>
                    <div>Payment</div>
                    <div>Tokens</div>
                    <div>USD</div>
                    <div>Status</div>
                </div>

                <?php if (count($transactions) > 0): ?>
                    <?php foreach ($transactions as $tx): ?>
                        <div style="
                            display:grid;
                            grid-template-columns: 1.3fr 1fr 1fr 1fr 1fr;
                            padding:16px 20px;
                            border-top:1px solid rgba(255,255,255,.06);
                            align-items:center;
                        ">
                            <div class="muted"><?= date('M d, Y', strtotime($tx['created_at'])) ?></div>

                            <div>
                                <?= htmlspecialchars($tx['crypto_type']) ?>                                <span class="muted">· <?= htmlspecialchars($tx['network']) ?></span>
                            </div>

                            <div><?= formatNumber($tx['amount'], 2) ?> X</div>
                            <div>$<?= formatNumber($tx['usd_amount'], 2) ?></div>

                            <div>
                                <?php 
                                $statusColor = 'color:#facc15;'; // pending - yellow
                                if ($tx['status'] === 'completed' || $tx['status'] === 'approved') {
                                    $statusColor = 'color:#22c55e;'; // green
                                } elseif ($tx['status'] === 'failed' || $tx['status'] === 'cancelled' || $tx['status'] === 'rejected') {
                                    $statusColor = 'color:#ef4444;'; // red
                                }
                                ?>
                                <span style="<?= $statusColor ?>"><?= ucfirst($tx['status']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding:40px 20px; text-align:center; color:#848e9c;">
                        No transactions yet. <a href="buy.php" style="color:#1D9BF0;">Buy your first tokens</a>
                    </div>
                <?php endif; ?>
                                    
            </div>
        </div>
    </div>

    <script>
        function toggleTxHistory() {
            const box = document.getElementById("tx-history");
            box.style.display = box.style.display === "none" ? "block" : "none";
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
</script>    <script>
        function toggleMenu() {
            const menu = document.getElementById("navMenu");
            menu.classList.toggle("active");
        }
    </script>
</body>

</html>