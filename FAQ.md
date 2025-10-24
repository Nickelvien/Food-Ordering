# Frequently Asked Questions (FAQ)

## General Questions

### Q1: What is FoodHub?
**A:** FoodHub is a complete food ordering system that allows customers to browse menus, add items to cart, and place orders online. It includes both a customer-facing website and an admin panel for managing the business.

### Q2: What technologies are used?
**A:** 
- **Frontend**: HTML5, Tailwind CSS, JavaScript
- **Backend**: Native PHP (no frameworks)
- **Database**: MySQL
- **Server**: Apache (via XAMPP)
- **Icons**: Font Awesome

### Q3: Is this production-ready?
**A:** The system is fully functional and can be used for learning and development. For production deployment, you should:
- Change all default passwords
- Enable HTTPS
- Update security configurations
- Set up email notifications
- Configure proper backups

### Q4: Can I use this for commercial purposes?
**A:** Yes, the code is open-source and can be used for educational and commercial purposes. However, make sure to secure it properly before going live.

---

## Installation & Setup

### Q5: How do I install the system?
**A:** Follow these steps:
1. Install XAMPP
2. Start Apache and MySQL
3. Import the database file via phpMyAdmin
4. Access the system at `http://localhost/Food%20Ordering/`

See **SETUP_GUIDE.md** for detailed instructions.

### Q6: What are the system requirements?
**A:** Minimum requirements:
- XAMPP 8.x or compatible server
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser
- 500MB free disk space

### Q7: I get "Connection failed" error. What should I do?
**A:** This usually means:
- MySQL is not running → Start it in XAMPP
- Wrong database credentials → Check db.php
- Database not imported → Import the SQL file

### Q8: How do I import the database?
**A:** 
1. Open `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Click "Choose File"
4. Select `database/food_ordering_system.sql`
5. Click "Go"

### Q9: Can I change the database name?
**A:** Yes, but you need to:
1. Create new database in phpMyAdmin
2. Update `DB_NAME` in db.php
3. Import the SQL file to new database

---

## Login & Authentication

### Q10: What are the default login credentials?
**A:** 
**Admin:**
- Email: admin@foodorder.com
- Password: admin123

**Customer:**
- Email: customer@example.com
- Password: customer123

### Q11: How do I change the admin password?
**A:** 
1. Login as admin
2. For now, update directly in database:
   - Open phpMyAdmin
   - Go to `users` table
   - Edit admin user
   - Generate new hash using PHP's `password_hash()`
   - Update password field

### Q12: Can I create more admin accounts?
**A:** Yes:
1. Open phpMyAdmin
2. Go to `users` table
3. Insert new row with `role = 'admin'`
4. Use hashed password

### Q13: I forgot my password. How do I reset it?
**A:** Currently, reset password feature is not implemented. You can:
- Reset in database using phpMyAdmin
- Or use the default credentials

---

## Features & Functionality

### Q14: How does the shopping cart work?
**A:** The cart uses PHP sessions:
- Items are stored in `$_SESSION['cart']`
- Persists until checkout or manual clearing
- Updates via AJAX calls

### Q15: Can customers cancel orders?
**A:** Currently, no. Only admins can change order status. This can be added as a future feature.

### Q16: How do I add food images?
**A:** 
1. Place your image files in `assets/images/food/`
2. When adding food items, enter the filename (e.g., `burger.jpg`)
3. The system will display the image automatically
4. If image is missing, a placeholder is shown

### Q17: Can I add more categories?
**A:** Yes:
1. Login as admin (or use phpMyAdmin)
2. Go to phpMyAdmin → `categories` table
3. Insert new row with category name and description
4. It will appear automatically in menus

### Q18: How do order statuses work?
**A:** Order flow:
- **Pending**: Just placed, waiting confirmation
- **Confirmed**: Admin accepted order
- **Preparing**: Food is being prepared
- **Delivered**: Order completed
- **Cancelled**: Order was cancelled

### Q19: Can I enable online payments?
**A:** Currently, it's set to "demo mode". To enable real payments:
- Integrate Stripe or PayPal API
- Add payment processing in checkout.php
- Update database to store transaction IDs

---

## Customization

### Q20: How do I change the color scheme?
**A:** Edit the Tailwind config in each PHP file:
```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#YOUR_COLOR',
                secondary: '#YOUR_COLOR',
            }
        }
    }
}
```

### Q21: Can I change the website name?
**A:** Yes, search for "FoodHub" across all files and replace with your brand name.

### Q22: How do I add more pages?
**A:** 
1. Create new PHP file
2. Include db.php at top
3. Add navigation link
4. Follow existing page structure

### Q23: Can I use a different CSS framework?
**A:** Yes, but you'll need to:
- Replace Tailwind classes throughout
- Update all HTML files
- Maintain responsive design

---

## Admin Panel

### Q24: How do I access the admin panel?
**A:** 
1. Login with admin credentials
2. You'll be redirected to dashboard automatically
3. Or go directly to: `http://localhost/Food%20Ordering/admin/dashboard.php`

### Q25: Can I delete users?
**A:** Yes, in the admin panel:
- Go to "Manage Users"
- Click delete icon next to user
- Confirm deletion

### Q26: How do I add new food items?
**A:** 
1. Admin panel → Manage Food
2. Click "Add New Item"
3. Fill in all details
4. Upload/reference image
5. Check "Available" and "Featured" if needed
6. Click "Save"

