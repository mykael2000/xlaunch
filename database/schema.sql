-- X Token Presale Platform Database Schema

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    wallet_address VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Balances table
CREATE TABLE IF NOT EXISTS balances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    x_token_balance DECIMAL(20, 8) DEFAULT 0.00000000,
    usd_balance DECIMAL(20, 2) DEFAULT 0.00,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('buy', 'withdraw') NOT NULL,
    amount DECIMAL(20, 8) NOT NULL,
    crypto_type VARCHAR(20) DEFAULT NULL,
    crypto_amount DECIMAL(20, 8) DEFAULT NULL,
    wallet_address VARCHAR(255) DEFAULT NULL,
    tx_hash VARCHAR(255) DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    admin_verified TINYINT(1) DEFAULT 0,
    order_id VARCHAR(50) UNIQUE,
    network VARCHAR(50) DEFAULT NULL,
    usd_amount DECIMAL(20, 2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_type (type),
    INDEX idx_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) NOT NULL UNIQUE,
    `value` TEXT NOT NULL,
    description VARCHAR(500) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User status table
CREATE TABLE IF NOT EXISTS user_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status_level ENUM('Basic', 'Silver', 'Gold', 'Platinum', 'VIP') DEFAULT 'Basic',
    contribution_amount DECIMAL(20, 2) DEFAULT 0.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Withdrawals table
CREATE TABLE IF NOT EXISTS withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(20, 8) NOT NULL,
    wallet_address VARCHAR(255) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL DEFAULT NULL,
    admin_notes TEXT DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Support messages table
CREATE TABLE IF NOT EXISTS support_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    sender ENUM('user', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_sender (sender),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity log table
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT DEFAULT NULL,
    user_id INT DEFAULT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_admin_id (admin_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO settings (`key`, `value`, description) VALUES
('x_token_price', '5.44', 'Current X token price in USD'),
('current_stage', '3', 'Current presale stage'),
('tokens_sold', '5317977', 'Total tokens sold'),
('total_tokens', '6475000', 'Total tokens available in current stage'),
('btc_wallet', 'bc1qnx7lf5psmg5kn9j4vxwcfyflgqujnj9fnt28mc', 'Bitcoin wallet address'),
('eth_wallet', '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb', 'Ethereum wallet address'),
('usdt_wallet', '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb', 'USDT wallet address'),
('usdc_wallet', '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb', 'USDC wallet address'),
('doge_wallet', 'DH5yaieqoZN36fDVciNyRueRGvGLR3mr7L', 'Dogecoin wallet address'),
('bnb_wallet', '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb', 'BNB wallet address'),
('trx_wallet', 'TJRyWwFs9wTFGZg3JbrVriFbNfCug5tDeC', 'TRON wallet address'),
('sol_wallet', '7xKXtg2CW87d97TXJSDpbD5jBkheTqA83TZRuJosgAsU', 'Solana wallet address'),
('xrp_wallet', 'rEb8TK3gBgk5auZkwc6sHnwrGVJH8DuaLh', 'XRP wallet address'),
('min_purchase', '50', 'Minimum X tokens purchase'),
('max_purchase', '1000000', 'Maximum X tokens purchase'),
('registration_enabled', '1', 'Enable/disable registration'),
('smtp_host', 'smtp.gmail.com', 'SMTP server host'),
('smtp_port', '587', 'SMTP server port'),
('smtp_username', '', 'SMTP username'),
('smtp_password', '', 'SMTP password'),
('smtp_from_email', 'noreply@xtoken.com', 'From email address'),
('smtp_from_name', 'X Token', 'From name')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- Insert default admin (username: admin, password: Admin@123)
INSERT INTO admins (username, password, email) VALUES
('admin', '$2y$10$YPKmJ0yLJE3qO8GyFqNqAO8x8K0L9MZ0Z8F9vN5C7J6A5F8R4U2Vm', 'admin@xtoken.com')
ON DUPLICATE KEY UPDATE password = VALUES(password);
