# Tribal Crafts - Complete PHP E-Commerce Website

## Overview
A complete PHP-based marketplace connecting tribal artisans with buyers. Features include artisan registration, product management, shopping cart, wishlist, checkout, and multiple payment options (Razorpay for India, PayPal for international).

## Features

### For Buyers
- **Product Catalog** - Browse bamboo products by category
- **Shopping Cart** - Add/remove products, update quantities
- **Wishlist** - Save products for later
- **Checkout** - Secure checkout with shipping details
- **Multiple Payment Options**:
  - Razorpay (UPI, Cards, Netbanking, Wallets) - India
  - PayPal (International payments)
  - Cash on Delivery
- **Order Tracking** - View order status and history

### For Artisans
- **Registration** - Apply to join the platform
- **Product Submission** - Submit products for admin approval
- **Dashboard** - Track submitted products and their status
- **Direct Contact** - Buyers can call or email artisans directly

### For Admins
- **Dashboard** - Overview of artisans, products, orders, revenue
- **Artisan Management** - Add/edit/delete artisans, approve registrations
- **Product Management** - Add/edit/delete products, approve submissions
- **Order Management** - View orders, update status, track payments
- **Message Management** - View contact form submissions

## File Structure
```
tribal-crafts/
├── index.php                    # Home page
├── artisans.php                 # Artisans directory
├── products.php                 # Product catalog with cart/wishlist
├── about.php                    # About page
├── contact.php                  # Contact form
├── cart.php                     # Shopping cart
├── wishlist.php                 # Wishlist
├── checkout.php                 # Checkout page
├── payment_razorpay.php         # Razorpay payment
├── payment_paypal.php           # PayPal payment
├── order_success.php            # Order confirmation
├── artisan_register.php         # Artisan registration
├── artisan_dashboard.php        # Artisan product submission
├── setup.php                    # Database setup (delete after use)
├── reset_admin.php              # Admin password reset (delete after use)
├── database.sql                 # Complete database schema
├── .htaccess                    # Apache configuration
├── README.md                    # This file
│
├── css/
│   └── style.css                # Stylesheet
├── js/
│   └── main.js                  # JavaScript
│
├── config/
│   ├── database.php             # Database configuration
│   └── db_connect.php           # Database connection class
│
├── includes/
│   ├── header.php               # Common header with cart/wishlist icons
│   ├── footer.php               # Common footer
│   └── functions.php            # Helper functions
│
└── admin/
    ├── login.php                # Admin login
    ├── logout.php               # Admin logout
    ├── dashboard.php            # Admin dashboard with stats
    ├── artisans.php             # Manage artisans
    ├── artisan_add.php          # Add artisan
    ├── artisan_edit.php         # Edit artisan
    ├── artisan_requests.php     # Approve artisan registrations
    ├── products.php             # Manage products
    ├── product_add.php          # Add product
    ├── product_edit.php         # Edit product
    ├── product_submissions.php  # Approve artisan product submissions
    ├── orders.php               # Manage orders
    └── messages.php             # View contact messages
```

## Setup Instructions

### 1. Requirements
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache with mod_rewrite enabled
- XAMPP/WAMP/MAMP (for local development)

### 2. Database Setup

**Option A: Using setup.php (Easiest)**
1. Upload all files to your web server
2. Edit `config/database.php` with your database credentials
3. Visit `http://yourdomain.com/setup.php` in browser
4. The setup will create all tables including e-commerce tables
5. DELETE `setup.php` after setup for security

**Option B: Manual SQL Import**
1. Create a MySQL database named `tribal_crafts`
2. Import `database.sql` using phpMyAdmin or MySQL CLI:
   ```bash
   mysql -u root -p tribal_crafts < database.sql
   ```
3. Edit `config/database.php` with your credentials

