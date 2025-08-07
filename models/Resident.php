<?php
/**
 * Resident Model
 * Handles resident and household management
 */

class Resident extends Model {
    protected $table = 'residents';
    protected $fillable = [
        'household_id', 'first_name', 'last_name', 'middle_name', 'date_of_birth',
        'gender', 'civil_status', 'nationality', 'religion', 'occupation',
        'educational_attainment', 'contact_number', 'email', 'emergency_contact_name',
        'emergency_contact_number', 'emergency_contact_relationship', 'has_special_needs',
        'special_needs_description', 'is_household_head', 'is_active', 'created_by'
    ];

    /**
     * Get resident with household and barangay information
     */
    public function getResidentWithDetails($residentId) {
        $sql = "SELECT r.*, h.household_head, h.address, h.collection_point, 
                       h.evacuation_vehicle, h.vehicle_driver, h.assigned_evacuation_center,
                       h.phone_number as household_phone, h.control_number,
                       b.name as barangay_name, b.code as barangay_code
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                WHERE r.id = :id AND r.is_active = 1";
        
        return $this->queryFirst($sql, ['id' => $residentId]);
    }

    /**
     * Get residents by barangay with pagination
     */
    public function getResidentsByBarangay($barangayId, $page = 1, $limit = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT r.*, h.household_head, h.address, h.control_number,
                       b.name as barangay_name
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                WHERE r.is_active = 1";
        
        $params = [];
        
        // Add barangay filter only if specified (main admin can see all)
        if ($barangayId !== null) {
            $sql .= " AND h.barangay_id = :barangay_id";
            $params['barangay_id'] = $barangayId;
        }
        
        // Add search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (r.first_name LIKE :search OR r.last_name LIKE :search 
                          OR h.household_head LIKE :search OR h.control_number LIKE :search)";
            $params['search'] = "%{$filters['search']}%";
        }
        
        // Add gender filter
        if (!empty($filters['gender'])) {
            $sql .= " AND r.gender = :gender";
            $params['gender'] = $filters['gender'];
        }
        
        // Add age range filter
        if (!empty($filters['age_min']) || !empty($filters['age_max'])) {
            $sql .= " AND r.date_of_birth IS NOT NULL";
            if (!empty($filters['age_min'])) {
                $sql .= " AND r.date_of_birth <= :age_min_date";
                $params['age_min_date'] = date('Y-m-d', strtotime("-{$filters['age_min']} years"));
            }
            if (!empty($filters['age_max'])) {
                $sql .= " AND r.date_of_birth >= :age_max_date";
                $params['age_max_date'] = date('Y-m-d', strtotime("-{$filters['age_max']} years"));
            }
        }
        
        $sql .= " ORDER BY r.last_name, r.first_name LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->query($sql, $params);
    }

    /**
     * Count residents by barangay
     */
    public function countResidentsByBarangay($barangayId, $filters = []) {
        $sql = "SELECT COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE r.is_active = 1";
        
        $params = [];
        
        // Add barangay filter only if specified (main admin can see all)
        if ($barangayId !== null) {
            $sql .= " AND h.barangay_id = :barangay_id";
            $params['barangay_id'] = $barangayId;
        }
        
        // Add search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (r.first_name LIKE :search OR r.last_name LIKE :search 
                          OR h.household_head LIKE :search OR h.control_number LIKE :search)";
            $params['search'] = "%{$filters['search']}%";
        }
        
        // Add gender filter
        if (!empty($filters['gender'])) {
            $sql .= " AND r.gender = :gender";
            $params['gender'] = $filters['gender'];
        }
        
        // Add age range filter
        if (!empty($filters['age_min']) || !empty($filters['age_max'])) {
            $sql .= " AND r.date_of_birth IS NOT NULL";
            if (!empty($filters['age_min'])) {
                $sql .= " AND r.date_of_birth <= :age_min_date";
                $params['age_min_date'] = date('Y-m-d', strtotime("-{$filters['age_min']} years"));
            }
            if (!empty($filters['age_max'])) {
                $sql .= " AND r.date_of_birth >= :age_max_date";
                $params['age_max_date'] = date('Y-m-d', strtotime("-{$filters['age_max']} years"));
            }
        }
        
        $result = $this->queryFirst($sql, $params);
        return $result['count'] ?? 0;
    }

    /**
     * Get household members
     */
    public function getHouseholdMembers($householdId) {
        $sql = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) as age
                FROM residents r
                WHERE r.household_id = :household_id AND r.is_active = 1
                ORDER BY r.last_name, r.first_name";
        
        return $this->query($sql, ['household_id' => $householdId]);
    }
    
    /**
     * Count household members
     */
    public function countHouseholdMembers($householdId) {
        $sql = "SELECT COUNT(*) as count 
                FROM residents 
                WHERE household_id = :household_id AND is_active = 1";
        
        $result = $this->queryFirst($sql, ['household_id' => $householdId]);
        return $result['count'] ?? 0;
    }

    /**
     * Get residents by household (alias for getHouseholdMembers)
     */
    public function getResidentsByHousehold($householdId) {
        $sql = "SELECT r.*, TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) as age
                FROM residents r
                WHERE r.household_id = :household_id AND r.is_active = 1
                ORDER BY r.last_name, r.first_name";
        
        return $this->query($sql, ['household_id' => $householdId]);
    }

    /**
     * Get household head
     */
    public function getHouseholdHead($householdId) {
        return $this->whereFirst([
            'household_id' => $householdId,
            'is_household_head' => 1,
            'is_active' => 1
        ]);
    }

    /**
     * Get residents with special needs by barangay
     */
    public function getResidentsWithSpecialNeeds($barangayId) {
        $sql = "SELECT r.*, h.household_head, h.address, h.control_number,
                       b.name as barangay_name
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                WHERE h.barangay_id = :barangay_id 
                AND r.has_special_needs = 1 
                AND r.is_active = 1
                ORDER BY r.last_name, r.first_name";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get residents by age group
     */
    public function getResidentsByAgeGroup($barangayId) {
        $sql = "SELECT 
                    CASE 
                        WHEN TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) < 18 THEN 'Minor (0-17)'
                        WHEN TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) BETWEEN 18 AND 59 THEN 'Adult (18-59)'
                        ELSE 'Senior (60+)'
                    END as age_group,
                    COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id AND r.is_active = 1
                GROUP BY age_group
                ORDER BY age_group";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get residents by gender
     */
    public function getResidentsByGender($barangayId) {
        $sql = "SELECT r.gender, COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id AND r.is_active = 1
                GROUP BY r.gender
                ORDER BY r.gender";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Search residents
     */
    public function searchResidents($searchTerm, $barangayId = null, $limit = 20) {
        $sql = "SELECT r.*, h.household_head, h.address, h.control_number,
                       b.name as barangay_name
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                WHERE r.is_active = 1
                AND (r.first_name LIKE :search OR r.last_name LIKE :search 
                     OR h.household_head LIKE :search OR h.control_number LIKE :search)";
        
        $params = ['search' => "%{$searchTerm}%"];
        
        if ($barangayId !== null) {
            $sql .= " AND h.barangay_id = :barangay_id";
            $params['barangay_id'] = $barangayId;
        }
        
        $sql .= " ORDER BY r.last_name, r.first_name LIMIT :limit";
        $params['limit'] = $limit;
        
        return $this->query($sql, $params);
    }

    /**
     * Create resident with automatic household creation
     */
    public function createResident($data) {
        // Set created_by
        $data['created_by'] = $_SESSION['user_id'] ?? null;
        
        // Get user's barangay ID - allow override if user doesn't have barangay assignment
        $barangayId = $data['barangay_id'] ?? $_SESSION['barangay_id'] ?? null;
        if (!$barangayId) {
            throw new Exception('User barangay assignment not found. Please select a barangay.');
        }
        
        // Start transaction
        $this->beginTransaction();
        
        try {
            // Create household automatically
            $householdModel = new Household();
            $householdData = [
                'household_head' => $data['first_name'] . ' ' . $data['last_name'],
                'address' => $data['address'] ?? 'Address to be updated',
                'barangay_id' => $barangayId,
                'phone_number' => $data['contact_number'] ?? '',
                'collection_point' => $data['collection_point'] ?? '',
                'evacuation_vehicle' => $data['evacuation_vehicle'] ?? '',
                'vehicle_driver' => $data['vehicle_driver'] ?? '',
                'assigned_evacuation_center' => $data['assigned_evacuation_center'] ?? '',
                'created_by' => $data['created_by']
            ];
            
            $householdId = $householdModel->createHousehold($householdData);
            if (!$householdId) {
                throw new Exception('Failed to create household');
            }
            
            // Set household_id for resident
            $data['household_id'] = $householdId;
            $data['is_household_head'] = 1; // First resident is always household head
            
            // Create resident
            $residentId = $this->create($data);
            
            if (!$residentId) {
                throw new Exception('Failed to create resident');
            }
            
            // Auto-assign evacuation center if not specified
            if (empty($data['assigned_evacuation_center'])) {
                $evacuationCenterModel = new EvacuationCenter();
                try {
                    $evacuationCenter = $evacuationCenterModel->autoAssignHousehold($householdId, $barangayId);
                    if ($evacuationCenter) {
                        // Update household data with assigned center
                        $householdModel->update($householdId, [
                            'assigned_evacuation_center' => $evacuationCenter['name'],
                            'collection_point' => $evacuationCenter['address']
                        ]);
                    }
                } catch (Exception $e) {
                    // Log the error but don't fail the resident creation
                    error_log("Failed to auto-assign evacuation center: " . $e->getMessage());
                }
            }
            
            // Commit transaction
            $this->commit();
            
            return $residentId;
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Update resident and associated household
     */
    public function updateResident($residentId, $data) {
        // Validate resident exists
        $resident = $this->getById($residentId);
        if (!$resident) {
            throw new Exception('Resident not found');
        }
        
        // Start transaction
        $this->beginTransaction();
        
        try {
            // Update resident data
            $residentData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'middle_name' => $data['middle_name'] ?? '',
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'civil_status' => $data['civil_status'],
                'nationality' => $data['nationality'] ?? 'Filipino',
                'religion' => $data['religion'] ?? '',
                'occupation' => $data['occupation'] ?? '',
                'educational_attainment' => $data['educational_attainment'] ?? '',
                'contact_number' => $data['contact_number'] ?? '',
                'email' => $data['email'] ?? '',
                'emergency_contact_name' => $data['emergency_contact_name'] ?? '',
                'emergency_contact_number' => $data['emergency_contact_number'] ?? '',
                'emergency_contact_relationship' => $data['emergency_contact_relationship'] ?? '',
                'has_special_needs' => $data['has_special_needs'] ?? 0,
                'special_needs_description' => $data['special_needs_description'] ?? ''
            ];
            
            $residentResult = $this->update($residentId, $residentData);
            
            // Update household data if this is the household head
            if ($resident['is_household_head']) {
                $householdModel = new Household();
                $householdData = [
                    'household_head' => $data['first_name'] . ' ' . $data['last_name'],
                    'address' => $data['address'],
                    'phone_number' => $data['contact_number'] ?? '',
                    'collection_point' => $data['collection_point'] ?? '',
                    'evacuation_vehicle' => $data['evacuation_vehicle'] ?? '',
                    'vehicle_driver' => $data['vehicle_driver'] ?? '',
                    'assigned_evacuation_center' => $data['assigned_evacuation_center'] ?? ''
                ];
                
                $householdResult = $householdModel->update($resident['household_id'], $householdData);
                
                if (!$householdResult) {
                    throw new Exception('Failed to update household information');
                }
            }
            
            // Commit transaction
            $this->commit();
            
            return $residentResult;
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Delete resident (soft delete)
     */
    public function deleteResident($residentId) {
        return $this->update($residentId, ['is_active' => 0]);
    }

    /**
     * Get resident statistics by barangay
     */
    public function getBarangayStatistics($barangayId) {
        $sql = "SELECT 
                    COUNT(*) as total_residents,
                    SUM(CASE WHEN r.gender = 'male' THEN 1 ELSE 0 END) as male_count,
                    SUM(CASE WHEN r.gender = 'female' THEN 1 ELSE 0 END) as female_count,
                    SUM(CASE WHEN r.has_special_needs = 1 THEN 1 ELSE 0 END) as special_needs_count,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) < 18 THEN 1 ELSE 0 END) as minors_count,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) >= 60 THEN 1 ELSE 0 END) as seniors_count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id AND r.is_active = 1";
        
        return $this->queryFirst($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get residents by evacuation center
     */
    public function getResidentsByEvacuationCenter($barangayId) {
        $sql = "SELECT h.assigned_evacuation_center, COUNT(*) as resident_count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id 
                AND r.is_active = 1
                AND h.assigned_evacuation_center IS NOT NULL 
                AND h.assigned_evacuation_center != ''
                GROUP BY h.assigned_evacuation_center
                ORDER BY resident_count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get special needs residents by evacuation center
     */
    public function getSpecialNeedsByEvacuationCenter($barangayId) {
        $sql = "SELECT h.assigned_evacuation_center, COUNT(*) as special_needs_count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id 
                AND r.is_active = 1
                AND r.has_special_needs = 1
                AND h.assigned_evacuation_center IS NOT NULL 
                AND h.assigned_evacuation_center != ''
                GROUP BY h.assigned_evacuation_center
                ORDER BY special_needs_count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get residents by civil status
     */
    public function getResidentsByCivilStatus($barangayId) {
        $sql = "SELECT r.civil_status, COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id AND r.is_active = 1
                GROUP BY r.civil_status
                ORDER BY count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get residents by education
     */
    public function getResidentsByEducation($barangayId) {
        $sql = "SELECT r.educational_attainment, COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id AND r.is_active = 1
                GROUP BY r.educational_attainment
                ORDER BY count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get residents by occupation
     */
    public function getResidentsByOccupation($barangayId) {
        $sql = "SELECT r.occupation, COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id AND r.is_active = 1
                GROUP BY r.occupation
                ORDER BY count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get residents by religion
     */
    public function getResidentsByReligion($barangayId) {
        $sql = "SELECT r.religion, COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id AND r.is_active = 1
                GROUP BY r.religion
                ORDER BY count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get special needs by type
     */
    public function getSpecialNeedsByType($barangayId) {
        $sql = "SELECT r.special_needs_type, COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id 
                AND r.is_active = 1
                AND r.has_special_needs = 1
                GROUP BY r.special_needs_type
                ORDER BY count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get special needs by age group
     */
    public function getSpecialNeedsByAgeGroup($barangayId) {
        $sql = "SELECT 
                    CASE 
                        WHEN TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) < 18 THEN 'Minor'
                        WHEN TIMESTAMPDIFF(YEAR, r.date_of_birth, CURDATE()) < 60 THEN 'Adult'
                        ELSE 'Senior'
                    END as age_group,
                    COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id 
                AND r.is_active = 1
                AND r.has_special_needs = 1
                GROUP BY age_group
                ORDER BY age_group";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get special needs by gender
     */
    public function getSpecialNeedsByGender($barangayId) {
        $sql = "SELECT r.gender, COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id 
                AND r.is_active = 1
                AND r.has_special_needs = 1
                GROUP BY r.gender
                ORDER BY r.gender";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Count residents with special needs
     */
    public function countResidentsWithSpecialNeeds($barangayId) {
        $sql = "SELECT COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id 
                AND r.is_active = 1
                AND r.has_special_needs = 1";
        
        $result = $this->queryFirst($sql, ['barangay_id' => $barangayId]);
        return $result['count'] ?? 0;
    }

    /**
     * Get residents by date range
     */
    public function getResidentsByDateRange($barangayId, $dateFrom, $dateTo, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT r.*, h.household_head, h.address, h.control_number, b.name as barangay_name
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                WHERE h.barangay_id = :barangay_id 
                AND r.is_active = 1
                AND DATE(r.created_at) BETWEEN :date_from AND :date_to
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        return $this->query($sql, [
            'barangay_id' => $barangayId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    /**
     * Count residents by date range
     */
    public function countResidentsByDateRange($barangayId, $dateFrom, $dateTo) {
        $sql = "SELECT COUNT(*) as count
                FROM residents r
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id 
                AND r.is_active = 1
                AND DATE(r.created_at) BETWEEN :date_from AND :date_to";
        
        $result = $this->queryFirst($sql, [
            'barangay_id' => $barangayId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ]);
        
        return $result['count'] ?? 0;
    }

    /**
     * Add resident to existing household
     */
    public function addResidentToHousehold($data, $householdId) {
        // Set created_by
        $data['created_by'] = $_SESSION['user_id'] ?? null;
        
        // Validate household exists
        $householdModel = new Household();
        $household = $householdModel->getById($householdId);
        
        if (!$household) {
            throw new Exception('Household not found');
        }
        
        // Set household_id for resident
        $data['household_id'] = $householdId;
        $data['is_household_head'] = 0; // Additional residents are not household heads
        
        // Create resident
        return $this->create($data);
    }

    /**
     * Get available households for adding residents (for admin use)
     */
    public function getAvailableHouseholds($barangayId) {
        $sql = "SELECT h.*, COUNT(r.id) as member_count
                FROM households h
                LEFT JOIN residents r ON h.id = r.household_id AND r.is_active = 1
                WHERE h.barangay_id = :barangay_id
                GROUP BY h.id
                ORDER BY h.household_head ASC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }
}
?> 