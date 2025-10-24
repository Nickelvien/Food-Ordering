# FoodHub Setup Guide

## Complete Step-by-Step Installation Instructions

### Prerequisites
Before you begin, ensure you have:
- Windows PC (7, 8, 10, or 11)
- At least 500MB of free disk space
- Administrator access to install software

---

## Part 1: Installing XAMPP

### Step 1: Download XAMPP
1. Open your web browser
2. Go to: https://www.apachefriends.org
3. Click "Download" button
4. Select "XAMPP for Windows"
5. Download the latest version (PHP 8.x recommended)

### Step 2: Install XAMPP
1. Locate the downloaded file (usually in Downloads folder)
2. Right-click the installer and select "Run as administrator"
3. Click "Yes" if User Account Control asks for permission
4. Follow the installation wizard:
   - Click "Next"
   - Select components (keep default: Apache, MySQL, PHP, phpMyAdmin)
   - Choose installation folder (default: C:\xampp)
   - Click "Next" and then "Install"
5. Wait for installation to complete (2-5 minutes)
6. Click "Finish"

### Step 3: Start XAMPP Services
1. Open "XAMPP Control Panel" from Start Menu
2. Click "Start" button next to **Apache**
3. Click "Start" button next to **MySQL**
4. Both should show "Running" in green

**Troubleshooting:**
- If Apache won't start, check if port 80 is in use by Skype or IIS
- Solution: Close Skype or change Apache port in httpd.conf

---

## Part 2: Setting Up the Database

### Step 1: Access phpMyAdmin
1. Open your web browser
2. Type in address bar: `http://localhost/phpmyadmin`
3. Press Enter
4. You should see phpMyAdmin dashboard

### Step 2: Import Database

**Method 1: Using Import (Recommended)**
1. Click "Import" tab at the top
2. Click "Choose File" button
3. Navigate to: `C:\xampp\htdocs\Food Ordering\database\`
4. Select `food_ordering_system.sql`
5. Click "Go" button at the bottom
6. Wait for success message: "Import has been successfully finished"

**Method 2: Manual Creation**
1. Click "New" in left sidebar
2. Database name: `food_ordering_system`
3. Collation: `utf8mb4_general_ci`
4. Click "Create"
5. Click on the new database name
6. Click "SQL" tab
7. Open `food_ordering_system.sql` in notepad
8. Copy all content
9. Paste into SQL tab
10. Click "Go"

### Step 3: Verify Database
1. Click on `food_ordering_system` in left sidebar
2. You should see tables:
   - users
   - categories
   - food_items
   - orders
   - order_items
   - messages
3. Click on "users" table
4. Click "Browse" - you should see 2 users (admin and customer)

---

## Part 3: Installing the Food Ordering System

### Step 1: Locate Project Files
The project is already in: `C:\xampp\htdocs\Food Ordering\`

### Step 2: Verify File Structure
Open File Explorer and navigate to `C:\xampp\htdocs\Food Ordering\`

You should see:
```
Food Ordering/
├── admin/
├── assets/
├── auth/
├── customer/
├── database/
├── db.php
├── index.php
├── about.php
├── contact.php
├── logout.php
└── README.md
```

### Step 3: Configure Database Connection
1. Open `db.php` in a text editor (Notepad, VS Code, etc.)
2. Verify these settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'food_ordering_system');
   ```
3. If you changed MySQL password during XAMPP setup, update `DB_PASS`
4. Save the file

---

## Part 4: Accessing the Application

### Step 1: Open the Website
1. Make sure Apache and MySQL are running in XAMPP
2. Open your web browser
3. Type in address bar: `http://localhost/Food%20Ordering/index.php`
4. Press Enter
5. You should see the FoodHub homepage!

### Step 2: Test Customer Login
1. Click "Login" button in navigation
2. Use these credentials:
   - **Email**: customer@example.com
   - **Password**: customer123
3. Click "Sign In"
4. You should be redirected to the menu page

### Step 3: Test Admin Login
1. Logout if you're logged in
2. Go to login page
3. Use these credentials:
   - **Email**: admin@foodorder.com
   - **Password**: admin123
4. Click "Sign In"
5. You should see the Admin Dashboard

---

## Part 5: Testing Features

### Customer Features to Test:

1. **Browse Menu**
   - Go to Menu page
   - Try filtering by category
   - Use search bar
   - Try sorting options

2. **Shopping Cart**
   - Click "Add to Cart" on any item
   - Click cart icon in navigation
   - Update quantities
   - Remove items

3. **Place Order**
   - Add items to cart
   - Click "Proceed to Checkout"
   - Fill in delivery information
   - Choose payment method
   - Click "Place Order"
   - Check order confirmation

4. **View Orders**
   - Click on your name in navigation
   - Select "My Orders"
   - View order history

### Admin Features to Test:

1. **Dashboard**
   - View statistics
   - Check recent orders

