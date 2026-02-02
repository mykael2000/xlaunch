<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$userId = getUserId();
$user = getUserById($userId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detailed How to Buy Guide | X Token</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root {
            --accent-blue: #3498db;
            --bg-dark: #0b0e11;
            --card-gray: #161a1e;
            --border-gray: #2b3139;
            --text-muted: #848e9c;
            --success-green: #2ecc71;
        }

        body {
            background-color: var(--bg-dark);
            color: white;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }

        .guide-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .hero-section {
            text-align: center;
            padding: 40px 0;
        }

        .hero-section h1 {
            font-size: 2.8rem;
            color: var(--accent-blue);
            margin-bottom: 10px;
        }

        .section-heading {
            font-size: 1.6rem;
            margin: 50px 0 30px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-gray);
        }

        /* Step Container with Connector Line */
        .step-row {
            display: flex;
            gap: 25px;
            margin-bottom: 40px;
            position: relative;
        }

        /* The vertical connecting line */
        .step-row:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 22px;
            top: 50px;
            bottom: -45px;
            width: 2px;
            background: linear-gradient(to bottom, var(--accent-blue), transparent);
        }

        .step-number-circle {
            min-width: 46px;
            height: 46px;
            background: var(--accent-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
            box-shadow: 0 0 20px rgba(52, 152, 219, 0.4);
            z-index: 2;
        }

        .step-content-box {
            background: var(--card-gray);
            border: 1px solid var(--border-gray);
            border-radius: 16px;
            padding: 25px;
            flex-grow: 1;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icon-square {
            width: 50px;
            height: 50px;
            background: rgba(52, 152, 219, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--accent-blue);
        }

        .text-area h3 {
            margin: 0 0 8px 0;
            font-size: 1.25rem;
        }

        .text-area p {
            margin: 0;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Feature Cards at Bottom */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 60px;
        }

        .feature-mini-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-gray);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        .feature-mini-card h4 {
            margin: 10px 0 5px;
        }

        .feature-mini-card p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 0;
        }

        .big-buy-btn {
            display: block;
            width: fit-content;
            margin: 50px auto;
            background: var(--accent-blue);
            color: white;
            padding: 18px 50px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.2rem;
            transition: 0.3s;
        }

        .big-buy-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(52, 152, 219, 0.4);
        }
    </style>
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
            <a href="buy.php" class="">Buy Token</a>
            <a href="profile.php" class="">Profile</a>
            <a href="my-x-token.php" class="">My X Token</a>
            <a href="status.php" class="">Status</a>
            <a href="how-to-buy.php" class="active">How to Buy</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="guide-container">
        <div class="hero-section">
            <h1>How to Buy X Tokens</h1>
            <p>A simple, step-by-step guide for beginners to secure their investment.</p>
        </div>

        <h2 class="section-heading">Option 1: Using Mobile Apps (Recommended)</h2>

        <div class="step-row">
            <div class="step-number-circle">1</div>
            <div class="step-content-box">
                <div class="icon-square">📱</div>
                <div class="text-area">
                    <h3>Install TrustWallet or MetaMask</h3>
                    <p>Download the official app from the Google Play Store or Apple App Store. This is your personal digital safe.</p>
                </div>
            </div>
        </div>

        <div class="step-row">
            <div class="step-number-circle">2</div>
            <div class="step-content-box">
                <div class="icon-square">💳</div>
                <div class="text-area">
                    <h3>Use Your Credit or Debit Card</h3>
                    <p>Tap the 'Buy' button inside your wallet app. Purchase USDT, ETH, or BTC using your regular bank card.</p>
                </div>
            </div>
        </div>

        <div class="step-row">
            <div class="step-number-circle">3</div>
            <div class="step-content-box">
                <div class="icon-square">🔑</div>
                <div class="text-area">
                    <h3>Secure Your Recovery Phrase</h3>
                    <p>Write down your 12-word recovery phrase on paper and hide it. Never share this with anyone!</p>
                </div>
            </div>
        </div>

        <div class="step-row">
            <div class="step-number-circle">4</div>
            <div class="step-content-box">
                <div class="icon-square">➡</div>
                <div class="text-area">
                    <h3>Transfer to X Token Address</h3>
                    <p>Copy the payment address from our 'Buy Token' page and send your crypto there to receive your X Tokens.</p>
                </div>
            </div>
        </div>

        <h2 class="section-heading">Detailed Purchase Guide</h2>

        <div class="step-row">
            <div class="step-number-circle">1</div>
            <div class="step-content-box">
                <div class="icon-square">💰</div>
                <div class="text-area">
                    <h3>Select Your Currency</h3>
                    <p>On the Buy Token page, pick which coin you want to pay with (USDT, ETH, or BTC).</p>
                </div>
            </div>
        </div>

        <div class="step-row">
            <div class="step-number-circle">2</div>
            <div class="step-content-box">
                <div class="icon-square">⌨</div>
                <div class="text-area">
                    <h3>Enter Amount</h3>
                    <p>Type in how much you want to spend. Our system will instantly show you how many X Tokens you will get.</p>
                </div>
            </div>
        </div>

        <div class="step-row">
            <div class="step-number-circle">3</div>
            <div class="step-content-box">
                <div class="icon-square">🔍</div>
                <div class="text-area">
                    <h3>Review Transaction</h3>
                    <p>Double-check the amounts one last time before you click 'Proceed'.</p>
                </div>
            </div>
        </div>

        <div class="step-row">
            <div class="step-number-circle">4</div>
            <div class="step-content-box">
                <div class="icon-square">✅</div>
                <div class="text-area">
                    <h3>Complete Payment</h3>
                    <p>Send the exact amount to the provided wallet address. Tokens will appear in your account automatically.</p>
                </div>
            </div>
        </div>

        <div class="feature-grid">
            <div class="feature-mini-card">
                <span>🔒</span>
                <h4>Secure</h4>
                <p>Top-tier encryption.</p>
            </div>
            <div class="feature-mini-card">
                <span>⚡</span>
                <h4>Fast</h4>
                <p>Instant processing.</p>
            </div>
            <div class="feature-mini-card">
                <span>💵</span>
                <h4>Best Rates</h4>
                <p>Value token pricing.</p>
            </div>
        </div>

        <a href="buy.php" class="big-buy-btn">Ready? Buy X Tokens Now →</a>
    </div>
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