### 3. Configuration

Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'tribal_crafts');
define('DB_USER', 'root');        // Your MySQL username
define('DB_PASS', '');            // Your MySQL password
```

### 4. Payment Gateway Setup

**Razorpay (India)**
1. Sign up at [razorpay.com](https://razorpay.com)
2. Get API keys from Dashboard > Settings > API Keys
3. Update `payment_razorpay.php` with your keys:
   ```php
   $razorpayKeyId = 'rzp_live_XXXXXXXXXXXXXXX';
   $razorpayKeySecret = 'YOUR_LIVE_SECRET';
   ```

**PayPal (International)**
1. Create PayPal Business account at [paypal.com](https://www.paypal.com/business)
2. Go to Developer Dashboard and create an app
3. Get Client ID and Secret
4. Update `payment_paypal.php` with your credentials

### 5. Default Admin Login
- **Username:** `admin`
- **Password:** `admin123`

**IMPORTANT:** Change the password after first login using `reset_admin.php`

### 6. Access the Website
- **Frontend:** `http://yourdomain.com/`
- **Admin Panel:** `http://yourdomain.com/admin/login.php`
- **Artisan Registration:** `http://yourdomain.com/artisan_register.php`
- **Artisan Dashboard:** `http://yourdomain.com/artisan_dashboard.php`

## How It Works

### For Artisans
1. **Register** - Fill out the registration form at `/artisan_register.php`
2. **Wait for Approval** - Admin reviews and approves the application
3. **Access Dashboard** - Login with registered email at `/artisan_dashboard.php`
4. **Submit Products** - Add product details and submit for review
5. **Admin Approval** - Admin reviews and approves products
6. **Go Live** - Approved products appear on the marketplace

### For Buyers
1. **Browse Products** - View products at `/products.php`
2. **Add to Cart/Wishlist** - Click buttons on product cards
3. **Checkout** - Go to `/checkout.php` and enter shipping details
4. **Payment** - Choose Razorpay, PayPal, or COD
5. **Order Confirmation** - Receive order number and tracking

### For Admins
1. **Dashboard** - View stats: artisans, products, orders, revenue
2. **Approve Artisans** - Review registration requests
3. **Approve Products** - Review artisan product submissions
4. **Manage Orders** - Update order status (pending, confirmed, shipped, delivered)
5. **View Messages** - Read contact form submissions

## Database Tables

### Core Tables
- `admins` - Admin users
- `artisans` - Registered artisans
- `products` - Approved products
- `contact_messages` - Contact form submissions

### E-Commerce Tables
- `cart` - Shopping cart items
- `wishlist` - Saved products
- `orders` - Customer orders
- `order_items` - Order line items
- `payment_methods` - Available payment options

### Artisan Management Tables
- `artisan_requests` - Registration requests (pending/approved/rejected)
- `product_submissions` - Product submissions for approval

## Security Notes

1. **Delete setup.php and reset_admin.php** after setup
2. **Change default admin password** immediately
3. **Use HTTPS** in production
4. **Protect config directory** (already done via .htaccess)
5. **Regular backups** of your database
6. **Validate payment callbacks** from Razorpay/PayPal

## Customization

### Change Site Name
Edit `config/database.php`:
```php
define('SITE_NAME', 'Your Site Name');
```

### Add New Categories
Edit `includes/functions.php` in the `getCategories()` function.

### Modify Shipping Rules
Edit `checkout.php` and `cart.php` to change shipping calculation.

### Add More Payment Methods
1. Add to `payment_methods` table
2. Create payment processing page
3. Update checkout.php to include new option

## Troubleshooting

**Database connection failed:**
- Check MySQL is running
- Verify credentials in `config/database.php`
- Ensure database `tribal_crafts` exists

**Cart/Wishlist not working:**
- Check if session is started
- Verify tables exist in database
- Check browser console for JavaScript errors

**Payment not processing:**
- Verify API keys are correct
- Check payment gateway is in test/live mode
- Review error logs in payment pages

**Admin login not working:**
- Run `reset_admin.php` to reset password
- Re-run `database.sql` to reset admin user

## Support

For issues or questions, contact: https://linkedin.com/in/ankitrathva

## License

This project is created for Tribal Crafts marketplace.
