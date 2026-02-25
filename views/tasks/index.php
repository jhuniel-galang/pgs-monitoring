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
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
            <i class="bi bi-plus-circle"></i> Create New Task
        </button>
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
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createTaskModalLabel">Create New Task</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?action=create_task" method="POST" id="createTaskForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="modal_task_details" class="form-label">Task Details <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="modal_task_details" name="task_details" rows="3" required></textarea>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="modal_functional_division" class="form-label">Division <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_functional_division" name="functional_division" required onchange="filterUnitsByDivision()">
                                <option value="">Select Division</option>
                                <option value="OSDS">OSDS</option>
                                <option value="CID">CID</option>
                                <option value="SGOD">SGOD</option>
                                <option value="Schools">Schools</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="modal_priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_priority" name="priority" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="modal_budget" class="form-label">Budget Allocation</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="modal_budget" name="budget_allocation" step="0.01" value="0.00">
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Target Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="target_completion_date" name="target_completion_date" 
                                   placeholder="e.g., Quarterly, Annually, December 2025, etc." required>
                            <small class="text-muted">You can enter a specific date or a timeframe</small>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Participating Units <span class="text-danger">*</span></label>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                You can select multiple units to participate in this task.
                            </div>
                            
                            <div class="card">
                                <div class="card-header bg-light">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select class="form-select form-select-sm" id="filter_division_task" onchange="filterTaskUnitsByDivision()">
                                                <option value="">All Divisions</option>
                                                <option value="OSDS">OSDS</option>
                                                <option value="CID">CID</option>
                                                <option value="SGOD">SGOD</option>
                                                <option value="Schools">Schools</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkAllTaskUnits()">Check All</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="uncheckAllTaskUnits()">Uncheck All</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                    <div class="row" id="task-units-container">
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    <span id="task-selected-count">0</span> unit(s) selected
                                </div>
                            </div>
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


<!-- Update Task Modal -->
<div class="modal fade" id="updateTaskModal" tabindex="-1" aria-labelledby="updateTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="updateTaskModalLabel">Update Task</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?action=update_task" method="POST" id="updateTaskForm">
                <input type="hidden" name="task_id" id="update_task_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="update_task_details" class="form-label">Task Details <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="update_task_details" name="task_details" rows="3" required></textarea>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="update_functional_division" class="form-label">Division <span class="text-danger">*</span></label>
                            <select class="form-select" id="update_functional_division" name="functional_division" required>
                                <option value="OSDS">OSDS</option>
                                <option value="CID">CID</option>
                                <option value="SGOD">SGOD</option>
                                <option value="Schools">Schools</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="update_priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="update_priority" name="priority" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="update_budget" class="form-label">Budget Allocation</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="update_budget" name="budget_allocation" step="0.01">
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="update_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="update_status" name="status" required>
                                <option value="not_started">Not Started</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="update_target_date" class="form-label">Target Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="update_target_date" name="target_completion_date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="update_actual_date" class="form-label">Actual Completion Date</label>
                            <input type="date" class="form-control" id="update_actual_date" name="actual_completion_date">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="update_remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="update_remarks" name="remarks" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

var allUnits = <?php echo json_encode(array_map(function($unit) {
    return [
        'id' => $unit['id'],
        'name' => $unit['unit_name'],
        'division' => $unit['functional_division'],
        'pic' => $unit['person_in_charge'] ?? 'N/A'
    ];
}, $units)); ?>;

// Function to display all units as checkboxes
function displayTaskUnits() {
    var container = document.getElementById('task-units-container');
    
    if (!container) return;
    
    // Clear container
    container.innerHTML = '';
    
    if (!allUnits || allUnits.length === 0) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-warning">No units available.</div></div>';
        return;
    }
    
    // Group units by division
    var osdsUnits = allUnits.filter(u => u.division === 'OSDS');
    var cidUnits = allUnits.filter(u => u.division === 'CID');
    var sgodUnits = allUnits.filter(u => u.division === 'SGOD');
    var schoolsUnits = allUnits.filter(u => u.division === 'Schools');
    
    // Create sections for each division
    if (osdsUnits.length > 0) {
        container.appendChild(createTaskDivisionSection('OSDS', 'primary', osdsUnits));
    }
    
    if (cidUnits.length > 0) {
        container.appendChild(createTaskDivisionSection('CID', 'success', cidUnits));
    }
    
    if (sgodUnits.length > 0) {
        container.appendChild(createTaskDivisionSection('SGOD', 'info', sgodUnits));
    }
    
    if (schoolsUnits.length > 0) {
        container.appendChild(createTaskDivisionSection('Schools', 'secondary', schoolsUnits));
    }
    
    updateTaskSelectedCount();
}

