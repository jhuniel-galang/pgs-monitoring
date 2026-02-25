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
    
    // Task routes
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
    
case 'update_status':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->updateStatus();
    break;
    
case 'update_task_page':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->updatePage($_GET['id']);
    break;
    
case 'view_task':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->view($_GET['id']);
    break;

// ADD THIS NEW CASE for AJAX task fetching
case 'get_task':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->getTaskJson();
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

case 'create_task_page':
    require_once 'controllers/TaskController.php';
    $controller = new TaskController();
    $controller->createPage();
    break;

case 'update_task_page':
    require_once 'controllers/TaskController.php';
    $controller = new TaskController();
    $controller->updatePage($_GET['id'] ?? 0);
    break;
        
    default:
        // 404 page
        header("HTTP/1.0 404 Not Found");
        echo "<h1>Page Not Found</h1>";
        echo "<p>The requested action '".htmlspecialchars($action)."' does not exist.</p>";
        echo '<p><a href="index.php?action=dashboard">Go to Dashboard</a></p>';
        break;
}
?>