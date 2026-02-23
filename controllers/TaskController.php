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
    
    // Get tasks based on user role
    if($user['role'] == 'admin') {
        // Admin sees all tasks
        $tasks = $this->task->getAllTasks();
        $division_summary = $this->task->getDivisionSummary();
    } else {
        // Encoder only sees their division tasks
        $tasks = $this->task->getTasksByDivisionForEncoder($user['functional_division']);
        
        // Get division summary - FIX: Ensure it's always an array
        $summary = $this->task->getDivisionSummaryByDivision($user['functional_division']);
        
        // Initialize division_summary as an empty array
        $division_summary = [];
        
        // Check if summary is valid and add to array
        if($summary && is_array($summary) && isset($summary['functional_division'])) {
            $division_summary = [$summary];
        } elseif($summary && is_array($summary) && isset($summary[0])) {
            // If it's already an array of summaries
            $division_summary = $summary;
        } else {
            // Create a default summary if none exists
            $division_summary = [[
                'functional_division' => $user['functional_division'],
                'total_tasks' => count($tasks),
                'completed_tasks' => 0,
                'average_percentage' => 0
            ]];
        }
    }
    
    // Debug: Log what we're passing to the view
    error_log("Division summary type: " . gettype($division_summary));
    error_log("Division summary: " . print_r($division_summary, true));
    
    $units = $this->unit->getAllUnits();
    
    require_once __DIR__ . '/../views/tasks/index.php';
}

    public function create() {
        $user = $this->auth->getCurrentUser();
        
        // Only admin can create tasks
        if($user['role'] != 'admin') {
            header("Location: index.php?action=tasks&error=Unauthorized access");
            exit();
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'task_details' => $_POST['task_details'],
                'unit_id' => $_POST['unit_id'],
                'functional_division' => $_POST['functional_division'],
                'target_completion_date' => $_POST['target_completion_date'],
                'priority' => $_POST['priority'],
                'created_by' => $_SESSION['user_id']
            ];
            
            if($this->task->createTask($data)) {
                header("Location: index.php?action=tasks&msg=Task created successfully");
                exit();
            } else {
                $error = "Failed to create task";
            }
        }
        
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
    
    // Get task details
    $task = $this->task->getTaskById($task_id);
    
    // Check if task exists
    if(!$task) {
        $_SESSION['error'] = "Task not found";
        header("Location: index.php?action=tasks");
        exit();
    }
    
    // Get status history
    $status_history = $this->status->getTaskStatusHistory($task_id);
    
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
        
        // Get division summary - FIX: Ensure it's always an array
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
        
        $task = $this->task->getTaskById($task_id);
        $status_history = $this->status->getTaskStatusHistory($task_id);
        
        require_once __DIR__ . '/../views/tasks/update.php';
    }

    
}
?>