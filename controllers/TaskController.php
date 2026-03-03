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
    
    // ADD THIS - Define canUpdate variable for the view
    $canUpdate = ($user['role'] == 'admin');
    
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
        
        // Validate input
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
        
        // Check for unit_ids - could be array or single value
        $unit_ids = [];
        if(isset($_POST['unit_ids']) && is_array($_POST['unit_ids'])) {
            $unit_ids = $_POST['unit_ids'];
        } elseif(isset($_POST['unit_id']) && !empty($_POST['unit_id'])) {
            // Handle the old single select if present
            $unit_ids = [$_POST['unit_id']];
        }
        
        if(empty($unit_ids)) {
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
            'functional_division' => $_POST['functional_division'], // Use functional_division directly
            'project_id' => !empty($_POST['project_id']) ? $_POST['project_id'] : null,
            'target_completion_date' => $_POST['target_completion_date'],
            'priority' => $_POST['priority'] ?? 'medium',
            'budget_allocation' => $_POST['budget_allocation'] ?? 0,
            'created_by' => $_SESSION['user_id']
        ];
        
        // Use the method that handles multiple units
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
    
    // Get projects with their task summaries
    require_once __DIR__ . '/../models/Project.php';
    $projectModel = new Project();
    
    if($user['role'] == 'admin') {
        // For admin: get all projects
        $projects = $projectModel->getAllProjects([], 100, 0);
        
        // Group projects by division and calculate summaries
        $division_summary = [];
        $divisions = ['OSDS', 'CID', 'SGOD', 'Schools'];
        
        foreach($divisions as $division) {
            $division_projects = array_filter($projects, function($p) use ($division) {
                return ($p['functional_division'] ?? '') == $division;
            });
            
            $total_projects = count($division_projects);
            $total_tasks = 0;
            $total_completed_tasks = 0;
            $total_progress = 0;
            
            foreach($division_projects as $project) {
                $total_tasks += $project['total_tasks'] ?? 0;
                $total_completed_tasks += $project['completed_tasks'] ?? 0;
                $total_progress += $project['progress_percentage'] ?? 0;
            }
            
            $division_summary[] = [
                'functional_division' => $division,
                'total_projects' => $total_projects,
                'total_tasks' => $total_tasks,
                'completed_tasks' => $total_completed_tasks,
                'average_progress' => $total_projects > 0 ? round($total_progress / $total_projects, 2) : 0
            ];
        }
        
        // Get ALL tasks for the project task slides
        $all_tasks = $this->task->getAllTasks();
        
        // Get recent tasks (5 most recent)
        $recent_tasks = $this->task->getAllTasks();
        $recent_tasks = array_slice($recent_tasks, 0, 5);
        
    } else {
        // For encoder: get only their division projects
        $filters = ['division' => $user['functional_division']];
        $projects = $projectModel->getAllProjects($filters, 100, 0);
        
        // Calculate summary for encoder's division
        $total_projects = count($projects);
        $total_tasks = 0;
        $total_completed_tasks = 0;
        $total_progress = 0;
        
        foreach($projects as $project) {
            $total_tasks += $project['total_tasks'] ?? 0;
            $total_completed_tasks += $project['completed_tasks'] ?? 0;
            $total_progress += $project['progress_percentage'] ?? 0;
        }
        
        $division_summary = [[
            'functional_division' => $user['functional_division'],
            'total_projects' => $total_projects,
            'total_tasks' => $total_tasks,
            'completed_tasks' => $total_completed_tasks,
            'average_progress' => $total_projects > 0 ? round($total_progress / $total_projects, 2) : 0
        ]];
        
        // Get ALL tasks for encoder (only their division)
        $all_tasks = $this->task->getTasksByDivisionForEncoder($user['functional_division']);
        
        // Get recent tasks for encoder (only their division)
        $recent_tasks = $this->task->getTasksByDivisionForEncoder($user['functional_division']);
        $recent_tasks = array_slice($recent_tasks, 0, 5);
    }
    
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
    
    // Get task details
    $task = $this->task->getTaskWithUnits($task_id);
    
    if(!$task) {
        $_SESSION['error'] = "Task not found";
        header("Location: index.php?action=tasks");
        exit();
    }
    
    // Get status history
    $status_history = $this->status->getTaskStatusHistory($task_id);
    
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

    public function getTaskJson() {
    header('Content-Type: application/json');
    
    // Check if user is logged in
    if(!$this->auth->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }
    
    $task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if($task_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid task ID']);
        exit;
    }
    
    $user = $this->auth->getCurrentUser();
    
    // Check permission
    if($user['role'] != 'admin') {
        if(!$this->task->canUserAccessTask($task_id, $user['functional_division'], $user['role'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Permission denied']);
            exit;
        }
    }
    
    // Get task details
    $task = $this->task->getTaskById($task_id);
    
    if($task) {
        // Format the response
        $response = [
            'id' => $task['task_id'],
            'task_id' => $task['task_id'],
            'task_details' => $task['task_details'],
            'functional_division' => $task['functional_division'],
            'priority' => $task['priority'],
            'budget_allocation' => $task['budget_allocation'] ?? 0,
            'status' => $this->getStatusFromPercentage($task['current_percentage'] ?? 0),
            'target_completion_date' => $task['target_completion_date'],
            'actual_completion_date' => $task['actual_completion_date'] ?? '',
            'remarks' => $task['latest_remarks'] ?? '',
            'current_percentage' => $task['current_percentage'] ?? 0,
            'unit_names' => $task['unit_names'] ?? 'N/A'
        ];
        
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Task not found']);
    }
    exit;
}

// Add this helper method to TaskController
private function getStatusFromPercentage($percentage) {
    if($percentage >= 100) return 'completed';
    if($percentage > 0) return 'in_progress';
    return 'not_started';
}



















// Show create task page
public function createPage() {
    $user = $this->auth->getCurrentUser();
    
    // Only admin can access create page
    if($user['role'] != 'admin') {
        $_SESSION['error'] = "Unauthorized access";
        header("Location: index.php?action=tasks");
        exit();
    }
    
    // Get all units for the form
    $units = $this->unit->getAllUnits();
    
    // Get all projects for the dropdown
    require_once __DIR__ . '/../models/Project.php';
    $projectModel = new Project();
    $projects = $projectModel->getProjectsForDropdown();
    
    // Debug - remove after confirming it works
    error_log("Projects fetched for dropdown: " . print_r($projects, true));
    
    require_once __DIR__ . '/../views/tasks/create.php';
}


public function delete() {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = $this->auth->getCurrentUser();
        
        // Only admin can delete
        if($user['role'] != 'admin') {
            $_SESSION['error'] = "Unauthorized access";
            header("Location: index.php?action=tasks");
            exit();
        }
        
        $task_id = $_POST['task_id'];
        
        $result = $this->task->deleteTask($task_id);
        
        if($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        header("Location: index.php?action=tasks");
        exit();
    }
}


// Show edit task page
public function editPage($task_id) {
    $user = $this->auth->getCurrentUser();
    
    // Only admin can edit task details
    if($user['role'] != 'admin') {
        $_SESSION['error'] = "Unauthorized access";
        header("Location: index.php?action=tasks");
        exit();
    }
    
    // Get task details
    $task = $this->task->getTaskWithUnits($task_id);
    
    if(!$task) {
        $_SESSION['error'] = "Task not found";
        header("Location: index.php?action=tasks");
        exit();
    }
    
    // Get all units for the form
    $units = $this->unit->getAllUnits();
    
    // Get all projects for the dropdown
    require_once __DIR__ . '/../models/Project.php';
    $projectModel = new Project();
    $projects = $projectModel->getProjectsForDropdown();
    
    require_once __DIR__ . '/../views/tasks/edit.php';
}


// Update task details (not progress)
public function updateTask() {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = $this->auth->getCurrentUser();
        
        // Only admin can update task details
        if($user['role'] != 'admin') {
            $_SESSION['error'] = "Unauthorized access";
            header("Location: index.php?action=tasks");
            exit();
        }
        
        $task_id = $_POST['task_id'];
        
        // Validate input
        if(empty($_POST['task_details'])) {
            $_SESSION['error'] = "Task details is required";
            header("Location: index.php?action=edit_task_page&id=" . $task_id);
            exit();
        }
        
        if(empty($_POST['functional_division'])) {
            $_SESSION['error'] = "Division is required";
            header("Location: index.php?action=edit_task_page&id=" . $task_id);
            exit();
        }
        
        // Get unit IDs
        $unit_ids = $_POST['unit_ids'] ?? [];
        
        if(empty($unit_ids)) {
            $_SESSION['error'] = "At least one unit must be selected";
            header("Location: index.php?action=edit_task_page&id=" . $task_id);
            exit();
        }
        
        if(empty($_POST['target_completion_date'])) {
            $_SESSION['error'] = "Target completion date is required";
            header("Location: index.php?action=edit_task_page&id=" . $task_id);
            exit();
        }
        
        // Prepare data
        $data = [
            'task_details' => $_POST['task_details'],
            'functional_division' => $_POST['functional_division'],
            'project_id' => !empty($_POST['project_id']) ? $_POST['project_id'] : null,
            'target_completion_date' => $_POST['target_completion_date'],
            'priority' => $_POST['priority'] ?? 'medium',
            'budget_allocation' => $_POST['budget_allocation'] ?? 0
        ];
        
        // Update task with units
        $result = $this->task->updateTaskWithUnits($task_id, $data, $unit_ids);
        
        if($result['success']) {
            $_SESSION['success'] = "Task updated successfully";
            header("Location: index.php?action=view_task&id=" . $task_id);
        } else {
            $_SESSION['error'] = $result['message'];
            header("Location: index.php?action=edit_task_page&id=" . $task_id);
        }
        exit();
    }
}


public function deleteDirect($task_id) {
    $user = $this->auth->getCurrentUser();
    
    // Only admin can delete
    if($user['role'] != 'admin') {
        $_SESSION['error'] = "Unauthorized access";
        header("Location: index.php?action=tasks");
        exit();
    }
    
    error_log("deleteDirect called for task_id: " . $task_id);
    
    $result = $this->task->deleteTask($task_id);
    
    if($result['success']) {
        $_SESSION['success'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
    
    header("Location: index.php?action=tasks");
    exit();
}


    
}
?>