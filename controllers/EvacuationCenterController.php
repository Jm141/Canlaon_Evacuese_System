<?php
require_once 'config/config.php';

class EvacuationCenterController extends Controller {
    public function index() {
        $this->requirePermission('evacuation_center_management');
        
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $barangayId = $_GET['barangay_id'] ?? '';
        
        $evacuationCenterModel = new EvacuationCenter();
        $filters = [
            'search' => $search,
            'barangay_id' => $barangayId
        ];
        
        $evacuationCenters = $evacuationCenterModel->getAllEvacuationCenters($page, ITEMS_PER_PAGE, $filters);
        $totalCenters = $evacuationCenterModel->countEvacuationCenters($filters);
        
        $pagination = $this->getPaginationData($page, $totalCenters);
        
        // Get barangays for filter dropdown
        $barangayModel = new Barangay();
        $barangays = $barangayModel->getAllBarangays();
        
        $this->render('evacuation-centers/index', [
            'pageTitle' => 'Evacuation Center Management',
            'currentPage' => 'evacuation-centers',
            'user' => $this->user,
            'evacuationCenters' => $evacuationCenters,
            'totalCenters' => $totalCenters,
            'barangays' => $barangays,
            'pagination' => $pagination,
            'filters' => $filters
        ]);
    }
    
    public function add() {
        $this->requirePermission('evacuation_center_management');
        
        // Get barangays for dropdown
        $barangayModel = new Barangay();
        $barangays = $barangayModel->getAllBarangays();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'name', 'barangay_id', 'address', 'capacity'
            ]);
            
            // Validate capacity is positive
            if (!empty($data['capacity']) && $data['capacity'] <= 0) {
                $errors[] = 'Capacity must be greater than 0.';
            }
            
            if (empty($errors)) {
                $evacuationCenterModel = new EvacuationCenter();
                try {
                    $centerId = $evacuationCenterModel->createEvacuationCenter($data);
                    if ($centerId) {
                        $this->setFlashMessage('success', 'Evacuation center added successfully!');
                        redirect('evacuation-centers.php?action=view&id=' . $centerId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to add evacuation center.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('evacuation-centers/add', [
            'pageTitle' => 'Add Evacuation Center',
            'currentPage' => 'evacuation-centers',
            'user' => $this->user,
            'barangays' => $barangays,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function edit() {
        $this->requirePermission('evacuation_center_management');
        
        $centerId = $_GET['id'] ?? null;
        if (!$centerId) {
            redirect('evacuation-centers.php');
        }
        
        $evacuationCenterModel = new EvacuationCenter();
        $center = $evacuationCenterModel->getById($centerId);
        
        if (!$center) {
            $this->setFlashMessage('error', 'Evacuation center not found.');
            redirect('evacuation-centers.php');
        }
        
        // Get barangays for dropdown
        $barangayModel = new Barangay();
        $barangays = $barangayModel->getAllBarangays();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateRequired($data, [
                'name', 'barangay_id', 'address', 'capacity'
            ]);
            
            // Validate capacity is not less than current occupancy
            if (!empty($data['capacity']) && $data['capacity'] < $center['current_occupancy']) {
                $errors[] = 'Capacity cannot be less than current occupancy (' . $center['current_occupancy'] . ').';
            }
            
            if (empty($errors)) {
                try {
                    $result = $evacuationCenterModel->updateEvacuationCenter($centerId, $data);
                    if ($result) {
                        $this->setFlashMessage('success', 'Evacuation center updated successfully!');
                        redirect('evacuation-centers.php?action=view&id=' . $centerId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to update evacuation center.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode(', ', $errors));
            }
        }
        
        $this->render('evacuation-centers/edit', [
            'pageTitle' => 'Edit Evacuation Center',
            'currentPage' => 'evacuation-centers',
            'user' => $this->user,
            'center' => $center,
            'barangays' => $barangays,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    public function view() {
        $this->requirePermission('evacuation_center_management');
        
        $centerId = $_GET['id'] ?? null;
        if (!$centerId) {
            redirect('evacuation-centers.php');
        }
        
        $evacuationCenterModel = new EvacuationCenter();
        $center = $evacuationCenterModel->getEvacuationCenterWithHouseholds($centerId);
        
        if (!$center) {
            $this->setFlashMessage('error', 'Evacuation center not found.');
            redirect('evacuation-centers.php');
        }
        
        $this->render('evacuation-centers/view', [
            'pageTitle' => 'View Evacuation Center',
            'currentPage' => 'evacuation-centers',
            'user' => $this->user,
            'center' => $center
        ]);
    }
    
    public function delete() {
        $this->requirePermission('evacuation_center_management');
        
        $centerId = $_POST['center_id'] ?? null;
        if (!$centerId) {
            $this->jsonResponse(['success' => false, 'message' => 'Evacuation center ID is required.'], 400);
        }
        
        $evacuationCenterModel = new EvacuationCenter();
        try {
            $result = $evacuationCenterModel->deleteEvacuationCenter($centerId);
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Evacuation center deleted successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to delete evacuation center.'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function autoAssign() {
        $this->requirePermission('evacuation_center_management');
        
        $barangayId = $_POST['barangay_id'] ?? null;
        if (!$barangayId) {
            $this->jsonResponse(['success' => false, 'message' => 'Barangay ID is required.'], 400);
        }
        
        $evacuationCenterModel = new EvacuationCenter();
        $householdModel = new Household();
        
        try {
            // Get unassigned households for the barangay
            $unassignedHouseholds = $householdModel->where([
                'barangay_id' => $barangayId,
                'assigned_evacuation_center' => null
            ]);
            
            $assignedCount = 0;
            $failedCount = 0;
            
            foreach ($unassignedHouseholds as $household) {
                try {
                    $evacuationCenterModel->autoAssignHousehold($household['id'], $barangayId);
                    $assignedCount++;
                } catch (Exception $e) {
                    $failedCount++;
                }
            }
            
            $message = "Auto-assignment completed. {$assignedCount} households assigned successfully.";
            if ($failedCount > 0) {
                $message .= " {$failedCount} households could not be assigned due to insufficient capacity.";
            }
            
            $this->jsonResponse(['success' => true, 'message' => $message]);
            
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function reassign() {
        $this->requirePermission('evacuation_center_management');
        
        $householdId = $_POST['household_id'] ?? null;
        $newCenterId = $_POST['center_id'] ?? null;
        
        if (!$householdId || !$newCenterId) {
            $this->jsonResponse(['success' => false, 'message' => 'Household ID and Center ID are required.'], 400);
        }
        
        $evacuationCenterModel = new EvacuationCenter();
        try {
            $result = $evacuationCenterModel->reassignHousehold($householdId, $newCenterId);
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Household reassigned successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to reassign household.'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function search() {
        $this->requirePermission('evacuation_center_management');
        
        $searchTerm = $_GET['q'] ?? '';
        
        $evacuationCenterModel = new EvacuationCenter();
        $results = $evacuationCenterModel->searchEvacuationCenters($searchTerm, 10);
        
        $this->jsonResponse(['results' => $results]);
    }
}
?> 