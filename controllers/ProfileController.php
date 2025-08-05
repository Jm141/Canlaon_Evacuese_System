<?php
require_once 'config/config.php';

class ProfileController extends Controller {
    public function index() {
        $userModel = new User();
        $user = $userModel->getById($this->user['id']);
        
        // Get user activity logs
        $activityLogs = $userModel->getUserActivityLogs($this->user['id'], 1, 10);
        
        $this->render('profile/index', [
            'pageTitle' => 'My Profile',
            'currentPage' => 'profile',
            'user' => $this->user,
            'profileData' => $user,
            'activityLogs' => $activityLogs
        ]);
    }
    
    public function edit() {
        $userModel = new User();
        $user = $userModel->getById($this->user['id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'full_name', 'email'
            ]);
            
            // Validate email uniqueness (excluding current user)
            if ($userModel->isEmailExists($data['email'], $this->user['id'])) {
                $errors[] = 'Email address already exists.';
            }
            
            if (empty($errors)) {
                try {
                    $result = $userModel->updateProfile($this->user['id'], $data);
                    if ($result) {
                        // Update session data
                        $_SESSION['full_name'] = $data['full_name'];
                        $_SESSION['email'] = $data['email'];
                        
                        $this->setFlashMessage('success', 'Profile updated successfully!');
                        redirect('profile.php');
                    } else {
                        $this->setFlashMessage('error', 'Failed to update profile.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('profile/edit', [
            'pageTitle' => 'Edit Profile',
            'currentPage' => 'profile',
            'user' => $this->user,
            'profileData' => $user,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'current_password', 'new_password', 'confirm_password'
            ]);
            
            // Validate password confirmation
            if ($data['new_password'] !== $data['confirm_password']) {
                $errors[] = 'New passwords do not match.';
            }
            
            // Validate password strength
            if (strlen($data['new_password']) < 6) {
                $errors[] = 'New password must be at least 6 characters long.';
            }
            
            if (empty($errors)) {
                $userModel = new User();
                try {
                    $result = $userModel->changePassword(
                        $this->user['id'], 
                        $data['current_password'], 
                        $data['new_password']
                    );
                    
                    if ($result) {
                        $this->setFlashMessage('success', 'Password changed successfully!');
                        redirect('profile.php');
                    } else {
                        $this->setFlashMessage('error', 'Current password is incorrect.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('profile/change_password', [
            'pageTitle' => 'Change Password',
            'currentPage' => 'profile',
            'user' => $this->user,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function activityLogs() {
        $page = $_GET['page'] ?? 1;
        $userModel = new User();
        $activityLogs = $userModel->getUserActivityLogs($this->user['id'], $page, ITEMS_PER_PAGE);
        $totalLogs = $userModel->countUserActivityLogs($this->user['id']);
        
        $pagination = $this->getPaginationData($page, $totalLogs);
        
        $this->render('profile/activity_logs', [
            'pageTitle' => 'Activity Logs',
            'currentPage' => 'profile',
            'user' => $this->user,
            'activityLogs' => $activityLogs,
            'pagination' => $pagination
        ]);
    }
    
    public function exportActivityLogs() {
        $userModel = new User();
        $activityLogs = $userModel->getUserActivityLogs($this->user['id'], 1, 10000);
        
        $filename = 'activity_logs_' . date('Y-m-d') . '.csv';
        $this->outputCSV($filename, $activityLogs, [
            'Date' => 'created_at',
            'Action' => 'action',
            'Table' => 'table_name',
            'Record ID' => 'record_id',
            'IP Address' => 'ip_address',
            'User Agent' => 'user_agent'
        ]);
    }
    
    private function outputCSV($filename, $data, $columns) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        // Write headers
        fputcsv($output, array_keys($columns));
        
        // Write data
        foreach ($data as $row) {
            $csvRow = [];
            foreach ($columns as $column) {
                $csvRow[] = $row[$column] ?? '';
            }
            fputcsv($output, $csvRow);
        }
        
        fclose($output);
        exit();
    }
}
