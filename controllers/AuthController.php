<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login($username, $password) {
        // Basic validation
        if(empty($username) || empty($password)) {
            return "Username and password are required";
        }

        $user = $this->user->login($username, $password);
        
        if($user) {
            // Clear any existing session
            $_SESSION = array();
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['functional_division'] = isset($user['functional_division']) ? $user['functional_division'] : null;
            $_SESSION['logged_in'] = true;
            
            // Debug log to file
            error_log("===== LOGIN DEBUG =====");
            error_log("User ID: " . $user['id']);
            error_log("Username: " . $user['username']);
            error_log("Role: " . $user['role']);
            error_log("Functional Division from DB: " . (isset($user['functional_division']) ? $user['functional_division'] : 'NOT SET'));
            error_log("Session functional_division: " . ($_SESSION['functional_division'] ?? 'NOT SET'));
            error_log("======================");
            
            // Redirect based on role
            header("Location: index.php?action=dashboard");
            exit();
        } else {
            return "Invalid username or password";
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit();
    }

    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function getCurrentUser() {
        if($this->isLoggedIn()) {
            return $this->user->getUserById($_SESSION['user_id']);
        }
        return null;
    }
}
?>