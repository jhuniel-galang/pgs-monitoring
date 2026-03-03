<?php
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Unit.php';

class ProjectController {
    private $auth;
    private $project;
    private $unit;

    public function __construct() {
        $this->auth = new AuthController();
        $this->project = new Project();
        $this->unit = new Unit();
        
        // Check if user is logged in
        if(!$this->auth->isLoggedIn()) {
            header("Location: index.php");
            exit();
        }
    }


// List all projects
public function index() {
    $user = $this->auth->getCurrentUser();
    
    // Pagination settings
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    
    // Build filters
    $filters = [
        'search' => $_GET['search'] ?? '',
        'division' => $_GET['division'] ?? '',
        'priority' => $_GET['priority'] ?? '',
        'year' => $_GET['year'] ?? '' // Add year filter as text
    ];
    
    // Add role-based filter for encoders
    if($user['role'] == 'encoder') {
        $filters['division'] = $user['functional_division'];
    }
    
    // Get filtered projects
    $projects = $this->project->getAllProjects($filters, $limit, $offset);
    $total_projects = $this->project->getTotalProjectCount($filters);
    $total_pages = ceil($total_projects / $limit);
    
    // Get summary for cards
    $project_summary = $this->project->getProjectSummary();
    
    // Get all units for the project modals
    $units = $this->unit->getAllUnits();
    
    require_once __DIR__ . '/../views/projects/index.php';
}

    // Store new project
public function store() {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = $this->auth->getCurrentUser();
        
        if($user['role'] != 'admin') {
            $_SESSION['error'] = "Unauthorized access";
            header("Location: index.php?action=projects");
            exit();
        }
        
        $data = [
            'project_code' => $_POST['project_code'],
            'project_name' => $_POST['project_name'],
            'project_description' => $_POST['project_description'] ?? null,
            'functional_division' => $_POST['functional_division'],
            'year' => $_POST['year'] ?? '', // ADD THIS LINE - include the year field
            'project_lead' => $_POST['project_lead'] ?? null,
            'lead_designation' => $_POST['lead_designation'] ?? null,
            'start_date' => $_POST['start_date'] ?? null,
            'target_end_date' => $_POST['target_end_date'] ?? null,
            'budget_allocation' => $_POST['budget_allocation'] ?? 0,
            'priority' => $_POST['priority'] ?? 'medium',
            'status' => $_POST['status'] ?? 'planning',
            'created_by' => $_SESSION['user_id']
        ];
        
        $unit_ids = $_POST['unit_ids'] ?? [];
        
        $result = $this->project->createProject($data, $unit_ids);
        
        if($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        header("Location: index.php?action=projects");
        exit();
    }
}

    // Update project
public function update() {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = $this->auth->getCurrentUser();
        
        if($user['role'] != 'admin') {
            $_SESSION['error'] = "Unauthorized access";
            header("Location: index.php?action=projects");
            exit();
        }
        
        $project_id = $_POST['project_id'];
        
        $data = [
            'project_code' => $_POST['project_code'],
            'project_name' => $_POST['project_name'],
            'project_description' => $_POST['project_description'] ?? null,
            'functional_division' => $_POST['functional_division'],
            'year' => $_POST['year'] ?? '', // ADD THIS LINE - include the year field
            'project_lead' => $_POST['project_lead'] ?? null,
            'lead_designation' => $_POST['lead_designation'] ?? null,
            'start_date' => $_POST['start_date'] ?? null,
            'target_end_date' => $_POST['target_end_date'] ?? null,
            'budget_allocation' => $_POST['budget_allocation'] ?? 0,
            'priority' => $_POST['priority'] ?? 'medium',
            'status' => $_POST['status'] ?? 'planning'
        ];
        
        $unit_ids = $_POST['unit_ids'] ?? [];
        
        $result = $this->project->updateProject($project_id, $data, $unit_ids);
        
        if($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        header("Location: index.php?action=projects");
        exit();
    }
}
    // Delete project
    public function delete() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = $this->auth->getCurrentUser();
            