2. **Manage Food**
   - Click "Manage Food" in sidebar
   - Try adding a new food item
   - Edit existing item
   - Delete an item (use test data)

3. **Manage Orders**
   - Click "Manage Orders"
   - Change order status
   - View order details

4. **Reports**
   - Click "Reports"
   - View daily sales
   - View monthly sales
   - Check top-selling items

---

## Part 6: Customization

### Change Website Colors
1. Open any PHP file (e.g., index.php)
2. Find the Tailwind config section:
   ```javascript
   tailwind.config = {
       theme: {
           extend: {
               colors: {
                   primary: '#f59e0b',    // Change this
                   secondary: '#fb923c',  // Change this
               }
           }
       }
   }
   ```
3. Replace color codes with your preferred colors
4. Save and refresh browser

### Add Your Own Food Items
1. Login as admin
2. Go to "Manage Food"
3. Click "Add New Item"
4. Fill in details:
   - Name: e.g., "Spicy Pizza"
   - Category: Select from dropdown
   - Price: e.g., 15.99
   - Description: Write description
   - Image: Enter filename (e.g., pizza.jpg)
   - Check "Available" and "Featured" if needed
5. Click "Save Food Item"

### Add Food Images
1. Find or create food images (JPG or PNG)
2. Rename them (e.g., burger.jpg, pizza.jpg)
3. Copy to: `C:\xampp\htdocs\Food Ordering\assets\images\food\`
4. Use these filenames when adding food items

---

## Part 7: Troubleshooting

### Problem: "Connection failed" Error
**Solution:**
1. Check if MySQL is running in XAMPP Control Panel
2. Verify database name is correct in db.php
3. Check if database was imported correctly in phpMyAdmin

### Problem: Blank White Page
**Solution:**
1. Enable error display:
   - Open db.php
   - Add at top: `error_reporting(E_ALL); ini_set('display_errors', 1);`
2. Check Apache error log in XAMPP Control Panel
3. Verify all PHP tags are closed properly

### Problem: Images Not Showing
**Solution:**
1. Check if images exist in `assets/images/food/` folder
2. Verify image filenames match database entries
3. System uses placeholder images if actual images are missing

### Problem: Can't Login
**Solution:**
1. Verify database was imported correctly
2. Check users table in phpMyAdmin
3. Try default credentials again
4. Clear browser cache and cookies

### Problem: Shopping Cart Not Working
**Solution:**
1. Check browser console for JavaScript errors (F12)
2. Verify session is starting (check db.php)
3. Clear cookies and try again

---

## Part 8: Maintenance

### Backup Database
1. Open phpMyAdmin
2. Click on `food_ordering_system` database
3. Click "Export" tab
4. Click "Go" button
5. Save SQL file to safe location

### Reset to Default
1. Open phpMyAdmin
2. Click on `food_ordering_system` database
3. Check all tables
4. Select "Drop" from dropdown
5. Re-import original SQL file

### Update PHP Version
1. Download latest XAMPP
2. Install to different folder
3. Copy `Food Ordering` folder to new htdocs
4. Export/Import database

---

## Additional Resources

### File Locations
- **XAMPP**: C:\xampp\
- **Web files**: C:\xampp\htdocs\
- **Apache config**: C:\xampp\apache\conf\httpd.conf
- **PHP config**: C:\xampp\php\php.ini
- **MySQL data**: C:\xampp\mysql\data\

### Useful URLs
- **Homepage**: http://localhost/Food%20Ordering/
- **phpMyAdmin**: http://localhost/phpmyadmin
- **Admin Panel**: http://localhost/Food%20Ordering/admin/dashboard.php

### Default Passwords
All default passwords are: `admin123` or `customer123`

To change passwords:
1. Login to account
2. Password is hashed - need to update through PHP
3. Or update directly in database using MD5/password_hash

---

## Security Recommendations

For Production Deployment:
1. Change all default passwords
2. Use strong passwords (min 12 characters)
3. Enable HTTPS/SSL
4. Update database credentials
5. Disable error display
6. Enable PHP security features
7. Regular backups
8. Keep XAMPP updated

---

## Getting Help

If you encounter issues:
1. Check this guide carefully
2. Review error messages
3. Check XAMPP logs
4. Search online for specific error messages
5. Review code comments in PHP files

---

## Success Checklist

- [ ] XAMPP installed and running
- [ ] Apache and MySQL services started
- [ ] Database imported successfully
- [ ] Can access homepage
- [ ] Customer login works
- [ ] Admin login works
- [ ] Can browse menu
- [ ] Shopping cart functions
- [ ] Can place orders
- [ ] Admin panel accessible

If all checkboxes are checked, your installation is successful! 🎉

---

**Congratulations! Your Food Ordering System is ready to use!**

Enjoy exploring and customizing your new food ordering platform! 🍔🍕🍰
