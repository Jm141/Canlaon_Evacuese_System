<?php
require_once 'config/config.php';

class ReportController extends Controller {
    public function index() {
        $this->requirePermission('reports');
        
        $barangayId = $this->getBarangayFilter();
        $reportType = $_GET['type'] ?? 'overview';
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01'); // First day of current month
        $dateTo = $_GET['date_to'] ?? date('Y-m-d'); // Today
        $format = $_GET['format'] ?? 'html';
        
        // For main admin, allow filtering by specific barangay
        if (isMainAdmin() && !empty($_GET['barangay_id'])) {
            $barangayId = $_GET['barangay_id'];
        }
        
        $residentModel = new Resident();
        $householdModel = new Household();
        $idCardModel = new IdCard();
        
        $data = [];
        
        switch ($reportType) {
            case 'overview':
                $data = $this->getOverviewReport($barangayId, $dateFrom, $dateTo);
                break;
            case 'residents':
                $data = $this->getResidentsReport($barangayId, $dateFrom, $dateTo);
                break;
            case 'households':
                $data = $this->getHouseholdsReport($barangayId, $dateFrom, $dateTo);
                break;
            case 'id_cards':
                $data = $this->getIdCardsReport($barangayId, $dateFrom, $dateTo);
                break;
            case 'special_needs':
                $data = $this->getSpecialNeedsReport($barangayId);
                break;
            case 'evacuation':
                $data = $this->getEvacuationReport($barangayId);
                break;
            case 'demographics':
                $data = $this->getDemographicsReport($barangayId);
                break;
            default:
                $data = $this->getOverviewReport($barangayId, $dateFrom, $dateTo);
                break;
        }
        
        if ($format === 'pdf') {
            $this->generatePDFReport($reportType, $data, $dateFrom, $dateTo);
        } elseif ($format === 'excel') {
            $this->generateExcelReport($reportType, $data, $dateFrom, $dateTo);
        } else {
            // Get barangays for filter (only for main admin)
            $barangays = [];
            if (isMainAdmin()) {
                $barangayModel = new Barangay();
                $barangays = $barangayModel->getAllBarangays();
            }
            
            $this->render('reports/index', [
                'pageTitle' => 'Reports',
                'currentPage' => 'reports',
                'user' => $this->user,
                'reportType' => $reportType,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'data' => $data,
                'barangays' => $barangays
            ]);
        }
    }
    
    private function getOverviewReport($barangayId, $dateFrom, $dateTo) {
        $residentModel = new Resident();
        $householdModel = new Household();
        $idCardModel = new IdCard();
        
        return [
            'total_residents' => $residentModel->countResidentsByBarangay($barangayId),
            'total_households' => $householdModel->countHouseholdsByBarangay($barangayId),
            'active_id_cards' => $idCardModel->countActiveCardsByBarangay($barangayId),
            'special_needs' => $residentModel->countResidentsWithSpecialNeeds($barangayId),
            'recent_additions' => [
                'residents' => $residentModel->getResidentsByDateRange($barangayId, $dateFrom, $dateTo),
                'households' => $householdModel->getHouseholdsByDateRange($barangayId, $dateFrom, $dateTo),
                'id_cards' => $idCardModel->getIdCardsByDateRange($barangayId, $dateFrom, $dateTo)
            ],
            'age_distribution' => $residentModel->getResidentsByAgeGroup($barangayId),
            'gender_distribution' => $residentModel->getResidentsByGender($barangayId)
        ];
    }
    
    private function getResidentsReport($barangayId, $dateFrom, $dateTo) {
        $residentModel = new Resident();
        
        return [
            'residents' => $residentModel->getResidentsByDateRange($barangayId, $dateFrom, $dateTo, 1, 1000),
            'total_count' => $residentModel->countResidentsByDateRange($barangayId, $dateFrom, $dateTo),
            'by_gender' => $residentModel->getResidentsByGender($barangayId),
            'by_age_group' => $residentModel->getResidentsByAgeGroup($barangayId),
            'by_civil_status' => $residentModel->getResidentsByCivilStatus($barangayId),
            'special_needs' => $residentModel->getResidentsWithSpecialNeeds($barangayId)
        ];
    }
    
