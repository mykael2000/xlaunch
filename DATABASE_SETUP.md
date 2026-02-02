# Database Setup Guide

This guide provides detailed instructions for setting up the X Token presale platform database.

## 📋 Prerequisites

- MySQL 5.7 or higher / MariaDB 10.2 or higher
- Database user with CREATE, INSERT, UPDATE, DELETE, SELECT privileges
- Command-line access or phpMyAdmin

## 🚀 Quick Setup

### Method 1: Command Line (Recommended)

```bash
# 1. Create the database
mysql -u root -p -e "CREATE DATABASE xtoken_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Import the schema
mysql -u root -p xtoken_db < database/schema.sql

# 3. Verify tables created
mysql -u root -p xtoken_db -e "SHOW TABLES;"
```

### Method 2: phpMyAdmin

1. Login to phpMyAdmin
2. Click "New" to create a new database
3. Name: `xtoken_db`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"
6. Select the `xtoken_db` database
7. Click "Import" tab
8. Choose file: `database/schema.sql`
9. Click "Go"

## 📊 Database Schema Overview

### Tables Created

1. **users** - User accounts
2. **balances** - User token balances
3. **transactions** - Purchase and withdrawal transactions
4. **settings** - System configuration
5. **user_status** - User tier levels
6. **withdrawals** - Withdrawal requests
7. **support_messages** - Live chat messages
8. **admins** - Admin accounts
9. **activity_log** - Admin action audit trail

## 🔐 Default Data

The schema automatically creates:

### Default Admin Account
- **Username:** `admin`
- **Password:** `Admin@123` (hashed)
- **Email:** `admin@xtoken.com`

**⚠️ IMPORTANT:** Change this password immediately after installation!

### Default Settings

| Setting Key | Default Value | Description |
|------------|---------------|-------------|
| x_token_price | 5.44 | Current token price in USD |
| current_stage | 3 | Presale stage number |
| tokens_sold | 5317977 | Tokens sold count |
| total_tokens | 6475000 | Total tokens in current stage |
| btc_wallet | bc1q... | Bitcoin wallet address |
| eth_wallet | 0x... | Ethereum wallet address |
| usdt_wallet | 0x... | USDT wallet address |
| usdc_wallet | 0x... | USDC wallet address |
| doge_wallet | DH5... | Dogecoin wallet address |
| bnb_wallet | 0x... | BNB wallet address |
| trx_wallet | TJR... | TRON wallet address |
| sol_wallet | 7xK... | Solana wallet address |
| xrp_wallet | rEb... | XRP wallet address |
| min_purchase | 50 | Minimum tokens per purchase |
| max_purchase | 1000000 | Maximum tokens per purchase |
| registration_enabled | 1 | Enable user registration |

## 🔧 Post-Installation Configuration

### 1. Update Wallet Addresses

**IMPORTANT:** Replace default wallet addresses with your actual cryptocurrency wallets!

```sql
USE xtoken_db;

-- Update Bitcoin wallet
UPDATE settings SET value = 'YOUR_BTC_WALLET_ADDRESS' WHERE `key` = 'btc_wallet';

-- Update Ethereum wallet
UPDATE settings SET value = 'YOUR_ETH_WALLET_ADDRESS' WHERE `key` = 'eth_wallet';

-- Update USDT wallet (ERC20/TRC20/BEP20)
UPDATE settings SET value = 'YOUR_USDT_WALLET_ADDRESS' WHERE `key` = 'usdt_wallet';

-- Update other wallets...
```

### 2. Configure SMTP for Emails

```sql
-- Gmail example
UPDATE settings SET value = 'smtp.gmail.com' WHERE `key` = 'smtp_host';
UPDATE settings SET value = '587' WHERE `key` = 'smtp_port';
UPDATE settings SET value = 'your-email@gmail.com' WHERE `key` = 'smtp_username';
UPDATE settings SET value = 'your-app-password' WHERE `key` = 'smtp_password';
UPDATE settings SET value = 'noreply@yourdomain.com' WHERE `key` = 'smtp_from_email';
UPDATE settings SET value = 'X Token' WHERE `key` = 'smtp_from_name';
```

### 3. Create Additional Admin Accounts

```sql
-- Replace with actual details
INSERT INTO admins (username, password, email) VALUES 
('newadmin', '$2y$10$GENERATE_NEW_HASH', 'newadmin@example.com');
```

To generate password hash:
```php
<?php
echo password_hash('YourSecurePassword', PASSWORD_BCRYPT);
?>
```

### 4. Adjust Token Pricing

```sql
-- Update current token price
UPDATE settings SET value = '5.44' WHERE `key` = 'x_token_price';

-- Update presale stage
UPDATE settings SET value = '3' WHERE `key` = 'current_stage';
```

## 🔍 Verify Installation

Run these queries to verify everything is set up correctly:

```sql
USE xtoken_db;

-- Check tables exist
SHOW TABLES;

-- Verify admin account
SELECT id, username, email, created_at FROM admins;

-- Check settings loaded
SELECT `key`, value FROM settings WHERE `key` IN ('x_token_price', 'current_stage');

-- Verify no users yet
SELECT COUNT(*) as user_count FROM users;

-- Check table structures
DESCRIBE users;
DESCRIBE transactions;
DESCRIBE balances;
```

