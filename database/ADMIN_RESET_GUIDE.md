# Admin Account Reset - Quick Reference

## ✅ What Was Done

1. **Created reset_admin.php** - PHP script to automatically reset users and create new admin
2. **Created reset_admin.sql** - SQL script for manual database reset

## 🔐 New Admin Credentials

| Field    | Value                 |
|----------|-----------------------|
| Email    | admin@foodhub.com     |
| Password | Admin@2025            |
| Role     | Administrator         |
| Name     | System Administrator  |

## 🚀 How to Reset Users

### Method 1: Automatic PHP Script (EASIEST)

1. Make sure XAMPP Apache and MySQL are running
2. Open browser and go to:
   ```
   http://localhost/Food_Ordering/database/reset_admin.php
   ```
3. The script will:
   - Delete all existing users
   - Create new admin account
   - Display success message with credentials
4. **Important:** Delete the `reset_admin.php` file after use for security

### Method 2: Manual SQL (phpMyAdmin)

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select `food_ordering_system` database
3. Click "SQL" tab
4. Copy this SQL and execute:

```sql
-- Delete all users
DELETE FROM users;

-- Reset auto-increment
ALTER TABLE users AUTO_INCREMENT = 1;

-- Create new admin (password: Admin@2025)
INSERT INTO users (full_name, email, password, phone, address, role) VALUES
('System Administrator', 
 'admin@foodhub.com', 
 '$2y$10$vQ5qF7xK8mHw9Y.rNx3zTOYGZJ8YH6XjF5B7kQtL9wX2Rp3VmN4Uy', 
 '+1-555-ADMIN', 
 '123 Admin Street, Admin City', 
 'admin');
```

## 🔄 After Reset

### Login as Admin
1. Go to: `http://localhost/Food_Ordering/auth/login.php`
2. Enter:
   - Email: `admin@foodhub.com`
   - Password: `Admin@2025`
3. Click "Sign In"
4. You'll be redirected to Admin Dashboard

### Admin Panel Access
Direct URL: `http://localhost/Food_Ordering/admin/dashboard.php`

## 📝 What You Can Do as Admin

✅ View Dashboard Statistics
✅ Manage Food Items (Add/Edit/Delete)
✅ Manage Orders (View/Update Status)
✅ Manage Users (View/Delete)
✅ Generate Sales Reports

## 🔒 Security Recommendations

1. **Change Password After First Login**
   - Use a strong, unique password
   - At least 12 characters
   - Mix of letters, numbers, and symbols

2. **Delete Reset Scripts**
   - Delete `database/reset_admin.php` after use
   - Keep `database/reset_admin.sql` only if needed

3. **For Production**
   - Never use default credentials
   - Enable HTTPS
   - Use environment variables for sensitive data
   - Implement 2FA (Two-Factor Authentication)

## 🆘 Troubleshooting

### Problem: "Connection failed" error
**Solution:** 
- Check MySQL is running in XAMPP
- Verify database name in `db.php`

### Problem: Can't login with new credentials
**Solution:**
- Clear browser cache and cookies
- Try incognito/private browsing mode
- Verify user was created in phpMyAdmin

### Problem: Script shows blank page
**Solution:**
- Check Apache error logs in XAMPP
- Enable error display in `db.php`
- Verify PHP is working (check other pages)

## 📋 Verify User Creation

Check in phpMyAdmin:
1. Go to `food_ordering_system` database
2. Click on `users` table
3. Click "Browse"
4. You should see the new admin user with ID 1

## 🎯 Quick Test Checklist

- [ ] Apache and MySQL running
- [ ] Ran reset script successfully
- [ ] Old users deleted
- [ ] New admin created
- [ ] Can login with new credentials
- [ ] Admin dashboard accessible
- [ ] Deleted reset_admin.php file

## 📞 Need to Create More Users?

### Create Customer Account
Users can register themselves at:
`http://localhost/Food_Ordering/auth/register.php`

### Create Admin Account Manually
Use phpMyAdmin SQL:
```sql
INSERT INTO users (full_name, email, password, role) VALUES
('Your Name', 
 'your.email@example.com', 
 -- Use PHP to hash: password_hash('yourpassword', PASSWORD_DEFAULT)
 '$2y$10$hashedpasswordhere', 
 'admin');
```

---

**Created:** October 8, 2025
**Last Updated:** October 8, 2025
