<?php require_once 'views/layout/header.php'; ?>

<?php if(isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php 
    echo $_SESSION['success']; 
    unset($_SESSION['success']);
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php 
    echo $_SESSION['error']; 
    unset($_SESSION['error']);
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Commitments</h2>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'encoder'): ?>
            <p class="text-muted">Viewing tasks for: <strong><?php echo $_SESSION['functional_division'] ?? 'N/A'; ?></strong></p>
        <?php endif; ?>
    </div>
    <div class="col-md-4 text-end">
    <div class="btn-group" role="group">
        <?php if(isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'encoder')): ?>
        <a href="index.php?action=create_task_page" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Commitment
        </a>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'encoder')): ?>
        <?php 
            // Build the URL properly
            $base_url = "index.php?action=task_report";
            $params = [];
            
            if(!empty($_GET['year'])) {
                $params[] = 'year=' . urlencode($_GET['year']);
            }
            
            // For encoders, force their division
            if($_SESSION['role'] == 'encoder') {
                $params[] = 'division=' . urlencode($_SESSION['functional_division']);
            } elseif($_SESSION['role'] == 'admin' && !empty($_GET['division'])) {
                $params[] = 'division=' . urlencode($_GET['division']);
            }
            
            if(!empty($_GET['priority'])) {
                $params[] = 'priority=' . urlencode($_GET['priority']);
            }
            
            if(!empty($_GET['status'])) {
                $params[] = 'status=' . urlencode($_GET['status']);
            }
            
            $report_url = $base_url . (!empty($params) ? '&' . implode('&', $params) : '');
        ?>
        <a href="<?php echo $report_url; ?>" class="btn btn-success" target="_blank">
            <i class="bi bi-printer"></i> Print Report
            <?php if($_SESSION['role'] == 'encoder'): ?>
                <small class="ms-1">(<?php echo $_SESSION['functional_division']; ?>)</small>
            <?php endif; ?>
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Division Summary Cards -->
<div class="row mb-4">
    <?php 
    if(isset($division_summary) && is_array($division_summary) && !empty($division_summary)): 
        foreach($division_summary as $summary): 
            if(!is_array($summary) || !isset($summary['functional_division'])) continue;
            
            // Determine card color based on division
            $card_color = 'secondary';
            if($summary['functional_division'] == 'OSDS') {
                $card_color = 'primary';
            } elseif($summary['functional_division'] == 'CID') {
                $card_color = 'success';
            } elseif($summary['functional_division'] == 'SGOD') {
                $card_color = 'info';
            } elseif($summary['functional_division'] == 'Schools') {
                $card_color = 'secondary';
            }
    ?>
    <div class="col-md-4">
        <div class="card text-white bg-<?php echo $card_color; ?> mb-3">
            <div class="card-header"><?php echo $summary['functional_division']; ?></div>
            <div class="card-body">
                <h5 class="card-title">Average Progress: <?php echo $summary['average_percentage'] ?? 0; ?>%</h5>
                <p class="card-text">
                    Total Commitments: <?php echo $summary['total_tasks'] ?? 0; ?><br>
                    Completed: <?php echo $summary['completed_tasks'] ?? 0; ?>
                </p>
            </div>
        </div>
    </div>
    <?php 
        endforeach; 
    endif; 
    ?>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Filter Commitments</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="action" value="tasks">
            
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search commitment or units..." 
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            
            <?php if($_SESSION['role'] == 'admin'): ?>
            <div class="col-md-2">
                <label for="division" class="form-label">Division</label>
                <select class="form-select" id="division" name="division">
                    <option value="">All Divisions</option>
                    <option value="OSDS" <?php echo (isset($_GET['division']) && $_GET['division'] == 'OSDS') ? 'selected' : ''; ?>>OSDS</option>
                    <option value="CID" <?php echo (isset($_GET['division']) && $_GET['division'] == 'CID') ? 'selected' : ''; ?>>CID</option>
                    <option value="SGOD" <?php echo (isset($_GET['division']) && $_GET['division'] == 'SGOD') ? 'selected' : ''; ?>>SGOD</option>
                    <option value="Schools" <?php echo (isset($_GET['division']) && $_GET['division'] == 'Schools') ? 'selected' : ''; ?>>Schools</option>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="col-md-2">
                <label for="year" class="form-label">Year</label>
                <input type="text" class="form-control" id="year" name="year" 
                       placeholder="e.g., 2024, 2025" 
                       value="<?php echo htmlspecialchars($_GET['year'] ?? ''); ?>">
            </div>
            
            <div class="col-md-2">
                <label for="priority" class="form-label">Priority</label>
                <select class="form-select" id="priority" name="priority">
                    <option value="">All Priorities</option>
                    <option value="low" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
                    <option value="critical" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'critical') ? 'selected' : ''; ?>>Critical</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="not_started" <?php echo (isset($_GET['status']) && $_GET['status'] == 'not_started') ? 'selected' : ''; ?>>Not Started</option>
                    <option value="in_progress" <?php echo (isset($_GET['status']) && $_GET['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
            
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Apply</button>
                <a href="index.php?action=tasks" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tasks Table -->
<div class="card">
    <div class="card-header">
        <h5>Commitment List (<?php echo $total_tasks; ?> Commitments found)</h5>
        <?php if($_SESSION['role'] == 'encoder'): ?>
        <small class="text-muted d-block">Showing only tasks from <?php echo $_SESSION['functional_division']; ?> division</small>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php 
        $filtered_tasks = $tasks;
        include 'tasks_table.php'; 
        ?>
        
        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav aria-label="Task pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?action=tasks&page=<?php echo ($page-1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&year=<?php echo urlencode($_GET['year'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>">
                        Previous
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?action=tasks&page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&year=<?php echo urlencode($_GET['year'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
                
                <?php if($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?action=tasks&page=<?php echo ($page+1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&year=<?php echo urlencode($_GET['year'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>">
                        Next
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Removed the Create Task Modal -->

<?php require_once 'views/layout/footer.php'; ?>