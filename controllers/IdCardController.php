<?php
require_once 'config/config.php';

class IdCardController extends Controller {
    public function index() {
        $this->requireAdmin();
        
        $barangayId = $this->getUserBarangayId();
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        
        $idCardModel = new IdCard();
        $filters = ['search' => $search];
        
        $idCards = $idCardModel->getIdCardsByBarangay($barangayId, $page);
        $totalCards = $idCardModel->countIdCardsByBarangay($barangayId);
        
        $pagination = $this->getPaginationData($page, $totalCards);
        
        // Get barangays for filter
        $barangayModel = new Barangay();
        $barangays = $barangayModel->getAllBarangays();
        
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
        $barangayId = $this->getUserBarangayId();
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
        $barangayId = $this->getUserBarangayId();
        
        $idCardModel = new IdCard();
        $results = $idCardModel->searchIdCards($searchTerm, $barangayId, 10);
        
        $this->jsonResponse(['results' => $results]);
    }
}
