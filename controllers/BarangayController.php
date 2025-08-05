<?php
require_once 'config/config.php';

class BarangayController extends Controller {
    public function index() {
        $this->requirePermission('user_management');
        
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $barangayModel = new Barangay();
        $filters = [
            'search' => $search,
            'status' => $status
        ];
        
        $barangays = $barangayModel->getAllBarangays($page, ITEMS_PER_PAGE, $filters);
        $totalBarangays = $barangayModel->countBarangays($filters);
        
        $pagination = $this->getPaginationData($page, $totalBarangays);
        
        $this->render('barangays/index', [
            'pageTitle' => 'Barangay Management',
            'currentPage' => 'barangays',
            'user' => $this->user,
            'barangays' => $barangays,
            'totalBarangays' => $totalBarangays,
            'pagination' => $pagination,
            'filters' => $filters
        ]);
    }
    
    public function add() {
        $this->requirePermission('user_management');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'name', 'code'
            ]);
            
            // Validate unique code
            $barangayModel = new Barangay();
            if ($barangayModel->isCodeExists($data['code'])) {
                $errors[] = 'Barangay code already exists.';
            }
            
            if (empty($errors)) {
                try {
                    $barangayId = $barangayModel->createBarangay($data);
                    if ($barangayId) {
                        $this->setFlashMessage('success', 'Barangay added successfully!');
                        redirect('barangays.php?action=view&id=' . $barangayId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to add barangay.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('barangays/add', [
            'pageTitle' => 'Add Barangay',
            'currentPage' => 'barangays',
            'user' => $this->user,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function edit() {
        $this->requirePermission('user_management');
        
        $barangayId = $_GET['id'] ?? null;
        if (!$barangayId) {
            redirect('barangays.php');
        }
        
        $barangayModel = new Barangay();
        $barangay = $barangayModel->getById($barangayId);
        
        if (!$barangay) {
            $this->setFlashMessage('error', 'Barangay not found.');
            redirect('barangays.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'name', 'code'
            ]);
            
            // Validate unique code (excluding current barangay)
            if ($barangayModel->isCodeExists($data['code'], $barangayId)) {
                $errors[] = 'Barangay code already exists.';
            }
            
            if (empty($errors)) {
                try {
                    $result = $barangayModel->updateBarangay($barangayId, $data);
                    if ($result) {
                        $this->setFlashMessage('success', 'Barangay updated successfully!');
                        redirect('barangays.php?action=view&id=' . $barangayId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to update barangay.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('barangays/edit', [
            'pageTitle' => 'Edit Barangay',
            'currentPage' => 'barangays',
            'user' => $this->user,
            'barangay' => $barangay,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function view() {
        $this->requirePermission('user_management');
        
        $barangayId = $_GET['id'] ?? null;
        if (!$barangayId) {
            redirect('barangays.php');
        }
        
        $barangayModel = new Barangay();
        $barangay = $barangayModel->getBarangayWithDetails($barangayId);
        
        if (!$barangay) {
            $this->setFlashMessage('error', 'Barangay not found.');
            redirect('barangays.php');
        }
        
        // Get barangay statistics
        $residentModel = new Resident();
        $householdModel = new Household();
        $userModel = new User();
        
        $residentStats = $residentModel->getBarangayStatistics($barangayId);
        $householdStats = $householdModel->getBarangayStatistics($barangayId);
        $userStats = $userModel->getBarangayStatistics($barangayId);
        
        // Get recent activities
        $recentResidents = $residentModel->getResidentsByBarangay($barangayId, 1, 5);
        $recentHouseholds = $householdModel->getHouseholdsByBarangay($barangayId, 1, 5);
        
        $this->render('barangays/view', [
            'pageTitle' => 'View Barangay',
            'currentPage' => 'barangays',
            'user' => $this->user,
            'barangay' => $barangay,
            'residentStats' => $residentStats,
            'householdStats' => $householdStats,
            'userStats' => $userStats,
            'recentResidents' => $recentResidents,
            'recentHouseholds' => $recentHouseholds
        ]);
    }
    
    public function delete() {
        $this->requirePermission('user_management');
        
        $barangayId = $_POST['barangay_id'] ?? null;
        if (!$barangayId) {
            $this->jsonResponse(['success' => false, 'message' => 'Barangay ID is required.'], 400);
        }
        
        $barangayModel = new Barangay();
        try {
            $result = $barangayModel->deleteBarangay($barangayId);
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Barangay deleted successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete barangay.'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function toggleStatus() {
        $this->requirePermission('user_management');
        
        $barangayId = $_POST['barangay_id'] ?? null;
        if (!$barangayId) {
            $this->jsonResponse(['success' => false, 'message' => 'Barangay ID is required.'], 400);
        }
        
        $barangayModel = new Barangay();
        try {
            $result = $barangayModel->toggleBarangayStatus($barangayId);
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Barangay status updated successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to update barangay status.'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function search() {
        $this->requirePermission('user_management');
        
        $searchTerm = $_GET['q'] ?? '';
        
        $barangayModel = new Barangay();
        $results = $barangayModel->searchBarangays($searchTerm, 10);
        
        $this->jsonResponse(['results' => $results]);
    }
}
