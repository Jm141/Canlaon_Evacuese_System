<?php
/**
 * Base Controller Class
 * All controllers extend this class for common functionality
 */

abstract class Controller {
    protected $user;
    protected $userModel;
    protected $barangayModel;

    public function __construct() {
        // Check if user is logged in
        if (!isLoggedIn()) {
            redirect('login.php');
        }

        // Check session validity
        $this->userModel = new User();
        if (!$this->userModel->isSessionValid()) {
            redirect('login.php');
        }

        // Load user data
        $this->user = $this->userModel->getUserWithBarangay($_SESSION['user_id']);
        $this->barangayModel = new Barangay();
    }

    /**
     * Render view with data
     */
    protected function render($view, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Include the view file
        $viewPath = APP_ROOT . "/views/{$view}.php";
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new Exception("View {$view} not found");
        }
    }

    /**
     * Render JSON response
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Redirect to another page
     */
    protected function redirect($url) {
        redirect($url);
    }

    /**
     * Check if user has permission
     */
    protected function requirePermission($permission) {
        if (!hasPermission($permission)) {
            $this->render('error/403', [
                'message' => 'You do not have permission to access this page.'
            ]);
            exit();
        }
    }

    /**
     * Check if user is main admin
     */
    protected function requireMainAdmin() {
        if (!isMainAdmin()) {
            $this->render('error/403', [
                'message' => 'This page is only accessible to Main Administrators.'
            ]);
            exit();
        }
    }

    /**
     * Check if user is admin or main admin
     */
    protected function requireAdmin() {
        if (!isAdmin() && !isMainAdmin()) {
            $this->render('error/403', [
                'message' => 'This page is only accessible to Administrators.'
            ]);
            exit();
        }
    }

    /**
     * Get user's barangay ID
     */
    protected function getUserBarangayId() {
        return $_SESSION['barangay_id'] ?? null;
    }

    /**
     * Validate CSRF token
     */
    protected function validateCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $this->render('error/403', [
                    'message' => 'Invalid CSRF token. Please try again.'
                ]);
                exit();
            }
        }
    }

    /**
     * Generate CSRF token
     */
    protected function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Handle file upload
     */
    protected function handleFileUpload($file, $allowedTypes = ALLOWED_IMAGE_TYPES, $maxSize = MAX_FILE_SIZE) {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception('Invalid file parameter');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload failed');
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('File too large');
        }

        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, $allowedTypes)) {
            throw new Exception('File type not allowed');
        }

        // Create upload directory if it doesn't exist
        if (!is_dir(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0755, true);
        }

        // Generate unique filename
        $filename = uniqid() . '.' . $extension;
        $filepath = UPLOAD_PATH . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to move uploaded file');
        }

        return $filename;
    }

    /**
     * Pagination helper
     */
    protected function getPaginationData($currentPage, $totalItems, $itemsPerPage = ITEMS_PER_PAGE) {
        $totalPages = ceil($totalItems / $itemsPerPage);
        $currentPage = max(1, min($currentPage, $totalPages));
        
        return [
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'items_per_page' => $itemsPerPage,
            'offset' => ($currentPage - 1) * $itemsPerPage,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_page' => $currentPage - 1,
            'next_page' => $currentPage + 1
        ];
    }

    /**
     * Sanitize input data
     */
    protected function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return sanitizeInput($data);
    }

    /**
     * Validate required fields
     */
    protected function validateRequired($data, $requiredFields) {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        return $errors;
    }

    /**
     * Validate email format
     */
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate date format
     */
    protected function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Get flash message
     */
    protected function getFlashMessage($key) {
        $message = $_SESSION['flash_messages'][$key] ?? null;
        unset($_SESSION['flash_messages'][$key]);
        return $message;
    }

    /**
     * Set flash message
     */
    protected function setFlashMessage($key, $message) {
        $_SESSION['flash_messages'][$key] = $message;
    }

    /**
     * Log activity
     */
    protected function logActivity($action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null) {
        logActivity($action, $tableName, $recordId, $oldValues, $newValues);
    }
}
?> 