Expected output:
- 9 tables created
- 1 admin account
- 20+ settings entries
- 0 users initially

## 🔄 Database Maintenance

### Regular Backups

**Daily Backup Script:**
```bash
#!/bin/bash
BACKUP_DIR="/path/to/backups"
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u root -p xtoken_db > "$BACKUP_DIR/xtoken_backup_$DATE.sql"
# Keep only last 30 days
find $BACKUP_DIR -name "xtoken_backup_*.sql" -mtime +30 -delete
```

**Automated Backup (cron):**
```bash
# Run daily at 2 AM
0 2 * * * /path/to/backup_script.sh
```

### Database Optimization

Run monthly:
```sql
-- Optimize all tables
OPTIMIZE TABLE users, balances, transactions, settings, 
                user_status, withdrawals, support_messages, 
                admins, activity_log;

-- Analyze tables for better query performance
ANALYZE TABLE users, transactions, balances;
```

### Clean Old Data

```sql
-- Delete old activity logs (older than 90 days)
DELETE FROM activity_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Delete read support messages (older than 30 days)
DELETE FROM support_messages 
WHERE is_read = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

## 🔒 Security Best Practices

### 1. Database User Permissions

Create a dedicated database user (not root):

```sql
-- Create user
CREATE USER 'xtoken_user'@'localhost' IDENTIFIED BY 'strong_password_here';

-- Grant only necessary privileges
GRANT SELECT, INSERT, UPDATE, DELETE ON xtoken_db.* TO 'xtoken_user'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;
```

Update `includes/config.php` with new credentials.

### 2. Disable Remote Access (if not needed)

In `my.cnf` or `my.ini`:
```ini
[mysqld]
bind-address = 127.0.0.1
```

### 3. Regular Security Audits

```sql
-- Check for suspicious admin logins
SELECT * FROM admins WHERE last_login > DATE_SUB(NOW(), INTERVAL 24 HOUR);

-- Monitor failed transactions
SELECT COUNT(*) FROM transactions WHERE status = 'rejected' 
GROUP BY user_id HAVING COUNT(*) > 5;

-- Check unusual balances
SELECT u.email, b.x_token_balance 
FROM users u JOIN balances b ON u.id = b.user_id 
WHERE b.x_token_balance > 100000;
```

## 📈 Performance Tuning

### Add Indexes for Faster Queries

```sql
-- If experiencing slow queries, add these indexes:

-- Transaction lookups
CREATE INDEX idx_transactions_created_at ON transactions(created_at);
CREATE INDEX idx_transactions_user_status ON transactions(user_id, status);

-- Support message queries
CREATE INDEX idx_support_user_created ON support_messages(user_id, created_at);

-- Activity log queries
CREATE INDEX idx_activity_created_admin ON activity_log(created_at, admin_id);
```

### MySQL Configuration

Add to `my.cnf`:
```ini
[mysqld]
# Connection settings
max_connections = 200
connect_timeout = 10

# Buffer pool (adjust based on your RAM)
innodb_buffer_pool_size = 256M

# Query cache (for MySQL 5.7)
query_cache_type = 1
query_cache_size = 32M

# Logging
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2
```

## 🐛 Troubleshooting

### Issue: Foreign Key Constraint Fails

```sql
-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS=0;

-- Run your query
-- ...

-- Re-enable checks
SET FOREIGN_KEY_CHECKS=1;
```

### Issue: Character Encoding Problems

```sql
-- Check database charset
SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME
FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = 'xtoken_db';

-- Fix if needed
ALTER DATABASE xtoken_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Issue: Table Doesn't Exist

```sql
-- Check which tables exist
SHOW TABLES FROM xtoken_db;

-- Re-import schema if needed
SOURCE /path/to/database/schema.sql;
```

### Issue: Cannot Connect from PHP

Check:
1. MySQL service running: `systemctl status mysql`
2. Credentials in `includes/config.php` are correct
3. User has proper permissions
4. PDO extension enabled: `php -m | grep pdo`

## 🔄 Migration & Upgrades

### Adding New Columns

```sql
-- Example: Add phone number to users
ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email;

-- Add index if needed
CREATE INDEX idx_users_phone ON users(phone);
```

### Adding New Settings

```sql
INSERT INTO settings (`key`, `value`, description) VALUES
('new_setting', 'default_value', 'Description of setting');
```

## 📊 Useful Queries

### User Statistics
```sql
SELECT 
    COUNT(*) as total_users,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_users,
    COUNT(CASE WHEN created_at > DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as new_users_7days
FROM users;
```

### Transaction Statistics
```sql
SELECT 
    status,
    COUNT(*) as count,
    SUM(amount) as total_tokens,
    SUM(usd_amount) as total_usd
FROM transactions
GROUP BY status;
```

### Top Users by Balance
```sql
SELECT u.email, u.fullname, b.x_token_balance, b.usd_balance
FROM users u
JOIN balances b ON u.id = b.user_id
ORDER BY b.x_token_balance DESC
LIMIT 10;
```

---

**Need Help?**

If you encounter issues during database setup:
1. Check MySQL error logs
2. Verify MySQL version compatibility
3. Ensure proper user permissions
4. Contact support with error messages

**Version:** 1.0.0  
**Last Updated:** February 2, 2026
