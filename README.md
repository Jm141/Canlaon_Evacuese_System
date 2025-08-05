# Resident Management System

A comprehensive PHP-based Resident Management System designed for local government units to manage resident information, households, and generate evacuation ID cards with barcodes.

## 🏗️ System Architecture

- **Framework**: Custom PHP MVC Architecture
- **Database**: MySQL
- **Frontend**: Bootstrap 5 + Custom CSS
- **Barcode**: Custom Code 128 Generator
- **Charts**: Chart.js for data visualization

## ✨ Features

### 🔐 User Management
- **Main Admin**: Full system access, user management
- **Admin**: Barangay-specific access, ID card generation
- **Staff**: Resident data encoding only

### 👥 Resident Management
- Complete personal information tracking
- Household management with evacuation details
- Special needs identification
- Age and gender distribution analytics
- Search and filtering capabilities

### 🏠 Household Management
- Family unit organization
- Evacuation center assignment
- Collection point tracking
- Vehicle and driver assignment
- Control number generation

### 🆔 ID Card System
- **Kanlaon Evacuation Plan Bakwit Card** design
- Automatic barcode generation (Code 128)
- Unique card number generation
- Print-ready format
- Expiry date management

### 📊 Dashboard & Analytics
- Real-time statistics
- Age and gender distribution charts
- Special needs tracking
- Evacuation center overview
- Recent activities feed

### 🔍 Search & Reports
- Advanced search functionality
- Filtering by various criteria
- Export capabilities
- Activity logging

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- GD extension for image processing

### Setup Instructions

1. **Clone/Download the project**
   ```bash
   git clone <https://github.com/Jm141/Canlaon_Evacuese_System.git>
   cd Resident_Mngmnt
   ```

2. **Database Setup**
   - Create a MySQL database
   - Import the `database_schema.sql` file
   - Update database credentials in `config/config.php`

3. **Configuration**
   ```php
   // config/config.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'resident_management');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

4. **File Permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 config/config.php
   ```

5. **Web Server Configuration**
   - Point your web server to the project directory
   - Ensure URL rewriting is enabled (for clean URLs)

## 🔑 Default Login

- **Username**: `mainadmin`
- **Password**: `admin123`

## 📁 Project Structure

```
Resident_Mngmnt/
├── config/
│   ├── config.php          # Main configuration
│   └── database.php        # Database connection
├── models/
│   ├── Model.php           # Base model class
│   ├── User.php            # User management
│   ├── Resident.php        # Resident management
│   ├── Household.php       # Household management
│   ├── Barangay.php        # Barangay management
│   └── IdCard.php          # ID card management
├── controllers/
│   └── Controller.php      # Base controller
├── views/
│   ├── layouts/
│   │   └── main.php        # Main layout template
│   ├── dashboard/
│   │   └── index.php       # Dashboard view
│   └── id-cards/
│       └── view.php        # ID card view
├── includes/
│   └── BarcodeGenerator.php # Barcode generation
├── uploads/                # File uploads directory
├── login.php              # Login page
├── dashboard.php          # Dashboard controller
├── id-cards.php           # ID cards controller
├── logout.php             # Logout functionality
├── database_schema.sql    # Database schema
└── README.md              # This file
```

## 🎨 Design Features

### Color Palette
- **Primary Blue**: #1e3a8a (Dark Blue)
- **Secondary Blue**: #1e40af (Medium Blue)
- **Accent Blue**: #3b82f6 (Light Blue)
- **Dark Blue**: #1e1b4b (Very Dark Blue)

### Responsive Design
- Mobile-first approach
- Bootstrap 5 grid system
- Custom responsive sidebar
- Touch-friendly interface

### Minimalist UI
- Clean, uncluttered design
- Consistent spacing and typography
- Intuitive navigation
- Professional appearance

## 🔧 Configuration Options

### System Settings
```php
// ID Card validity period (years)
define('ID_CARD_VALIDITY_YEARS', 5);

// Items per page for pagination
define('ITEMS_PER_PAGE', 20);

// Session lifetime (seconds)
define('SESSION_LIFETIME', 3600);
```

### File Upload Settings
```php
// Maximum file size (5MB)
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

// Allowed image types
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
```

## 📋 User Roles & Permissions

### Main Administrator
- ✅ User management (create admin accounts)
- ✅ System-wide access
- ✅ Barangay management
- ✅ All resident operations
- ✅ ID card generation
- ✅ Reports and analytics

### Administrator
- ✅ Create staff accounts
- ✅ Manage residents in assigned barangay
- ✅ Generate ID cards
- ✅ View reports
- ❌ User management
- ❌ Barangay management

### Staff
- ✅ Encode resident information
- ✅ View residents in assigned barangay
- ❌ ID card generation
- ❌ User management
- ❌ Reports access

## 🆔 ID Card Features

### Design Elements
- **Header**: "KANLAON EVACUATION PLAN - BAKWIT CARD"
- **Household Information**: Head, members, address
- **Evacuation Details**: Center, vehicle, driver, collection point
- **Contact Information**: Phone numbers
- **Special Needs**: Identification and description
- **Barcode**: Code 128 format with card number
- **Control Numbers**: Unique household identifiers

### Generation Process
1. Select resident without active ID card
2. System validates eligibility
3. Generates unique card number
4. Creates barcode with card data
5. Sets expiry date (5 years by default)
6. Logs generation activity

## 🔍 Search & Filtering

### Resident Search
- By name (first, last, or full)
- By household head
- By control number
- By barangay
- By age range
- By gender
- By special needs

### Advanced Filters
- Date range filtering
- Status-based filtering
- Evacuation center filtering
- Collection point filtering

## 📊 Reporting Features

### Dashboard Analytics
- Total residents count
- Household statistics
- ID card status
- Special needs tracking
- Age distribution charts
- Gender distribution charts

### Export Capabilities
- PDF reports
- Excel/CSV exports
- Print-friendly formats
- Custom date ranges

## 🔒 Security Features

### Authentication
- Password hashing (bcrypt)
- Session management
- CSRF protection
- Input sanitization

### Authorization
- Role-based access control
- Barangay-specific data isolation
- Permission validation
- Activity logging

### Data Protection
- SQL injection prevention
- XSS protection
- File upload validation
- Secure session handling

## 🚨 Emergency Features

### Evacuation Management
- **Collection Points**: Designated pickup locations
- **Evacuation Centers**: Assigned shelters
- **Transportation**: Vehicle and driver assignment
- **Special Needs**: Priority assistance identification
- **Contact Information**: Emergency communication

### Quick Access
- Special needs residents list
- Evacuation center assignments
- Household contact details
- Emergency contact information

## 🛠️ Maintenance

### Database Maintenance
```sql
-- Update expired ID cards
UPDATE id_cards SET status = 'expired' 
WHERE expiry_date < CURDATE() AND status = 'active';

-- Clean up old activity logs (older than 1 year)
DELETE FROM activity_logs 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

### File Cleanup
```bash
# Clean up old uploads (older than 30 days)
find uploads/ -type f -mtime +30 -delete
```

## 📞 Support

For technical support or feature requests, please contact:
- **Email**: support@example.com
- **Phone**: +63 XXX XXX XXXX

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📝 Changelog

### Version 1.0.0
- Initial release
- Complete resident management system
- ID card generation with barcodes
- User role management
- Dashboard analytics
- Responsive design

---

**Built with ❤️ for efficient resident management and emergency preparedness.** 