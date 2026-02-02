<?php
/**
 * Email Functions
 * X Token Presale Platform
 * 
 * Note: This uses PHP's mail() function. For production, install PHPMailer via Composer:
 * composer require phpmailer/phpmailer
 */

if (!defined('X_TOKEN_APP')) {
    define('X_TOKEN_APP', true);
}

/**
 * Send email
 * @param string $to
 * @param string $subject
 * @param string $body
 * @param bool $isHTML
 * @return bool
 */
function sendEmail($to, $subject, $body, $isHTML = true) {
    try {
        $fromEmail = getSetting('smtp_from_email', 'noreply@xtoken.com');
        $fromName = getSetting('smtp_from_name', 'X Token');
        
        $headers = [];
        $headers[] = "From: $fromName <$fromEmail>";
        $headers[] = "Reply-To: $fromEmail";
        $headers[] = "X-Mailer: PHP/" . phpversion();
        
        if ($isHTML) {
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-Type: text/html; charset=UTF-8";
        }
        
        return mail($to, $subject, $body, implode("\r\n", $headers));
    } catch (Exception $e) {
        error_log("Email Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send welcome email
 * @param string $email
 * @param string $fullname
 * @return bool
 */
function sendWelcomeEmail($email, $fullname) {
    $subject = "Welcome to X Token Presale!";
    
    $body = getEmailTemplate('welcome', [
        'fullname' => $fullname,
        'login_url' => SITE_URL . '/login.php',
        'dashboard_url' => SITE_URL . '/user/dashboard.php'
    ]);
    
    return sendEmail($email, $subject, $body);
}

/**
 * Send transaction notification to admin
 * @param array $transaction
 * @param array $user
 * @return bool
 */
function sendAdminTransactionNotification($transaction, $user) {
    $adminEmail = getSetting('admin_email', 'admin@xtoken.com');
    $subject = "New Transaction: " . $transaction['order_id'];
    
    $body = getEmailTemplate('admin_transaction', [
        'order_id' => $transaction['order_id'],
        'user_email' => $user['email'],
        'user_fullname' => $user['fullname'],
        'amount' => $transaction['amount'],
        'crypto_type' => $transaction['crypto_type'],
        'crypto_amount' => $transaction['crypto_amount'],
        'network' => $transaction['network'],
        'admin_url' => SITE_URL . '/admin/transactions.php'
    ]);
    
    return sendEmail($adminEmail, $subject, $body);
}

/**
 * Send transaction approval notification
 * @param string $email
 * @param array $transaction
 * @return bool
 */
function sendTransactionApprovedEmail($email, $transaction) {
    $subject = "Transaction Approved - " . $transaction['order_id'];
    
    $body = getEmailTemplate('transaction_approved', [
        'order_id' => $transaction['order_id'],
        'amount' => $transaction['amount'],
        'dashboard_url' => SITE_URL . '/user/dashboard.php'
    ]);
    
    return sendEmail($email, $subject, $body);
}

/**
 * Send transaction rejection notification
 * @param string $email
 * @param array $transaction
 * @param string $reason
 * @return bool
 */
function sendTransactionRejectedEmail($email, $transaction, $reason = '') {
    $subject = "Transaction Rejected - " . $transaction['order_id'];
    
    $body = getEmailTemplate('transaction_rejected', [
        'order_id' => $transaction['order_id'],
        'amount' => $transaction['amount'],
        'reason' => $reason,
        'support_url' => SITE_URL . '/user/dashboard.php'
    ]);
    
    return sendEmail($email, $subject, $body);
}

/**
 * Send withdrawal approval notification
 * @param string $email
 * @param array $withdrawal
 * @return bool
 */
function sendWithdrawalApprovedEmail($email, $withdrawal) {
    $subject = "Withdrawal Approved";
    
    $body = getEmailTemplate('withdrawal_approved', [
        'amount' => $withdrawal['amount'],
        'wallet_address' => $withdrawal['wallet_address'],
        'dashboard_url' => SITE_URL . '/user/dashboard.php'
    ]);
    
    return sendEmail($email, $subject, $body);
}

/**
 * Send withdrawal rejection notification
 * @param string $email
 * @param array $withdrawal
 * @param string $reason
 * @return bool
 */
function sendWithdrawalRejectedEmail($email, $withdrawal, $reason = '') {
    $subject = "Withdrawal Request Rejected";
    
    $body = getEmailTemplate('withdrawal_rejected', [
        'amount' => $withdrawal['amount'],
        'reason' => $reason,
        'support_url' => SITE_URL . '/user/dashboard.php'
    ]);
    
    return sendEmail($email, $subject, $body);
}

/**
 * Get email template
 * @param string $template
 * @param array $data
 * @return string
 */
function getEmailTemplate($template, $data = []) {
    $templates = [
        'welcome' => '
            <html>
            <body style="font-family: Arial, sans-serif; background-color: #0b0f1a; color: #fff; padding: 20px;">
                <div style="max-width: 600px; margin: 0 auto; background: linear-gradient(180deg, rgba(79, 140, 255, 0.15), rgba(0, 0, 0, 0.4)); border-radius: 10px; padding: 30px;">
                    <h1 style="color: #4f8cff;">Welcome to X Token!</h1>
                    <p>Hi {fullname},</p>
                    <p>Thank you for registering with X Token Presale Platform. Your account has been created successfully!</p>
                    <p>You can now login and start purchasing X tokens.</p>
                    <p><a href="{login_url}" style="display: inline-block; background: #4f8cff; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 10px;">Login to Dashboard</a></p>
                    <p style="margin-top: 30px; font-size: 12px; color: #cbd5e1;">If you did not create this account, please ignore this email.</p>
                </div>
            </body>
            </html>
        ',
        
        'admin_transaction' => '
            <html>
            <body style="font-family: Arial, sans-serif; background-color: #0b0f1a; color: #fff; padding: 20px;">
                <div style="max-width: 600px; margin: 0 auto; background: linear-gradient(180deg, rgba(79, 140, 255, 0.15), rgba(0, 0, 0, 0.4)); border-radius: 10px; padding: 30px;">
                    <h1 style="color: #4f8cff;">New Transaction</h1>
                    <p><strong>Order ID:</strong> {order_id}</p>
                    <p><strong>User:</strong> {user_fullname} ({user_email})</p>
                    <p><strong>X Tokens:</strong> {amount}</p>
                    <p><strong>Payment:</strong> {crypto_amount} {crypto_type} ({network})</p>
                    <p><a href="{admin_url}" style="display: inline-block; background: #4f8cff; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 10px;">Review Transaction</a></p>
                </div>
            </body>
            </html>
        ',
        
        'transaction_approved' => '
            <html>
            <body style="font-family: Arial, sans-serif; background-color: #0b0f1a; color: #fff; padding: 20px;">
                <div style="max-width: 600px; margin: 0 auto; background: linear-gradient(180deg, rgba(79, 140, 255, 0.15), rgba(0, 0, 0, 0.4)); border-radius: 10px; padding: 30px;">
                    <h1 style="color: #22c55e;">Transaction Approved!</h1>
                    <p>Your transaction has been approved and your X tokens have been credited to your account.</p>
                    <p><strong>Order ID:</strong> {order_id}</p>
                    <p><strong>X Tokens:</strong> {amount}</p>
                    <p><a href="{dashboard_url}" style="display: inline-block; background: #4f8cff; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 10px;">View Dashboard</a></p>
                </div>
            </body>
            </html>
        ',
        
        'transaction_rejected' => '
            <html>
            <body style="font-family: Arial, sans-serif; background-color: #0b0f1a; color: #fff; padding: 20px;">
                <div style="max-width: 600px; margin: 0 auto; background: linear-gradient(180deg, rgba(79, 140, 255, 0.15), rgba(0, 0, 0, 0.4)); border-radius: 10px; padding: 30px;">
                    <h1 style="color: #ef4444;">Transaction Rejected</h1>
                    <p>Unfortunately, your transaction has been rejected.</p>
                    <p><strong>Order ID:</strong> {order_id}</p>
                    <p><strong>Amount:</strong> {amount} X</p>
                    ' . ('{reason}' ? '<p><strong>Reason:</strong> {reason}</p>' : '') . '
                    <p>Please contact support if you have any questions.</p>
                    <p><a href="{support_url}" style="display: inline-block; background: #4f8cff; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 10px;">Contact Support</a></p>
                </div>
            </body>
            </html>
        ',
        
        'withdrawal_approved' => '
            <html>
            <body style="font-family: Arial, sans-serif; background-color: #0b0f1a; color: #fff; padding: 20px;">
                <div style="max-width: 600px; margin: 0 auto; background: linear-gradient(180deg, rgba(79, 140, 255, 0.15), rgba(0, 0, 0, 0.4)); border-radius: 10px; padding: 30px;">
                    <h1 style="color: #22c55e;">Withdrawal Approved!</h1>
                    <p>Your withdrawal request has been approved and processed.</p>
                    <p><strong>Amount:</strong> {amount} X</p>
                    <p><strong>Wallet Address:</strong> {wallet_address}</p>
                    <p><a href="{dashboard_url}" style="display: inline-block; background: #4f8cff; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 10px;">View Dashboard</a></p>
                </div>
            </body>
            </html>
        ',
        
        'withdrawal_rejected' => '
            <html>
            <body style="font-family: Arial, sans-serif; background-color: #0b0f1a; color: #fff; padding: 20px;">
                <div style="max-width: 600px; margin: 0 auto; background: linear-gradient(180deg, rgba(79, 140, 255, 0.15), rgba(0, 0, 0, 0.4)); border-radius: 10px; padding: 30px;">
                    <h1 style="color: #ef4444;">Withdrawal Rejected</h1>
                    <p>Unfortunately, your withdrawal request has been rejected.</p>
                    <p><strong>Amount:</strong> {amount} X</p>
                    ' . ('{reason}' ? '<p><strong>Reason:</strong> {reason}</p>' : '') . '
                    <p>Please contact support if you have any questions.</p>
                    <p><a href="{support_url}" style="display: inline-block; background: #4f8cff; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 10px;">Contact Support</a></p>
                </div>
            </body>
            </html>
        '
    ];
    
    $html = $templates[$template] ?? '';
    
    // Replace placeholders
    foreach ($data as $key => $value) {
        $html = str_replace('{' . $key . '}', htmlspecialchars($value), $html);
    }
    
    return $html;
}
