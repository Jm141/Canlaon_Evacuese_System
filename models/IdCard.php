<?php
/**
 * ID Card Model
 * Handles ID card generation and barcode management
 */

class IdCard extends Model {
    protected $table = 'id_cards';
    protected $fillable = [
        'resident_id', 'card_number', 'barcode_data', 'issue_date', 
        'expiry_date', 'status', 'generated_by'
    ];

    /**
     * Generate new ID card
     */
    public function generateIdCard($residentId, $generatedBy) {
        // Get resident details
        $residentModel = new Resident();
        $resident = $residentModel->getResidentWithDetails($residentId);
        
        if (!$resident) {
            throw new Exception('Resident not found');
        }
        
        // Check if resident already has an active ID card
        $existingCard = $this->getActiveCardByResident($residentId);
        if ($existingCard) {
            throw new Exception('Resident already has an active ID card');
        }
        
        // Generate card number
        $cardNumber = $this->generateCardNumber($resident);
        
        // Generate barcode data
        $barcodeData = $this->generateBarcodeData($resident, $cardNumber);
        
        // Set expiry date
        $issueDate = date('Y-m-d');
        $expiryDate = date('Y-m-d', strtotime("+" . ID_CARD_VALIDITY_YEARS . " years"));
        
        $data = [
            'resident_id' => $residentId,
            'card_number' => $cardNumber,
            'barcode_data' => $barcodeData,
            'issue_date' => $issueDate,
            'expiry_date' => $expiryDate,
            'status' => 'active',
            'generated_by' => $generatedBy
        ];
        
        $cardId = $this->create($data);
        
        if ($cardId) {
            logActivity('id_card_generated', 'id_cards', $cardId, null, json_encode($data));
            return $cardId;
        }
        
        return false;
    }

    /**
     * Get active ID card for resident
     */
    public function getActiveCardByResident($residentId) {
        return $this->whereFirst([
            'resident_id' => $residentId,
            'status' => 'active'
        ]);
    }

    /**
     * Get ID card with resident details
     */
    public function getIdCardWithDetails($cardId) {
        $sql = "SELECT ic.*, r.first_name, r.last_name, r.middle_name, r.date_of_birth,
                       r.gender, r.civil_status, r.nationality, r.religion, r.occupation,
                       r.contact_number, r.email, r.has_special_needs, r.special_needs_description,
                       h.household_head, h.address, h.collection_point, h.evacuation_vehicle,
                       h.vehicle_driver, h.assigned_evacuation_center, h.phone_number as household_phone,
                       h.control_number, b.name as barangay_name, b.code as barangay_code,
                       u.full_name as generated_by_name,
                       (SELECT COUNT(*) FROM residents r2 WHERE r2.household_id = h.id AND r2.is_active = 1) as household_member_count
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                INNER JOIN users u ON ic.generated_by = u.id
                WHERE ic.id = :id";
        
        return $this->queryFirst($sql, ['id' => $cardId]);
    }

