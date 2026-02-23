<?php
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $auth;
    private $user;

    public function __construct() {
        $this->auth = new AuthController();
        $this->user = new User();
        
        // Check if user is logged in
        if(!$this->auth->isLoggedIn()) {
            header("Location: index.php");
            exit();
        }
    }

    public function profile() {
        $user = $this->auth->getCurrentUser();
        require_once __DIR__ . '/../views/user/profile.php';
    }

    public function list() {
        // Only admin can see all users
        if($_SESSION['role'] != 'admin') {
            header("Location: index.php?action=dashboard");
            exit();
        }
        
        $users = $this->user->getAllUsers();
        require_once __DIR__ . '/../views/user/list.php';
    }
}
?>