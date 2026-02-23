<?php
require_once __DIR__ . '/Database.php';

class User extends DatabaseModel {
    private $table = "users";

    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE username = :username 
                  AND password = :password 
                  AND status = 'active'";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        
        if($stmt->execute()) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user) {
                // Update last login
                $this->updateLastLogin($user['id']);
                return $user;
            }
        }
        return false;
    }

    private function updateLastLogin($user_id) {
        $query = "UPDATE " . $this->table . " 
                  SET last_login = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
    }

    public function getUserById($id) {
        $query = "SELECT id, username, email, full_name, role, functional_division, last_login 
                  FROM " . $this->table . " 
                  WHERE id = :id";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $query = "SELECT id, username, email, full_name, role, functional_division, status, last_login 
                  FROM " . $this->table . " 
                  ORDER BY created_at DESC";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get encoders by division
    public function getEncodersByDivision($division) {
        $query = "SELECT id, username, full_name, email 
                  FROM " . $this->table . " 
                  WHERE role = 'encoder' 
                  AND functional_division = :division 
                  AND status = 'active'";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':division', $division);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>