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
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $agree = $_POST['agree'] ?? '';
    
    if (!$agree) {
        $error = 'You must agree to the privacy policy';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $result = registerUser($email, $password, $fullname);
        
        if ($result['success']) {
            $success = 'Registration successful! You can now login.';
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register • X Token</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
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

        /* Card */
        .auth-card {
            width: 100%;
            max-width: 460px;
            background: linear-gradient(180deg, rgba(79, 140, 255, 0.15), rgba(0, 0, 0, 0.45));
            backdrop-filter: blur(18px);
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.55);
        }

        /* Header */
        .auth-top {
            text-align: center;
            font-size: 14px;
            color: #cbd5e1;
            margin-bottom: 18px;
        }

        .auth-top a {
            color: #4f8cff;
            text-decoration: none;
            font-weight: 600;
        }

        /* Inputs */
        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            outline: none;
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .form-group input:focus {
            border-color: #4f8cff;
            box-shadow: 0 0 0 2px rgba(79, 140, 255, 0.25);
        }

        /* Checkbox */
        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            margin: 14px 0;
        }

        .checkbox a {
            color: #4f8cff;
            text-decoration: none;
        }

        /* Button */
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
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .auth-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 30px rgba(79, 140, 255, 0.45);
        }

        /* Error */
        .error {
            background: rgba(239, 68, 68, 0.15);
            color: #fecaca;
            border: 1px solid rgba(239, 68, 68, 0.4);
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            text-align: center;
        }

        /* Success */
        .success {
            background: rgba(34, 197, 94, 0.15);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.4);
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="auth-card">

        <div class="auth-top">
            Already have an account?
            <a href="login.php">Sign in</a>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="username" placeholder="Enter your full name" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm your password" required>
            </div>

            <div class="checkbox">
                <input type="checkbox" name="agree" required>
                <span>I agree to the <a href="privacy-policy.html">Privacy Policy</a></span>
            </div>

            <button type="submit" class="auth-btn">
                Create Account →
            </button>

        </form>

    </div>

</body>
</html>
