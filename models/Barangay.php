<?php
/**
 * Barangay Model
 * Handles barangay management
 */

class Barangay extends Model {
    protected $table = 'barangays';
    protected $fillable = ['name', 'code', 'description', 'is_active'];

    /**
     * Get active barangays
     */
    public function getActiveBarangays() {
        return $this->where(['is_active' => 1]);
    }

    /**
     * Get all barangays (active and inactive)
     */
    public function getAllBarangays($page = 1, $limit = ITEMS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        $whereConditions = [];
        $params = [];
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(name LIKE :search OR code LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        if (isset($filters['is_active'])) {
            $whereConditions[] = "is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $sql = "SELECT * FROM barangays {$whereClause} ORDER BY name ASC";
        
        // If pagination is requested
        if ($page > 0 && $limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }
        
        return $this->query($sql, $params);
    }

    /**
     * Get barangay with statistics
     */
    public function getBarangayWithStats($barangayId) {
        $barangay = $this->getById($barangayId);
        
        if ($barangay) {
            $householdModel = new Household();
            $residentModel = new Resident();
            
            $barangay['household_stats'] = $householdModel->getBarangayStatistics($barangayId);
            $barangay['resident_stats'] = $residentModel->getBarangayStatistics($barangayId);
        }
        
        return $barangay;
    }

    /**
     * Check if barangay code exists
     */
    public function codeExists($code, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM barangays WHERE code = :code";
        $params = ['code' => $code];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->queryFirst($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Count barangays with filters
     */
    public function countBarangays($filters = []) {
        $whereConditions = [];
        $params = [];
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(name LIKE :search OR code LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        if (isset($filters['is_active'])) {
            $whereConditions[] = "is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $sql = "SELECT COUNT(*) as count FROM barangays {$whereClause}";
        $result = $this->queryFirst($sql, $params);
        
        return $result['count'] ?? 0;
    }

    /**
     * Search barangays
     */
    public function searchBarangays($searchTerm, $limit = 20) {
        $sql = "SELECT * FROM barangays 
                WHERE (name LIKE :search OR code LIKE :search) 
                AND is_active = 1
                ORDER BY name ASC 
                LIMIT :limit";
        
        return $this->query($sql, [
            'search' => "%{$searchTerm}%",
            'limit' => $limit
        ]);
    }

    /**
     * Delete barangay (soft delete)
     */
    public function deleteBarangay($barangayId) {
        // Check if barangay has households
        $householdModel = new Household();
        $households = $householdModel->where(['barangay_id' => $barangayId]);
        
        if (!empty($households)) {
            throw new Exception('Cannot delete barangay with existing households. Please remove all households first.');
        }
        
        // Check if barangay has users
        $userModel = new User();
        $users = $userModel->where(['barangay_id' => $barangayId]);
        
        if (!empty($users)) {
            throw new Exception('Cannot delete barangay with existing users. Please remove all users first.');
        }
        
        return $this->delete($barangayId);
    }

    /**
     * Toggle barangay status
     */
    public function toggleBarangayStatus($barangayId) {
        $barangay = $this->getById($barangayId);
        if (!$barangay) {
            throw new Exception('Barangay not found');
        }
        
        $newStatus = $barangay['is_active'] ? 0 : 1;
        return $this->update($barangayId, ['is_active' => $newStatus]);
    }

    /**
     * Create barangay with validation
     */
    public function createBarangay($data) {
        // Validate code uniqueness
        if ($this->codeExists($data['code'])) {
            throw new Exception('Barangay code already exists');
        }
        
        return $this->create($data);
    }

    /**
     * Update barangay with validation
     */
    public function updateBarangay($barangayId, $data) {
        // Validate barangay exists
        $barangay = $this->getById($barangayId);
        if (!$barangay) {
            throw new Exception('Barangay not found');
        }
        
        // Validate code uniqueness if changed
        if (isset($data['code']) && $data['code'] !== $barangay['code']) {
            if ($this->codeExists($data['code'], $barangayId)) {
                throw new Exception('Barangay code already exists');
            }
        }
        
        return $this->update($barangayId, $data);
    }

    /**
     * Check if code exists (alias for codeExists)
     */
    public function isCodeExists($code, $excludeId = null) {
        return $this->codeExists($code, $excludeId);
    }
}
?> 