    private function getHouseholdsReport($barangayId, $dateFrom, $dateTo) {
        $householdModel = new Household();
        
        return [
            'households' => $householdModel->getHouseholdsByDateRange($barangayId, $dateFrom, $dateTo, 1, 1000),
            'total_count' => $householdModel->countHouseholdsByDateRange($barangayId, $dateFrom, $dateTo),
            'by_evacuation_center' => $householdModel->getHouseholdsByEvacuationCenter($barangayId),
            'by_collection_point' => $householdModel->getHouseholdsByCollectionPoint($barangayId),
            'evacuation_centers' => $householdModel->getEvacuationCentersByBarangay($barangayId)
        ];
    }
    
    private function getIdCardsReport($barangayId, $dateFrom, $dateTo) {
        $idCardModel = new IdCard();
        
        return [
            'id_cards' => $idCardModel->getIdCardsByDateRange($barangayId, $dateFrom, $dateTo, 1, 1000),
            'total_count' => $idCardModel->countIdCardsByDateRange($barangayId, $dateFrom, $dateTo),
            'active_cards' => $idCardModel->countActiveCardsByBarangay($barangayId),
            'expired_cards' => $idCardModel->countExpiredCardsByBarangay($barangayId),
            'by_status' => $idCardModel->getIdCardsByStatus($barangayId),
            'expiring_soon' => $idCardModel->getExpiringCards($barangayId, 30) // Next 30 days
        ];
    }
    
    private function getSpecialNeedsReport($barangayId) {
        $residentModel = new Resident();
        
        return [
            'special_needs_residents' => $residentModel->getResidentsWithSpecialNeeds($barangayId, 1, 1000),
            'total_count' => $residentModel->countResidentsWithSpecialNeeds($barangayId),
            'by_type' => $residentModel->getSpecialNeedsByType($barangayId),
            'by_age_group' => $residentModel->getSpecialNeedsByAgeGroup($barangayId),
            'by_gender' => $residentModel->getSpecialNeedsByGender($barangayId)
        ];
    }
    
    private function getEvacuationReport($barangayId) {
        $householdModel = new Household();
        $residentModel = new Resident();
        
        return [
            'evacuation_centers' => $householdModel->getEvacuationCentersByBarangay($barangayId),
            'collection_points' => $householdModel->getCollectionPointsByBarangay($barangayId),
            'households_by_center' => $householdModel->getHouseholdsByEvacuationCenter($barangayId),
            'residents_by_center' => $residentModel->getResidentsByEvacuationCenter($barangayId),
            'special_needs_by_center' => $residentModel->getSpecialNeedsByEvacuationCenter($barangayId),
            'vehicle_assignments' => $householdModel->getVehicleAssignments($barangayId)
        ];
    }
    
    private function getDemographicsReport($barangayId) {
        $residentModel = new Resident();
        
        return [
            'age_distribution' => $residentModel->getResidentsByAgeGroup($barangayId),
            'gender_distribution' => $residentModel->getResidentsByGender($barangayId),
            'civil_status_distribution' => $residentModel->getResidentsByCivilStatus($barangayId),
            'educational_attainment' => $residentModel->getResidentsByEducation($barangayId),
            'occupation_distribution' => $residentModel->getResidentsByOccupation($barangayId),
            'religion_distribution' => $residentModel->getResidentsByReligion($barangayId)
        ];
    }
    
    private function generatePDFReport($reportType, $data, $dateFrom, $dateTo) {
        // This would require a PDF library like TCPDF or FPDF
        // For now, we'll redirect to HTML view with print styles
        $this->setFlashMessage('info', 'PDF generation feature coming soon. Use browser print function for now.');
        redirect('reports.php?type=' . $reportType . '&date_from=' . $dateFrom . '&date_to=' . $dateTo);
    }
    
    private function generateExcelReport($reportType, $data, $dateFrom, $dateTo) {
        // This would require a library like PhpSpreadsheet
        // For now, we'll redirect to HTML view
        $this->setFlashMessage('info', 'Excel export feature coming soon.');
        redirect('reports.php?type=' . $reportType . '&date_from=' . $dateFrom . '&date_to=' . $dateTo);
    }
    