// Helper function to create division section for tasks
function createTaskDivisionSection(division, color, units) {
    var section = document.createElement('div');
    section.className = 'col-12 mb-3';
    
    var header = document.createElement('h6');
    header.className = 'text-' + color;
    header.innerHTML = '<span class="badge bg-' + color + '">' + division + ' Division</span>';
    section.appendChild(header);
    
    var row = document.createElement('div');
    row.className = 'row';
    
    units.forEach(function(unit) {
        var col = document.createElement('div');
        col.className = 'col-md-4 mb-2 task-unit-item';
        col.setAttribute('data-division', unit.division);
        
        var div = document.createElement('div');
        div.className = 'form-check';
        
        var checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'form-check-input task-unit-checkbox';
        checkbox.name = 'unit_ids[]';
        checkbox.value = unit.id;
        checkbox.id = 'task_unit_' + unit.id;
        checkbox.addEventListener('change', function() {
            updateTaskSelectedCount();
        });
        
        var label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = 'task_unit_' + unit.id;
        label.innerHTML = '<strong>' + unit.name + '</strong><br>' +
                         '<small class="text-muted">PIC: ' + unit.pic + '</small>';
        
        div.appendChild(checkbox);
        div.appendChild(label);
        col.appendChild(div);
        row.appendChild(col);
    });
    
    section.appendChild(row);
    return section;
}

// Filter task units by division
function filterTaskUnitsByDivision() {
    var division = document.getElementById('filter_division_task').value;
    var unitItems = document.getElementsByClassName('task-unit-item');
    
    for(var i = 0; i < unitItems.length; i++) {
        if (division === '' || unitItems[i].getAttribute('data-division') === division) {
            unitItems[i].style.display = '';
        } else {
            unitItems[i].style.display = 'none';
        }
    }
}

// Check all task units
function checkAllTaskUnits() {
    var checkboxes = document.getElementsByClassName('task-unit-checkbox');
    for(var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = true;
    }
    updateTaskSelectedCount();
}

// Uncheck all task units
function uncheckAllTaskUnits() {
    var checkboxes = document.getElementsByClassName('task-unit-checkbox');
    for(var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = false;
    }
    updateTaskSelectedCount();
}

// Update task selected count
function updateTaskSelectedCount() {
    var checkboxes = document.getElementsByClassName('task-unit-checkbox');
    var count = 0;
    for(var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) count++;
    }
    var countElement = document.getElementById('task-selected-count');
    if (countElement) {
        countElement.innerText = count;
    }
}

// Filter units when division is selected
function filterUnitsByDivision() {
    var division = document.getElementById('modal_functional_division').value;
    var filterSelect = document.getElementById('filter_division_task');
    if (filterSelect) {
        filterSelect.value = division;
        filterTaskUnitsByDivision();
    }
}

// Initialize modal when shown
document.addEventListener('DOMContentLoaded', function() {
    var createTaskModal = document.getElementById('createTaskModal');
    if (createTaskModal) {
        createTaskModal.addEventListener('show.bs.modal', function() {
            displayTaskUnits();
        });
        
        createTaskModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('modal_task_details').value = '';
            document.getElementById('modal_functional_division').value = '';
            document.getElementById('target_completion_date').value = '';
            document.getElementById('modal_priority').value = 'medium';
            document.getElementById('modal_budget').value = '0.00';
            
            // Uncheck all checkboxes
            var checkboxes = document.getElementsByClassName('task-unit-checkbox');
            for(var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = false;
            }
            updateTaskSelectedCount();
        });
    }
});




function openUpdateModal(taskId) {
    // Fetch task data via AJAX
    fetch(`index.php?action=get_task&id=${taskId}`)
        .then(response => response.json())
        .then(task => {
            document.getElementById('update_task_id').value = task.id;
            document.getElementById('update_task_details').value = task.task_details;
            document.getElementById('update_functional_division').value = task.functional_division;
            document.getElementById('update_priority').value = task.priority;
            document.getElementById('update_budget').value = task.budget_allocation || 0;
            document.getElementById('update_status').value = task.status;
            document.getElementById('update_target_date').value = task.target_completion_date;
            document.getElementById('update_actual_date').value = task.actual_completion_date || '';
            document.getElementById('update_remarks').value = task.remarks || '';
            
            // Show the modal
            var updateModal = new bootstrap.Modal(document.getElementById('updateTaskModal'));
            updateModal.show();
        })
        .catch(error => {
            console.error('Error fetching task:', error);
            alert('Error loading task data');
        });
}

// Fix for the create task form submission
document.getElementById('createTaskForm')?.addEventListener('submit', function(e) {
    var checkboxes = document.getElementsByClassName('task-unit-checkbox');
    var checkedCount = 0;
    for(var i = 0; i < checkboxes.length; i++) {
        if(checkboxes[i].checked) checkedCount++;
    }
    
    if(checkedCount === 0) {
        e.preventDefault();
        alert('Please select at least one participating unit.');
        return false;
    }
});
</script>

<?php require_once 'views/layout/footer.php'; ?>