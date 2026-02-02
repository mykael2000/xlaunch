# X Token Presale Platform

A complete dynamic PHP-based cryptocurrency presale platform with admin panel, user dashboard, payment processing, and comprehensive management features.

## 🌟 Features

### User Features
- **User Registration & Authentication**
  - Secure registration with email validation
  - Login with rate limiting and lockout protection
  - Session management with CSRF protection
  - Password change functionality

- **Dashboard**
  - Real-time X token balance display
  - USD equivalent calculation
  - User tier/status tracking (Basic, Silver, Gold, Platinum, VIP)
  - Token sales progress visualization
  - Transaction history

- **Token Purchase**
  - Support for multiple cryptocurrencies (BTC, ETH, USDT, USDC, BNB, TRX, SOL, XRP, DOGE)
  - Network selection for multi-network tokens
  - Real-time price calculation
  - Payment confirmation workflow
  - Email notifications

- **User Profile**
  - Edit personal information
  - Update wallet address
  - Change password
  - View account statistics

- **Withdrawal System**
  - Request X token withdrawals
  - Track withdrawal status
  - Email notifications on approval/rejection

- **Live Support Chat**
  - Real-time messaging with admin
  - Message history
  - Unread message notifications

### Admin Features
- **Dashboard**
  - Platform statistics overview
  - Recent transactions monitoring
  - User growth tracking
  - Support message alerts

- **Transaction Management**
  - Approve/reject purchase transactions
  - Automatic balance updates on approval
  - User tier upgrades
  - Email notifications to users

- **User Management**
  - View all users with search and filtering
  - User details modal with full information
  - Suspend/activate user accounts
  - Edit user wallet addresses

- **Withdrawal Management**
  - Review withdrawal requests
  - Approve/reject with balance refund on rejection
  - Add admin notes
  - Process bulk withdrawals

- **System Settings**
  - Configure X token price per stage
  - Update cryptocurrency wallet addresses
  - Set min/max purchase limits
  - Toggle registration on/off
  - Manage presale stages

- **Support Management**
  - View all support conversations
  - Reply to user messages
  - Mark conversations as read
  - Quick user information access

- **Bulk Email**
  - Send announcements to all users
  - HTML email support
  - Preview before sending

### API Endpoints
- `GET /api/get_token_price.php` - Current token price
- `GET /api/get_stats.php` - Public platform statistics
- `GET /api/verify_transaction.php?order_id=XXX` - Verify transaction status

## 🚀 Installation

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PDO extension enabled
- mod_rewrite enabled (for clean URLs)

### Setup Steps

1. **Clone the repository**
   ```bash
   # Replace with your repository URL
   git clone https://github.com/yourusername/xlaunch.git
   cd xlaunch
   ```

2. **Create database**
   ```bash
   mysql -u root -p
   CREATE DATABASE xtoken_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   EXIT;
   ```

3. **Import database schema**
   ```bash
   mysql -u root -p xtoken_db < database/schema.sql
   ```

4. **Configure database connection**
   
   Edit `includes/config.php` or set environment variables:
   ```php
   DB_HOST=localhost
   DB_NAME=xtoken_db
   DB_USER=root
   DB_PASS=your_password
   SITE_URL=http://yoursite.com
   ```

5. **Set file permissions**
   ```bash
   chmod 755 -R .
   chmod 777 uploads/ # Create if doesn't exist
   ```

6. **Access the platform**
   - User Frontend: `http://yoursite.com/`
   - User Login: `http://yoursite.com/login.php`
   - Admin Panel: `http://yoursite.com/admin/`

## 🔐 Default Credentials

### Admin Access
- **Username:** admin
- **Password:** Admin@123

⚠️ **IMPORTANT:** Change the default admin password immediately after first login!

To create additional admin accounts:
```sql
INSERT INTO admins (username, password, email) VALUES 
('newadmin', '$2y$10$YPKmJ0yLJE3qO8GyFqNqAO8x8K0L9MZ0Z8F9vN5C7J6A5F8R4U2Vm', 'admin@example.com');
-- Password is: Admin@123 (change this hash using password_hash() in PHP)
```

## 📁 File Structure

```
xlaunch/
├── admin/                      # Admin panel
│   ├── includes/               # Admin-specific includes
│   ├── ajax/                   # Admin AJAX endpoints
│   ├── dashboard.php           # Admin dashboard
│   ├── transactions.php        # Transaction management
│   ├── users.php               # User management
│   ├── withdrawals.php         # Withdrawal management
│   ├── settings.php            # System settings
│   ├── support.php             # Support messages
│   ├── email_users.php         # Bulk email
│   └── login.php               # Admin login
│
├── api/                        # Public API endpoints
│   ├── get_token_price.php
│   ├── get_stats.php
│   └── verify_transaction.php
│
├── ajax/                       # User AJAX endpoints
│   ├── support.php             # Live chat
│   ├── get_balance.php         # Balance updates
│   ├── check_transaction.php   # Transaction status
│   └── get_exchange_rate.php   # Current rates
│
├── database/                   # Database files
│   └── schema.sql              # Database structure
│
├── includes/                   # Core PHP files
│   ├── config.php              # Database configuration
│   ├── session.php             # Session management
│   ├── auth.php                # Authentication functions
│   ├── functions.php           # Helper functions
│   └── mailer.php              # Email functions
│
├── user/                       # User dashboard
│   ├── dashboard.php           # User dashboard
│   ├── buy.php                 # Purchase tokens
│   ├── payment.php             # Payment confirmation
│   ├── my-x-token.php          # Token balance
│   ├── profile.php             # User profile
│   ├── status.php              # User tier status
│   ├── withdraw.php            # Withdrawal requests
│   └── how-to-buy.php          # Purchase guide
│
├── assets/                     # Static assets (CSS, JS, images)
├── login.php                   # User login
├── register.php                # User registration
├── logout.php                  # User logout
└── index.html                  # Landing page
```

