
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
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
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="buy.php" class="">Buy Token</a>
            <a href="profile.php" class="">Profile</a>
            <a href="my-x-token.php" class="">My X Token</a>
            <a href="status.php" class="">Status</a>
            <a href="how-to-buy.php" class="">How to Buy</a>
        </div>
    </div>

    <div class="container">

        <h1 class="page-title">Dashboard Overview</h1>

        <!-- DASHBOARD CARDS -->
        <div class="cards dashboard-cards">

            <div class="card">
                <p class="card-title">Balance</p>
                <h2 class="card-value">
                    0.00 <span>X</span>
                </h2>
                <p class="muted">Your current X token balance</p>
                <a href="how-to-buy.php" class="btn small">How to Buy →</a>
            </div>

            <div class="card green-glow">
                <p class="card-title">USD Equivalent</p>
                <h2 class="card-value green">
                    $0.00                </h2>
                <p class="muted">1 X = $5.44 · Stage 3 Price</p>
            </div>

            <div class="card purple-glow">
                <p class="card-title">Status</p>
                <h2 class="card-value blue">
                    Basic                </h2>
                <p class="muted">Based on your contribution</p>

                <div class="progress mini">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>

                <a href="buy.php" class="btn">Buy X Tokens Now →</a>
            </div>

        </div>

        <!-- TWEET / ELON SECTION (UNTOUCHED) -->
        <div class="tweet-box">
            <div class="tweet-header">
                <img src="https://abs.twimg.com/sticky/default_profile_images/default_profile_400x400.png" alt="Elon Musk">
                <div class="tweet-user">
                    <div class="name-row">
                        <span class="name">Elon Musk</span>
                        <svg class="verified" viewBox="0 0 24 24">
                            <path fill="#1D9BF0"
                                d="M22.5 12l-2.5 2.9.3 3.8-3.7.9-1.9 3.3-3.7-1.5-3.7 1.5-1.9-3.3-3.7-.9.3-3.8L1.5 12l2.5-2.9-.3-3.8 3.7-.9 1.9-3.3 3.7 1.5 3.7-1.5 1.9 3.3 3.7.9-.3 3.8z" />
                            <path fill="#fff"
                                d="M10.1 13.7l-1.8-1.8-1.1 1.1 2.9 2.9 6-6-1.1-1.1z" />
                        </svg>
                    </div>
                    <span class="handle">@elonmusk</span>
                </div>
            </div>

            <div class="tweet-content">
                <p>
                    We extend a warm welcome as you embark on a unique opportunity to be part of the future of blockchain
                    technology and decentralized possibilities through X Coin. As we commence our presale stages, currently
                    at Stage 3 with a price of $5.44, you have the exclusive chance to invest in X Coin before its value
                    escalates in subsequent stages, culminating in the public sale at $27.50.
                </p>

                <p>
                    Owning X Coin is more than a financial investment; it’s an active endorsement of a visionary project
                    propelling the progress of blockchain technology, laying the groundwork for a decentralized future.
                </p>

                <p>
                    At X Coin, we prioritize top-tier security and unwavering transparency for every transaction, all
                    conducted directly on our platform. Our dedicated live chat support team is available around the clock
                    to address any inquiries or concerns you may have.
                </p>

                <p>
                    Seize this unique opportunity to shape the future of blockchain technology. Invest in X Coin today and
                    embark on an exciting journey with us!
                </p>
            </div>
        </div>

        <!-- TOKEN SALES PROGRESS (FIXED TO MATCH IMAGE 2) -->
        <div class="token-progress-section">

            <div class="token-progress-title">
                Token Sales Progress
            </div>

            <div class="token-progress-row">
                <span class="token-progress-left">
                    Raised Amount: 5,317,977 X
                </span>

                <div class="token-progress-bar">
                    <div class="token-progress-fill" style="width: 82.13%;"></div>
                </div>

                <span class="token-progress-right">
                    Total Tokens: 6,475,000 X
                </span>
            </div>

            <div class="token-progress-percent">
                82.13% Complete
            </div>

            <div class="token-progress-action">
                <a href="buy.php" class="btn">
                    Buy X Tokens Now →
                </a>
            </div>

        </div>

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