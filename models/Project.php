<?php
require_once __DIR__ . '/Database.php';

class Project extends DatabaseModel {
    private $table = "tbl_projects";

    // READ - Get all projects with filters
    public function getAllProjects($filters = [], $limit = 10, $offset = 0) {
        $query = "SELECT p.*, 
                  COUNT(DISTINCT pu.unit_id) as total_units,
                  COUNT(DISTINCT t.task_id) as total_tasks,
                  (SELECT COUNT(*) FROM tbl_task WHERE project_id = p.project_id AND 
                    (SELECT percentage FROM tbl_status WHERE task_id = tbl_task.task_id ORDER BY created_at DESC LIMIT 1) >= 100) as completed_tasks
                  FROM " . $this->table . " p
                  LEFT JOIN tbl_project_units pu ON p.project_id = pu.project_id
                  LEFT JOIN tbl_task t ON p.project_id = t.project_id
                  WHERE 1=1";
        
        $params = [];
        
        // Apply filters
        if(!empty($filters['search'])) {
            $query .= " AND (p.project_name LIKE :search OR p.project_code LIKE :search OR p.project_description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        if(!empty($filters['division'])) {
            $query .= " AND p.functional_division = :division";
            $params[':division'] = $filters['division'];
        }
        
        if(!empty($filters['status'])) {
            $query .= " AND p.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if(!empty($filters['priority'])) {
            $query .= " AND p.priority = :priority";
            $params[':priority'] = $filters['priority'];
        }
        
        $query .= " GROUP BY p.project_id ORDER BY p.created_at DESC";
        
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

    public function getTotalProjectCount($filters = []) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
        
        $params = [];
        
        if(!empty($filters['search'])) {
            $query .= " AND (project_name LIKE :search OR project_code LIKE :search OR project_description LIKE :search)";
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
        
        if(!empty($filters['priority'])) {
            $query .= " AND priority = :priority";
            $params[':priority'] = $filters['priority'];
        }
        
        $stmt = $this->getConnection()->prepare($query);
        
        foreach($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // READ - Get single project by ID
    public function getProjectById($project_id) {
    $query = "SELECT p.*, 
              GROUP_CONCAT(DISTINCT u.id) as unit_ids,
              GROUP_CONCAT(DISTINCT u.unit_name SEPARATOR '||') as unit_names,
              COUNT(DISTINCT t.task_id) as total_tasks,
              SUM(CASE WHEN (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) >= 100 THEN 1 ELSE 0 END) as completed_tasks,
              ROUND(AVG(CASE 
                  WHEN (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1) IS NOT NULL 
                  THEN (SELECT percentage FROM tbl_status WHERE task_id = t.task_id ORDER BY created_at DESC LIMIT 1)
                  ELSE 0 
              END), 2) as avg_progress
              FROM " . $this->table . " p
              LEFT JOIN tbl_project_units pu ON p.project_id = pu.project_id
              LEFT JOIN tbl_units u ON pu.unit_id = u.id
              LEFT JOIN tbl_task t ON p.project_id = t.project_id
              WHERE p.project_id = :project_id
              GROUP BY p.project_id";
    
    $stmt = $this->getConnection()->prepare($query);
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    // CREATE - Add new project
    public function createProject($data, $unit_ids = []) {
        // Check if project code already exists
        if($this->projectCodeExists($data['project_code'])) {
            return ['success' => false, 'message' => 'Project code already exists'];
        }
        
        $this->getConnection()->beginTransaction();
        
        try {
            $query = "INSERT INTO " . $this->table . " 
          (project_code, project_name, project_description, functional_division, 
           project_lead, lead_designation, target_end_date, 
           budget_allocation, priority, status, created_by) 
          VALUES 
          (:project_code, :project_name, :project_description, :functional_division,
           :project_lead, :lead_designation, :target_end_date,
           :budget_allocation, :priority, :status, :created_by)";
            
            $stmt = $this->getConnection()->prepare($query);
            
            $success = $stmt->execute([
    ':project_code' => $data['project_code'],
    ':project_name' => $data['project_name'],
    ':project_description' => $data['project_description'] ?? null,
    ':functional_division' => $data['functional_division'],
    ':project_lead' => $data['project_lead'] ?? null,
    ':lead_designation' => $data['lead_designation'] ?? null,
    ':target_end_date' => $data['target_end_date'] ?? null,
    ':budget_allocation' => $data['budget_allocation'] ?? 0,
    ':priority' => $data['priority'] ?? 'medium',
    ':status' => $data['status'] ?? 'planning',
    ':created_by' => $data['created_by']
]);
            
            if(!$success) {
                throw new Exception("Failed to create project");
            }
            
            $project_id = $this->getConnection()->lastInsertId();
            
            // Link units to project
            if(!empty($unit_ids)) {
                $this->linkUnitsToProject($project_id, $unit_ids);
            }
            
            $this->getConnection()->commit();
            return ['success' => true, 'message' => 'Project created successfully', 'id' => $project_id];
            
        } catch (Exception $e) {
            $this->getConnection()->rollBack();
            return ['success' => false, 'message' => 'Failed to create project: ' . $e->getMessage()];
        }
    }

    // UPDATE - Update project
    public function updateProject($project_id, $data, $unit_ids = []) {
        // Check if project code already exists (excluding current project)
        if(isset($data['project_code']) && $this->projectCodeExists($data['project_code'], $project_id)) {
            return ['success' => false, 'message' => 'Project code already exists'];
        }
        
        $this->getConnection()->beginTransaction();
        
        try {
            $query = "UPDATE " . $this->table . " 
          SET project_code = :project_code,
              project_name = :project_name,
              project_description = :project_description,
              functional_division = :functional_division,
              project_lead = :project_lead,
              lead_designation = :lead_designation,
              target_end_date = :target_end_date,
              budget_allocation = :budget_allocation,
              priority = :priority,
              status = :status
          WHERE project_id = :project_id";
            
            $stmt = $this->getConnection()->prepare($query);
            
            $success = $stmt->execute([
    ':project_id' => $project_id,
    ':project_code' => $data['project_code'],
    ':project_name' => $data['project_name'],
    ':project_description' => $data['project_description'] ?? null,
    ':functional_division' => $data['functional_division'],
    ':project_lead' => $data['project_lead'] ?? null,
    ':lead_designation' => $data['lead_designation'] ?? null,
    ':target_end_date' => $data['target_end_date'] ?? null,
    ':budget_allocation' => $data['budget_allocation'] ?? 0,
    ':priority' => $data['priority'] ?? 'medium',
    ':status' => $data['status'] ?? 'planning'
]);
            
            if(!$success) {
                throw new Exception("Failed to update project");
            }
            
            // Update project units
            if(!empty($unit_ids)) {
                // Remove existing links
                $this->removeAllUnitsFromProject($project_id);
                // Add new links
                $this->linkUnitsToProject($project_id, $unit_ids);
            }
            
            $this->getConnection()->commit();
            return ['success' => true, 'message' => 'Project updated successfully'];
            
        } catch (Exception $e) {
            $this->getConnection()->rollBack();
            return ['success' => false, 'message' => 'Failed to update project: ' . $e->getMessage()];
        }
    }

    // DELETE - Delete project
    public function deleteProject($project_id) {
        // Check if project has tasks
        $query = "SELECT COUNT(*) as total FROM tbl_task WHERE project_id = :project_id";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($result['total'] > 0) {
            return ['success' => false, 'message' => 'Cannot delete project with existing tasks'];
        }
        
        $query = "DELETE FROM " . $this->table . " WHERE project_id = :project_id";
        $stmt = $this->getConnection()->prepare($query);
        
        $success = $stmt->execute([':project_id' => $project_id]);
        
        if($success) {
            return ['success' => true, 'message' => 'Project deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete project'];
        }
    }

    // Helper methods for project units
    // Change from private to public
public function linkUnitsToProject($project_id, $unit_ids) {
    $query = "INSERT INTO tbl_project_units (project_id, unit_id) VALUES ";
    $values = [];
    $params = [];
    
    foreach($unit_ids as $index => $unit_id) {
        $values[] = "(:project_id, :unit_id{$index})";
        $params[":unit_id{$index}"] = $unit_id;
    }
    
    $query .= implode(", ", $values);
    $stmt = $this->getConnection()->prepare($query);
    $params[':project_id'] = $project_id;
    
    return $stmt->execute($params);
}

    private function removeAllUnitsFromProject($project_id) {
        $query = "DELETE FROM tbl_project_units WHERE project_id = :project_id";
        $stmt = $this->getConnection()->prepare($query);
        return $stmt->execute([':project_id' => $project_id]);
    }

    public function getProjectUnits($project_id) {
        $query = "SELECT u.* FROM tbl_units u
                  INNER JOIN tbl_project_units pu ON u.id = pu.unit_id
                  WHERE pu.project_id = :project_id";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function projectCodeExists($project_code, $exclude_id = null) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE project_code = :project_code";
        if($exclude_id) {
            $query .= " AND project_id != :exclude_id";
        }
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':project_code', $project_code);
        if($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    // Get project summary by division
    public function getProjectSummary() {
        $query = "SELECT 
                    functional_division,
                    COUNT(*) as total_projects,
                    SUM(CASE WHEN status = 'ongoing' THEN 1 ELSE 0 END) as ongoing_projects,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_projects,
                    SUM(CASE WHEN status = 'planning' THEN 1 ELSE 0 END) as planning_projects,
                    AVG(progress_percentage) as avg_progress
                  FROM " . $this->table . " 
                  GROUP BY functional_division";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get projects for a specific unit
    public function getProjectsByUnit($unit_id) {
        $query = "SELECT p.* FROM " . $this->table . " p
                  INNER JOIN tbl_project_units pu ON p.project_id = pu.project_id
                  WHERE pu.unit_id = :unit_id
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getProjectsForDropdown() {
    $query = "SELECT 
                project_id as id, 
                project_name, 
                functional_division, 
                target_end_date, 
                budget_allocation 
              FROM " . $this->table . " 
              WHERE status != 'cancelled' 
              ORDER BY project_name ASC";
    
    $stmt = $this->getConnection()->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>