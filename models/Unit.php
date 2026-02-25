<?php
require_once __DIR__ . '/Database.php';

class Unit extends DatabaseModel {
    private $table = "tbl_units";

    // READ - Get all units
    public function getAllUnits() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY functional_division, unit_name";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ - Get units by division
    public function getUnitsByDivision($division) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE functional_division = :division 
                  ORDER BY unit_name";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':division', $division);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ - Get single unit by ID
    public function getUnitById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE - Add new unit
    public function createUnit($data) {
        // Check if unit name already exists in the same division
        if($this->unitNameExists($data['unit_name'], $data['functional_division'])) {
            return ['success' => false, 'message' => 'Unit name already exists in this division'];
        }
        
        $query = "INSERT INTO " . $this->table . " 
                  (functional_division, unit_name, person_in_charge, designation, email, contact_number, status) 
                  VALUES (:functional_division, :unit_name, :person_in_charge, :designation, :email, :contact_number, :status)";
        
        $stmt = $this->getConnection()->prepare($query);
        
        $success = $stmt->execute([
            ':functional_division' => $data['functional_division'],
            ':unit_name' => $data['unit_name'],
            ':person_in_charge' => $data['person_in_charge'] ?: null,
            ':designation' => $data['designation'] ?: null,
            ':email' => $data['email'] ?: null,
            ':contact_number' => $data['contact_number'] ?: null,
            ':status' => $data['status'] ?? 'active'
        ]);
        
        if($success) {
            return ['success' => true, 'message' => 'Unit created successfully', 'id' => $this->getConnection()->lastInsertId()];
        } else {
            return ['success' => false, 'message' => 'Failed to create unit'];
        }
    }

    // UPDATE - Update unit
    public function updateUnit($id, $data) {
        // Check if unit name already exists in the same division (excluding current unit)
        if($this->unitNameExists($data['unit_name'], $data['functional_division'], $id)) {
            return ['success' => false, 'message' => 'Unit name already exists in this division'];
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET functional_division = :functional_division,
                      unit_name = :unit_name,
                      person_in_charge = :person_in_charge,
                      designation = :designation,
                      email = :email,
                      contact_number = :contact_number,
                      status = :status
                  WHERE id = :id";
        
        $stmt = $this->getConnection()->prepare($query);
        
        $success = $stmt->execute([
            ':id' => $id,
            ':functional_division' => $data['functional_division'],
            ':unit_name' => $data['unit_name'],
            ':person_in_charge' => $data['person_in_charge'] ?: null,
            ':designation' => $data['designation'] ?: null,
            ':email' => $data['email'] ?: null,
            ':contact_number' => $data['contact_number'] ?: null,
            ':status' => $data['status']
        ]);
        
        if($success) {
            return ['success' => true, 'message' => 'Unit updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update unit'];
        }
    }

    // DELETE - Delete unit
    public function deleteUnit($id) {
        // Check if unit has tasks
        if($this->hasTasks($id)) {
            return ['success' => false, 'message' => 'Cannot delete unit with existing tasks'];
        }
        
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->getConnection()->prepare($query);
        
        $success = $stmt->execute([':id' => $id]);
        
        if($success) {
            return ['success' => true, 'message' => 'Unit deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete unit'];
        }
    }

    // Helper methods
    private function unitNameExists($unit_name, $division, $exclude_id = null) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " 
                  WHERE unit_name = :unit_name AND functional_division = :division";
        if($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':unit_name', $unit_name);
        $stmt->bindParam(':division', $division);
        if($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    private function hasTasks($unit_id) {
        $query = "SELECT COUNT(*) FROM tbl_task WHERE unit_id = :unit_id";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    // Get active units
    public function getActiveUnits() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE status = 'active' 
                  ORDER BY functional_division, unit_name";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get division summary
    public function getDivisionSummary() {
        $query = "SELECT 
                    functional_division,
                    COUNT(*) as total_units,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_units
                  FROM " . $this->table . " 
                  GROUP BY functional_division";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




public function getUnitsWithFilters($filters = [], $limit = 10, $offset = 0) {
    $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
    
    $params = [];
    
    // Apply filters
    if(!empty($filters['search'])) {
        $query .= " AND (unit_name LIKE :search OR person_in_charge LIKE :search OR email LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }
    
    if(!empty($filters['division'])) {
        $query .= " AND functional_division = :division";
        $params[':division'] = $filters['division'];
    }
    
    if(!empty($filters['status'])) {
        $query .= " AND status = :status";
        $params[':status'] = $filters['status'];
    }
    
    // Add ordering
    $query .= " ORDER BY functional_division, unit_name";
    
    // Add pagination
    $query .= " LIMIT :limit OFFSET :offset";
    
    $stmt = $this->getConnection()->prepare($query);
    
    // Bind parameters
    foreach($params as $key => &$val) {
        $stmt->bindParam($key, $val);
    }
    
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getTotalUnitCount($filters = []) {
    $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
    
    $params = [];
    
    // Apply same filters
    if(!empty($filters['search'])) {
        $query .= " AND (unit_name LIKE :search OR person_in_charge LIKE :search OR email LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }
    
    if(!empty($filters['division'])) {
        $query .= " AND functional_division = :division";
        $params[':division'] = $filters['division'];
    }
    
    if(!empty($filters['status'])) {
        $query .= " AND status = :status";
        $params[':status'] = $filters['status'];
    }
    
    $stmt = $this->getConnection()->prepare($query);
    
    foreach($params as $key => &$val) {
        $stmt->bindParam($key, $val);
    }
    
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}


// Get units that are part of a specific project
public function getUnitsByProject($project_id) {
    $query = "SELECT u.* FROM tbl_units u
              INNER JOIN tbl_project_units pu ON u.id = pu.unit_id
              WHERE pu.project_id = :project_id
              ORDER BY u.functional_division, u.unit_name";
    
    $stmt = $this->getConnection()->prepare($query);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>