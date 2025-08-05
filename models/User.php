<?php
/**
 * User Model
 * Handles user authentication and management
 */

class User extends Model {
    protected $table = 'users';
    protected $fillable = [
        'username', 'password', 'email', 'full_name', 'role', 
        'barangay_id', 'is_active', 'created_by'
    ];

    /**
     * Authenticate user
     */
    public function authenticate($username, $password) {
        $user = $this->whereFirst(['username' => $username, 'is_active' => 1]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['barangay_id'] = $user['barangay_id'];
            $_SESSION['login_time'] = time();
            
            // Log activity
            logActivity('user_login', 'users', $user['id']);
            
            return $user;
        }
        
        return false;
    }

    /**
     * Create user with validation
     */
    public function createUser($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Set created_by
        $data['created_by'] = $_SESSION['user_id'] ?? null;
        
        // Validate username uniqueness
        if ($this->usernameExists($data['username'])) {
            throw new Exception('Username already exists');
        }
        
        // Validate email uniqueness
        if ($this->emailExists($data['email'])) {
            throw new Exception('Email already exists');
        }
        
        return $this->create($data);
    }

    /**
     * Update user with validation
     */
    public function updateUser($userId, $data) {
        // Validate user exists
        $user = $this->getById($userId);
        if (!$user) {
            throw new Exception('User not found');
        }
        
        // Validate username uniqueness if changed
        if (isset($data['username']) && $data['username'] !== $user['username']) {
            if ($this->usernameExists($data['username'], $userId)) {
                throw new Exception('Username already exists');
            }
        }
        
        // Validate email uniqueness if changed
        if (isset($data['email']) && $data['email'] !== $user['email']) {
            if ($this->emailExists($data['email'], $userId)) {
                throw new Exception('Email already exists');
            }
        }
        
        return $this->update($userId, $data);
    }

    /**
     * Update user password
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password' => $hashedPassword]);
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role, $barangayId = null) {
        $conditions = ['role' => $role, 'is_active' => 1];
        
        if ($barangayId) {
            $conditions['barangay_id'] = $barangayId;
        }
        
        return $this->where($conditions);
    }

    /**
     * Get admin users for a specific barangay
     */
    public function getBarangayAdmins($barangayId) {
        return $this->where([
            'role' => 'admin',
            'barangay_id' => $barangayId,
            'is_active' => 1
        ]);
    }

    /**
     * Get user with barangay information
     */
    public function getUserWithBarangay($userId) {
        $sql = "SELECT u.*, b.name as barangay_name, b.code as barangay_code 
                FROM users u 
                LEFT JOIN barangays b ON u.barangay_id = b.id 
                WHERE u.id = :id";
        
        return $this->queryFirst($sql, ['id' => $userId]);
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";
        $params = ['username' => $username];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->queryFirst($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $params = ['email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->queryFirst($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Get users with pagination
     */
    public function getUsersWithPagination($page = 1, $limit = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        $whereConditions = ['is_active' => 1];
        $params = [];
        
        if (!empty($filters['role'])) {
            $whereConditions['role'] = $filters['role'];
        }
        
        if (!empty($filters['barangay_id'])) {
            $whereConditions['barangay_id'] = $filters['barangay_id'];
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $sql = "SELECT u.*, b.name as barangay_name 
                    FROM users u 
                    LEFT JOIN barangays b ON u.barangay_id = b.id 
                    WHERE u.is_active = 1 
                    AND (u.username LIKE :search OR u.full_name LIKE :search OR u.email LIKE :search)";
            
            if (!empty($filters['role'])) {
                $sql .= " AND u.role = :role";
                $params['role'] = $filters['role'];
            }
            
            if (!empty($filters['barangay_id'])) {
                $sql .= " AND u.barangay_id = :barangay_id";
                $params['barangay_id'] = $filters['barangay_id'];
            }
            
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['search'] = "%{$search}%";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
            
            return $this->query($sql, $params);
        }
        
        return $this->where($whereConditions, $limit, $offset);
    }

    /**
     * Count users with filters
     */
    public function countUsers($filters = []) {
        $whereConditions = ['is_active' => 1];
        
        if (!empty($filters['role'])) {
            $whereConditions['role'] = $filters['role'];
        }
        
        if (!empty($filters['barangay_id'])) {
            $whereConditions['barangay_id'] = $filters['barangay_id'];
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $sql = "SELECT COUNT(*) as count FROM users u 
                    WHERE u.is_active = 1 
                    AND (u.username LIKE :search OR u.full_name LIKE :search OR u.email LIKE :search)";
            
            $params = ['search' => "%{$search}%"];
            
            if (!empty($filters['role'])) {
                $sql .= " AND u.role = :role";
                $params['role'] = $filters['role'];
            }
            
            if (!empty($filters['barangay_id'])) {
                $sql .= " AND u.barangay_id = :barangay_id";
                $params['barangay_id'] = $filters['barangay_id'];
            }
            
            $result = $this->queryFirst($sql, $params);
            return $result['count'];
        }
        
        return $this->count($whereConditions);
    }

    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            logActivity('user_logout', 'users', $_SESSION['user_id']);
        }
        
        // Destroy session
        session_destroy();
        session_start();
    }

    /**
     * Check if user session is still valid
     */
    public function isSessionValid() {
        if (!isset($_SESSION['login_time'])) {
            return false;
        }
        
        $sessionLifetime = SESSION_LIFETIME;
        $currentTime = time();
        
        if (($currentTime - $_SESSION['login_time']) > $sessionLifetime) {
            $this->logout();
            return false;
        }
        
        // Update login time
        $_SESSION['login_time'] = $currentTime;
        return true;
    }

    /**
     * Get all users (alias for getUsersWithPagination)
     */
    public function getAllUsers($page = 1, $limit = ITEMS_PER_PAGE, $filters = []) {
        return $this->getUsersWithPagination($page, $limit, $filters);
    }

    /**
     * Search users
     */
    public function searchUsers($searchTerm, $limit = 20) {
        $sql = "SELECT u.*, b.name as barangay_name
                FROM users u
                LEFT JOIN barangays b ON u.barangay_id = b.id
                WHERE u.is_active = 1
                AND (u.username LIKE :search OR u.full_name LIKE :search OR u.email LIKE :search)
                ORDER BY u.full_name ASC
                LIMIT :limit";
        
        return $this->query($sql, [
            'search' => "%{$searchTerm}%",
            'limit' => $limit
        ]);
    }
}
?> 