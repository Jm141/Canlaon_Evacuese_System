<?php
/**
 * Evacuation Center Model
 * Handles evacuation center management and automatic resident assignment
 */

class EvacuationCenter extends Model {
    protected $table = 'evacuation_centers';
    protected $fillable = [
        'name', 'barangay_id', 'address', 'capacity', 'current_occupancy',
        'contact_person', 'contact_number', 'is_active', 'created_by'
    ];

    /**
     * Get evacuation centers by barangay
     */
    public function getEvacuationCentersByBarangay($barangayId) {
        $sql = "SELECT ec.*, b.name as barangay_name,
                       (ec.capacity - ec.current_occupancy) as available_capacity
                FROM evacuation_centers ec
                LEFT JOIN barangays b ON ec.barangay_id = b.id
                WHERE ec.barangay_id = :barangay_id AND ec.is_active = 1
                ORDER BY ec.name ASC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get all evacuation centers with barangay info
     */
    public function getAllEvacuationCenters($page = 1, $limit = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        $whereConditions = ['ec.is_active' => 1];
        $params = [];
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(ec.name LIKE :search OR ec.address LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        if (!empty($filters['barangay_id'])) {
            $whereConditions[] = "ec.barangay_id = :barangay_id";
            $params['barangay_id'] = $filters['barangay_id'];
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        $sql = "SELECT ec.*, b.name as barangay_name,
                       (ec.capacity - ec.current_occupancy) as available_capacity
                FROM evacuation_centers ec
                LEFT JOIN barangays b ON ec.barangay_id = b.id
                {$whereClause}
                ORDER BY ec.name ASC
                LIMIT :limit OFFSET :offset";
        
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->query($sql, $params);
    }

    /**
     * Count evacuation centers with filters
     */
    public function countEvacuationCenters($filters = []) {
        $whereConditions = ['is_active' => 1];
        $params = [];
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(name LIKE :search OR address LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        if (!empty($filters['barangay_id'])) {
            $whereConditions[] = "barangay_id = :barangay_id";
            $params['barangay_id'] = $filters['barangay_id'];
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        $sql = "SELECT COUNT(*) as count FROM evacuation_centers {$whereClause}";
        $result = $this->queryFirst($sql, $params);
        
        return $result['count'] ?? 0;
    }

    /**
     * Find available evacuation center for barangay
     */
    public function findAvailableEvacuationCenter($barangayId, $requiredCapacity = 1) {
        $sql = "SELECT ec.*, (ec.capacity - ec.current_occupancy) as available_capacity
                FROM evacuation_centers ec
                WHERE ec.barangay_id = :barangay_id 
                AND ec.is_active = 1
                AND (ec.capacity - ec.current_occupancy) >= :required_capacity
                ORDER BY (ec.capacity - ec.current_occupancy) ASC
                LIMIT 1";
        
        return $this->queryFirst($sql, [
            'barangay_id' => $barangayId,
            'required_capacity' => $requiredCapacity
        ]);
    }

    /**
     * Automatically assign household to evacuation center
     */
    public function autoAssignHousehold($householdId, $barangayId) {
        // Get household member count
        $residentModel = new Resident();
        $memberCount = count($residentModel->getHouseholdMembers($householdId));
        
        // Find available evacuation center
        $evacuationCenter = $this->findAvailableEvacuationCenter($barangayId, $memberCount);
        
        if (!$evacuationCenter) {
            throw new Exception('No available evacuation center with sufficient capacity for this household');
        }
        
        // Update household with evacuation center assignment
        $householdModel = new Household();
        $updateData = [
            'assigned_evacuation_center' => $evacuationCenter['name'],
            'collection_point' => $evacuationCenter['address']
        ];
        
        $householdResult = $householdModel->update($householdId, $updateData);
        
        if (!$householdResult) {
            throw new Exception('Failed to assign household to evacuation center');
        }
        
        // Update evacuation center occupancy
        $newOccupancy = $evacuationCenter['current_occupancy'] + $memberCount;
        $this->update($evacuationCenter['id'], ['current_occupancy' => $newOccupancy]);
        
        return $evacuationCenter;
    }

    /**
     * Create evacuation center
     */
    public function createEvacuationCenter($data) {
        // Set created_by
        $data['created_by'] = $_SESSION['user_id'] ?? null;
        $data['current_occupancy'] = 0; // Start with 0 occupancy
        
        return $this->create($data);
    }

    /**
     * Update evacuation center
     */
    public function updateEvacuationCenter($centerId, $data) {
        // Validate center exists
        $center = $this->getById($centerId);
        if (!$center) {
            throw new Exception('Evacuation center not found');
        }
        
        // Validate capacity is not less than current occupancy
        if (isset($data['capacity']) && $data['capacity'] < $center['current_occupancy']) {
            throw new Exception('Capacity cannot be less than current occupancy (' . $center['current_occupancy'] . ')');
        }
        
        return $this->update($centerId, $data);
    }

    /**
     * Delete evacuation center (soft delete)
     */
    public function deleteEvacuationCenter($centerId) {
        // Check if center has assigned households
        $householdModel = new Household();
        $households = $householdModel->where(['assigned_evacuation_center' => $centerId]);
        
        if (!empty($households)) {
            throw new Exception('Cannot delete evacuation center with assigned households. Please reassign households first.');
        }
        
        return $this->update($centerId, ['is_active' => 0]);
    }

    /**
     * Get evacuation center statistics by barangay
     */
    public function getBarangayStatistics($barangayId) {
        $sql = "SELECT 
                    COUNT(*) as total_centers,
                    SUM(capacity) as total_capacity,
                    SUM(current_occupancy) as total_occupancy,
                    SUM(capacity - current_occupancy) as total_available
                FROM evacuation_centers 
                WHERE barangay_id = :barangay_id AND is_active = 1";
        
        return $this->queryFirst($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get evacuation center with assigned households
     */
    public function getEvacuationCenterWithHouseholds($centerId) {
        $sql = "SELECT ec.*, b.name as barangay_name
                FROM evacuation_centers ec
                LEFT JOIN barangays b ON ec.barangay_id = b.id
                WHERE ec.id = :id";
        
        $center = $this->queryFirst($sql, ['id' => $centerId]);
        
        if ($center) {
            // Get assigned households
            $householdModel = new Household();
            $center['assigned_households'] = $householdModel->where([
                'assigned_evacuation_center' => $center['name'],
                'barangay_id' => $center['barangay_id']
            ]);
        }
        
        return $center;
    }

    /**
     * Search evacuation centers
     */
    public function searchEvacuationCenters($searchTerm, $limit = 20) {
        $sql = "SELECT ec.*, b.name as barangay_name,
                       (ec.capacity - ec.current_occupancy) as available_capacity
                FROM evacuation_centers ec
                LEFT JOIN barangays b ON ec.barangay_id = b.id
                WHERE ec.is_active = 1
                AND (ec.name LIKE :search OR ec.address LIKE :search OR b.name LIKE :search)
                ORDER BY ec.name ASC
                LIMIT :limit";
        
        return $this->query($sql, [
            'search' => "%{$searchTerm}%",
            'limit' => $limit
        ]);
    }

    /**
     * Reassign household to different evacuation center
     */
    public function reassignHousehold($householdId, $newCenterId) {
        // Get household info
        $householdModel = new Household();
        $household = $householdModel->getById($householdId);
        
        if (!$household) {
            throw new Exception('Household not found');
        }
        
        // Get new evacuation center
        $newCenter = $this->getById($newCenterId);
        if (!$newCenter) {
            throw new Exception('Evacuation center not found');
        }
        
        // Get household member count
        $residentModel = new Resident();
        $memberCount = count($residentModel->getHouseholdMembers($householdId));
        
        // Check if new center has capacity
        if (($newCenter['capacity'] - $newCenter['current_occupancy']) < $memberCount) {
            throw new Exception('Evacuation center does not have sufficient capacity');
        }
        
        // Start transaction
        $this->beginTransaction();
        
        try {
            // Remove from old center if assigned
            if (!empty($household['assigned_evacuation_center'])) {
                $oldCenter = $this->whereFirst(['name' => $household['assigned_evacuation_center']]);
                if ($oldCenter) {
                    $oldOccupancy = $oldCenter['current_occupancy'] - $memberCount;
                    $this->update($oldCenter['id'], ['current_occupancy' => $oldOccupancy]);
                }
            }
            
            // Assign to new center
            $householdModel->update($householdId, [
                'assigned_evacuation_center' => $newCenter['name'],
                'collection_point' => $newCenter['address']
            ]);
            
            // Update new center occupancy
            $newOccupancy = $newCenter['current_occupancy'] + $memberCount;
            $this->update($newCenterId, ['current_occupancy' => $newOccupancy]);
            
            // Commit transaction
            $this->commit();
            
            return true;
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->rollback();
            throw $e;
        }
    }
}
?> 