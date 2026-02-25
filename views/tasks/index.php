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
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'encoder'): ?>
            <p class="text-muted">Viewing tasks for: <strong><?php echo $_SESSION['functional_division'] ?? 'N/A'; ?></strong></p>
        <?php endif; ?>
    </div>
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
<div class="col-md-4 text-end">
    <a href="index.php?action=create_task_page" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create New Task
    </a>
</div>
<?php endif; ?>
</div>

<!-- Division Summary Cards -->
<div class="row mb-4">
    <?php 
    if(isset($division_summary) && is_array($division_summary) && !empty($division_summary)): 
        foreach($division_summary as $summary): 
            if(!is_array($summary) || !isset($summary['functional_division'])) continue;
    ?>
    <div class="col-md-4">
        <div class="card text-white bg-<?php 
            echo $summary['functional_division'] == 'OSDS' ? 'primary' : 
                ($summary['functional_division'] == 'CID' ? 'success' : 'info'); 
        ?> mb-3">
            <div class="card-header"><?php echo $summary['functional_division']; ?></div>
            <div class="card-body">
                <h5 class="card-title">Average Progress: <?php echo $summary['average_percentage'] ?? 0; ?>%</h5>
                <p class="card-text">
                    Total Tasks: <?php echo $summary['total_tasks'] ?? 0; ?><br>
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
        <h5 class="mb-0">Filter Tasks</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="action" value="tasks">
            
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search tasks or units..." 
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
                </select>
            </div>
            <?php endif; ?>
            
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
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                <a href="index.php?action=tasks" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tasks Table -->
<div class="card">
    <div class="card-header">
        <h5>Task List (<?php echo $total_tasks; ?> tasks found)</h5>
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
                    <a class="page-link" href="?action=tasks&page=<?php echo ($page-1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>">
                        Previous
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?action=tasks&page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
                
                <?php if($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?action=tasks&page=<?php echo ($page+1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>">
                        Next
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>










<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Create New Task</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=create_task" method="POST">
                <div class="modal-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="modal_task_details" class="form-label">Task Details <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="modal_task_details" name="task_details" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="modal_functional_division" class="form-label">Division <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_functional_division" name="functional_division" required onchange="modalLoadUnits()">
                                <option value="">Select Division</option>
                                <option value="OSDS">OSDS</option>
                                <option value="CID">CID</option>
                                <option value="SGOD">SGOD</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="modal_unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_unit_id" name="unit_id" required>
                                <option value="">Select Division First</option>
                                <?php foreach($units as $unit): ?>
                                <option value="<?php echo $unit['id']; ?>" data-division="<?php echo $unit['functional_division']; ?>">
                                    <?php echo htmlspecialchars($unit['unit_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="modal_target_completion_date" class="form-label">Target Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="target_completion_date" name="target_completion_date" 
       placeholder="e.g., Quarterly, Annually, or specific date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="modal_priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_priority" name="priority" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('createTaskModal');

    if (!modalEl) return; // IMPORTANT: prevents JS crash

    modalEl.addEventListener('hidden.bs.modal', function () {
        document.getElementById('modal_task_details').value = '';
        document.getElementById('modal_functional_division').value = '';
        document.getElementById('modal_unit_id').value = '';
        document.getElementById('modal_target_completion_date').value = '';
        document.getElementById('modal_priority').value = 'medium';

        var options = document.getElementById('modal_unit_id').options;
        for (let i = 0; i < options.length; i++) {
            options[i].style.display = '';
        }
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>