### Q27: Can I export reports?
**A:** Currently, reports are view-only. To export:
- You can add PDF export functionality
- Or copy data from browser
- Or query database directly

---

## Technical Questions

### Q28: Why no frameworks?
**A:** This project uses native PHP to:
- Simplify learning
- Reduce dependencies
- Make code easy to understand
- Allow full customization

### Q29: Can I convert this to use a framework?
**A:** Yes! You can:
- Migrate to Laravel, CodeIgniter, etc.
- Keep the database structure
- Rebuild views with framework templates
- Add more advanced features

### Q30: Is the code secure?
**A:** The code includes:
- Password hashing
- Input sanitization
- SQL injection prevention
- XSS protection

For production, also add:
- CSRF tokens
- Rate limiting
- HTTPS
- Regular security updates

### Q31: Can I use this with PostgreSQL?
**A:** Yes, but you'll need to:
- Update database connection code
- Modify SQL queries for PostgreSQL syntax
- Test all functionality

### Q32: Does it support multiple languages?
**A:** No, currently English only. To add:
- Implement i18n system
- Create language files
- Update all text strings

---

## Troubleshooting

### Q33: The page is blank. What's wrong?
**A:** 
1. Enable error display in db.php:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
2. Check Apache error log
3. Verify PHP syntax

### Q34: Images are not showing
**A:** 
- Check if files exist in `assets/images/food/`
- Verify filename matches database entry
- Check file permissions
- System uses placeholders if image missing

### Q35: Cart is empty after refresh
**A:** 
- Check if session is starting (session_start())
- Verify cookies are enabled in browser
- Check browser console for errors

### Q36: Can't login as admin
**A:** 
- Verify database was imported
- Check users table has admin user
- Try default credentials
- Clear browser cookies

### Q37: SQL errors appearing
**A:** 
- Check database connection
- Verify all tables were created
- Check for syntax errors in queries
- Review error message carefully

---

## Performance & Scaling

### Q38: How many orders can it handle?
**A:** With proper server configuration:
- Thousands of orders
- Hundreds of concurrent users
- Add indexes for better performance

### Q39: How do I improve performance?
**A:** 
1. Add database indexes
2. Enable caching
3. Optimize images
4. Use CDN for assets
5. Enable gzip compression
6. Upgrade server resources

### Q40: Can I use this for multiple restaurants?
**A:** Not currently. To support multiple restaurants:
- Add restaurants table
- Link menu items to restaurants
- Add restaurant selection
- Update admin panel

---

## Deployment

### Q41: How do I deploy to a live server?
**A:** 
1. Get web hosting with PHP + MySQL
2. Upload files via FTP
3. Create database and import SQL
4. Update db.php with server credentials
5. Configure domain/SSL

### Q42: What hosting do I need?
**A:** Requirements:
- PHP 7.4+ support
- MySQL 5.7+ database
- Apache with mod_rewrite
- At least 1GB RAM
- SSL certificate (recommended)

### Q43: Can I use this on shared hosting?
**A:** Yes! Most shared hosting supports:
- PHP and MySQL
- File uploads
- .htaccess files

---

## Features Requests

### Q44: Can you add feature X?
**A:** The system is designed to be extended. Check **DEVELOPER_NOTES.md** for guidance on adding features.

### Q45: Will there be updates?
**A:** This is version 1.0. Future enhancements are listed in **CHANGELOG.md**.

### Q46: Can I contribute?
**A:** Yes! If using version control:
- Fork the repository
- Make improvements
- Test thoroughly
- Submit pull request

---

## Support & Help

### Q47: Where can I find more help?
**A:** Documentation files:
- **README.md** - Overview
- **SETUP_GUIDE.md** - Installation
- **QUICK_REFERENCE.md** - Quick tips
- **DEVELOPER_NOTES.md** - Technical details

### Q48: How do I report bugs?
**A:** 
1. Check if it's in FAQ
2. Review error messages
3. Check documentation
4. Create detailed bug report

### Q49: Can I hire someone to customize this?
**A:** Yes, you can:
- Hire a PHP developer
- Post on freelance platforms
- Contact local developers

### Q50: Is training available?
**A:** The code is extensively commented. Also check:
- Code comments in files
- Documentation guides
- Online PHP tutorials
- MySQL documentation

---

## Best Practices

### Q51: How often should I backup?
**A:** Recommended:
- Daily for production
- Before major changes
- After adding new data

### Q52: Should I modify core files?
**A:** 
- It's okay for learning
- Keep backups before changes
- Document your modifications
- Test thoroughly

### Q53: How do I keep the system secure?
**A:** 
1. Change default passwords
2. Keep PHP and MySQL updated
3. Regular backups
4. Monitor for suspicious activity
5. Use HTTPS
6. Validate all inputs

---

## Additional Resources

### Q54: Where can I learn more about PHP?
**A:** 
- Official PHP Documentation: https://php.net
- W3Schools PHP Tutorial
- PHP The Right Way: https://phptherightway.com

### Q55: Where can I learn Tailwind CSS?
**A:** 
- Official Docs: https://tailwindcss.com
- Tailwind Play (online editor)
- YouTube tutorials

---

**Still have questions?**

Check the comprehensive documentation files or review the code comments for detailed explanations!

**Last Updated**: October 2025
