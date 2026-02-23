<?php
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $auth;
    private $user;

    public function __construct() {
        $this->auth = new AuthController();
        $this->user = new User();
        
        // Check if user is logged in and is admin
        if(!$this->auth->isLoggedIn() || $_SESSION['role'] != 'admin') {
            header("Location: index.php");
            exit();
        }
    }

    // List all users (READ)
    public function list() {
        $users = $this->user->getAllUsers();
        require_once __DIR__ . '/../views/user/list.php';
    }

    // Show create form
    public function create() {
        require_once __DIR__ . '/../views/user/create.php';
    }

    // Store new user (CREATE)
    public function store() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'email' => $_POST['email'] ?? null,
                'full_name' => $_POST['full_name'] ?? null,
                'role' => $_POST['role'],
                'functional_division' => $_POST['functional_division'] ?? null,
                'status' => $_POST['status'] ?? 'active'
            ];
            
            $result = $this->user->createUser($data);
            
            if($result['success']) {
                $_SESSION['success'] = $result['message'];
                header("Location: index.php?action=users");
                exit();
            } else {
                $_SESSION['error'] = $result['message'];
                header("Location: index.php?action=create_user");
                exit();
            }
        }
    }

    // Show edit form
    public function edit($id) {
        $user = $this->user->getUserById($id);
        if(!$user) {
            $_SESSION['error'] = "User not found";
            header("Location: index.php?action=users");
            exit();
        }
        require_once __DIR__ . '/../views/user/edit.php';
    }

    // Update user (UPDATE)
    public function update() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            
            $data = [
                'username' => $_POST['username'],
                'email' => $_POST['email'] ?? null,
                'full_name' => $_POST['full_name'] ?? null,
                'role' => $_POST['role'],
                'functional_division' => $_POST['functional_division'] ?? null,
                'status' => $_POST['status']
            ];
            
            // Only include password if provided
            if(!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }
            
            $result = $this->user->updateUser($id, $data);
            
            if($result['success']) {
                $_SESSION['success'] = $result['message'];
                header("Location: index.php?action=users");
                exit();
            } else {
                $_SESSION['error'] = $result['message'];
                header("Location: index.php?action=edit_user&id=" . $id);
                exit();
            }
        }
    }

    // Delete user (DELETE)
    public function delete() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            
            // Don't allow deleting yourself
            if($id == $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot delete your own account";
                header("Location: index.php?action=users");
                exit();
            }
            
            $result = $this->user->deleteUser($id);
            
            if($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            header("Location: index.php?action=users");
            exit();
        }
    }

    public function profile() {
        $user = $this->auth->getCurrentUser();
        require_once __DIR__ . '/../views/user/profile.php';
    }
}
?>