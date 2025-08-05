<?php
require_once 'config/config.php';

class DashboardController extends Controller {
    public function index() {
        $barangayId = $this->getUserBarangayId();
        
        // Load models
        $residentModel = new Resident();
        $householdModel = new Household();
        $idCardModel = new IdCard();
        
        // Get statistics
        $residentStats = $residentModel->getBarangayStatistics($barangayId);
        $householdStats = $householdModel->getBarangayStatistics($barangayId);
        $idCardStats = $idCardModel->getBarangayStatistics($barangayId);
        
        // Get recent activities
        $recentResidents = $residentModel->getResidentsByBarangay($barangayId, 1, 5);
        $recentHouseholds = $householdModel->getHouseholdsByBarangay($barangayId, 1, 5);
        
        // Get age and gender distribution
        $ageDistribution = $residentModel->getResidentsByAgeGroup($barangayId);
        $genderDistribution = $residentModel->getResidentsByGender($barangayId);
        
        // Get special needs residents
        $specialNeedsResidents = $residentModel->getResidentsWithSpecialNeeds($barangayId);
        
        // Get evacuation centers
        $evacuationCenters = $householdModel->getEvacuationCentersByBarangay($barangayId);
        
        $this->render('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'currentPage' => 'dashboard',
            'user' => $this->user,
            'residentStats' => $residentStats,
            'householdStats' => $householdStats,
            'idCardStats' => $idCardStats,
            'recentResidents' => $recentResidents,
            'recentHouseholds' => $recentHouseholds,
            'ageDistribution' => $ageDistribution,
            'genderDistribution' => $genderDistribution,
            'specialNeedsResidents' => $specialNeedsResidents,
            'evacuationCenters' => $evacuationCenters
        ]);
    }
}
