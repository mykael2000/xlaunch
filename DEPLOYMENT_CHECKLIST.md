# Deployment Checklist

Use this checklist to ensure proper deployment of the X Token Presale Platform.

## 📋 Pre-Deployment Checklist

### 1. Server Requirements ✓

- [ ] PHP 7.4 or higher installed
- [ ] MySQL 5.7 or higher / MariaDB 10.2+ installed
- [ ] Apache/Nginx web server configured
- [ ] PDO extension enabled
- [ ] mod_rewrite enabled (for Apache)
- [ ] SSL certificate installed (for HTTPS)
- [ ] Domain name configured

### 2. Database Setup ✓

- [ ] Create database: `xtoken_db`
- [ ] Import schema: `mysql -u root -p xtoken_db < database/schema.sql`
- [ ] Verify all tables created (9 tables)
- [ ] Check default admin account exists
- [ ] Verify default settings loaded

```bash
# Quick verification
mysql -u root -p xtoken_db -e "SHOW TABLES;"
mysql -u root -p xtoken_db -e "SELECT COUNT(*) FROM settings;"
```

### 3. Configuration Files ✓

- [ ] Update `includes/config.php`:
  - [ ] Set correct DB_HOST
  - [ ] Set correct DB_NAME
  - [ ] Set correct DB_USER
  - [ ] Set correct DB_PASS
  - [ ] Set correct SITE_URL (with HTTPS)

- [ ] Update `.htaccess` (if using Apache):
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 4. Security Configuration ✓

- [ ] Change default admin password
  - Login to admin panel
  - Go to profile settings
  - Change from `Admin@123` to strong password

- [ ] Update database user permissions:
```sql
CREATE USER 'xtoken_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON xtoken_db.* TO 'xtoken_user'@'localhost';
FLUSH PRIVILEGES;
```

- [ ] Set proper file permissions:
```bash
chmod 755 -R /path/to/xlaunch
chmod 644 includes/config.php
chmod 600 includes/config.php  # Even more secure
```

- [ ] Disable error display in production:
```php
// Add to includes/config.php
error_reporting(0);
ini_set('display_errors', 0);
```

### 5. Cryptocurrency Wallet Configuration ✓

⚠️ **CRITICAL:** Replace ALL default wallet addresses!

Login to Admin Panel → Settings and update:

- [ ] Bitcoin (BTC) wallet address
- [ ] Ethereum (ETH) wallet address
- [ ] USDT wallet address (supports ERC20/TRC20/BEP20)
- [ ] USDC wallet address (supports ERC20/BEP20)
- [ ] Dogecoin (DOGE) wallet address
- [ ] BNB wallet address
- [ ] TRON (TRX) wallet address
- [ ] Solana (SOL) wallet address
- [ ] XRP wallet address

**Verification:** Make a small test transaction to each wallet to verify addresses are correct.

### 6. Email Configuration ✓

Choose one method:

#### Option A: Using Gmail SMTP
- [ ] Create Gmail account or use existing
- [ ] Enable 2FA on Gmail account
- [ ] Generate App Password
- [ ] Update in Admin Panel → Settings:
  - SMTP Host: `smtp.gmail.com`
  - SMTP Port: `587`
  - SMTP Username: Your Gmail address
  - SMTP Password: App password (not regular password)
  - From Email: `noreply@yourdomain.com`
  - From Name: `X Token`

#### Option B: Using Other SMTP Provider
- [ ] Get SMTP credentials from provider
- [ ] Update settings accordingly
- [ ] Test email delivery

#### Testing Emails
```bash
# Register a test account
# Check if welcome email is received
# Make a test purchase
# Approve from admin and check notification email
```

### 7. Platform Settings ✓

Login to Admin Panel → Settings and configure:

- [ ] X Token Price (current stage price)
- [ ] Current Stage (1-5)
- [ ] Tokens Sold (for progress tracking)
- [ ] Total Tokens (per stage)
- [ ] Minimum Purchase (tokens)
- [ ] Maximum Purchase (tokens)
- [ ] Registration Enabled (yes/no)

### 8. Legal & Compliance ✓

- [ ] Review and update `privacy-policy.html`
- [ ] Add Terms of Service page
- [ ] Add Disclaimer page
- [ ] Ensure compliance with local cryptocurrency regulations
- [ ] Consider KYC/AML requirements

### 9. Testing ✓

#### User Flow Testing
- [ ] Register new account → Check welcome email
- [ ] Login with new account
- [ ] Purchase tokens (minimum amount)
- [ ] Verify payment page displays correct details
- [ ] Confirm payment
- [ ] Check transaction appears in dashboard as "pending"

#### Admin Flow Testing
- [ ] Login to admin panel
- [ ] View pending transaction
- [ ] Approve transaction
- [ ] Verify user received:
  - [ ] X token balance update
  - [ ] USD balance update
  - [ ] Tier upgrade (if applicable)
  - [ ] Approval email notification
- [ ] Test rejection workflow
- [ ] Test withdrawal approval
- [ ] Test support message reply
- [ ] Test bulk email feature
- [ ] Update settings and verify changes

