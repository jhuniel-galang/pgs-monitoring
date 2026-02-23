<?php
require_once __DIR__ . '/AuthController.php';

class DashboardController {
    private $auth;

    public function __construct() {
        $this->auth = new AuthController();
        
        // Check if user is logged in
        if(!$this->auth->isLoggedIn()) {
            header("Location: index.php");
            exit();
        }
    }

    public function index() {
        $user = $this->auth->getCurrentUser();
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
?>