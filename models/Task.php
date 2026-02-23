<?php
require_once __DIR__ . '/Database.php';

class Task extends DatabaseModel {
    private $table = "tbl_task";

    public function getAllTasks() {
        $query = "SELECT t.*, u.unit_name, u.person_in_charge,
                  (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as current_percentage,
                  (SELECT remarks FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as latest_remarks,
                  (SELECT update_date FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as last_update,
                  (SELECT updated_by FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as last_updated_by
                  FROM " . $this->table . " t
                  LEFT JOIN tbl_units u ON t.unit_id = u.id
                  ORDER BY t.functional_division, t.priority DESC, t.created_at DESC";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // For encoders - only get tasks from their division
    public function getTasksByDivisionForEncoder($division, $user_id = null) {
        $query = "SELECT t.*, u.unit_name, u.person_in_charge,
                  (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as current_percentage,
                  (SELECT remarks FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as latest_remarks,
                  (SELECT update_date FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as last_update,
                  (SELECT updated_by FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as last_updated_by
                  FROM " . $this->table . " t
                  LEFT JOIN tbl_units u ON t.unit_id = u.id
                  WHERE t.functional_division = :division
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
        $query = "SELECT t.*, u.unit_name, u.person_in_charge,
                  (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) as current_percentage
                  FROM " . $this->table . " t
                  LEFT JOIN tbl_units u ON t.unit_id = u.id
                  WHERE t.task_id = :task_id";
        
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

    public function createTask($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (task_details, unit_id, functional_division, target_completion_date, priority, created_by) 
                  VALUES (:task_details, :unit_id, :functional_division, :target_completion_date, :priority, :created_by)";
        
        $stmt = $this->getConnection()->prepare($query);
        return $stmt->execute($data);
    }

    public function updateTask($task_id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET task_details = :task_details, 
                      unit_id = :unit_id, 
                      functional_division = :functional_division, 
                      target_completion_date = :target_completion_date, 
                      priority = :priority 
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
}
?>