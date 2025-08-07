<?php
/**
 * Household Model
 * Handles household data and operations
 */

class Household extends Model {
    protected $table = 'households';
    protected $fillable = [
        'household_head', 'address', 'barangay_id', 'collection_point',
        'evacuation_vehicle', 'vehicle_driver', 'assigned_evacuation_center',
        'phone_number', 'control_number', 'created_by'
    ];

    /**
     * Get households by barangay with pagination
     */
    public function getHouseholdsByBarangay($barangayId, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT h.*, b.name as barangay_name, u.full_name as created_by_name
                FROM households h
                LEFT JOIN barangays b ON h.barangay_id = b.id
                LEFT JOIN users u ON h.created_by = u.id
                WHERE h.barangay_id = :barangay_id
                ORDER BY h.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        return $this->query($sql, [
            'barangay_id' => $barangayId,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    /**
     * Count households by barangay
     */
    public function countHouseholdsByBarangay($barangayId) {
        $sql = "SELECT COUNT(*) as count FROM households WHERE barangay_id = :barangay_id";
        $result = $this->queryFirst($sql, ['barangay_id' => $barangayId]);
        return $result['count'] ?? 0;
    }

    /**
     * Get household statistics for barangay
     */
    public function getBarangayStatistics($barangayId) {
        $sql = "SELECT 
                    COUNT(*) as total_households,
                    COUNT(DISTINCT barangay_id) as barangays_covered,
                    COUNT(CASE WHEN assigned_evacuation_center IS NOT NULL AND assigned_evacuation_center != '' THEN 1 END) as households_with_evacuation_center,
                    COUNT(CASE WHEN evacuation_vehicle IS NOT NULL AND evacuation_vehicle != '' THEN 1 END) as households_with_vehicle
                FROM households 
                WHERE barangay_id = :barangay_id";
        
        return $this->queryFirst($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get household with residents
     */
    public function getHouseholdWithResidents($householdId) {
        $sql = "SELECT h.*, b.name as barangay_name, u.full_name as created_by_name
                FROM households h
                LEFT JOIN barangays b ON h.barangay_id = b.id
                LEFT JOIN users u ON h.created_by = u.id
                WHERE h.id = :id";
        
        $household = $this->queryFirst($sql, ['id' => $householdId]);
        
        if ($household) {
            // Get residents for this household
            $residentModel = new Resident();
            $household['residents'] = $residentModel->getResidentsByHousehold($householdId);
        }
        
        return $household;
    }

    /**
     * Get evacuation centers by barangay
     */
    public function getEvacuationCentersByBarangay($barangayId) {
        $sql = "SELECT DISTINCT assigned_evacuation_center, COUNT(*) as household_count
                FROM households 
                WHERE assigned_evacuation_center IS NOT NULL 
                AND assigned_evacuation_center != ''";
        
        $params = [];
        
        if ($barangayId !== null) {
            $sql .= " AND barangay_id = :barangay_id";
            $params['barangay_id'] = $barangayId;
        }
        
        $sql .= " GROUP BY assigned_evacuation_center
                ORDER BY household_count DESC";
        
        return $this->query($sql, $params);
    }

    /**
     * Get households with pagination and filters
     */
    public function getHouseholdsWithPagination($page = 1, $limit = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        $whereConditions = [];
        $params = [];
        
        if (!empty($filters['barangay_id'])) {
            $whereConditions[] = "h.barangay_id = :barangay_id";
            $params['barangay_id'] = $filters['barangay_id'];
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(h.household_head LIKE :search OR h.address LIKE :search OR h.control_number LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        if (!empty($filters['evacuation_center'])) {
            $whereConditions[] = "h.assigned_evacuation_center = :evacuation_center";
            $params['evacuation_center'] = $filters['evacuation_center'];
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $sql = "SELECT h.*, b.name as barangay_name, u.full_name as created_by_name
                FROM households h
                LEFT JOIN barangays b ON h.barangay_id = b.id
                LEFT JOIN users u ON h.created_by = u.id
                {$whereClause}
                ORDER BY h.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->query($sql, $params);
    }

    /**
     * Count households with filters
     */
    public function countHouseholds($filters = []) {
        $whereConditions = [];
        $params = [];
        
        if (!empty($filters['barangay_id'])) {
            $whereConditions[] = "barangay_id = :barangay_id";
            $params['barangay_id'] = $filters['barangay_id'];
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(household_head LIKE :search OR address LIKE :search OR control_number LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        if (!empty($filters['evacuation_center'])) {
            $whereConditions[] = "assigned_evacuation_center = :evacuation_center";
            $params['evacuation_center'] = $filters['evacuation_center'];
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $sql = "SELECT COUNT(*) as count FROM households {$whereClause}";
        
        $result = $this->queryFirst($sql, $params);
        return $result['count'] ?? 0;
    }

    /**
     * Check if control number exists
     */
    public function controlNumberExists($controlNumber, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM households WHERE control_number = :control_number";
        $params = ['control_number' => $controlNumber];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->queryFirst($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Generate unique control number
     */
    public function generateControlNumber($barangayCode) {
        $prefix = date('Y') . '-' . $barangayCode . '-';
        $counter = 1;
        
        do {
            $controlNumber = $prefix . str_pad($counter, 4, '0', STR_PAD_LEFT);
            $counter++;
        } while ($this->controlNumberExists($controlNumber));
        
        return $controlNumber;
    }

    /**
     * Get household summary for dashboard
     */
    public function getHouseholdSummary($barangayId) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN assigned_evacuation_center IS NOT NULL AND assigned_evacuation_center != '' THEN 1 END) as with_evacuation_center,
                    COUNT(CASE WHEN evacuation_vehicle IS NOT NULL AND evacuation_vehicle != '' THEN 1 END) as with_vehicle,
                    COUNT(CASE WHEN phone_number IS NOT NULL AND phone_number != '' THEN 1 END) as with_phone
                FROM households 
                WHERE barangay_id = :barangay_id";
        
        return $this->queryFirst($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get households by date range
     */
    public function getHouseholdsByDateRange($barangayId, $dateFrom, $dateTo, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT h.*, b.name as barangay_name, u.full_name as created_by_name
                FROM households h
                LEFT JOIN barangays b ON h.barangay_id = b.id
                LEFT JOIN users u ON h.created_by = u.id
                WHERE h.barangay_id = :barangay_id 
                AND DATE(h.created_at) BETWEEN :date_from AND :date_to
                ORDER BY h.created_at DESC
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
     * Count households by date range
     */
    public function countHouseholdsByDateRange($barangayId, $dateFrom, $dateTo) {
        $sql = "SELECT COUNT(*) as count 
                FROM households 
                WHERE barangay_id = :barangay_id 
                AND DATE(created_at) BETWEEN :date_from AND :date_to";
        
        $result = $this->queryFirst($sql, [
            'barangay_id' => $barangayId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ]);
        
        return $result['count'] ?? 0;
    }

    /**
     * Get households by evacuation center
     */
    public function getHouseholdsByEvacuationCenter($barangayId) {
        $sql = "SELECT assigned_evacuation_center, COUNT(*) as household_count
                FROM households 
                WHERE barangay_id = :barangay_id 
                AND assigned_evacuation_center IS NOT NULL 
                AND assigned_evacuation_center != ''
                GROUP BY assigned_evacuation_center
                ORDER BY household_count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get households by collection point
     */
    public function getHouseholdsByCollectionPoint($barangayId) {
        $sql = "SELECT collection_point, COUNT(*) as household_count
                FROM households 
                WHERE barangay_id = :barangay_id 
                AND collection_point IS NOT NULL 
                AND collection_point != ''
                GROUP BY collection_point
                ORDER BY household_count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get collection points by barangay
     */
    public function getCollectionPointsByBarangay($barangayId) {
        $sql = "SELECT DISTINCT collection_point, COUNT(*) as household_count
                FROM households 
                WHERE barangay_id = :barangay_id 
                AND collection_point IS NOT NULL 
                AND collection_point != ''
                GROUP BY collection_point
                ORDER BY household_count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get vehicle assignments
     */
    public function getVehicleAssignments($barangayId) {
        $sql = "SELECT evacuation_vehicle, vehicle_driver, COUNT(*) as household_count
                FROM households 
                WHERE barangay_id = :barangay_id 
                AND evacuation_vehicle IS NOT NULL 
                AND evacuation_vehicle != ''
                GROUP BY evacuation_vehicle, vehicle_driver
                ORDER BY household_count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Create household with validation
     */
    public function createHousehold($data) {
        // Set created_by
        $data['created_by'] = $_SESSION['user_id'] ?? null;
        
        // Generate control number if not provided
        if (empty($data['control_number'])) {
            $barangayModel = new Barangay();
            $barangay = $barangayModel->getById($data['barangay_id']);
            if ($barangay) {
                $data['control_number'] = $this->generateControlNumber($barangay['code']);
            }
        }
        
        // Validate control number uniqueness
        if ($this->controlNumberExists($data['control_number'])) {
            throw new Exception('Control number already exists');
        }
        
        return $this->create($data);
    }

    /**
     * Update household with validation
     */
    public function updateHousehold($householdId, $data) {
        // Validate household exists
        $household = $this->getById($householdId);
        if (!$household) {
            throw new Exception('Household not found');
        }
        
        // Validate control number uniqueness if changed
        if (isset($data['control_number']) && $data['control_number'] !== $household['control_number']) {
            if ($this->controlNumberExists($data['control_number'], $householdId)) {
                throw new Exception('Control number already exists');
            }
        }
        
        return $this->update($householdId, $data);
    }

    /**
     * Search households
     */
    public function searchHouseholds($searchTerm, $barangayId = null, $limit = 20) {
        $sql = "SELECT h.*, b.name as barangay_name
                FROM households h
                LEFT JOIN barangays b ON h.barangay_id = b.id
                WHERE (h.household_head LIKE :search OR h.address LIKE :search OR h.control_number LIKE :search)";
        
        $params = ['search' => '%' . $searchTerm . '%'];
        
        if ($barangayId !== null) {
            $sql .= " AND h.barangay_id = :barangay_id";
            $params['barangay_id'] = $barangayId;
        }
        
        $sql .= " ORDER BY h.household_head ASC LIMIT :limit";
        $params['limit'] = $limit;
        
        return $this->query($sql, $params);
    }

    /**
     * Delete household (soft delete)
     */
    public function deleteHousehold($householdId) {
        // Check if household has residents
        $residentModel = new Resident();
        $residents = $residentModel->getResidentsByHousehold($householdId);
        
        if (!empty($residents)) {
            throw new Exception('Cannot delete household with existing residents. Please remove all residents first.');
        }
        
        return $this->delete($householdId);
    }
}
?> 