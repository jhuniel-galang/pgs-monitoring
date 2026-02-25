<?php
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Unit.php';

class UnitController {
    private $auth;
    private $unit;

    public function __construct() {
        $this->auth = new AuthController();
        $this->unit = new Unit();
        
        // Check if user is logged in and is admin
        if(!$this->auth->isLoggedIn() || $_SESSION['role'] != 'admin') {
            header("Location: index.php");
            exit();
        }
    }

    // List all units (READ)
    public function index() {
    // Pagination settings
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    
    // Build filters
    $filters = [
        'search' => $_GET['search'] ?? '',
        'division' => $_GET['division'] ?? '',
        'status' => $_GET['status'] ?? ''
    ];
    
    // Get filtered units with pagination
    $units = $this->unit->getUnitsWithFilters($filters, $limit, $offset);
    $total_units = $this->unit->getTotalUnitCount($filters);
    $total_pages = ceil($total_units / $limit);
    
    // Get division summary for cards
    $division_summary = $this->unit->getDivisionSummary();
    
    require_once __DIR__ . '/../views/units/index.php';
}

    // Store new unit (CREATE)
    public function store() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'functional_division' => $_POST['functional_division'],
                'unit_name' => $_POST['unit_name'],
                'person_in_charge' => $_POST['person_in_charge'] ?? null,
                'designation' => $_POST['designation'] ?? null,
                'email' => $_POST['email'] ?? null,
                'contact_number' => $_POST['contact_number'] ?? null,
                'status' => $_POST['status'] ?? 'active'
            ];
            
            $result = $this->unit->createUnit($data);
            
            if($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            header("Location: index.php?action=units");
            exit();
        }
    }

    // Update unit (UPDATE)
    public function update() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            
            $data = [
                'functional_division' => $_POST['functional_division'],
                'unit_name' => $_POST['unit_name'],
                'person_in_charge' => $_POST['person_in_charge'] ?? null,
                'designation' => $_POST['designation'] ?? null,
                'email' => $_POST['email'] ?? null,
                'contact_number' => $_POST['contact_number'] ?? null,
                'status' => $_POST['status']
            ];
            
            $result = $this->unit->updateUnit($id, $data);
            
            if($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            header("Location: index.php?action=units");
            exit();
        }
    }

    // Delete unit (DELETE)
    public function delete() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            
            $result = $this->unit->deleteUnit($id);
            
            if($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            header("Location: index.php?action=units");
            exit();
        }
    }
}
?>