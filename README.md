# FoodHub - Food Ordering System

A complete food ordering system built with HTML, Tailwind CSS, JavaScript, PHP, and MySQL. This system allows customers to browse menus, place orders, and track their deliveries, while administrators can manage food items, orders, and users.

## 🌟 Features

### Customer Features
- **User Authentication**: Register, login, and logout functionality with password hashing
- **Browse Menu**: View all available food items with categories and search
- **Shopping Cart**: Add items, update quantities, and remove items
- **Order Placement**: Complete checkout with delivery information
- **Order History**: View past and current orders with status tracking
- **Responsive Design**: Works perfectly on mobile, tablet, and desktop

### Admin Features
- **Dashboard**: Overview of orders, revenue, customers, and statistics
- **Manage Food Items**: Add, edit, delete food items with categories
- **Manage Orders**: View all orders and update order status
- **Manage Users**: View and manage customer accounts
- **Sales Reports**: Daily and monthly sales reports, top-selling items

## 🛠️ Technology Stack

- **Frontend**: HTML5, Tailwind CSS, JavaScript
- **Backend**: PHP (Native, no frameworks)
- **Database**: MySQL
- **Icons**: Font Awesome
- **Server**: XAMPP (Apache + MySQL)

## 📋 Prerequisites

- XAMPP (or any Apache + MySQL + PHP environment)
- Web browser (Chrome, Firefox, Safari, etc.)
- Text editor (VS Code, Sublime Text, etc.)

## 🚀 Installation & Setup

### Step 1: Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Install XAMPP on your computer
3. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Setup Database

1. Open your web browser and go to `http://localhost/phpmyadmin`
2. Click on "Import" tab
3. Click "Choose File" and select `database/food_ordering_system.sql`
4. Click "Go" to import the database
5. The database `food_ordering_system` will be created with sample data

**Alternative Method:**
1. Go to `http://localhost/phpmyadmin`
2. Create a new database named `food_ordering_system`
3. Copy the SQL content from `database/food_ordering_system.sql`
4. Paste it into the SQL tab and execute

### Step 3: Copy Project Files

1. Copy the entire `Food Ordering` folder to `C:\xampp\htdocs\`
2. The project should be located at: `C:\xampp\htdocs\Food Ordering\`

### Step 4: Configure Database Connection

The database connection is already configured in `db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'food_ordering_system');
```

If you have custom MySQL credentials, update these values in `db.php`.

### Step 5: Access the Application

1. Open your web browser
2. Navigate to: `http://localhost/Food%20Ordering/index.php`
3. The homepage should load successfully!

## 👤 Default Login Credentials

### Admin Account
- **Email**: admin@foodorder.com
- **Password**: admin123

### Customer Account
- **Email**: customer@example.com
- **Password**: customer123

## 📁 Project Structure

```
Food Ordering/
├── admin/                      # Admin panel pages
│   ├── dashboard.php          # Admin dashboard
│   ├── manage_food.php        # Manage food items
│   ├── manage_orders.php      # Manage orders
│   ├── manage_users.php       # Manage users
│   └── reports.php            # Sales reports
├── assets/                     # Static assets
│   ├── css/                   # Custom CSS (if any)
│   ├── js/                    # Custom JavaScript
│   └── images/                # Images
│       └── food/              # Food item images
├── auth/                       # Authentication pages
│   ├── login.php              # Login page
│   └── register.php           # Registration page
├── customer/                   # Customer pages
│   ├── menu.php               # Browse menu
│   ├── cart.php               # Shopping cart
│   ├── checkout.php           # Checkout page
│   ├── orders.php             # Order history
│   ├── cart_handler.php       # Cart AJAX handler
│   └── get_cart_count.php     # Cart count API
├── database/                   # Database files
│   └── food_ordering_system.sql
├── db.php                      # Database connection
├── index.php                   # Homepage
├── about.php                   # About us page
├── contact.php                 # Contact page
├── logout.php                  # Logout handler
└── README.md                   # This file
```

## 🔐 Security Features

1. **Password Hashing**: All passwords are hashed using PHP's `password_hash()` function
2. **SQL Injection Prevention**: Input sanitization using `mysqli_real_escape_string()`
3. **XSS Protection**: Output escaping using `htmlspecialchars()`
4. **Session Management**: Secure session handling for user authentication
5. **Role-Based Access**: Separate admin and customer areas with access control

## 🎨 Customization

### Change Color Scheme

The primary colors are defined in Tailwind config in each file:
```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#f59e0b',    // Orange
                secondary: '#fb923c',  // Light Orange
            }
        }
    }
}
```

### Add Food Items

1. Login as admin
2. Go to "Manage Food" section
3. Click "Add New Item"
4. Fill in the details and save

**Note**: For images, place food images in `assets/images/food/` directory and reference the filename when adding items.

## 📊 Database Schema

### Tables:
- **users**: User accounts (admin and customers)
- **categories**: Food categories
- **food_items**: Food menu items
- **orders**: Customer orders
- **order_items**: Items in each order
- **messages**: Contact form messages

## 🐛 Troubleshooting

### Issue: "Connection failed" error
**Solution**: Make sure MySQL is running in XAMPP and database credentials are correct in `db.php`

### Issue: Images not showing
**Solution**: Make sure image files exist in `assets/images/food/` directory. The system uses placeholder images if actual images are not found.

### Issue: "Cannot modify header information" error
**Solution**: Make sure there's no output (spaces, HTML) before `<?php` tags in PHP files

### Issue: Session not working
**Solution**: Ensure `session_start()` is called at the beginning of pages and PHP session directory is writable

## 🔄 Future Enhancements

- Online payment integration (Stripe, PayPal)
- Real-time order tracking
- Email notifications
- Review and rating system
- Coupon/discount system
- Multi-language support
- Mobile app (React Native)

## 📝 License

This project is open-source and available for educational purposes.

## 👨‍💻 Support

For issues or questions:
- Check the troubleshooting section above
- Review the code comments for detailed explanations
- Contact: info@foodhub.com

## 🙏 Credits

- **Tailwind CSS**: https://tailwindcss.com
- **Font Awesome**: https://fontawesome.com
- **XAMPP**: https://www.apachefriends.org

---

**Developed with ❤️ for learning purposes**

Enjoy your Food Ordering System! 🍔🍕🍰
