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
        $query = "SELECT id, username, email, full_name, role, functional_division, last_login, status 
                  FROM " . $this->table . " 
                  WHERE id = :id";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $query = "SELECT id, username, email, full_name, role, functional_division, status, last_login, created_at
                  FROM " . $this->table . " 
                  ORDER BY created_at DESC";
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CREATE - Add new user
    public function createUser($data) {
        // Check if username already exists
        if($this->usernameExists($data['username'])) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        // Check if email already exists (if provided)
        if(!empty($data['email']) && $this->emailExists($data['email'])) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        $query = "INSERT INTO " . $this->table . " 
                  (username, password, email, full_name, role, functional_division, status) 
                  VALUES (:username, :password, :email, :full_name, :role, :functional_division, :status)";
        
        $stmt = $this->getConnection()->prepare($query);
        
        $success = $stmt->execute([
            ':username' => $data['username'],
            ':password' => $data['password'], // No hashing as per requirement
            ':email' => $data['email'] ?: null,
            ':full_name' => $data['full_name'] ?: null,
            ':role' => $data['role'],
            ':functional_division' => $data['functional_division'] ?: null,
            ':status' => $data['status'] ?? 'active'
        ]);
        
        if($success) {
            return ['success' => true, 'message' => 'User created successfully', 'id' => $this->getConnection()->lastInsertId()];
        } else {
            return ['success' => false, 'message' => 'Failed to create user'];
        }
    }

    // UPDATE - Update user
    public function updateUser($id, $data) {
        // Check if username already exists (excluding current user)
        if(isset($data['username']) && $this->usernameExists($data['username'], $id)) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        // Check if email already exists (excluding current user)
        if(!empty($data['email']) && $this->emailExists($data['email'], $id)) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET username = :username,
                      email = :email,
                      full_name = :full_name,
                      role = :role,
                      functional_division = :functional_division,
                      status = :status";
        
        // Add password to update if provided
        if(!empty($data['password'])) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->getConnection()->prepare($query);
        
        $params = [
            ':id' => $id,
            ':username' => $data['username'],
            ':email' => $data['email'] ?: null,
            ':full_name' => $data['full_name'] ?: null,
            ':role' => $data['role'],
            ':functional_division' => $data['functional_division'] ?: null,
            ':status' => $data['status']
        ];
        
        if(!empty($data['password'])) {
            $params[':password'] = $data['password'];
        }
        
        $success = $stmt->execute($params);
        
        if($success) {
            return ['success' => true, 'message' => 'User updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update user'];
        }
    }

    // DELETE - Delete user
    public function deleteUser($id) {
        // Don't allow deleting the last admin
        if($this->isLastAdmin($id)) {
            return ['success' => false, 'message' => 'Cannot delete the last admin user'];
        }
        
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->getConnection()->prepare($query);
        
        $success = $stmt->execute([':id' => $id]);
        
        if($success) {
            return ['success' => true, 'message' => 'User deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete user'];
        }
    }

    // Helper methods
    private function usernameExists($username, $exclude_id = null) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE username = :username";
        if($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':username', $username);
        if($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    private function emailExists($email, $exclude_id = null) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE email = :email";
        if($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':email', $email);
        if($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    private function isLastAdmin($id) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE role = 'admin' AND id != :id";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetchColumn() == 0;
    }

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