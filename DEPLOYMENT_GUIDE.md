# Hostinger Deployment Guide for 45G1 Shop

## Step 1: Get Your Database Credentials from Hostinger

1. Log into your Hostinger account
2. Go to **Databases** section
3. Create a new database or use an existing one
4. Note down:
   - Database name
   - Database username
   - Database password
   - Database host (usually `localhost`)

## Step 2: Update Configuration

1. Edit `config.php` in your project
2. Replace the following values with your Hostinger database credentials:

```php
private $host = 'localhost';  // Usually 'localhost' on Hostinger
private $db_name = 'your_hostinger_database_name';
private $username = 'your_hostinger_database_username';
private $password = 'your_hostinger_database_password';
```

## Step 3: Upload Files via FileZilla or FTP

### Using Hostinger File Manager:
1. Log into Hostinger hPanel
2. Go to **File Manager**
3. Navigate to `public_html` folder
4. Delete the default `index.html` if it exists
5. Upload all your project files

### Using FTP (FileZilla):
1. Download FileZilla
2. Connect to your Hostinger FTP:
   - Host: `ftp.yourdomain.com` or the IP address provided by Hostinger
   - Username: Your Hostinger FTP username
   - Password: Your Hostinger FTP password
   - Port: 21
3. Upload all project files to `public_html` folder

## Step 4: Set Correct File Permissions

After uploading, set these permissions:
1. All folders (including `uploads/`): 755
2. All PHP files: 644
3. Make sure `uploads/` folder has write permissions (755 or 777)

## Step 5: Create Database Tables

1. In Hostinger hPanel, go to **phpMyAdmin**
2. Select your database
3. Go to **SQL** tab
4. Copy and paste the SQL from `db.php` file
5. Click **Go** to execute

**IMPORTANT:** The SQL in `db.php` includes:
- CREATE TABLE statements for all tables
- Sample data (admin user, categories, brands, products)
- The admin credentials are: email: `admin@shop.com`, password: `password`

## Step 6: Set Up the Database in Production

Run this SQL in phpMyAdmin to set up everything:

```sql
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS your_database_name;
USE your_database_name;

-- Then run all the SQL from db.php file
```

## Step 7: Test Your Website

Visit: `http://yourdomain.com/index.php`

Default admin login:
- Email: `admin@shop.com`
- Password: `password`

## Troubleshooting

### 1. Database Connection Error
- Check your `config.php` has correct Hostinger credentials
- Verify database exists in phpMyAdmin

### 2. Images Not Showing
- Check `uploads/` folder has write permissions (755 or 777)
- Verify image paths in database

### 3. Session Issues
- Make sure `core.php` is in the correct location
- Check session_start() is called

### 4. 500 Internal Server Error
- Check error logs in Hostinger hPanel
- Verify file permissions are correct
- Make sure PHP version is 7.4 or higher

## Files to Upload:
```
/
├── actions/
├── assets/
├── controllers/
├── models/
├── uploads/
├── views/
├── config.php
├── core.php
├── db.php
└── index.php
```

## Important Notes:

1. **Don't upload**:
   - `.git` folder
   - Any test files (test_*.php, debug_*.php)
   - `e-commerce-part3.zip`

2. **Must upload**:
   - All folders (actions, assets, controllers, models, uploads, views)
   - All PHP files in root directory
   - Make sure `uploads/` folder is uploaded

3. **Security**:
   - After deployment, change admin password
   - Consider using environment variables for sensitive data
   - Restrict access to `config.php` if possible

## After Deployment Checklist:

- [ ] Test homepage loads correctly
- [ ] Test admin login works
- [ ] Test product display works
- [ ] Test adding product to cart works
- [ ] Test viewing cart works
- [ ] Test removing items from cart works
- [ ] Verify images are loading
- [ ] Check all navigation links work
- [ ] Test search functionality
- [ ] Test filtering by category/brand

