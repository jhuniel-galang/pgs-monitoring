<?php
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Task.php';

class PublicController {
    private $project;
    private $task;

    public function __construct() {
        $this->project = new Project();
        $this->task = new Task();
    }

    public function loginPage() {
        // Get selected year from URL parameter (for the slideshow filter)
        $selected_year = isset($_GET['year']) && !empty($_GET['year']) ? $_GET['year'] : '2026';
        
        // Get available years for the filter dropdown
        $available_years = $this->project->getDistinctYears();
        
        // Get all projects (public view - no filters by division)
        $filters = [];
        if ($selected_year) {
            $filters['year'] = $selected_year;
        }
        $projects = $this->project->getAllProjects($filters, 100, 0);
        
        // Get all tasks for the project task slides
        $all_tasks = $this->task->getAllTasks();
        
        // Filter tasks by year if selected
        if ($selected_year) {
            $all_tasks = array_filter($all_tasks, function($task) use ($selected_year) {
                return isset($task['year']) && $task['year'] == $selected_year;
            });
        }
        
        // Get division summary (public view)
        $division_summary = $this->project->getProjectSummary($selected_year);
        
        // Add task counts to division summary
        foreach ($division_summary as &$division) {
            $div = $division['functional_division'];
            $division_tasks = array_filter($all_tasks, function($task) use ($div) {
                return isset($task['functional_division']) && $task['functional_division'] == $div;
            });
            $division['total_tasks'] = count($division_tasks);
            $division['completed_tasks'] = count(array_filter($division_tasks, function($task) {
                return isset($task['current_percentage']) && $task['current_percentage'] >= 100;
            }));
        }
        
        // Get recent tasks (first 8)
        $recent_tasks = array_slice($all_tasks, 0, 8);
        
        // Count filtered projects and tasks
        $filtered_project_count = count($projects);
        $filtered_task_count = count($all_tasks);
        
        // Load the login view with data
        $error = isset($_GET['error']) ? $_GET['error'] : '';
        require_once __DIR__ . '/../views/auth/login.php';
    }


    public function report() {
    // Get filter parameters
    $selected_year = $_GET['year'] ?? date('Y');
    $selected_division = $_GET['division'] ?? '';
    $selected_status = $_GET['status'] ?? '';
    $selected_priority = $_GET['priority'] ?? '';
    
    // Build filters for tasks
    $filters = ['year' => $selected_year];
    if (!empty($selected_division)) {
        $filters['functional_division'] = $selected_division;
    }
    
    // Get tasks with filters
    $tasks = $this->task->getAllTasks($filters);
    
    // Manual filtering for status and priority since getAllTasks might not support these
    if (!empty($selected_status)) {
        $tasks = array_filter($tasks, function($task) use ($selected_status) {
            $percentage = $task['current_percentage'] ?? 0;
            if ($selected_status == 'completed') return $percentage >= 100;
            if ($selected_status == 'in_progress') return $percentage > 0 && $percentage < 100;
            if ($selected_status == 'not_started') return $percentage == 0;
            return true;
        });
    }
    
    if (!empty($selected_priority)) {
        $tasks = array_filter($tasks, function($task) use ($selected_priority) {
            return isset($task['priority']) && $task['priority'] == $selected_priority;
        });
    }
    
    // Get current date for the report
    $current_date = date('F d, Y');
    
    // Load the report view
    require_once __DIR__ . '/../views/tasks/public_report.php';
}
}
?>