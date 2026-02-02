
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Status Tiers | X Token</title>
    <link rel="stylesheet" href="assets/style.css?v=2">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Custom Tier & Accordion Styling */
        :root {
            --accent-blue: #3498db;
            --bg-dark: #0b0e11;
            --card-gray: #161a1e;
            --border-gray: #2b3139;
            --text-muted: #848e9c;
            --warning-yellow: #ffc107;
        }

        body {
            background-color: var(--bg-dark);
            color: white;
            margin: 0;
        }

        .status-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        .main-card {
            background: var(--card-gray);
            border: 1px solid var(--border-gray);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .glow-blue {
            color: var(--accent-blue);
            text-shadow: 0 0 15px rgba(52, 152, 219, 0.5);
        }

        /* Accordion Structure */
        .tier-item {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-gray);
            border-radius: 12px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            overflow: hidden;
        }

        .tier-item.active {
            border-color: var(--accent-blue);
            background: rgba(52, 152, 219, 0.05);
        }

        .tier-header {
            display: flex;
            align-items: center;
            padding: 24px;
            justify-content: space-between;
        }

        .tier-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .hex-icon {
            width: 44px;
            height: 44px;
            background: rgba(52, 152, 219, 0.1);
            border: 2px solid var(--accent-blue);
            clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-blue);
            font-weight: bold;
        }

        .tier-badge {
            background: #2b3139;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-left: 10px;
        }

        .benefits-panel {
            max-height: 0;
            opacity: 0;
            padding: 0 24px;
            transition: all 0.4s ease-in-out;
            background: rgba(0, 0, 0, 0.15);
        }

        .tier-item.active .benefits-panel {
            max-height: 600px;
            opacity: 1;
            padding-bottom: 30px;
            padding-top: 10px;
        }

        .benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
            color: #d1d5db;
            font-size: 0.95rem;
        }

        .benefit-item .icon {
            color: var(--accent-blue);
            font-weight: bold;
        }

        .arrow {
            transition: transform 0.3s ease;
            color: var(--text-muted);
        }

        .tier-item.active .arrow {
            transform: rotate(180deg);
            color: var(--accent-blue);
        }

        /* Progress Bar Styling */
        .prog-bg {
            background: #23272c;
            height: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .prog-fill {
            background: linear-gradient(90deg, #3498db, #2ecc71);
            height: 100%;
            border-radius: 5px;
            width: 0%;
            transition: width 1.5s ease-in-out;
        }

        /* Important Note Section matching your screenshot */
        .important-note {
            margin-top: 40px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            background: rgba(255, 193, 7, 0.05);
            border: 1px solid rgba(255, 193, 7, 0.2);
            padding: 20px;
            border-radius: 12px;
        }

        .warning-icon {
            background: var(--warning-yellow);
            color: #000;
            width: 24px;
            height: 24px;
            min-width: 24px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 14px;
        }

        .note-content h4 {
            margin: 0 0 5px 0;
            color: var(--warning-yellow);
            font-size: 1rem;
        }

        .note-content p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.4;
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
            <a href="buy.php">Buy Token</a>
            <a href="profile.php">Profile</a>
            <a href="my-x-token.php">My X Token</a>
            <a href="status.php" class="active">Status</a>
            <a href="how-to-buy.php">How to Buy</a>
        </div>
    </div>

    <div class="status-container">
        <div class="main-card">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <span style="color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Balance</span>
                    <h2 style="font-size: 2.2rem; margin: 5px 0;">0 X</h2>
                </div>
                <div style="text-align: right;">
                    <span style="color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Current Tier</span>
                    <h2 id="tier-name-top" class="glow-blue">Basic Status</h2>
                </div>
            </div>

            <div class="prog-bg">
                <div id="bar-fill" class="prog-fill"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--text-muted);">
                <span id="needed-txt">Calculating...</span>
                <span id="percent-txt">0%</span>
            </div>
        </div>

        <div id="accordion-root"></div>

        <div class="important-note">
            <div class="warning-icon">!</div>
            <div class="note-content">
                <h4>Important Note</h4>
                <p>Token holding requirements must be maintained to keep tier status and benefits. Benefits are subject to terms and conditions.</p>
            </div>
        </div>
    </div>

    <script>
        const balance = 0;

        const tiers = [{
                name: "Bronze",
                min: 200,
                max: 500,
                perks: ["Reduced withdrawal fees (2% lower than the standard rate)", "A chance to win a Tesla Plaid"]
            },
            {
                name: "Silver",
                min: 500,
                max: 2000,
                perks: ["Zero trading fees", "24/7 priority support", "Participation in a Zoom call with developers and other investors", "Increased chances to win a Tesla Plaid"]
            },
            {
                name: "Gold",
                min: 2000,
                max: 10000,
                perks: ["10% discount on all Tesla products for 2 years", "Ability to withdraw tokens before listing", "Participation in quarterly meetings with top management and token developers", "Additional bonus: +5% on all token deposits"]
            },
            {
                name: "Platinum",
                min: 10000,
                max: 50000,
                perks: ["Lifetime 20% discount on all Tesla products", "Personal financial advisor", "Access to all VIP events", "5-year gift subscription to Starlink", "Access to an exclusive alpha group with insider information", "Special NFT artwork from our community as a gift"]
            },
            {
                name: "Diamond",
                min: 50000,
                max: 100000,
                perks: ["Personal meeting with the CEO and project founders", "Priority investment opportunities in new company projects", "Free Tesla Plaid upon reaching a 50,000 X contribution", "Unique community status with voting rights on important decisions", "Early access to the app (new trading platform X)"]
            },
            {
                name: "Legendary",
                min: 100000,
                max: Infinity,
                perks: ["Full access to all previous bonuses", "A share of the profits from future company projects", "Exclusive terms for token purchases in future presales", "Ability to propose your own improvements to the X ecosystem", "A personalized Tesla of any model with a limited edition design from the company", "A share in the X company's stock upon reaching a 200,000 X contribution"]
            }
        ];

        function toggleTier(element) {
            const allItems = document.querySelectorAll('.tier-item');
            const isActive = element.classList.contains('active');
            allItems.forEach(item => item.classList.remove('active'));
            if (!isActive) {
                element.classList.add('active');
            }
        }

        function init() {
            let currentTier = {
                name: "Basic",
                min: 0
            };
            let nextTier = tiers[0];

            for (let i = 0; i < tiers.length; i++) {
                if (balance >= tiers[i].min) {
                    currentTier = tiers[i];
                    nextTier = tiers[i + 1] || null;
                }
            }

            document.getElementById('tier-name-top').innerText = currentTier.name + " Status";

            if (nextTier) {
                const range = nextTier.min - currentTier.min;
                const progressInsideTier = balance - currentTier.min;
                const percent = Math.floor((progressInsideTier / range) * 100);

                document.getElementById('bar-fill').style.width = Math.min(percent, 100) + "%";
                document.getElementById('percent-txt').innerText = Math.min(percent, 100) + "%";
                document.getElementById('needed-txt').innerText = (nextTier.min - balance).toLocaleString() + " X needed to reach " + nextTier.name + " Status";
            } else {
                document.getElementById('bar-fill').style.width = "100%";
                document.getElementById('percent-txt').innerText = "100%";
                document.getElementById('needed-txt').innerText = "Maximum Status Achieved";
            }

            const root = document.getElementById('accordion-root');
            root.innerHTML = tiers.map(t => {
                const unlocked = balance >= t.min;
                return `
                    <div class="tier-item" onclick="toggleTier(this)">
                        <div class="tier-header">
                            <div class="tier-info">
                                <div class="hex-icon">${unlocked ? '✓' : '★'}</div>
                                <div>
                                    <div style="font-weight:bold; font-size:1.1rem;">${t.name} Status <span class="tier-badge">Tier</span></div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted)">${t.min.toLocaleString()} - ${t.max === Infinity ? '+' : t.max.toLocaleString()} tokens</div>
                                </div>
                            </div>
                            <div class="arrow">▼</div>
                        </div>
                        <div class="benefits-panel">
                            ${t.perks.map(p => `<div class="benefit-item"><span class="icon">✦</span> <span>${p}</span></div>`).join('')}
                        </div>
                    </div>
                `;
            }).join('');
        }

        window.onload = init;
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