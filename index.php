<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session - only once here
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Simple router
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

switch($action) {
    // Auth routes
    case 'login':
    // For public view, we need to load projects for the slideshow
    require_once 'models/Project.php';
    require_once 'models/Task.php';
    
    $projectModel = new Project();
    $taskModel = new Task();
    
    // Get selected year from URL parameter
    $selected_year = isset($_GET['year']) && !empty($_GET['year']) ? $_GET['year'] : '2026';
    
    // Get available years for the filter dropdown
    $available_years = $projectModel->getDistinctYears();
    
    // Get projects filtered by year
    $filters = [];
    if ($selected_year) {
        $filters['year'] = $selected_year;
    }
    $projects = $projectModel->getAllProjects($filters, 100, 0);
    
    // Get all tasks
    $all_tasks = $taskModel->getAllTasks();
    
    // Filter tasks by year if selected
    if ($selected_year) {
        $all_tasks = array_filter($all_tasks, function($task) use ($selected_year) {
            return isset($task['year']) && $task['year'] == $selected_year;
        });
    }
    
    // Get division summary
    $division_summary = $projectModel->getProjectSummary($selected_year);
    
    // Get recent tasks
    $recent_tasks = array_slice($all_tasks, 0, 8);
    
    // Count filtered projects and tasks
    $filtered_project_count = count($projects);
    $filtered_task_count = count($all_tasks);
    
    // Store error if any from authentication
    $error = isset($_GET['error']) ? $_GET['error'] : '';
    
    require_once 'views/auth/login.php';
    break;
        
    case 'authenticate':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController();
        $error = $auth->login($_POST['username'], $_POST['password']);
        if($error) {
            require_once 'views/auth/login.php';
        }
        break;
        
    case 'logout':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        break;
    
    // Dashboard routes
    case 'dashboard':
        require_once 'controllers/TaskController.php';
        $taskController = new TaskController();
        $taskController->dashboard();
        break;
    
    // Task routes - CLEANED UP VERSION
case 'tasks':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->index();
    break;

case 'create_task':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->create();
    break;

case 'create_task_page':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->createPage();
    break;

case 'update_status':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->updateStatus();
    break;

case 'update_task_page':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->updatePage($_GET['id'] ?? 0);
    break;

case 'view_task':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->view($_GET['id'] ?? 0);
    break;

case 'get_task':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->getTaskJson();
    break;

case 'edit_task_page':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->editPage($_GET['id'] ?? 0);
    break;

case 'update_task':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->updateTask();
    break;

case 'delete_task':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->delete();
    break;

// Optional: Add this for testing direct delete
case 'delete_task_direct':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->deleteDirect($_GET['id'] ?? 0);
    break;
    
    // User routes
    case 'profile':
        require_once 'controllers/UserController.php';
        $userController = new UserController();
        $userController->profile();
        break;
        
    case 'users':
        require_once 'controllers/UserController.php';
        $userController = new UserController();
        $userController->list();
        break;

    case 'create_user':
        require_once 'controllers/UserController.php';
        $userController = new UserController();
        $userController->create();
        break;

    case 'store_user':
        require_once 'controllers/UserController.php';
        $userController = new UserController();
        $userController->store();
        break;

    case 'edit_user':
        require_once 'controllers/UserController.php';
        $userController = new UserController();
        $userController->edit($_GET['id']);
        break;

    case 'update_user':
        require_once 'controllers/UserController.php';
        $userController = new UserController();
        $userController->update();
        break;

    case 'delete_user':
        require_once 'controllers/UserController.php';
        $userController = new UserController();
        $userController->delete();
        break;
    
    // Unit routes
    case 'units':
        require_once 'controllers/UnitController.php';
        $unitController = new UnitController();
        $unitController->index();
        break;

    case 'store_unit':
        require_once 'controllers/UnitController.php';
        $unitController = new UnitController();
        $unitController->store();
        break;

    case 'update_unit':
        require_once 'controllers/UnitController.php';
        $unitController = new UnitController();
        $unitController->update();
        break;

    case 'delete_unit':
        require_once 'controllers/UnitController.php';
        $unitController = new UnitController();
        $unitController->delete();
        break;


    // Project routes
case 'projects':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->index();
    break;

case 'store_project':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->store();
    break;

case 'update_project':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->update();
    break;

case 'delete_project':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->delete();
    break;

case 'view_project':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->view($_GET['id']);
    break;






    case 'update_project_progress':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->updateProgress();
    break;

case 'add_project_units':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->addUnits();
    break;

case 'remove_project_unit':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->removeUnit();
    break;

// Add this near your other action cases
case 'get_project_json':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->getProjectJson();
    break;

    // Add these cases to your index.php switch statement

case 'update_project_progress':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->updateProgress();
    break;

case 'add_project_units':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->addProjectUnits();
    break;

case 'remove_project_unit':
    require_once 'controllers/ProjectController.php';
    $projectController = new ProjectController();
    $projectController->removeProjectUnit();
    break;

case 'delete_task_direct':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->deleteDirect($_GET['id'] ?? 0);
    break;

    

        // 404 page
        header("HTTP/1.0 404 Not Found");
        echo "<h1>Page Not Found</h1>";
        echo "<p>The requested action '".htmlspecialchars($action)."' does not exist.</p>";
        echo '<p><a href="index.php?action=dashboard">Go to Dashboard</a></p>';
        break;
}
?>