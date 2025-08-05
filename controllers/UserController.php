<?php
require_once 'config/config.php';

class UserController extends Controller {
    public function index() {
        $this->requirePermission('user_management');
        
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $userModel = new User();
        $filters = [
            'search' => $search,
            'role' => $role,
            'status' => $status
        ];
        
        $users = $userModel->getAllUsers($page, ITEMS_PER_PAGE, $filters);
        $totalUsers = $userModel->countUsers($filters);
        
        $pagination = $this->getPaginationData($page, $totalUsers);
        
        $this->render('users/index', [
            'pageTitle' => 'User Management',
            'currentPage' => 'users',
            'user' => $this->user,
            'users' => $users,
            'totalUsers' => $totalUsers,
            'pagination' => $pagination,
            'filters' => $filters
        ]);
    }
    
    public function add() {
        $this->requirePermission('user_management');
        
        $barangayModel = new Barangay();
        $barangays = $barangayModel->getAllBarangays();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'username', 'email', 'full_name', 'role', 'password', 'confirm_password'
            ]);
            
            if ($data['password'] !== $data['confirm_password']) {
                $errors[] = 'Passwords do not match.';
            }
            
            if (strlen($data['password']) < 6) {
                $errors[] = 'Password must be at least 6 characters long.';
            }
            
            if (empty($errors)) {
                $userModel = new User();
                try {
                    $userId = $userModel->createUser($data);
                    if ($userId) {
                        $this->setFlashMessage('success', 'User added successfully!');
                        redirect('users.php?action=view&id=' . $userId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to add user.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('users/add', [
            'pageTitle' => 'Add User',
            'currentPage' => 'users',
            'user' => $this->user,
            'barangays' => $barangays,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function edit() {
        $this->requirePermission('user_management');
        
        $userId = $_GET['id'] ?? null;
        if (!$userId) {
            redirect('users.php');
        }
        
        $userModel = new User();
        $user = $userModel->getById($userId);
        
        if (!$user) {
            $this->setFlashMessage('error', 'User not found.');
            redirect('users.php');
        }
        
        $barangayModel = new Barangay();
        $barangays = $barangayModel->getAllBarangays();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'username', 'email', 'full_name', 'role'
            ]);
            
            if (empty($errors)) {
                try {
                    $result = $userModel->updateUser($userId, $data);
                    if ($result) {
                        $this->setFlashMessage('success', 'User updated successfully!');
                        redirect('users.php?action=view&id=' . $userId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to update user.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('users/edit', [
            'pageTitle' => 'Edit User',
            'currentPage' => 'users',
            'user' => $this->user,
            'editUser' => $user,
            'barangays' => $barangays,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function view() {
        $this->requirePermission('user_management');
        
        $userId = $_GET['id'] ?? null;
        if (!$userId) {
            redirect('users.php');
        }
        
        $userModel = new User();
        $user = $userModel->getUserWithDetails($userId);
        
        if (!$user) {
            $this->setFlashMessage('error', 'User not found.');
            redirect('users.php');
        }
        
        $activityLogs = $userModel->getUserActivityLogs($userId, 1, 10);
        
        $this->render('users/view', [
            'pageTitle' => 'View User',
            'currentPage' => 'users',
            'user' => $this->user,
            'viewUser' => $user,
            'activityLogs' => $activityLogs
        ]);
    }
    
    public function delete() {
        $this->requirePermission('user_management');
        
        $userId = $_POST['user_id'] ?? null;
        if (!$userId) {
            $this->jsonResponse(['success' => false, 'message' => 'User ID is required.'], 400);
        }
        
        if ($userId == $this->user['id']) {
            $this->jsonResponse(['success' => false, 'message' => 'You cannot delete your own account.'], 400);
        }
        
        $userModel = new User();
        try {
            $result = $userModel->deleteUser($userId);
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'User deleted successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete user.'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function toggleStatus() {
        $this->requirePermission('user_management');
        
        $userId = $_POST['user_id'] ?? null;
        if (!$userId) {
            $this->jsonResponse(['success' => false, 'message' => 'User ID is required.'], 400);
        }
        
        if ($userId == $this->user['id']) {
            $this->jsonResponse(['success' => false, 'message' => 'You cannot deactivate your own account.'], 400);
        }
        
        $userModel = new User();
        try {
            $result = $userModel->toggleUserStatus($userId);
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'User status updated successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to update user status.'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function resetPassword() {
        $this->requirePermission('user_management');
        
        $userId = $_POST['user_id'] ?? null;
        if (!$userId) {
            $this->jsonResponse(['success' => false, 'message' => 'User ID is required.'], 400);
        }
        
        $userModel = new User();
        try {
            $newPassword = $userModel->resetUserPassword($userId);
            if ($newPassword) {
                $this->jsonResponse([
                    'success' => true, 
                    'message' => 'Password reset successfully.',
                    'new_password' => $newPassword
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to reset password.'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function search() {
        $this->requirePermission('user_management');
        
        $searchTerm = $_GET['q'] ?? '';
        
        $userModel = new User();
        $results = $userModel->searchUsers($searchTerm, 10);
        
        $this->jsonResponse(['results' => $results]);
    }
} 