            if($user['role'] != 'admin') {
                $_SESSION['error'] = "Unauthorized access";
                header("Location: index.php?action=projects");
                exit();
            }
            
            $project_id = $_POST['project_id'];
            
            $result = $this->project->deleteProject($project_id);
            
            if($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            header("Location: index.php?action=projects");
            exit();
        }
    }

    // View project details
    // View project details
public function view($project_id) {
    $user = $this->auth->getCurrentUser();
    
    $project = $this->project->getProjectById($project_id);
    
    if(!$project) {
        $_SESSION['error'] = "Project not found";
        header("Location: index.php?action=projects");
        exit();
    }
    
    // Check if user can view this project
    if($user['role'] == 'encoder' && $project['functional_division'] != $user['functional_division']) {
        $_SESSION['error'] = "You don't have permission to view this project";
        header("Location: index.php?action=projects");
        exit();
    }
    
    $project_units = $this->project->getProjectUnits($project_id);
    
    // Get all units for the Add Units modal
    $units = $this->unit->getAllUnits();
    
    require_once __DIR__ . '/../views/projects/view.php';
}


    public function updateProgress() {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = $this->auth->getCurrentUser();
        $project_id = $_POST['project_id'];
        
        $data = [
            'progress_percentage' => $_POST['progress_percentage'],
            'status' => $_POST['status']
        ];
        
        $query = "UPDATE tbl_projects SET progress_percentage = :progress, status = :status WHERE project_id = :id";
        $stmt = $this->project->getConnection()->prepare($query);
        
        if($stmt->execute([
            ':progress' => $data['progress_percentage'],
            ':status' => $data['status'],
            ':id' => $project_id
        ])) {
            $_SESSION['success'] = "Project progress updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update project progress";
        }
        
        header("Location: index.php?action=view_project&id=" . $project_id);
        exit();
    }
}

// Add units to project
public function addUnits() {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $project_id = $_POST['project_id'];
        $unit_ids = $_POST['unit_ids'] ?? [];
        
        if(!empty($unit_ids)) {
            $this->project->linkUnitsToProject($project_id, $unit_ids);
            $_SESSION['success'] = "Units added to project successfully";
        } else {
            $_SESSION['error'] = "No units selected";
        }
        
        header("Location: index.php?action=view_project&id=" . $project_id);
        exit();
    }
}

// Remove unit from project
public function removeUnit() {
    $project_id = $_GET['project_id'];
    $unit_id = $_GET['unit_id'];
    
    $query = "DELETE FROM tbl_project_units WHERE project_id = :project_id AND unit_id = :unit_id";
    $stmt = $this->project->getConnection()->prepare($query);
    
    if($stmt->execute([':project_id' => $project_id, ':unit_id' => $unit_id])) {
        $_SESSION['success'] = "Unit removed from project successfully";
    } else {
        $_SESSION['error'] = "Failed to remove unit";
    }
    
    header("Location: index.php?action=view_project&id=" . $project_id);
    exit();
}


