<?php
define('X_TOKEN_APP', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/user/dashboard.php');
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = loginUser($email, $password);
    
    if ($result['success']) {
        redirect(SITE_URL . '/user/dashboard.php');
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login • X Token</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* ---------- BASE ---------- */
        * {
            box-sizing: border-box;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(circle at top, #1c2540, #0b0f1a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        /* ---------- CARD ---------- */
        .auth-card {
            width: 100%;
            max-width: 420px;
            background: linear-gradient(180deg, rgba(79, 140, 255, 0.15), rgba(0, 0, 0, 0.4));
            backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        /* ---------- HEADER ---------- */
        .auth-card h1 {
            margin: 0 0 6px;
            font-size: 28px;
            font-weight: 800;
            text-align: center;
        }

        .auth-card .sub {
            text-align: center;
            font-size: 14px;
            color: #cbd5e1;
            margin-bottom: 28px;
        }

        .auth-card .sub a {
            color: #4f8cff;
            text-decoration: none;
            font-weight: 600;
        }

        /* ---------- FORM ---------- */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #e5e7eb;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            outline: none;
            font-size: 14px;
        }

        .input-wrap input::placeholder {
            color: #9ca3af;
        }

        .input-wrap input:focus {
            border-color: #4f8cff;
            box-shadow: 0 0 0 2px rgba(79, 140, 255, 0.25);
        }

        /* ---------- OPTIONS ---------- */
        .form-options {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #cbd5e1;
            margin-bottom: 22px;
        }

        .form-options input {
            accent-color: #4f8cff;
        }

        /* ---------- BUTTON ---------- */
        .auth-btn {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: none;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            color: #fff;
            background: linear-gradient(135deg, #4f8cff, #6d5dfc);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .auth-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 30px rgba(79, 140, 255, 0.5);
        }

        /* ---------- ERROR ---------- */
        .error {
            background: rgba(239, 68, 68, 0.15);
            color: #fecaca;
            border: 1px solid rgba(239, 68, 68, 0.4);
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 18px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="auth-card">

        <h1>Welcome Back</h1>
        <p class="sub">
            New to our platform?
            <a href="register.php">Create an account</a>
        </p>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>

            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrap">
                    <input type="email" name="email" placeholder="Enter your email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>

            <div class="form-options">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="auth-btn">
                Sign in →
            </button>

        </form>

    </div>

</body>
</html>