#### API Testing
```bash
# Test public API endpoints
curl https://yoursite.com/api/get_token_price.php
curl https://yoursite.com/api/get_stats.php
curl https://yoursite.com/api/verify_transaction.php?order_id=ORD-XXXXX
```

### 10. Performance Optimization ✓

- [ ] Enable PHP OPcache:
```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

- [ ] Configure MySQL for performance:
```ini
; my.cnf
[mysqld]
innodb_buffer_pool_size = 256M
query_cache_size = 32M
```

- [ ] Enable Gzip compression:
```apache
# .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### 11. Backup Strategy ✓

- [ ] Set up automated database backups:
```bash
#!/bin/bash
# /usr/local/bin/backup_xtoken.sh
mysqldump -u root -p'password' xtoken_db > /backups/xtoken_$(date +%Y%m%d).sql
```

- [ ] Add to crontab:
```bash
0 2 * * * /usr/local/bin/backup_xtoken.sh
```

- [ ] Test backup restoration:
```bash
mysql -u root -p xtoken_db < /backups/xtoken_20260202.sql
```

### 12. Monitoring & Logging ✓

- [ ] Set up error logging:
```php
// includes/config.php
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/logs/php-errors.log');
```

- [ ] Monitor disk space
- [ ] Monitor database size
- [ ] Set up uptime monitoring (e.g., UptimeRobot)
- [ ] Monitor transaction failures

### 13. Security Hardening ✓

- [ ] Install SSL certificate (Let's Encrypt recommended)
- [ ] Force HTTPS on all pages
- [ ] Hide PHP version in headers:
```ini
; php.ini
expose_php = Off
```

- [ ] Protect sensitive files:
```apache
# .htaccess
<FilesMatch "^(config\.php|.*\.sql)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

- [ ] Disable directory listing:
```apache
Options -Indexes
```

- [ ] Set secure session cookies:
```php
// Already configured in includes/session.php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
```

### 14. Final Verification ✓

#### Functionality Checklist
- [ ] Users can register
- [ ] Users can login
- [ ] Users can purchase tokens
- [ ] Payment details are correct
- [ ] Admins can approve transactions
- [ ] Balances update correctly
- [ ] Tiers upgrade properly
- [ ] Emails are sent
- [ ] Users can request withdrawal
- [ ] Admins can process withdrawals
- [ ] Support chat works
- [ ] Settings can be updated
- [ ] API endpoints return data

#### Security Checklist
- [ ] SQL injection tested (use parameterized queries)
- [ ] XSS prevention tested (all output escaped)
- [ ] CSRF tokens working
- [ ] Session security configured
- [ ] Rate limiting on login
- [ ] Admin panel accessible only to admins
- [ ] User data isolated (users can't see others' data)

#### Performance Checklist
- [ ] Page load time < 2 seconds
- [ ] Database queries optimized
- [ ] Images optimized
- [ ] Caching enabled where possible

## 🚀 Go Live Process

### 1. Final Steps Before Launch
```bash
# 1. Make final database backup
mysqldump -u root -p xtoken_db > pre_launch_backup.sql

# 2. Verify all configurations
php -v  # Check PHP version
mysql --version  # Check MySQL version

# 3. Clear any test data (optional)
# mysql -u root -p xtoken_db -e "TRUNCATE TABLE transactions;"
# mysql -u root -p xtoken_db -e "DELETE FROM users WHERE email != 'admin@xtoken.com';"
```

### 2. Launch
- [ ] Remove maintenance mode (if any)
- [ ] Announce launch on social media
- [ ] Monitor first few transactions closely
- [ ] Be ready to provide support

### 3. Post-Launch Monitoring (First 24 Hours)
- [ ] Monitor server resources (CPU, RAM, disk)
- [ ] Check error logs regularly
- [ ] Verify email delivery
- [ ] Monitor transaction flow
- [ ] Check admin panel functionality
- [ ] Respond to support messages quickly

## 📞 Support & Troubleshooting

### Common Issues

**Issue: Emails not sending**
- Check SMTP credentials
- Verify firewall allows port 587/465
- Test with a different email provider
- Check spam folder

**Issue: Database connection failed**
- Verify MySQL is running: `systemctl status mysql`
- Check credentials in config.php
- Verify user permissions
- Check if database exists

**Issue: 500 Internal Server Error**
- Check error logs
- Verify file permissions
- Check .htaccess syntax
- Ensure all required PHP extensions installed

**Issue: Sessions not persisting**
- Check session directory is writable
- Verify session.save_path
- Check cookies are enabled

## ✅ Post-Deployment

After successful deployment:

- [ ] Document admin passwords securely
- [ ] Set up regular maintenance schedule
- [ ] Plan for scalability if needed
- [ ] Monitor user feedback
- [ ] Plan for updates and improvements

---

## 📝 Deployment Notes

**Date:** _______________  
**Deployed By:** _______________  
**Environment:** ☐ Development  ☐ Staging  ☐ Production  
**Issues Encountered:** _______________  
**Resolution:** _______________

---

**Version:** 1.0.0  
**Last Updated:** February 2, 2026

**Congratulations on deploying the X Token Presale Platform! 🎉**
