<?php
/**
 * Main Configuration File
 * Resident Management System
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'u651277261_evac');
define('DB_USER', 'u651277261_evac');
define('DB_PASS', 's5CdWGsG?5M');

// Application Configuration
define('APP_NAME', 'Resident Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/Resident_Mngmnt');
define('APP_ROOT', dirname(dirname(__FILE__)));

// Session Configuration
define('SESSION_NAME', 'resident_mgmt_session');
define('SESSION_LIFETIME', 3600); // 1 hour

// File Upload Configuration
define('UPLOAD_PATH', APP_ROOT . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// ID Card Configuration
define('ID_CARD_WIDTH', 800);
define('ID_CARD_HEIGHT', 500);
define('ID_CARD_VALIDITY_YEARS', 5);

// Pagination Configuration
define('ITEMS_PER_PAGE', 20);

// Security Configuration
define('HASH_COST', 12);
define('JWT_SECRET', 'your-secret-key-here-change-in-production');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Manila');

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Autoloader function
spl_autoload_register(function ($class) {
    $paths = [
        APP_ROOT . '/models/',
        APP_ROOT . '/controllers/',
        APP_ROOT . '/includes/',
        APP_ROOT . '/config/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Explicitly require Database class to ensure it's loaded
require_once APP_ROOT . '/config/database.php';

// Helper functions
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function isMainAdmin() {
    return getUserRole() === 'main_admin';
}

function isAdmin() {
    return getUserRole() === 'admin';
}

function isStaff() {
    return getUserRole() === 'staff';
}

function hasPermission($permission) {
    $role = getUserRole();
    
    switch ($permission) {
        case 'user_management':
            return $role === 'main_admin';
        case 'admin_management':
            return $role === 'main_admin';
        case 'staff_management':
            return in_array($role, ['main_admin', 'admin']);
        case 'resident_management':
            return in_array($role, ['main_admin', 'admin', 'staff']);
        case 'household_management':
            return in_array($role, ['main_admin', 'admin', 'staff']);
        case 'evacuation_center_management':
            return in_array($role, ['main_admin', 'admin']);
        case 'id_card_generation':
            return in_array($role, ['main_admin', 'admin']);
        case 'reports':
            return in_array($role, ['main_admin', 'admin']);
        case 'barangay_management':
            return $role === 'main_admin';
        default:
            return false;
    }
}

// New function to check if user can create users with specific role
function canCreateRole($targetRole) {
    $currentRole = getUserRole();
    
    switch ($currentRole) {
        case 'main_admin':
            return in_array($targetRole, ['admin', 'staff']);
        case 'admin':
            return $targetRole === 'staff';
        default:
            return false;
    }
}

// New function to get user's barangay ID
function getUserBarangayId() {
    return $_SESSION['barangay_id'] ?? null;
}

// New function to check if user can access barangay data
function canAccessBarangay($barangayId) {
    $userRole = getUserRole();
    $userBarangayId = getUserBarangayId();
    
    // Main admin can access all barangays
    if ($userRole === 'main_admin') {
        return true;
    }
    
    // Admin and staff can only access their assigned barangay
    return $userBarangayId == $barangayId;
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function logActivity($action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null) {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $userId = $_SESSION['user_id'] ?? null;
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $query = "INSERT INTO activity_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent) 
              VALUES (:user_id, :action, :table_name, :record_id, :old_values, :new_values, :ip_address, :user_agent)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':action', $action);
    $stmt->bindParam(':table_name', $tableName);
    $stmt->bindParam(':record_id', $recordId);
    $stmt->bindParam(':old_values', $oldValues);
    $stmt->bindParam(':new_values', $newValues);
    $stmt->bindParam(':ip_address', $ipAddress);
    $stmt->bindParam(':user_agent', $userAgent);
    
    return $stmt->execute();
}
?> 