<?php
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Unit.php';
require_once __DIR__ . '/../models/Status.php';

class TaskController {
    private $auth;
    private $task;
    private $unit;
    private $status;

    public function __construct() {
        $this->auth = new AuthController();
        $this->task = new Task();
        $this->unit = new Unit();
        $this->status = new Status();
        
        // Check if user is logged in
        if(!$this->auth->isLoggedIn()) {
            header("Location: index.php");
            exit();
        }
    }

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
            'status' => $_GET['status'] ?? ''
        ];
        
        // Add role-based filter
        if($user['role'] == 'encoder') {
            $filters['role'] = 'encoder';
            $filters['user_division'] = $user['functional_division'];
        }
        
        // Get filtered tasks with pagination
        $tasks = $this->task->getTasksWithFilters($filters, $limit, $offset);
        $total_tasks = $this->task->getTotalTaskCount($filters);
        $total_pages = ceil($total_tasks / $limit);
        
        // Get division summary (for the cards)
        if($user['role'] == 'admin') {
            $division_summary = $this->task->getDivisionSummary();
        } else {
            $summary = $this->task->getDivisionSummaryByDivision($user['functional_division']);
            $division_summary = $summary ? [$summary] : [];
        }
        
        // Get all units for the create modal (for multiple selection)
        $units = $this->unit->getAllUnits();
        
        require_once __DIR__ . '/../views/tasks/index.php';
    }

    public function create() {
    $user = $this->auth->getCurrentUser();
    
    // Only admin can create tasks
    if($user['role'] != 'admin') {
        $_SESSION['error'] = "Unauthorized access";
        header("Location: index.php?action=tasks");
        exit();
    }
    
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Debug: Check what's being posted
        error_log("POST data: " . print_r($_POST, true));
        
        // Validate input - Note: field name is 'functional_division' not 'functional_direction'
        if(empty($_POST['task_details'])) {
            $_SESSION['error'] = "Task details is required";
            header("Location: index.php?action=tasks");
            exit();
        }
        
        if(empty($_POST['functional_division'])) {
            $_SESSION['error'] = "Division is required";
            header("Location: index.php?action=tasks");
            exit();
        }
        
        if(empty($_POST['unit_ids']) || !is_array($_POST['unit_ids']) || count($_POST['unit_ids']) == 0) {
            $_SESSION['error'] = "At least one unit must be selected";
            header("Location: index.php?action=tasks");
            exit();
        }
        
        if(empty($_POST['target_completion_date'])) {
            $_SESSION['error'] = "Target completion date is required";
            header("Location: index.php?action=tasks");
            exit();
        }
        
        // Prepare data - use 'functional_division' from form
        $data = [
            'task_details' => $_POST['task_details'],
            'functional_direction' => $_POST['functional_division'], // Map form field to model field
            'project_id' => !empty($_POST['project_id']) ? $_POST['project_id'] : null,
            'target_completion_date' => $_POST['target_completion_date'],
            'priority' => $_POST['priority'] ?? 'medium',
            'budget_allocation' => $_POST['budget_allocation'] ?? 0,
            'created_by' => $_SESSION['user_id']
        ];
        
        $unit_ids = $_POST['unit_ids']; // This is already an array from the checkboxes
        
        // Use the new method that handles multiple units
        $result = $this->task->createTaskWithUnits($data, $unit_ids);
        
        if($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        // Redirect back to the referring page if available
        if(isset($_POST['from_project']) && $_POST['from_project'] == 'yes') {
            header("Location: index.php?action=view_project&id=" . $_POST['project_id']);
        } else {
            header("Location: index.php?action=tasks");
        }
        exit();
    }
    
    // If not POST request, show the create page
    $units = $this->unit->getAllUnits();
    require_once __DIR__ . '/../views/tasks/create.php';
}

    public function updateStatus() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = $this->auth->getCurrentUser();
            $task_id = $_POST['task_id'];
            
            // Check if user can update this task
            if($user['role'] != 'admin') {
                // Verify encoder has access to this task's division
                if(!$this->task->canUserAccessTask($task_id, $user['functional_division'], $user['role'])) {
                    $_SESSION['error'] = "You don't have permission to update this task";
                    header("Location: index.php?action=tasks");
                    exit();
                }
            }
            
            $data = [
                'task_id' => $task_id,
                'percentage' => $_POST['percentage'],
                'remarks' => $_POST['remarks'],
                'updated_by' => $_SESSION['user_id'],
                'update_date' => date('Y-m-d')
            ];
            
            if($this->status->updateTaskStatus($data)) {
                $_SESSION['success'] = "Status updated successfully";
                header("Location: index.php?action=tasks");
                exit();
            } else {
                $_SESSION['error'] = "Failed to update status";
                header("Location: index.php?action=tasks");
                exit();
            }
        }
    }

    public function view($task_id) {
        $user = $this->auth->getCurrentUser();
        
        // Check if task_id is valid
        if(!$task_id || $task_id <= 0) {
            $_SESSION['error'] = "Invalid task ID";
            header("Location: index.php?action=tasks");
            exit();
        }
        
        // Check if user can view this task
        if($user['role'] != 'admin') {
            if(!$this->task->canUserAccessTask($task_id, $user['functional_division'], $user['role'])) {
                $_SESSION['error'] = "You don't have permission to view this task";
                header("Location: index.php?action=tasks");
                exit();
            }
        }
        
        // Get task details with units (using new method)
        $task = $this->task->getTaskWithUnits($task_id);
        
        // Check if task exists
        if(!$task) {
            $_SESSION['error'] = "Task not found";
            header("Location: index.php?action=tasks");
            exit();
        }
        
        // Get status history
        $status_history = $this->status->getTaskStatusHistory($task_id);
        
        // Get all units for potential editing
        $all_units = $this->unit->getAllUnits();
        
        // Load the view
        require_once __DIR__ . '/../views/tasks/view.php';
    }

    public function dashboard() {
        $user = $this->auth->getCurrentUser();
        
        if($user['role'] == 'admin') {
            $division_summary = $this->task->getDivisionSummary();
            $recent_tasks = $this->task->getAllTasks();
        } else {
            // For encoder, get only their division data
            $recent_tasks = $this->task->getTasksByDivisionForEncoder($user['functional_division']);
            
            // Get division summary
            $summary = $this->task->getDivisionSummaryByDivision($user['functional_division']);
            
            // Initialize division_summary as an empty array
            $division_summary = [];
            
            // Check if summary is valid and add to array
            if($summary && is_array($summary) && isset($summary['functional_division'])) {
                $division_summary = [$summary];
            } else {
                // Create a default summary if none exists
                $division_summary = [[
                    'functional_division' => $user['functional_division'],
                    'total_tasks' => count($recent_tasks),
                    'completed_tasks' => 0,
                    'average_percentage' => 0
                ]];
            }
        }
        
        // Get only 5 most recent
        $recent_tasks = array_slice($recent_tasks, 0, 5);
        
        require_once __DIR__ . '/../views/tasks/dashboard.php';
    }

    // New method for dedicated update page
    public function updatePage($task_id) {
        $user = $this->auth->getCurrentUser();
        
        // Check if user can update this task
        if($user['role'] != 'admin') {
            if(!$this->task->canUserAccessTask($task_id, $user['functional_division'], $user['role'])) {
                $_SESSION['error'] = "You don't have permission to update this task";
                header("Location: index.php?action=tasks");
                exit();
            }
        }
        
        // Get task with units
        $task = $this->task->getTaskWithUnits($task_id);
        $status_history = $this->status->getTaskStatusHistory($task_id);
        $all_units = $this->unit->getAllUnits();
        
        require_once __DIR__ . '/../views/tasks/update.php';
    }

    // New method for updating task (with units)
    public function update() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = $this->auth->getCurrentUser();
            
            if($user['role'] != 'admin') {
                $_SESSION['error'] = "Unauthorized access";
                header("Location: index.php?action=tasks");
                exit();
            }
            
            $task_id = $_POST['task_id'];
            
            $data = [
                'task_details' => $_POST['task_details'],
                'functional_direction' => $_POST['functional_direction'],
                'target_completion_date' => $_POST['target_completion_date'],
                'priority' => $_POST['priority'],
                'budget_allocation' => $_POST['budget_allocation'] ?? 0
            ];
            
            $unit_ids = $_POST['unit_ids'] ?? [];
            
            $result = $this->task->updateTaskWithUnits($task_id, $data, $unit_ids);
            
            if($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            header("Location: index.php?action=view_task&id=" . $task_id);
            exit();
        }
    }

    // New method to get task JSON for AJAX
    public function getTaskJson() {
        header('Content-Type: application/json');
        
        if(!$this->auth->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }
        
        $task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if($task_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
            exit;
        }
        
        $user = $this->auth->getCurrentUser();
        
        // Check permission
        if($user['role'] != 'admin') {
            if(!$this->task->canUserAccessTask($task_id, $user['functional_division'], $user['role'])) {
                echo json_encode(['success' => false, 'message' => 'Permission denied']);
                exit;
            }
        }
        
        $task = $this->task->getTaskWithUnits($task_id);
        
        if($task) {
            echo json_encode(['success' => true, 'task' => $task]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Task not found']);
        }
        exit;
    }


    
}
?>