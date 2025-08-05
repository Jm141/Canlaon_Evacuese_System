<?php
require_once 'config/config.php';

class ResidentController extends Controller {
    public function index() {
        $this->requirePermission('resident_management');
        
        $barangayId = $this->getUserBarangayId();
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $gender = $_GET['gender'] ?? '';
        $ageMin = $_GET['age_min'] ?? '';
        $ageMax = $_GET['age_max'] ?? '';
        
        $residentModel = new Resident();
        $filters = [
            'search' => $search,
            'gender' => $gender,
            'age_min' => $ageMin,
            'age_max' => $ageMax
        ];
        
        $residents = $residentModel->getResidentsByBarangay($barangayId, $page, ITEMS_PER_PAGE, $filters);
        $totalResidents = $residentModel->countResidentsByBarangay($barangayId, $filters);
        
        $pagination = $this->getPaginationData($page, $totalResidents);
        
        $this->render('residents/index', [
            'pageTitle' => 'Residents',
            'currentPage' => 'residents',
            'user' => $this->user,
            'residents' => $residents,
            'totalResidents' => $totalResidents,
            'pagination' => $pagination,
            'filters' => $filters
        ]);
    }
    
    public function add() {
        $this->requirePermission('resident_management');
        
        // Check if user has barangay assignment
        $userBarangayId = $this->getUserBarangayId();
        $barangays = null;
        
        // If user doesn't have barangay assignment, get all barangays for selection
        if (!$userBarangayId) {
            $barangayModel = new Barangay();
            $barangays = $barangayModel->getAllBarangays();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $requiredFields = ['first_name', 'last_name', 'date_of_birth', 'gender', 'civil_status', 'address'];
            
            // Add barangay_id to required fields if user doesn't have barangay assignment
            if (!$userBarangayId) {
                $requiredFields[] = 'barangay_id';
            }
            
            $errors = $this->validateRequired($data, $requiredFields);
            
            if (empty($errors)) {
                $residentModel = new Resident();
                try {
                    $residentId = $residentModel->createResident($data);
                    if ($residentId) {
                        $this->setFlashMessage('success', 'Resident added successfully! A new household was automatically created.');
                        redirect('residents.php?action=view&id=' . $residentId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to add resident.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('residents/add', [
            'pageTitle' => 'Add Resident',
            'currentPage' => 'residents',
            'user' => $this->user,
            'barangays' => $barangays,
            'userBarangayId' => $userBarangayId,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function edit() {
        $this->requirePermission('resident_management');
        
        $residentId = $_GET['id'] ?? null;
        if (!$residentId) {
            redirect('residents.php');
        }
        
        $residentModel = new Resident();
        $resident = $residentModel->getResidentWithDetails($residentId);
        
        if (!$resident) {
            $this->setFlashMessage('error', 'Resident not found.');
            redirect('residents.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'first_name', 'last_name', 'date_of_birth', 'gender', 
                'civil_status', 'address'
            ]);
            
            if (empty($errors)) {
                try {
                    $result = $residentModel->updateResident($residentId, $data);
                    if ($result) {
                        $this->setFlashMessage('success', 'Resident updated successfully!');
                        redirect('residents.php?action=view&id=' . $residentId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to update resident.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('residents/edit', [
            'pageTitle' => 'Edit Resident',
            'currentPage' => 'residents',
            'user' => $this->user,
            'resident' => $resident,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function view() {
        $this->requirePermission('resident_management');
        
        $residentId = $_GET['id'] ?? null;
        if (!$residentId) {
            redirect('residents.php');
        }
        
        $residentModel = new Resident();
        $resident = $residentModel->getResidentWithDetails($residentId);
        
        if (!$resident) {
            $this->setFlashMessage('error', 'Resident not found.');
            redirect('residents.php');
        }
        
        // Get household members
        $householdMembers = $residentModel->getHouseholdMembers($resident['household_id']);
        
        // Get ID card if exists
        $idCardModel = new IdCard();
        $idCard = $idCardModel->getActiveCardByResident($residentId);
        
        $this->render('residents/view', [
            'pageTitle' => 'View Resident',
            'currentPage' => 'residents',
            'user' => $this->user,
            'resident' => $resident,
            'householdMembers' => $householdMembers,
            'idCard' => $idCard
        ]);
    }
    
    public function delete() {
        $this->requirePermission('resident_management');
        
        $residentId = $_POST['resident_id'] ?? null;
        if (!$residentId) {
            $this->jsonResponse(['success' => false, 'message' => 'Resident ID is required.'], 400);
        }
        
        $residentModel = new Resident();
        try {
            $result = $residentModel->deleteResident($residentId);
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Resident deleted successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete resident.'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function search() {
        $this->requirePermission('resident_management');
        
        $searchTerm = $_GET['q'] ?? '';
        $barangayId = $this->getUserBarangayId();
        
        $residentModel = new Resident();
        $results = $residentModel->searchResidents($searchTerm, $barangayId, 10);
        
        $this->jsonResponse(['results' => $results]);
    }
    
    public function addFamilyMember() {
        $this->requirePermission('resident_management');
        
        $barangayId = $this->getUserBarangayId();
        $residentModel = new Resident();
        $households = $residentModel->getAvailableHouseholds($barangayId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'first_name', 'last_name', 'date_of_birth', 'gender', 
                'civil_status', 'household_id'
            ]);
            
            if (empty($errors)) {
                try {
                    $residentId = $residentModel->addResidentToHousehold($data, $data['household_id']);
                    if ($residentId) {
                        $this->setFlashMessage('success', 'Family member added successfully!');
                        redirect('residents.php?action=view&id=' . $residentId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to add family member.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('residents/add-family-member', [
            'pageTitle' => 'Add Family Member',
            'currentPage' => 'residents',
            'user' => $this->user,
            'households' => $households,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
}
