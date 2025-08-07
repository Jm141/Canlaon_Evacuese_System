# Hostinger FTP Deployment Guide
## Canlaon Evacuee System

### Prerequisites
- Hostinger hosting account with FTP access
- Database already created on Hostinger (u651277261_evac)
- FTP client (FileZilla, WinSCP, or similar)

---

## Step 1: Database Setup

### 1.1 Create Database on Hostinger
1. Log in to your Hostinger control panel
2. Go to "Databases" → "MySQL Databases"
3. Create a new database named `u651277261_evac`
4. Create a database user `u651277261_evac` with password `s5CdWGsG?5M`
5. Assign the user to the database with all privileges

### 1.2 Import Database Schema
1. In Hostinger control panel, go to "Databases" → "phpMyAdmin"
2. Select your database `u651277261_evac`
3. Go to "Import" tab
4. Upload and import the following files:
   - `database_schema.sql`
   - `migration_add_evacuation_centers.sql`

---

## Step 2: File Preparation

### 2.1 Clean Up Development Files
Remove these files before uploading:
- `test_current_project.php`
- `debug_view_path.php`
- `check_db.php`
- `test_login_fix.php`
- `debug_login.php`

### 2.2 Update Configuration
The following files have been updated with Hostinger credentials:
- `config/config.php` ✅
- `dbCon.php` ✅

### 2.3 Create .htaccess File
Create a `.htaccess` file in your root directory:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# Disable directory browsing
Options -Indexes

# Protect sensitive files
<Files "*.sql">
    Order allow,deny
    Deny from all
</Files>

<Files "*.log">
    Order allow,deny
    Deny from all
</Files>
```

---

## Step 3: FTP Upload

### 3.1 Connect to Hostinger FTP
- **Host**: Your domain or FTP hostname (provided by Hostinger)
- **Username**: Your FTP username
- **Password**: Your FTP password
- **Port**: 21 (default)

### 3.2 Upload Files
1. Connect to your FTP server
2. Navigate to the `public_html` folder (or your domain's root)
3. Upload all project files maintaining the directory structure:
   ```
   public_html/
   ├── config/
   ├── controllers/
   ├── includes/
   ├── models/
   ├── views/
   ├── uploads/ (create this folder)
   ├── .htaccess
   ├── login.php
   ├── logout.php
   ├── dbCon.php
   └── [other PHP files]
   ```

### 3.3 Set File Permissions
Set the following permissions:
- **Directories**: 755
- **PHP files**: 644
- **Uploads folder**: 755
- **Configuration files**: 644

---

## Step 4: Post-Upload Configuration

### 4.1 Update Application URL
In `config/config.php`, update the APP_URL:
```php
define('APP_URL', 'https://yourdomain.com');
```

### 4.2 Create Uploads Directory
1. Create an `uploads` folder in your root directory
2. Set permissions to 755
3. Create subdirectories if needed:
   - `uploads/id_cards/`
   - `uploads/profiles/`
   - `uploads/documents/`

### 4.3 Test Database Connection
Create a test file `test_db.php`:
```php
<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = Database::getInstance();
    echo "Database connection successful!";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
```

---

## Step 5: Security Configuration

### 5.1 Update JWT Secret
In `config/config.php`, change the JWT_SECRET:
```php
define('JWT_SECRET', 'your-unique-secret-key-here');
```

### 5.2 Disable Error Display (Production)
In `config/config.php`, change:
```php
error_reporting(0);
ini_set('display_errors', 0);
```

### 5.3 SSL Configuration
Ensure your site uses HTTPS:
- Update all URLs to use `https://`
- Configure SSL certificate in Hostinger

---

## Step 6: Testing

### 6.1 Test Login
1. Visit `https://yourdomain.com/login.php`
2. Try logging in with admin credentials
3. Check if all features work properly

### 6.2 Test File Uploads
1. Test ID card generation
2. Test profile picture uploads
3. Verify uploads folder permissions

### 6.3 Test Database Operations
1. Create a new resident
2. Generate an ID card
3. Test search functionality

---

## Step 7: Final Cleanup

### 7.1 Remove Test Files
Delete these files after testing:
- `test_db.php`
- Any other test/debug files

### 7.2 Backup Configuration
Keep a backup of your production configuration files.

---

## Troubleshooting

### Common Issues:

1. **Database Connection Failed**
   - Verify database credentials
   - Check if database exists
   - Ensure user has proper privileges

2. **500 Internal Server Error**
   - Check file permissions
   - Verify .htaccess syntax
   - Check error logs in Hostinger

3. **File Upload Issues**
   - Verify uploads folder exists
   - Check folder permissions (755)
   - Ensure PHP upload settings are correct

4. **Page Not Found**
   - Verify .htaccess file is uploaded
   - Check if mod_rewrite is enabled
   - Ensure file paths are correct

### Hostinger Support
If you encounter issues:
1. Check Hostinger's error logs
2. Contact Hostinger support
3. Verify PHP version compatibility (recommended: PHP 7.4+)

---

## Maintenance

### Regular Tasks:
1. **Backup Database**: Weekly backups via phpMyAdmin
2. **Update Logs**: Monitor error logs
3. **Security Updates**: Keep PHP and dependencies updated
4. **File Permissions**: Regularly check file permissions

### Monitoring:
- Set up error monitoring
- Monitor disk space usage
- Check database performance

---

## Contact Information
For technical support or questions about this deployment guide, refer to your development team or hosting provider. 