    /**
     * Get ID cards by barangay
     */
    public function getIdCardsByBarangay($barangayId, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT ic.*, r.first_name, r.last_name, r.middle_name,
                       CONCAT(r.first_name, ' ', r.last_name) as resident_name,
                       CONCAT(r.first_name, ' ', r.last_name, ' (', h.control_number, ')') as resident_details,
                       h.household_head, h.control_number, b.name as barangay_name,
                       u.full_name as generated_by_name
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                LEFT JOIN users u ON ic.generated_by = u.id
                WHERE h.barangay_id = :barangay_id
                ORDER BY ic.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        return $this->query($sql, [
            'barangay_id' => $barangayId,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    /**
     * Get active ID cards by barangay (for bulk printing)
     */
    public function getActiveIdCardsByBarangay($barangayId) {
        $sql = "SELECT ic.*, r.first_name, r.last_name, r.middle_name, r.date_of_birth, 
                       r.gender, r.civil_status, r.contact_number, r.emergency_contact_name, 
                       r.emergency_contact_number, r.address, r.assigned_evacuation_center, 
                       r.collection_point, r.evacuation_vehicle, r.vehicle_driver,
                       h.household_head, h.control_number, b.name as barangay_name,
                       u.full_name as generated_by_name,
                       (SELECT COUNT(*) FROM residents r2 WHERE r2.household_id = h.id AND r2.is_active = 1) as household_member_count
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                LEFT JOIN users u ON ic.generated_by = u.id
                WHERE h.barangay_id = :barangay_id 
                AND ic.status = 'active'
                ORDER BY r.last_name, r.first_name";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Count ID cards by barangay
     */
    public function countIdCardsByBarangay($barangayId) {
        $sql = "SELECT COUNT(*) as count
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id";
        
        $result = $this->queryFirst($sql, ['barangay_id' => $barangayId]);
        return $result['count'];
    }

    /**
     * Search ID cards
     */
    public function searchIdCards($searchTerm, $barangayId = null, $limit = 20) {
        $sql = "SELECT ic.*, r.first_name, r.last_name, r.middle_name,
                       CONCAT(r.first_name, ' ', r.last_name) as resident_name,
                       CONCAT(r.first_name, ' ', r.last_name, ' (', h.control_number, ')') as resident_details,
                       h.household_head, h.control_number, b.name as barangay_name,
                       u.full_name as generated_by_name
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                LEFT JOIN users u ON ic.generated_by = u.id
                WHERE (r.first_name LIKE :search OR r.last_name LIKE :search 
                       OR h.household_head LIKE :search OR ic.card_number LIKE :search
                       OR h.control_number LIKE :search)";
        
        $params = ['search' => "%{$searchTerm}%"];
        
        if ($barangayId) {
            $sql .= " AND h.barangay_id = :barangay_id";
            $params['barangay_id'] = $barangayId;
        }
        
        $sql .= " ORDER BY ic.created_at DESC LIMIT :limit";
        $params['limit'] = $limit;
        
        return $this->query($sql, $params);
    }

    /**
     * Cancel ID card
     */
    public function cancelIdCard($cardId) {
        $card = $this->getById($cardId);
        if (!$card) {
            throw new Exception('ID card not found');
        }
        
        $result = $this->update($cardId, ['status' => 'cancelled']);
        
        if ($result) {
            logActivity('id_card_cancelled', 'id_cards', $cardId);
        }
        
        return $result;
    }

    /**
     * Check if ID card is expired
     */
    public function isExpired($cardId) {
        $card = $this->getById($cardId);
        if (!$card) {
            return true;
        }
        
        return strtotime($card['expiry_date']) < time();
    }

    /**
     * Update expired cards status
     */
    public function updateExpiredCards() {
        $sql = "UPDATE id_cards SET status = 'expired' 
                WHERE expiry_date < CURDATE() AND status = 'active'";
        
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare($sql);
        return $stmt->execute();
    }

    /**
     * Generate card number
     */
    private function generateCardNumber($resident) {
        $barangayCode = $resident['barangay_code'];
        $year = date('Y');
        $month = date('m');
        
        // Get the last card number for this barangay and month
        $sql = "SELECT ic.card_number 
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                WHERE b.code = :barangay_code 
                AND ic.card_number LIKE :prefix 
                ORDER BY ic.card_number DESC 
                LIMIT 1";
        
        $result = $this->queryFirst($sql, [
            'barangay_code' => $barangayCode,
            'prefix' => "{$barangayCode}-{$year}{$month}-%"
        ]);
        
        if ($result) {
            // Extract the sequence number and increment
            $parts = explode('-', $result['card_number']);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf("%s-%s%02d-%04d", $barangayCode, $year, $month, $sequence);
    }

    /**
     * Generate barcode data
     */
    private function generateBarcodeData($resident, $cardNumber) {
        // Create a unique identifier for the barcode
        $data = [
            'card_number' => $cardNumber,
            'resident_id' => $resident['id'],
            'household_id' => $resident['household_id'],
            'barangay_code' => $resident['barangay_code'],
            'timestamp' => time()
        ];
        
        return base64_encode(json_encode($data));
    }

    /**
     * Get ID card statistics by barangay
     */
    public function getBarangayStatistics($barangayId) {
        $sql = "SELECT 
                    COUNT(*) as total_cards,
                    SUM(CASE WHEN ic.status = 'active' THEN 1 ELSE 0 END) as active_cards,
                    SUM(CASE WHEN ic.status = 'expired' THEN 1 ELSE 0 END) as expired_cards,
                    SUM(CASE WHEN ic.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_cards,
                    SUM(CASE WHEN ic.expiry_date < CURDATE() THEN 1 ELSE 0 END) as expired_cards_count
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id";
        
        return $this->queryFirst($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get ID cards by date range
     */
    public function getIdCardsByDateRange($barangayId, $dateFrom, $dateTo, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT ic.*, r.first_name, r.last_name, r.middle_name, r.date_of_birth, r.gender,
                       h.household_head, h.address, h.control_number, b.name as barangay_name,
                       u.full_name as generated_by_name
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                LEFT JOIN users u ON ic.generated_by = u.id
                WHERE h.barangay_id = :barangay_id 
                AND DATE(ic.created_at) BETWEEN :date_from AND :date_to
                ORDER BY ic.created_at DESC
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
     * Count ID cards by date range
     */
    public function countIdCardsByDateRange($barangayId, $dateFrom, $dateTo) {
        $sql = "SELECT COUNT(*) as count
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id 
                AND DATE(ic.created_at) BETWEEN :date_from AND :date_to";
        
        $result = $this->queryFirst($sql, [
            'barangay_id' => $barangayId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ]);
        
        return $result['count'] ?? 0;
    }

    /**
     * Count active cards by barangay
     */
    public function countActiveCardsByBarangay($barangayId) {
        $sql = "SELECT COUNT(*) as count
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id 
                AND ic.status = 'active'";
        
        $result = $this->queryFirst($sql, ['barangay_id' => $barangayId]);
        return $result['count'] ?? 0;
    }

    /**
     * Count expired cards by barangay
     */
    public function countExpiredCardsByBarangay($barangayId) {
        $sql = "SELECT COUNT(*) as count
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id 
                AND ic.status = 'expired'";
        
        $result = $this->queryFirst($sql, ['barangay_id' => $barangayId]);
        return $result['count'] ?? 0;
    }

    /**
     * Get ID cards by status
     */
    public function getIdCardsByStatus($barangayId) {
        $sql = "SELECT ic.status, COUNT(*) as count
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                WHERE h.barangay_id = :barangay_id 
                GROUP BY ic.status
                ORDER BY count DESC";
        
        return $this->query($sql, ['barangay_id' => $barangayId]);
    }

    /**
     * Get expiring cards
     */
    public function getExpiringCards($barangayId, $days = 30) {
        $sql = "SELECT ic.*, r.first_name, r.last_name, r.middle_name, r.date_of_birth, r.gender,
                       h.household_head, h.address, h.control_number, b.name as barangay_name,
                       DATEDIFF(ic.expiry_date, CURDATE()) as days_until_expiry
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                INNER JOIN households h ON r.household_id = h.id
                INNER JOIN barangays b ON h.barangay_id = b.id
                WHERE h.barangay_id = :barangay_id 
                AND ic.status = 'active'
                AND ic.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                ORDER BY ic.expiry_date ASC";
        
        return $this->query($sql, [
            'barangay_id' => $barangayId,
            'days' => $days
        ]);
    }

    /**
     * Get ID cards by household
     */
    public function getIdCardsByHousehold($householdId) {
        $sql = "SELECT ic.*, r.first_name, r.last_name, r.middle_name, r.date_of_birth, r.gender,
                       u.full_name as generated_by_name
                FROM id_cards ic
                INNER JOIN residents r ON ic.resident_id = r.id
                LEFT JOIN users u ON ic.generated_by = u.id
                WHERE r.household_id = :household_id
                ORDER BY ic.created_at DESC";
        
        return $this->query($sql, ['household_id' => $householdId]);
    }
}
?> 