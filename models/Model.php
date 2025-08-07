<?php
/**
 * Base Model Class
 * All models extend this class for common database operations
 */

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get all records
     */
    public function getAll($limit = null, $offset = null) {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM {$this->table}";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            if ($offset) {
                $sql .= " OFFSET :offset";
            }
        }
        
        $stmt = $conn->prepare($sql);
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            if ($offset) {
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get record by ID
     */
    public function getById($id) {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Create new record
     */
    public function create($data) {
        $conn = $this->db->getConnection();
        
        // Filter data to only include fillable fields
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        
        $columns = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $conn->prepare($sql);
        
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        if ($stmt->execute()) {
            return $conn->lastInsertId();
        }
        return false;
    }

    /**
     * Update record
     */
    public function update($id, $data) {
        $conn = $this->db->getConnection();
        
        // Filter data to only include fillable fields
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        
        $setClause = [];
        foreach (array_keys($filteredData) as $column) {
            $setClause[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
        $stmt = $conn->prepare($sql);
        
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    /**
     * Delete record
     */
    public function delete($id) {
        $conn = $this->db->getConnection();
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Find records by condition
     */
    public function where($conditions, $limit = null, $offset = null) {
        $conn = $this->db->getConnection();
        
        $whereClause = [];
        foreach (array_keys($conditions) as $column) {
            $whereClause[] = "{$column} = :{$column}";
        }
        $whereClause = implode(' AND ', $whereClause);
        
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            if ($offset) {
                $sql .= " OFFSET :offset";
            }
        }
        
        $stmt = $conn->prepare($sql);
        
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            if ($offset) {
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find single record by condition
     */
    public function whereFirst($conditions) {
        $conn = $this->db->getConnection();
        
        $whereClause = [];
        foreach (array_keys($conditions) as $column) {
            $whereClause[] = "{$column} = :{$column}";
        }
        $whereClause = implode(' AND ', $whereClause);
        
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} LIMIT 1";
        $stmt = $conn->prepare($sql);
        
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Count records
     */
    public function count($conditions = []) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach (array_keys($conditions) as $column) {
                $whereClause[] = "{$column} = :{$column}";
            }
            $whereClause = implode(' AND ', $whereClause);
            $sql .= " WHERE {$whereClause}";
        }
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Execute custom query
     */
    public function query($sql, $params = []) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Execute custom query for single result
     */
    public function queryFirst($sql, $params = []) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Begin database transaction
     */
    public function beginTransaction() {
        $conn = $this->db->getConnection();
        return $conn->beginTransaction();
    }

    /**
     * Commit database transaction
     */
    public function commit() {
        $conn = $this->db->getConnection();
        return $conn->commit();
    }

    /**
     * Rollback database transaction
     */
    public function rollback() {
        $conn = $this->db->getConnection();
        return $conn->rollback();
    }
}
?> 