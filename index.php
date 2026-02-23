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
    case 'login':
        // Show login page
        require_once 'views/auth/login.php';
        break;
        
    case 'authenticate':
        // Process login
        require_once 'controllers/AuthController.php';
        $auth = new AuthController();
        $error = $auth->login($_POST['username'], $_POST['password']);
        if($error) {
            require_once 'views/auth/login.php';
        }
        break;
        
    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        $dashboard = new DashboardController();
        $dashboard->index();
        break;
        
    case 'profile':
        require_once 'controllers/UserController.php';
        $user = new UserController();
        $user->profile();
        break;
        
    case 'users':
        require_once 'controllers/UserController.php';
        $user = new UserController();
        $user->list();
        break;
        
    case 'logout':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        break;
        
    default:
        // 404 page
        header("HTTP/1.0 404 Not Found");
        echo "Page not found";
        break;

        // Add these cases to your existing switch statement
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
    
case 'view_task':
    require_once 'controllers/TaskController.php';
    $taskController = new TaskController();
    $taskController->view($_GET['id']);
    break;
}
?>


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
    case 'login':
        // Show login page
        require_once 'views/auth/login.php';
        break;
        
    case 'authenticate':
        // Process login
        require_once 'controllers/AuthController.php';
        $auth = new AuthController();
        $error = $auth->login($_POST['username'], $_POST['password']);
        if($error) {
            require_once 'views/auth/login.php';
        }
        break;
        
    case 'dashboard':
        require_once 'controllers/TaskController.php';
        $taskController = new TaskController();
        $taskController->dashboard();
        break;
        
    case 'profile':
        require_once 'controllers/UserController.php';
        $user = new UserController();
        $user->profile();
        break;
        
    case 'users':
        require_once 'controllers/UserController.php';
        $user = new UserController();
        $user->list();
        break;
        
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
        
    case 'logout':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        break;
        
    default:
        // 404 page
        header("HTTP/1.0 404 Not Found");
        echo "Page not found";
        break;
}
?>