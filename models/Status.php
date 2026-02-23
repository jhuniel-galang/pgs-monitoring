<?php
require_once __DIR__ . '/Database.php';

class Status extends DatabaseModel {
    private $table = "tbl_status";

    public function updateTaskStatus($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (task_id, percentage, remarks, updated_by, update_date) 
                  VALUES (:task_id, :percentage, :remarks, :updated_by, :update_date)";
        
        $stmt = $this->getConnection()->prepare($query);
        return $stmt->execute($data);
    }

    public function getTaskStatusHistory($task_id) {
        $query = "SELECT s.*, u.username as updated_by_name 
                  FROM " . $this->table . " s
                  LEFT JOIN users u ON s.updated_by = u.id
                  WHERE s.task_id = :task_id
                  ORDER BY s.created_at DESC";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLatestStatus($task_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE task_id = :task_id 
                  ORDER BY created_at DESC 
                  LIMIT 1";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>