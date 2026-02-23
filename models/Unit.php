<?php
require_once __DIR__ . '/Database.php';

class Unit extends DatabaseModel {
    private $table = "tbl_units";

    public function getAllUnits() {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'active' ORDER BY functional_division, unit_name";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnitsByDivision($division) {
        $query = "SELECT * FROM " . $this->table . " WHERE functional_division = :division AND status = 'active' ORDER BY unit_name";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':division', $division);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnitById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->getConnection()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUnit($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (functional_division, unit_name, person_in_charge, designation, email, contact_number) 
                  VALUES (:functional_division, :unit_name, :person_in_charge, :designation, :email, :contact_number)";
        
        $stmt = $this->getConnection()->prepare($query);
        return $stmt->execute($data);
    }

    public function updateUnit($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET functional_division = :functional_division, 
                      unit_name = :unit_name, 
                      person_in_charge = :person_in_charge, 
                      designation = :designation, 
                      email = :email, 
                      contact_number = :contact_number 
                  WHERE id = :id";
        
        $data['id'] = $id;
        $stmt = $this->getConnection()->prepare($query);
        return $stmt->execute($data);
    }
}
?>