// Get project details as JSON for AJAX modal
// Add this method to your ProjectController.php
public function getProjectJson() {
    // Set header to return JSON
    header('Content-Type: application/json');
    
    // Check if user is logged in
    if(!$this->auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }
    
    // Get project ID
    $project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if($project_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid project ID']);
        exit;
    }
    
    // Get user for permission check
    $user = $this->auth->getCurrentUser();
    
    // Get project details
    $project = $this->project->getProjectById($project_id);
    
    if(!$project) {
        echo json_encode(['success' => false, 'message' => 'Project not found']);
        exit;
    }
    
    // Check if user can view this project
    if($user['role'] == 'encoder' && $project['functional_division'] != $user['functional_division']) {
        echo json_encode(['success' => false, 'message' => 'You don\'t have permission to view this project']);
        exit;
    }
    
    // Get project units
    $project_units = $this->project->getProjectUnits($project_id);
    
    // Get tasks for this project
    require_once __DIR__ . '/../models/Task.php';
    $taskModel = new Task();
    $project_tasks = $taskModel->getTasksByProject($project_id);
    
    // Format dates
    $start_date = $project['start_date'] ? date('F d, Y', strtotime($project['start_date'])) : null;
    $target_end_date = $project['target_end_date'] ? date('F d, Y', strtotime($project['target_end_date'])) : null;
    
    // Calculate days left or overdue
    $days_left = null;
    $is_overdue = false;
    if($project['target_end_date']) {
        $today = new DateTime();
        $end = new DateTime($project['target_end_date']);
        $interval = $today->diff($end);
        $days_left = $interval->days;
        $is_overdue = $today > $end;
    }
    
    // Format the data for JSON response
    echo json_encode([
        'success' => true, 
        'project' => [
            'project_id' => $project['project_id'],
            'project_code' => $project['project_code'],
            'project_name' => $project['project_name'],
            'project_description' => $project['project_description'] ?? 'No description provided.',
            'functional_division' => $project['functional_division'],
            'project_lead' => $project['project_lead'] ?? 'Not assigned',
            'lead_designation' => $project['lead_designation'] ?? '',
            'start_date' => $start_date,
            'target_end_date' => $target_end_date,
            'actual_end_date' => $project['actual_end_date'] ?? null,
            'status' => $project['status'],
            'priority' => $project['priority'],
            'progress_percentage' => $project['avg_progress'] ?? $project['progress_percentage'] ?? 0,
            'budget_allocation' => $project['budget_allocation'] ?? 0,
            'completed_tasks' => $project['completed_tasks'] ?? 0,
            'total_tasks' => $project['total_tasks'] ?? 0,
            'days_left' => $days_left,
            'is_overdue' => $is_overdue,
            'units' => array_map(function($unit) {
                return [
                    'id' => $unit['id'],
                    'unit_name' => $unit['unit_name'],
                    'person_in_charge' => $unit['person_in_charge'] ?? 'N/A',
                    'functional_division' => $unit['functional_division']
                ];
            }, $project_units),
            'tasks' => array_map(function($task) {
                // Get latest progress percentage for task
                $progress = 0;
                if(isset($task['current_percentage'])) {
                    $progress = $task['current_percentage'];
                }
                
                return [
                    'task_id' => $task['task_id'],
                    'task_details' => $task['task_details'],
                    'unit_name' => $task['unit_name'] ?? 'N/A',
                    'priority' => $task['priority'],
                    'progress' => $progress,
                    'last_update' => $task['last_update'] ?? null
                ];
            }, $project_tasks)
        ]
    ]);
    exit;
}





// Add units to project
public function addProjectUnits() {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $project_id = $_POST['project_id'];
        $unit_ids = $_POST['unit_ids'] ?? [];
        
        if(!empty($unit_ids)) {
            // Get current units
            $current_units = $this->project->getProjectUnits($project_id);
            $current_unit_ids = array_column($current_units, 'id');
            
            // Filter out units that are already assigned
            $new_unit_ids = array_diff($unit_ids, $current_unit_ids);
            
            if(!empty($new_unit_ids)) {
                $result = $this->project->linkUnitsToProject($project_id, $new_unit_ids);
                if($result) {
                    $_SESSION['success'] = count($new_unit_ids) . " unit(s) added to project successfully";
                } else {
                    $_SESSION['error'] = "Failed to add units to project";
                }
            } else {
                $_SESSION['error'] = "Selected units are already assigned to this project";
            }
        } else {
            $_SESSION['error'] = "No units selected";
        }
        
        header("Location: index.php?action=view_project&id=" . $project_id);
        exit();
    }
}

// Remove unit from project
public function removeProjectUnit() {
    $project_id = $_GET['project_id'];
    $unit_id = $_GET['unit_id'];
    
    $query = "DELETE FROM tbl_project_units WHERE project_id = :project_id AND unit_id = :unit_id";
    $stmt = $this->project->getConnection()->prepare($query);
    
    if($stmt->execute([':project_id' => $project_id, ':unit_id' => $unit_id])) {
        $_SESSION['success'] = "Unit removed from project successfully";
    } else {
        $_SESSION['error'] = "Failed to remove unit";
    }
    
    header("Location: index.php?action=view_project&id=" . $project_id);
    exit();
}
}
?>