# X Token Admin Panel

Complete admin panel system for managing the X Token presale platform.

## Features

### 1. Admin Login (`login.php`)
- Secure admin authentication
- Username/password login
- Session management
- Auto-redirect if already logged in

### 2. Dashboard (`dashboard.php`)
- Overview statistics:
  - Total users
  - Pending transactions
  - Approved transactions
  - Total tokens sold
  - Unread support messages
- Recent transactions table (last 10)
- Quick action buttons

### 3. Transaction Management (`transactions.php`) ⭐ MOST IMPORTANT
- View all transactions with filters:
  - Filter by status (pending, approved, rejected)
  - Search by order ID, email, or name
  - Pagination support
- **Approve transactions:**
  - Updates transaction status to 'approved'
  - Credits X tokens to user balance
  - Updates user USD balance
  - Updates user tier/status based on contribution
  - Sends approval email to user
  - Logs admin activity
- **Reject transactions:**
  - Updates transaction status to 'rejected'
  - Allows admin to specify rejection reason
  - Sends rejection email with reason
  - Logs admin activity
- Transaction details display:
  - Order ID, user info, token amount
  - Payment details (crypto type, amount, network)
  - Transaction hash (truncated)
  - Status badge
  - Action buttons

### 4. User Management (`users.php`)
- List all users with pagination
- Search by email or name
- Filter by account status (active, suspended, inactive)
- User details modal showing:
  - Full name, email
  - X Token balance, USD balance
  - Status level (Basic, Silver, Gold, Platinum, VIP)
  - Contribution amount
  - Wallet address
  - Registration date
  - Recent transactions
- User actions:
  - Suspend/activate user accounts
  - Edit user wallet address
- AJAX-powered user details view

### 5. Withdrawal Management (`withdrawals.php`)
- List all withdrawal requests
- Filter by status (pending, completed, rejected)
- **Approve withdrawals:**
  - Validates user has sufficient balance
  - Deducts X tokens from user balance
  - Marks withdrawal as completed
  - Sends approval email
  - Logs activity
- **Reject withdrawals:**
  - Allows admin to add rejection notes
  - Sends rejection email with reason
  - Logs activity
- Display wallet addresses and amounts

### 6. Settings (`settings.php`)
- **Token Settings:**
  - X Token price (USD)
  - Current presale stage
  - Tokens sold count
  - Total tokens for stage
  - Min/max purchase limits
- **Wallet Addresses:**
  - BTC, ETH, USDT, USDC
  - DOGE, BNB, TRX, SOL, XRP
- **System Settings:**
  - Enable/disable registration
- All changes logged with admin activity

### 7. Support Messages (`support.php`)
- View all support conversations grouped by user
- Conversation list showing:
  - User name and email
  - Unread message count
  - Last message timestamp
- Message thread view
- Reply to user messages
- Auto-mark messages as read
- Real-time conversation interface

### 8. Email Users (`email_users.php`)
- Send bulk emails to users
- Recipient options:
  - All users
  - Active users only
  - Suspended users only
- Displays user counts for each category
- Custom subject and message
- HTML email template with branding
- Confirmation before sending
- Success/failure reporting

## Security Features

- ✅ CSRF protection on all forms
- ✅ Admin session validation
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (htmlspecialchars)
- ✅ Activity logging for all admin actions
- ✅ Secure password verification
- ✅ Session regeneration on login

## Default Admin Credentials

**Username:** admin  
**Password:** Admin@123

⚠️ **IMPORTANT:** Change the default password immediately after first login!

## File Structure

```
admin/
├── ajax/
│   └── get_user_details.php    # AJAX endpoint for user details
├── includes/
│   ├── admin_auth.php          # Admin authentication helper
│   ├── header.php              # Admin panel header/nav
│   └── footer.php              # Admin panel footer
├── dashboard.php               # Main dashboard
├── transactions.php            # Transaction management ⭐
├── users.php                   # User management
├── withdrawals.php             # Withdrawal management
├── settings.php                # System settings
├── support.php                 # Support messages
├── email_users.php             # Bulk email tool
├── login.php                   # Admin login
├── logout.php                  # Admin logout
└── index.php                   # Redirects to login
```

## Usage Workflow

### Typical Transaction Approval Workflow:

1. User makes purchase from user dashboard
2. Transaction appears in Admin > Transactions as "pending"
3. Admin reviews transaction details:
   - Verify payment information
   - Check crypto transaction hash
   - Confirm amounts match
4. Admin clicks "Approve" button
5. System automatically:
   - Credits tokens to user account
   - Updates user tier based on contribution
   - Sends approval email
   - Logs the action
6. User receives email and can see tokens in their dashboard

### User Management Workflow:

1. View user list with search/filter
2. Click "View" to see user details modal
3. Review user information and transaction history
4. Take actions as needed:
   - Suspend problematic users
   - Activate suspended users
   - Update wallet addresses

## Important Functions Used

From `includes/functions.php`:
- `getUserBalance($userId)` - Get user's current balance
- `updateUserBalance($userId, $amount, $usd)` - Update user balance
- `updateUserStatus($userId, $amount)` - Update user tier
- `getSetting($key, $default)` - Get system setting
- `updateSetting($key, $value)` - Update system setting
- `logActivity($action, $details, $adminId, $userId)` - Log admin actions

From `includes/auth.php`:
- `loginAdmin($username, $password)` - Admin login
- `isAdminLoggedIn()` - Check admin session
- `getAdminId()` - Get current admin ID
- `requireAdminLogin()` - Require admin auth (redirect if not)

From `includes/mailer.php`:
- `sendTransactionApprovedEmail($email, $transaction)` - Send approval email
- `sendTransactionRejectedEmail($email, $transaction, $reason)` - Send rejection email
- `sendWithdrawalApprovedEmail($email, $withdrawal)` - Send withdrawal approval
- `sendWithdrawalRejectedEmail($email, $withdrawal, $reason)` - Send withdrawal rejection

## Styling

The admin panel uses a dark theme consistent with the user-facing pages:
- Dark blue gradient background
- Glassmorphism cards with backdrop blur
- Blue accent color (#4f8cff)
- Responsive grid layouts
- Status badges with color coding:
  - Pending: Orange
  - Approved/Completed: Green
  - Rejected: Red
  - Active: Green
  - Suspended: Red

## Tips

1. **Transaction Management** is the most critical function - focus on quick approval workflows
2. Always review transaction details before approving
3. Use the search and filter features to find specific transactions/users quickly
4. Monitor the dashboard for pending transactions that need attention
5. Regularly check support messages for user inquiries
6. Update wallet addresses in settings when needed
7. Be cautious with bulk emails - confirm recipient count before sending

## Database Tables Used

- `admins` - Admin accounts
- `users` - User accounts
- `transactions` - Purchase transactions
- `withdrawals` - Withdrawal requests
- `balances` - User token balances
- `user_status` - User tier/contribution info
- `support_messages` - Support conversations
- `settings` - System configuration
- `activity_log` - Admin action logging

## Future Enhancements

Potential additions for v2:
- Role-based access control (multiple admin roles)
- Advanced reporting and analytics
- Export data to CSV/Excel
- Transaction search by date range
- Batch transaction approval
- Email templates editor
- Two-factor authentication for admins
- Activity log viewer page
