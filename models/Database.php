<?php
require_once __DIR__ . '/../config/database.php';

class DatabaseModel {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>