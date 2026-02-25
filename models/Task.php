<?php
require_once __DIR__ . '/Database.php';

class Task extends DatabaseModel {
    private $table = "tbl_task";

    public function getAllTasks() {
    $query = "SELECT t.*, 
              GROUP_CONCAT(DISTINCT u.unit_name SEPARATOR ', ') as unit_names,
              (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as current_percentage,
              (SELECT remarks FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as latest_remarks,
              (SELECT update_date FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as last_update,
              (SELECT updated_by FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as last_updated_by
              FROM " . $this->table . " t
              LEFT JOIN tbl_task_units tu ON t.task_id = tu.task_id
              LEFT JOIN tbl_units u ON tu.unit_id = u.id
              GROUP BY t.task_id
              ORDER BY t.functional_division, t.priority DESC, t.created_at DESC";
    
    $stmt = $this->getConnection()->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // For encoders - only get tasks from their division
    public function getTasksByDivisionForEncoder($division, $user_id = null) {
    $query = "SELECT t.*, 
              GROUP_CONCAT(DISTINCT u.unit_name SEPARATOR ', ') as unit_names,
              (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as current_percentage,
              (SELECT remarks FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as latest_remarks,
              (SELECT update_date FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as last_update,
              (SELECT updated_by FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as last_updated_by
              FROM " . $this->table . " t
              LEFT JOIN tbl_task_units tu ON t.task_id = tu.task_id
              LEFT JOIN tbl_units u ON tu.unit_id = u.id
              WHERE t.functional_division = :division
              GROUP BY t.task_id
              ORDER BY t.priority DESC, t.created_at DESC";
    
    $stmt = $this->getConnection()->prepare($query);
    $stmt->bindParam(':division', $division);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getTasksByDivision($division) {
        $query = "SELECT t.*, u.unit_name, u.person_in_charge,
                  (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as current_percentage,
                  (SELECT remarks FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as latest_remarks
                  FROM " . $this->table . " t
                  LEFT JOIN tbl_units u ON t.unit_id = u.id
                  WHERE t.functional_division = :division
                  ORDER BY t.priority DESC, t.created_at DESC";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':division', $division);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTaskById($task_id) {
    $query = "SELECT t.*, 
              GROUP_CONCAT(DISTINCT u.unit_name SEPARATOR ', ') as unit_names,
              (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as current_percentage
              FROM " . $this->table . " t
              LEFT JOIN tbl_task_units tu ON t.task_id = tu.task_id
              LEFT JOIN tbl_units u ON tu.unit_id = u.id
              WHERE t.task_id = :task_id
              GROUP BY t.task_id";
    
    $stmt = $this->getConnection()->prepare($query);
    $stmt->bindParam(':task_id', $task_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    // Check if user can access task (for encoders)
    public function canUserAccessTask($task_id, $user_division, $user_role) {
    if($user_role == 'admin') {
        return true; // Admin can access all
    }
    
    $query = "SELECT functional_division FROM " . $this->table . " WHERE task_id = :task_id";
    $stmt = $this->getConnection()->prepare($query);
    $stmt->bindParam(':task_id', $task_id);
    $stmt->execute();
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return ($task && $task['functional_division'] == $user_division);
}

    

    public function updateTask($task_id, $data) {
    $query = "UPDATE " . $this->table . " 
              SET task_details = :task_details, 
                  functional_division = :functional_division, 
                  target_completion_date = :target_completion_date, 
                  priority = :priority,
                  budget_allocation = :budget_allocation
              WHERE task_id = :task_id";
    
    $data['task_id'] = $task_id;
    $stmt = $this->getConnection()->prepare($query);
    return $stmt->execute($data);
}

    public function getDivisionSummary() {
        $query = "SELECT 
                    functional_division,
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) >= 100 THEN 1 ELSE 0 END) as completed_tasks,
                    ROUND(AVG(CASE 
                        WHEN (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) IS NOT NULL 
                        THEN (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1)
                        ELSE 0 
                    END), 2) as average_percentage
                  FROM " . $this->table . " t
                  GROUP BY functional_division";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get summary for specific division (for encoder)
    public function getDivisionSummaryByDivision($division) {
    $query = "SELECT 
                functional_division,
                COUNT(*) as total_tasks,
                SUM(CASE WHEN (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) >= 100 THEN 1 ELSE 0 END) as completed_tasks,
                ROUND(AVG(CASE 
                    WHEN (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) IS NOT NULL 
                    THEN (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1)
                    ELSE 0 
                END), 2) as average_percentage
              FROM " . $this->table . " t
              WHERE functional_division = :division
              GROUP BY functional_division";
    
    try {
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':division', $division);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If no tasks found, return a default structure
        if(!$result) {
            return [
                'functional_division' => $division,
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'average_percentage' => 0
            ];
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error in getDivisionSummaryByDivision: " . $e->getMessage());
        return [
            'functional_division' => $division,
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'average_percentage' => 0
        ];
    }
}




public function getTasksWithFilters($filters = [], $limit = 10, $offset = 0) {
    $query = "SELECT t.*, 
              GROUP_CONCAT(DISTINCT u.unit_name SEPARATOR ', ') as unit_names,
              COUNT(DISTINCT tu.unit_id) as unit_count,
              (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as current_percentage,
              (SELECT created_at FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as last_update
              FROM tbl_task t
              LEFT JOIN tbl_task_units tu ON t.task_id = tu.task_id
              LEFT JOIN tbl_units u ON tu.unit_id = u.id
              WHERE 1=1";
    
    $params = [];
    
    // Apply filters
    if(!empty($filters['search'])) {
        $query .= " AND (t.task_details LIKE :search OR u.unit_name LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }
    
    if(!empty($filters['division'])) {
        $query .= " AND t.functional_division = :division"; // Changed from functional_direction
        $params[':division'] = $filters['division'];
    }
    
    if(!empty($filters['priority'])) {
        $query .= " AND t.priority = :priority";
        $params[':priority'] = $filters['priority'];
    }
    
    if(!empty($filters['project_id'])) {
        $query .= " AND t.project_id = :project_id";
        $params[':project_id'] = $filters['project_id'];
    }
    
    $query .= " GROUP BY t.task_id ORDER BY t.created_at DESC";
    
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

public function getTotalTaskCount($filters = []) {
    $query = "SELECT COUNT(DISTINCT t.task_id) as total FROM " . $this->table . " t";
    
    // If search filter is used, we need to join with task_units and units
    if(!empty($filters['search'])) {
        $query .= " LEFT JOIN tbl_task_units tu ON t.task_id = tu.task_id
                    LEFT JOIN tbl_units u ON tu.unit_id = u.id";
    }
    
    $whereConditions = [];
    $params = [];
    
    // Apply same filters as above
    if(!empty($filters['search'])) {
        $whereConditions[] = "(t.task_details LIKE :search OR u.unit_name LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }
    
    if(!empty($filters['division'])) {
        $whereConditions[] = "t.functional_division = :division"; // Changed from functional_direction
        $params[':division'] = $filters['division'];
    }
    
    if(!empty($filters['priority'])) {
        $whereConditions[] = "t.priority = :priority";
        $params[':priority'] = $filters['priority'];
    }
    
    if(!empty($filters['status'])) {
        if($filters['status'] == 'completed') {
            $whereConditions[] = "(SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) >= 100";
        } elseif($filters['status'] == 'in_progress') {
            $whereConditions[] = "(SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) > 0 AND (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) < 100";
        } elseif($filters['status'] == 'not_started') {
            $whereConditions[] = "((SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) IS NULL OR (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) = 0)";
        }
    }
    
    if(isset($filters['role']) && $filters['role'] == 'encoder' && !empty($filters['user_division'])) {
        $whereConditions[] = "t.functional_division = :user_division"; // Changed from functional_direction
        $params[':user_division'] = $filters['user_division'];
    }
    
    if(!empty($whereConditions)) {
        $query .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    $stmt = $this->getConnection()->prepare($query);
    
    foreach($params as $key => &$val) {
        $stmt->bindParam($key, $val);
    }
    
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}







// Add this method to get tasks for a specific project
public function getTasksByProject($project_id) {
    $query = "SELECT t.*, 
              GROUP_CONCAT(DISTINCT u.unit_name SEPARATOR ', ') as unit_names,
              (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as current_percentage,
              (SELECT remarks FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as latest_remarks,
              (SELECT update_date FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as last_update
              FROM " . $this->table . " t
              LEFT JOIN tbl_task_units tu ON t.task_id = tu.task_id
              LEFT JOIN tbl_units u ON tu.unit_id = u.id
              WHERE t.project_id = :project_id
              GROUP BY t.task_id
              ORDER BY t.priority DESC, t.created_at DESC";
    
    $stmt = $this->getConnection()->prepare($query);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function createTaskWithUnits($data, $unit_ids = []) {
    $this->getConnection()->beginTransaction();
    
    try {
        // Insert task - Note: column name is 'functional_division' not 'functional_direction'
        $query = "INSERT INTO tbl_task 
                  (task_details, project_id, functional_division, 
                   target_completion_date, priority, budget_allocation, created_by) 
                  VALUES 
                  (:task_details, :project_id, :functional_division,
                   :target_completion_date, :priority, :budget_allocation, :created_by)";
        
        $stmt = $this->getConnection()->prepare($query);
        
        $success = $stmt->execute([
            ':task_details' => $data['task_details'],
            ':project_id' => $data['project_id'] ?? null,
            ':functional_division' => $data['functional_division'] ?? $data['functional_division'], // Handle both possible keys
            ':target_completion_date' => $data['target_completion_date'] ?? null,
            ':priority' => $data['priority'] ?? 'medium',
            ':budget_allocation' => $data['budget_allocation'] ?? 0,
            ':created_by' => $data['created_by']
        ]);
        
        if(!$success) {
            throw new Exception("Failed to create task");
        }
        
        $task_id = $this->getConnection()->lastInsertId();
        
        // Link units to task
        if(!empty($unit_ids)) {
            $this->linkUnitsToTask($task_id, $unit_ids);
        }
        
        $this->getConnection()->commit();
        return ['success' => true, 'message' => 'Task created successfully', 'id' => $task_id];
        
    } catch (Exception $e) {
        $this->getConnection()->rollBack();
        return ['success' => false, 'message' => 'Failed to create task: ' . $e->getMessage()];
    }
}

// Link units to task
public function linkUnitsToTask($task_id, $unit_ids) {
    $query = "INSERT INTO tbl_task_units (task_id, unit_id) VALUES ";
    $values = [];
    $params = [];
    
    foreach($unit_ids as $index => $unit_id) {
        $values[] = "(:task_id, :unit_id{$index})";
        $params[":unit_id{$index}"] = $unit_id;
    }
    
    $query .= implode(", ", $values);
    $stmt = $this->getConnection()->prepare($query);
    $params[':task_id'] = $task_id;
    
    return $stmt->execute($params);
}

// Get task with its units
public function getTaskWithUnits($task_id) {
    $query = "SELECT t.*, 
              GROUP_CONCAT(DISTINCT tu.unit_id) as unit_ids,
              GROUP_CONCAT(DISTINCT u.unit_name SEPARATOR '||') as unit_names,
              GROUP_CONCAT(DISTINCT u.person_in_charge SEPARATOR '||') as unit_pics
              FROM tbl_task t
              LEFT JOIN tbl_task_units tu ON t.task_id = tu.task_id
              LEFT JOIN tbl_units u ON tu.unit_id = u.id
              WHERE t.task_id = :task_id
              GROUP BY t.task_id";
    
    $stmt = $this->getConnection()->prepare($query);
    $stmt->bindParam(':task_id', $task_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update task with units
public function updateTaskWithUnits($task_id, $data, $unit_ids = []) {
    $this->getConnection()->beginTransaction();
    
    try {
        $query = "UPDATE tbl_task 
                  SET task_details = :task_details,
                      functional_division = :functional_division,
                      target_completion_date = :target_completion_date,
                      priority = :priority,
                      budget_allocation = :budget_allocation
                  WHERE task_id = :task_id";
        
        $stmt = $this->getConnection()->prepare($query);
        
        $success = $stmt->execute([
            ':task_id' => $task_id,
            ':task_details' => $data['task_details'],
            ':functional_division' => $data['functional_division'] ?? $data['functional_division'],
            ':target_completion_date' => $data['target_completion_date'] ?? null,
            ':priority' => $data['priority'] ?? 'medium',
            ':budget_allocation' => $data['budget_allocation'] ?? 0
        ]);
        
        if(!$success) {
            throw new Exception("Failed to update task");
        }
        
        // Update task units - remove old and add new
        $this->removeAllUnitsFromTask($task_id);
        if(!empty($unit_ids)) {
            $this->linkUnitsToTask($task_id, $unit_ids);
        }
        
        $this->getConnection()->commit();
        return ['success' => true, 'message' => 'Task updated successfully'];
        
    } catch (Exception $e) {
        $this->getConnection()->rollBack();
        return ['success' => false, 'message' => 'Failed to update task: ' . $e->getMessage()];
    }
}

// Remove all units from task
public function removeAllUnitsFromTask($task_id) {
    $query = "DELETE FROM tbl_task_units WHERE task_id = :task_id";
    $stmt = $this->getConnection()->prepare($query);
    return $stmt->execute([':task_id' => $task_id]);
}



// Get all active units for selection
public function getAllUnits() {
    $query = "SELECT id, unit_name, functional_division, person_in_charge 
              FROM tbl_units 
              WHERE status = 'active' 
              ORDER BY functional_division, unit_name";
    $stmt = $this->getConnection()->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}
?>