    public function export() {
        $this->requirePermission('reports');
        
        $type = $_GET['type'] ?? 'residents';
        $format = $_GET['format'] ?? 'csv';
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');
        $barangayId = $this->getBarangayFilter();
        
        switch ($type) {
            case 'residents':
                $this->exportResidents($barangayId, $format, $dateFrom, $dateTo);
                break;
            case 'households':
                $this->exportHouseholds($barangayId, $format, $dateFrom, $dateTo);
                break;
            case 'id_cards':
                $this->exportIdCards($barangayId, $format, $dateFrom, $dateTo);
                break;
            case 'special_needs':
                $this->exportSpecialNeeds($barangayId, $format);
                break;
            default:
                $this->exportResidents($barangayId, $format, $dateFrom, $dateTo);
                break;
        }
    }
    
    private function exportResidents($barangayId, $format, $dateFrom, $dateTo) {
        $residentModel = new Resident();
        $residents = $residentModel->getResidentsByDateRange($barangayId, $dateFrom, $dateTo, 1, 10000);
        
        $filename = 'residents_report_' . date('Y-m-d') . '.csv';
        $this->outputCSV($filename, $residents, [
            'ID' => 'id',
            'First Name' => 'first_name',
            'Last Name' => 'last_name',
            'Middle Name' => 'middle_name',
            'Date of Birth' => 'date_of_birth',
            'Age' => 'age',
            'Gender' => 'gender',
            'Civil Status' => 'civil_status',
            'Contact Number' => 'contact_number',
            'Email' => 'email',
            'Special Needs' => 'has_special_needs',
            'Special Needs Description' => 'special_needs_description',
            'Household Head' => 'household_head',
            'Address' => 'address',
            'Barangay' => 'barangay_name',
            'Created Date' => 'created_at'
        ]);
    }
    
    private function exportHouseholds($barangayId, $format, $dateFrom, $dateTo) {
        $householdModel = new Household();
        $households = $householdModel->getHouseholdsByDateRange($barangayId, $dateFrom, $dateTo, 1, 10000);
        
        $filename = 'households_report_' . date('Y-m-d') . '.csv';
        $this->outputCSV($filename, $households, [
            'ID' => 'id',
            'Household Head' => 'household_head',
            'Address' => 'address',
            'Barangay' => 'barangay_name',
            'Collection Point' => 'collection_point',
            'Evacuation Vehicle' => 'evacuation_vehicle',
            'Vehicle Driver' => 'vehicle_driver',
            'Assigned Evacuation Center' => 'assigned_evacuation_center',
            'Phone Number' => 'phone_number',
            'Control Number' => 'control_number',
            'Created Date' => 'created_at'
        ]);
    }
    
    private function exportIdCards($barangayId, $format, $dateFrom, $dateTo) {
        $idCardModel = new IdCard();
        $idCards = $idCardModel->getIdCardsByDateRange($barangayId, $dateFrom, $dateTo, 1, 10000);
        
        $filename = 'id_cards_report_' . date('Y-m-d') . '.csv';
        $this->outputCSV($filename, $idCards, [
            'ID' => 'id',
            'Card Number' => 'card_number',
            'Resident Name' => 'resident_name',
            'Issue Date' => 'issue_date',
            'Expiry Date' => 'expiry_date',
            'Status' => 'status',
            'Generated By' => 'generated_by_name',
            'Created Date' => 'created_at'
        ]);
    }
    
    private function exportSpecialNeeds($barangayId, $format) {
        $residentModel = new Resident();
        $residents = $residentModel->getResidentsWithSpecialNeeds($barangayId, 1, 10000);
        
        $filename = 'special_needs_report_' . date('Y-m-d') . '.csv';
        $this->outputCSV($filename, $residents, [
            'ID' => 'id',
            'First Name' => 'first_name',
            'Last Name' => 'last_name',
            'Middle Name' => 'middle_name',
            'Date of Birth' => 'date_of_birth',
            'Age' => 'age',
            'Gender' => 'gender',
            'Special Needs Description' => 'special_needs_description',
            'Contact Number' => 'contact_number',
            'Emergency Contact' => 'emergency_contact_name',
            'Emergency Contact Number' => 'emergency_contact_number',
            'Household Head' => 'household_head',
            'Address' => 'address',
            'Barangay' => 'barangay_name'
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