## 🔧 Configuration

### Email Settings

Configure SMTP settings in the database `settings` table or through admin panel:

```sql
UPDATE settings SET value = 'smtp.gmail.com' WHERE `key` = 'smtp_host';
UPDATE settings SET value = '587' WHERE `key` = 'smtp_port';
UPDATE settings SET value = 'your-email@gmail.com' WHERE `key` = 'smtp_username';
UPDATE settings SET value = 'your-app-password' WHERE `key` = 'smtp_password';
UPDATE settings SET value = 'noreply@xtoken.com' WHERE `key` = 'smtp_from_email';
```

**Note:** For Gmail, you need to use an App Password, not your regular password.

### Cryptocurrency Wallet Addresses

Update wallet addresses through Admin Panel > Settings or directly in database:

```sql
UPDATE settings SET value = 'your-btc-address' WHERE `key` = 'btc_wallet';
UPDATE settings SET value = 'your-eth-address' WHERE `key` = 'eth_wallet';
-- etc.
```

### Token Price & Stage Management

Configure through Admin Panel > Settings:
- Current Stage (1-5)
- Token Price (USD per token)
- Tokens Sold (for progress tracking)
- Total Tokens (per stage)

## 🔒 Security Features

- **SQL Injection Prevention:** All queries use PDO prepared statements
- **XSS Protection:** All output escaped with htmlspecialchars()
- **CSRF Protection:** Token validation on all forms
- **Password Security:** Bcrypt hashing with cost factor 10
- **Session Security:** Secure cookies, HTTP-only, same-site strict
- **Rate Limiting:** Login attempts limited to prevent brute force
- **Input Validation:** Comprehensive validation on all user inputs
- **Activity Logging:** All admin actions logged for audit

## 🎨 Customization

### Changing Colors

Edit the CSS in each page or create a centralized stylesheet:
- Primary Blue: `#4f8cff`
- Dark Background: `#0b0f1a`
- Card Background: `rgba(79, 140, 255, 0.15)`

### Adding New Cryptocurrencies

1. Add wallet address in settings table
2. Update `buy.php` coin list
3. Add network configuration if needed
4. Update `getWalletAddress()` function in `includes/functions.php`

## 🧪 Testing

### Test User Flow
1. Register new account
2. Login
3. Purchase tokens (creates pending transaction)
4. Admin approves transaction
5. Check balance updated
6. Request withdrawal
7. Admin processes withdrawal

### Test Admin Flow
1. Login to admin panel
2. Review pending transactions
3. Approve/reject transactions
4. Manage users
5. Update settings
6. Reply to support messages

## 📊 Database Backup

Regular backups are crucial:

```bash
# Backup
mysqldump -u root -p xtoken_db > backup_$(date +%Y%m%d).sql

# Restore
mysql -u root -p xtoken_db < backup_20260202.sql
```

## 🐛 Troubleshooting

### Common Issues

**Issue:** Cannot connect to database
- Check credentials in `includes/config.php`
- Verify MySQL service is running
- Check database exists

**Issue:** Emails not sending
- Verify SMTP settings
- Check PHP mail() function is enabled
- For production, use PHPMailer (install via Composer)

**Issue:** Sessions not working
- Check PHP session directory is writable
- Verify session.save_path in php.ini
- Check cookies are enabled in browser

**Issue:** Admin cannot login
- Verify admin account exists in database
- Reset password using SQL:
  ```sql
  UPDATE admins SET password = '$2y$10$YPKmJ0yLJE3qO8GyFqNqAO8x8K0L9MZ0Z8F9vN5C7J6A5F8R4U2Vm' WHERE username = 'admin';
  ```

## 🚀 Production Deployment

### Pre-deployment Checklist

- [ ] Change default admin password
- [ ] Update database credentials
- [ ] Configure real SMTP settings
- [ ] Update cryptocurrency wallet addresses
- [ ] Set correct SITE_URL
- [ ] Enable HTTPS
- [ ] Set proper file permissions (no 777 in production)
- [ ] Configure error logging (don't display errors)
- [ ] Set up automated database backups
- [ ] Test all email notifications
- [ ] Test payment workflow end-to-end
- [ ] Review and update privacy policy

### Performance Optimization

```php
// php.ini recommended settings
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
session.gc_maxlifetime = 86400
```

### Security Hardening

1. **Disable directory listing**
   ```apache
   Options -Indexes
   ```

2. **Protect sensitive files**
   ```apache
   <Files "config.php">
       Order allow,deny
       Deny from all
   </Files>
   ```

3. **Force HTTPS**
   ```apache
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

## 📝 License

This project is proprietary software. All rights reserved.

## 🤝 Support

For support, please use the built-in live chat system or contact the development team.

## 🎯 Roadmap

Future enhancements:
- [ ] Two-factor authentication (2FA)
- [ ] Referral system with rewards
- [ ] Advanced analytics dashboard
- [ ] PDF receipt generation
- [ ] Multi-language support
- [ ] Mobile app API
- [ ] KYC/AML verification integration
- [ ] Automated cryptocurrency price fetching
- [ ] Smart contract integration

## 📚 Additional Resources

- [PHP PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
- [MySQL Security Best Practices](https://dev.mysql.com/doc/mysql-security-excerpt/8.0/en/)
- [OWASP Security Guidelines](https://owasp.org/)

---

**Version:** 1.0.0  
**Last Updated:** February 2, 2026  
**Developed by:** X Token Development Team
