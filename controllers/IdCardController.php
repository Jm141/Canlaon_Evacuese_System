<?php
require_once 'config/config.php';

class IdCardController extends Controller {
    public function index() {
        $this->requireAdmin();
        
        $barangayId = $this->getBarangayFilter();
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        
        // For main admin, allow filtering by specific barangay
        if (isMainAdmin() && !empty($_GET['barangay_id'])) {
            $barangayId = $_GET['barangay_id'];
        }
        
        $idCardModel = new IdCard();
        $filters = ['search' => $search];
        
        $idCards = $idCardModel->getIdCardsByBarangay($barangayId, $page);
        $totalCards = $idCardModel->countIdCardsByBarangay($barangayId);
        
        $pagination = $this->getPaginationData($page, $totalCards);
        
        // Get barangays for filter (only for main admin)
        $barangays = [];
        if (isMainAdmin()) {
            $barangayModel = new Barangay();
            $barangays = $barangayModel->getAllBarangays();
        }
        
        // Build query string for pagination
        $queryParams = [];
        if (!empty($search)) $queryParams['search'] = $search;
        if (!empty($_GET['status'])) $queryParams['status'] = $_GET['status'];
        if (!empty($_GET['barangay_id'])) $queryParams['barangay_id'] = $_GET['barangay_id'];
        $queryString = !empty($queryParams) ? '&' . http_build_query($queryParams) : '';
        
        $this->render('id-cards/index', [
            'pageTitle' => 'ID Cards',
            'currentPage' => 'id-cards',
            'user' => $this->user,
            'idCards' => $idCards,
            'totalCards' => $totalCards,
            'pagination' => $pagination,
            'search' => $search,
            'barangays' => $barangays,
            'csrf_token' => $this->generateCSRFToken(),
            'queryString' => $queryString
        ]);
    }
    
    public function generate() {
        $this->requireAdmin();
        
        $residentId = $_GET['resident_id'] ?? null;
        $residentModel = new Resident();
        $idCardModel = new IdCard();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $residentId = $_POST['resident_id'] ?? null;
            
            if ($residentId) {
                try {
                    $cardId = $idCardModel->generateIdCard($residentId, $_SESSION['user_id']);
                    if ($cardId) {
                        $this->setFlashMessage('success', 'ID Card generated successfully!');
                        redirect('id-cards.php?action=view&id=' . $cardId);
                    } else {
                        $this->setFlashMessage('error', 'Failed to generate ID card.');
                    }
                } catch (Exception $e) {
                    $this->setFlashMessage('error', $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', 'Please select a resident.');
            }
        }
        
        // Get residents without ID cards
        $barangayId = $this->getBarangayFilter();
        $residents = $residentModel->getResidentsByBarangay($barangayId, 1, 100);
        
        // Filter out residents who already have active ID cards
        $residentsWithoutCards = [];
        foreach ($residents as $resident) {
            $existingCard = $idCardModel->getActiveCardByResident($resident['id']);
            if (!$existingCard) {
                $residentsWithoutCards[] = $resident;
            }
        }
        
        $this->render('id-cards/generate', [
            'pageTitle' => 'Generate ID Card',
            'currentPage' => 'id-cards',
            'user' => $this->user,
            'residents' => $residentsWithoutCards,
            'selectedResidentId' => $residentId
        ]);
    }
    
    public function generateAll() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
        }
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['csrf_token'])) {
            $this->jsonResponse(['success' => false, 'message' => 'CSRF token required.'], 400);
        }
        
        // Validate CSRF token
        if (!$this->validateCSRFToken($input['csrf_token'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 403);
        }
        
        try {
            $residentModel = new Resident();
            $idCardModel = new IdCard();
            
            // Get all residents in the user's barangay
            $barangayId = $this->getUserBarangayId();
            $residents = $residentModel->getResidentsByBarangay($barangayId, 1, 1000); // Get up to 1000 residents
            
            $generatedCount = 0;
            $errors = [];
            
            foreach ($residents as $resident) {
                try {
                    // Check if resident already has an active ID card
                    $existingCard = $idCardModel->getActiveCardByResident($resident['id']);
                    if (!$existingCard) {
                        // Generate ID card for this resident
                        $cardId = $idCardModel->generateIdCard($resident['id'], $_SESSION['user_id']);
                        if ($cardId) {
                            $generatedCount++;
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = "Failed to generate ID card for {$resident['first_name']} {$resident['last_name']}: " . $e->getMessage();
                }
            }
            
            $message = "Successfully generated {$generatedCount} ID cards.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', $errors);
            }
            
            $this->jsonResponse([
                'success' => true,
                'generated' => $generatedCount,
                'message' => $message
            ]);
            
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function printAll() {
        $this->requireAdmin();
        
        $idCardModel = new IdCard();
        $barangayId = $this->getBarangayFilter();
        
        // Get all active ID cards for the barangay
        $idCards = $idCardModel->getActiveIdCardsByBarangay($barangayId);
        
        // Generate barcodes for all cards
        $barcodeGenerator = new BarcodeGenerator();
        foreach ($idCards as &$card) {
            $card['barcode_image'] = $barcodeGenerator->createBarcodeImage($card['card_number']);
        }
        
        $this->render('id-cards/print-all', [
            'pageTitle' => 'Print All ID Cards',
            'currentPage' => 'id-cards',
            'user' => $this->user,
            'idCards' => $idCards
        ]);
    }
    
    public function view() {
        $this->requireAdmin();
        
        $cardId = $_GET['id'] ?? null;
        if (!$cardId) {
            redirect('id-cards.php');
        }
        
        $idCardModel = new IdCard();
        $idCard = $idCardModel->getIdCardWithDetails($cardId);
        
        if (!$idCard) {
            $this->setFlashMessage('error', 'ID Card not found.');
            redirect('id-cards.php');
        }
        
        // Generate barcode
        $barcodeGenerator = new BarcodeGenerator();
        $barcodeImage = $barcodeGenerator->createBarcodeImage($idCard['card_number']);
        
        $this->render('id-cards/view', [
            'pageTitle' => 'View ID Card',
            'currentPage' => 'id-cards',
            'user' => $this->user,
            'idCard' => $idCard,
            'barcodeImage' => $barcodeImage
        ]);
    }
    
    public function cancel() {
        $this->requireAdmin();
        
        $cardId = $_POST['card_id'] ?? null;
        if (!$cardId) {
            $this->jsonResponse(['success' => false, 'message' => 'Card ID is required.'], 400);
        }
        
        $idCardModel = new IdCard();
        try {
            $result = $idCardModel->cancelIdCard($cardId);
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'ID Card cancelled successfully.']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Failed to cancel ID card.'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function search() {
        $this->requireAdmin();
        
        $searchTerm = $_GET['q'] ?? '';
        $barangayId = $this->getBarangayFilter();
        
        $idCardModel = new IdCard();
        $results = $idCardModel->searchIdCards($searchTerm, $barangayId, 10);
        
        $this->jsonResponse(['results' => $results]);
    }
}
