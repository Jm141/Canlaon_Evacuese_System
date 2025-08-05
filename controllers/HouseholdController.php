<?php
require_once 'config/config.php';

class HouseholdController extends Controller {
    public function index() {
        $this->requirePermission('resident_management');
        
        $barangayId = $this->getUserBarangayId();
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $evacuationCenter = $_GET['evacuation_center'] ?? '';
        
        $householdModel = new Household();
        $filters = [
            'barangay_id' => $barangayId,
            'search' => $search,
            'evacuation_center' => $evacuationCenter
        ];
        
        $households = $householdModel->getHouseholdsWithPagination($page, ITEMS_PER_PAGE, $filters);
        $totalHouseholds = $householdModel->countHouseholds($filters);
        
        $pagination = $this->getPaginationData($page, $totalHouseholds);
        
        // Get evacuation centers for filter
        $evacuationCenters = $householdModel->getEvacuationCentersByBarangay($barangayId);
        
        $this->render('households/index', [
            'pageTitle' => 'Households',
            'currentPage' => 'households',
            'user' => $this->user,
            'households' => $households,
            'pagination' => $pagination,
            'filters' => $filters,
            'evacuationCenters' => $evacuationCenters
        ]);
    }
    
    public function add() {
        $this->requirePermission('resident_management');
        
        $barangayModel = new Barangay();
        $barangayId = $this->getUserBarangayId();
        $barangays = $barangayModel->getAllBarangays();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'household_head', 'address', 'barangay_id', 'phone_number'
            ]);
            
            if (empty($errors)) {
                $householdModel = new Household();
                try {
                    $householdId = $householdModel->createHousehold($data);
                    if ($householdId) {
                        $this->setFlashMessage('success', 'Household added successfully!');
                        redirect('households.php?action=view&id=' . $householdId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to add household.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('households/add', [
            'pageTitle' => 'Add Household',
            'currentPage' => 'households',
            'user' => $this->user,
            'barangays' => $barangays,
            'userBarangayId' => $barangayId,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function edit() {
        $this->requirePermission('resident_management');
        
        $householdId = $_GET['id'] ?? null;
        if (!$householdId) {
            redirect('households.php');
        }
        
        $householdModel = new Household();
        $household = $householdModel->getById($householdId);
        
        if (!$household) {
            $this->setFlashMessage('error', 'Household not found.');
            redirect('households.php');
        }
        
        $barangayModel = new Barangay();
        $barangays = $barangayModel->getAllBarangays();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'household_head', 'address', 'barangay_id', 'phone_number'
            ]);
            
            if (empty($errors)) {
                try {
                    $result = $householdModel->updateHousehold($householdId, $data);
                    if ($result) {
                        $this->setFlashMessage('success', 'Household updated successfully!');
                        redirect('households.php?action=view&id=' . $householdId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to update household.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('households/edit', [
            'pageTitle' => 'Edit Household',
            'currentPage' => 'households',
            'user' => $this->user,
            'household' => $household,
            'barangays' => $barangays,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function view() {
        $this->requirePermission('resident_management');
        
        $householdId = $_GET['id'] ?? null;
        if (!$householdId) {
            redirect('households.php');
        }
        
        $householdModel = new Household();
        $household = $householdModel->getHouseholdWithDetails($householdId);
        
        if (!$household) {
            $this->setFlashMessage('error', 'Household not found.');
            redirect('households.php');
        }
        
        // Get household members
        $residentModel = new Resident();
        $householdMembers = $residentModel->getHouseholdMembers($householdId);
        
        // Get ID cards for household members
        $idCardModel = new IdCard();
        $idCards = $idCardModel->getIdCardsByHousehold($householdId);
        
        $this->render('households/view', [
            'pageTitle' => 'View Household',
            'currentPage' => 'households',
            'user' => $this->user,
            'household' => $household,
            'householdMembers' => $householdMembers,
            'idCards' => $idCards
        ]);
    }
    
    public function delete() {
        $this->requirePermission('resident_management');
        
        $householdId = $_POST['household_id'] ?? null;
        if (!$householdId) {
            $this->jsonResponse(['success' => false, 'message' => 'Household ID is required.'], 400);
        }
        
        $householdModel = new Household();
        try {
            $result = $householdModel->deleteHousehold($householdId);
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Household deleted successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete household.'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function search() {
        $this->requirePermission('resident_management');
        
        $searchTerm = $_GET['q'] ?? '';
        $barangayId = $this->getUserBarangayId();
        
        $householdModel = new Household();
        $results = $householdModel->searchHouseholds($searchTerm, $barangayId, 10);
        
        $this->jsonResponse(['results' => $results]);
    }

    /**
     * Toggle household status (placeholder - not implemented for households)
     */
    public function toggleStatus() {
        $this->requirePermission('resident_management');
        $this->jsonResponse(['success' => false, 'message' => 'Status toggle not available for households.'], 400);
    }

    /**
     * Reset password (placeholder - not applicable for households)
     */
    public function resetPassword() {
        $this->requirePermission('resident_management');
        $this->jsonResponse(['success' => false, 'message' => 'Password reset not applicable for households.'], 400);
    }
}
