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
        <h2>Task Management</h2>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'encoder'): ?>
            <p class="text-muted">Viewing tasks for: <strong><?php echo $_SESSION['functional_division'] ?? 'N/A'; ?></strong></p>
        <?php endif; ?>
    </div>
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
    <div class="col-md-4 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
    <i class="bi bi-plus-circle"></i> Create New Task
</button>
    </div>
    <?php endif; ?>
</div>

<?php if(isset($_GET['msg'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($_GET['msg']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($_GET['error']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Division Summary Cards -->
<div class="row mb-4">
    <?php 
    // Ensure division_summary is an array and not empty
    if(isset($division_summary) && is_array($division_summary) && !empty($division_summary)): 
        foreach($division_summary as $summary): 
            // Skip if summary is not an array or doesn't have functional_division
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
    else: 
    ?>
    <div class="col-12">
        <div class="alert alert-info">
            No division summary data available.
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Tasks Table -->
<div class="card">
    <div class="card-header">
        <h5>Task List</h5>
    </div>
    <div class="card-body">
        <?php 
        // Check if tasks is set and is an array
        if(!isset($tasks) || !is_array($tasks)) {
            $tasks = [];
        }
        
        // For encoders, just show all tasks (they're already filtered)
        if(isset($_SESSION['role']) && $_SESSION['role'] == 'encoder'): 
            $filtered_tasks = $tasks;
            include 'tasks_table.php';
        else: 
            // For admin, show tabs
        ?>
        <ul class="nav nav-tabs card-header-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#all">All Tasks</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#osds">OSDS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#cid">CID</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#sgod">SGOD</a>
            </li>
        </ul>
        
        <div class="tab-content">
            <div class="tab-pane active" id="all">
                <?php 
                $filtered_tasks = $tasks;
                include 'tasks_table.php'; 
                ?>
            </div>
            <div class="tab-pane" id="osds">
                <?php 
                $filtered_tasks = array_filter($tasks, function($t) { 
                    return is_array($t) && isset($t['functional_division']) && $t['functional_division'] == 'OSDS'; 
                });
                include 'tasks_table.php'; 
                ?>
            </div>
            <div class="tab-pane" id="cid">
                <?php 
                $filtered_tasks = array_filter($tasks, function($t) { 
                    return is_array($t) && isset($t['functional_division']) && $t['functional_division'] == 'CID'; 
                });
                include 'tasks_table.php'; 
                ?>
            </div>
            <div class="tab-pane" id="sgod">
                <?php 
                $filtered_tasks = array_filter($tasks, function($t) { 
                    return is_array($t) && isset($t['functional_division']) && $t['functional_division'] == 'SGOD'; 
                });
                include 'tasks_table.php'; 
                ?>
            </div>
        </div>
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
                            <input type="date" class="form-control" id="modal_target_completion_date" name="target_completion_date" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
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
function modalLoadUnits() {
    var division = document.getElementById('modal_functional_division').value;
    var unitSelect = document.getElementById('modal_unit_id');
    var options = unitSelect.getElementsByTagName('option');
    
    // Reset unit select
    unitSelect.value = '';
    
    // Show/hide units based on division
    for(var i = 0; i < options.length; i++) {
        var option = options[i];
        if(option.value === '') continue; // Skip the first option
        
        if(option.getAttribute('data-division') === division) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    }
}

// Reset modal form when closed
document.getElementById('createTaskModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modal_task_details').value = '';
    document.getElementById('modal_functional_division').value = '';
    document.getElementById('modal_unit_id').value = '';
    document.getElementById('modal_target_completion_date').value = '';
    document.getElementById('modal_priority').value = 'medium';
    
    // Reset unit options visibility
    var unitSelect = document.getElementById('modal_unit_id');
    var options = unitSelect.getElementsByTagName('option');
    for(var i = 0; i < options.length; i++) {
        options[i].style.display = '';
    }
});
</script>

<?php require_once 'views/layout/footer.php'; ?>