<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$userId = getUserId();
$user = getUserById($userId);
$tokenPrice = (float)getSetting('x_token_price', 5.44);

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_purchase'])) {
    $coin = sanitize($_POST['coin'] ?? '');
    $network = sanitize($_POST['network'] ?? '');
    $tokens = floatval($_POST['tokens'] ?? 0);
    
    if ($tokens < 50) {
        $error = 'Minimum purchase is 50 X tokens.';
    } elseif (empty($coin) || empty($network)) {
        $error = 'Please select a payment method and network.';
    } else {
        // Calculate amounts
        $usdAmount = $tokens * $tokenPrice;
        $orderId = generateOrderId();
        
        try {
            $pdo = getDB();
            
            // Create transaction record
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, crypto_type, usd_amount, network, order_id, status, created_at) VALUES (?, 'buy', ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([$userId, $tokens, $coin, $usdAmount, $network, $orderId]);
            
            $transactionId = $pdo->lastInsertId();
            
            // Redirect to payment page
            header('Location: payment.php?order=' . $orderId);
            exit;
        } catch (Exception $e) {
            $error = 'Failed to create order. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Buy X Tokens</title>
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
            <a href="buy.php" class="active">Buy Token</a>
            <a href="profile.php" class="">Profile</a>
            <a href="my-x-token.php" class="">My X Token</a>
            <a href="status.php" class="">Status</a>
            <a href="how-to-buy.php" class="">How to Buy</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container buy-page">
        
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

        <h1 class="buy-title">Buy X Tokens</h1>
        <p class="buy-sub">Select your preferred cryptocurrency</p>

        
        <form method="POST" id="buyForm">
            <input type="hidden" name="coin" id="selectedCoin">
            <input type="hidden" name="network" id="selectedNetwork">
            <input type="hidden" name="confirm_purchase" value="1">

            <!-- COINS -->
            <div class="coin-grid">
                                    <div class="coin-card" data-coin="BTC">
                        <span class="coin-check">✓</span>
                        <div class="coin">
                            <img src="https://cryptologos.cc/logos/bitcoin-btc-logo.png" alt="BTC">
                            <div>
                                <strong>BTC</strong>
                                <span>Pay with BTC</span>
                            </div>
                        </div>
                    </div>
                                    <div class="coin-card" data-coin="ETH">
                        <span class="coin-check">✓</span>
                        <div class="coin">
                            <img src="https://cryptologos.cc/logos/ethereum-eth-logo.png" alt="ETH">
                            <div>
                                <strong>ETH</strong>
                                <span>Pay with ETH</span>
                            </div>
                        </div>
                    </div>
                                    <div class="coin-card" data-coin="USDT">
                        <span class="coin-check">✓</span>
                        <div class="coin">
                            <img src="https://cryptologos.cc/logos/tether-usdt-logo.png" alt="USDT">
                            <div>
                                <strong>USDT</strong>
                                <span>Pay with USDT</span>
                            </div>
                        </div>
                    </div>
                                    <div class="coin-card" data-coin="USDC">
                        <span class="coin-check">✓</span>
                        <div class="coin">
                            <img src="https://cryptologos.cc/logos/usd-coin-usdc-logo.png" alt="USDC">
                            <div>
                                <strong>USDC</strong>
                                <span>Pay with USDC</span>
                            </div>
                        </div>
                    </div>
                                    <div class="coin-card" data-coin="BNB">
                        <span class="coin-check">✓</span>
                        <div class="coin">
                            <img src="https://cryptologos.cc/logos/binance-coin-bnb-logo.png" alt="BNB">
                            <div>
                                <strong>BNB</strong>
                                <span>Pay with BNB</span>
                            </div>
                        </div>
                    </div>
                                    <div class="coin-card" data-coin="TRX">
                        <span class="coin-check">✓</span>
                        <div class="coin">
                            <img src="https://cryptologos.cc/logos/tron-trx-logo.png" alt="TRX">
                            <div>
                                <strong>TRX</strong>
                                <span>Pay with TRX</span>
                            </div>
                        </div>
                    </div>
                                    <div class="coin-card" data-coin="SOL">
                        <span class="coin-check">✓</span>
                        <div class="coin">
                            <img src="https://cryptologos.cc/logos/solana-sol-logo.png" alt="SOL">
                            <div>
                                <strong>SOL</strong>
                                <span>Pay with SOL</span>
                            </div>
                        </div>
                    </div>
                                    <div class="coin-card" data-coin="XRP">
                        <span class="coin-check">✓</span>
                        <div class="coin">
                            <img src="https://cryptologos.cc/logos/xrp-xrp-logo.png" alt="XRP">
                            <div>
                                <strong>XRP</strong>
                                <span>Pay with XRP</span>
                            </div>
                        </div>
                    </div>
                                    <div class="coin-card" data-coin="DOGE">
                        <span class="coin-check">✓</span>
                        <div class="coin">
                            <img src="https://cryptologos.cc/logos/dogecoin-doge-logo.png" alt="DOGE">
                            <div>
                                <strong>DOGE</strong>
                                <span>Pay with DOGE</span>
                            </div>
                        </div>
                    </div>
                            </div>

            <!-- NETWORKS -->
            <div id="networkBox" style="display:none; margin-top:28px;">
                <label>Select Network</label>
                <div class="coin-grid" id="networkGrid"></div>
            </div>

            <!-- AMOUNT -->
            <div class="amount-box">
                <label>Amount of X Tokens</label>
                <input type="number" name="tokens" id="tokenInput" min="50" required>

                <p class="muted">
                    USD Equivalent:
                    <strong id="usdValue">$0.00</strong><br>
                    1 X = $<?= formatNumber($tokenPrice, 2) ?>                </p>
            </div>

            <button type="button" class="btn center" id="openModalBtn">
                Proceed to Payment
            </button>
        </form>
    </div>

    <!-- MODAL -->
    <div id="paymentModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Confirm Purchase</h3>
                <span class="close-modal" id="closeModalBtn">×</span>
            </div>

            <div class="tx-box">
                <div class="tx-row">
                    <span>Payment Amount</span>
                    <strong id="modalCoinAmount">$0.00</strong>
                </div>
                <div class="tx-row">
                    <span>Tokens</span>
                    <strong class="green" id="modalTokens">0 X</strong>
                </div>
                <div class="tx-row">
                    <span>Network</span>
                    <strong id="modalNetwork">—</strong>
                </div>
            </div>

            <label class="agree-box">
                <input type="checkbox" id="agreeTerms"> I agree to the terms
            </label>

            <button class="btn center" id="confirmPurchaseBtn">
                Proceed with Purchase
            </button>
        </div>
    </div>

    <script>
        const pricePerToken = <?= $tokenPrice ?>;

        const coinNetworks = {
            BTC: ["BTC"],
            ETH: ["ERC20"],
            USDT: ["ERC20", "TRC20", "BEP20"],
            USDC: ["ERC20", "BEP20"],
            BNB: ["BEP20"],
            TRX: ["TRC20"],
            SOL: ["SOL"],
            XRP: ["XRP"],
            DOGE: ["DOGE"]
        };

        const selectedCoinInput = document.getElementById("selectedCoin");
        const selectedNetworkInput = document.getElementById("selectedNetwork");
        const networkBox = document.getElementById("networkBox");
        const networkGrid = document.getElementById("networkGrid");

        document.querySelectorAll(".coin-card").forEach(card => {
            card.addEventListener("click", () => {
                document.querySelectorAll(".coin-card").forEach(c => c.classList.remove("selected"));
                card.classList.add("selected");

                const coin = card.dataset.coin;
                selectedCoinInput.value = coin;
                selectedNetworkInput.value = "";
                networkGrid.innerHTML = "";

                const nets = coinNetworks[coin] || [];

                if (nets.length === 1) {
                    selectedNetworkInput.value = nets[0];
                    networkBox.style.display = "none";
                } else {
                    networkBox.style.display = "block";
                    nets.forEach(net => {
                        const div = document.createElement("div");
                        div.className = "coin-card";
                        div.innerHTML = `<strong>${net}</strong>`;
                        div.onclick = () => {
                            document.querySelectorAll("#networkGrid .coin-card")
                                .forEach(n => n.classList.remove("selected"));
                            div.classList.add("selected");
                            selectedNetworkInput.value = net;
                        };
                        networkGrid.appendChild(div);
                    });
                }
            });
        });

        document.getElementById("tokenInput").addEventListener("input", e => {
            document.getElementById("usdValue").innerText =
                "$" + (e.target.value * pricePerToken || 0).toFixed(2);
        });

        document.getElementById("openModalBtn").onclick = () => {
            if (!selectedCoinInput.value || !selectedNetworkInput.value) {
                alert("Select coin and network.");
                return;
            }

            document.getElementById("modalTokens").innerText =
                document.getElementById("tokenInput").value + " X";

            document.getElementById("modalCoinAmount").innerText =
                "$" + (document.getElementById("tokenInput").value * pricePerToken).toFixed(2);

            document.getElementById("modalNetwork").innerText = selectedNetworkInput.value;

            document.getElementById("paymentModal").classList.add("show");
        };

        document.getElementById("closeModalBtn").onclick = () =>
            document.getElementById("paymentModal").classList.remove("show");

        document.getElementById("confirmPurchaseBtn").onclick = () => {
            if (!document.getElementById("agreeTerms").checked) {
                alert("You must agree.");
                return;
            }
            document.getElementById("buyForm").submit();
        };
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