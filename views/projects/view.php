<?php require_once 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Project Details</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="index.php?action=projects" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Projects
        </a>
    </div>
</div>

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

<!-- Project Overview Card -->
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Project Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold">Project Code</h6>
                        <p class="fs-5"><?php echo htmlspecialchars($project['project_code']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold">Project Name</h6>
                        <p class="fs-5"><?php echo htmlspecialchars($project['project_name']); ?></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <h6 class="fw-bold">Description</h6>
                        <p><?php echo nl2br(htmlspecialchars($project['project_description'] ?? 'No description provided.')); ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="fw-bold">Division</h6>
                        <p>
                            <span class="badge bg-<?php 
                                echo $project['functional_division'] == 'OSDS' ? 'primary' : 
                                    ($project['functional_division'] == 'CID' ? 'success' : 'info'); 
                            ?> fs-6 p-2">
                                <?php echo $project['functional_division']; ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="fw-bold">Project Lead</h6>
                        <p><?php echo htmlspecialchars($project['project_lead'] ?? 'Not assigned'); ?></p>
                        <?php if(!empty($project['lead_designation'])): ?>
                        <small class="text-muted"><?php echo htmlspecialchars($project['lead_designation']); ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="fw-bold">Status</h6>
                        <p>
                            <span class="badge bg-<?php 
                                echo $project['status'] == 'completed' ? 'success' : 
                                    ($project['status'] == 'ongoing' ? 'primary' : 
                                    ($project['status'] == 'planning' ? 'info' : 
                                    ($project['status'] == 'on_hold' ? 'warning' : 'secondary'))); 
                            ?> fs-6 p-2">
                                <?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
    
        
        <!-- Project Tasks -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Project Tasks</h5>

            </div>
            <div class="card-body">
                <?php
                // Get tasks for this project
                require_once 'models/Task.php';
                $taskModel = new Task();
                $project_tasks = $taskModel->getTasksByProject($project['project_id']);
                ?>
                
                <?php if(empty($project_tasks)): ?>
                <p class="text-muted">No tasks assigned to this project.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Unit</th>
                                <th>Priority</th>
                                <th>Progress</th>
                                <th>Last Update</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($project_tasks as $task): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(substr($task['task_details'], 0, 50)); ?>...</td>
                                <td><?php echo htmlspecialchars($task['unit_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $task['priority'] == 'critical' ? 'danger' : 
                                            ($task['priority'] == 'high' ? 'warning' : 
                                            ($task['priority'] == 'medium' ? 'info' : 'secondary')); 
                                    ?>">
                                        <?php echo ucfirst($task['priority']); ?>
                                    </span>
                                </td>
                                <td style="width: 150px;">
                                    <div class="progress" style="height: 20px;">
                                        <?php $percentage = $task['current_percentage'] ?? 0; ?>
                                        <div class="progress-bar bg-<?php 
                                            echo $percentage >= 100 ? 'success' : 
                                                ($percentage >= 50 ? 'info' : 'warning'); 
                                        ?>" style="width: <?php echo $percentage; ?>%">
                                            <?php echo $percentage; ?>%
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $task['last_update'] ? date('M d, Y', strtotime($task['last_update'])) : 'No updates'; ?></td>
                                <td>
                                    <a href="index.php?action=view_task&id=<?php echo $task['task_id']; ?>" 
                                       class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Progress Card -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Project Progress</h5>
            </div>
            <div class="card-body text-center">
                <h1 class="display-1 <?php 
                    echo $project['avg_progress'] >= 100 ? 'text-success' : 
                        ($project['avg_progress'] >= 50 ? 'text-info' : 'text-warning'); 
                ?>">
                    <?php echo round($project['avg_progress'] ?? 0); ?>%
                </h1>
                <div class="progress mt-3" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: <?php echo $project['avg_progress'] ?? 0; ?>%"></div>
                </div>
                <p class="mt-3">
                    Tasks: <?php echo $project['completed_tasks'] ?? 0; ?>/<?php echo $project['total_tasks'] ?? 0; ?> completed
                </p>
                
                <?php if($_SESSION['role'] == 'admin' || $_SESSION['functional_division'] == $project['functional_division']): ?>
                <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#updateProgressModal">
                    <i class="bi bi-pencil"></i> Update Progress
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Timeline Card -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Timeline</h5>
            </div>
            <div class="card-body">
                
                <?php if($project['target_end_date']): ?>
                <p><strong>Target End Date:</strong> <?php echo date('F d, Y', strtotime($project['target_end_date'])); ?></p>
                
                <?php 
                $today = new DateTime();
                $end = new DateTime($project['target_end_date']);
                $interval = $today->diff($end);
                $days_left = $interval->days;
                
                if($today > $end) {
                    echo '<p class="text-danger">Overdue by ' . $days_left . ' days</p>';
                } else {
                    echo '<p class="text-success">' . $days_left . ' days remaining</p>';
                }
                ?>
                <?php endif; ?>
                
                <?php if($project['actual_end_date']): ?>
                <p><strong>Completed:</strong> <?php echo date('F d, Y', strtotime($project['actual_end_date'])); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Budget Card -->
        <?php if($project['budget_allocation'] > 0): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Budget</h5>
            </div>
            <div class="card-body">
                <h3>₱ <?php echo number_format($project['budget_allocation'], 2); ?></h3>
                <p class="text-muted">Allocated Budget</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Update Progress Modal -->
<div class="modal fade" id="updateProgressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Update Project Progress</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=update_project_progress" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="project_id" value="<?php echo $project['project_id']; ?>">
                    
                    <div class="mb-3">
                        <label for="progress_percentage" class="form-label">Progress Percentage</label>
                        <input type="range" class="form-range" min="0" max="100" step="5" 
                               id="progress_percentage" name="progress_percentage" 
                               value="<?php echo round($project['avg_progress'] ?? 0); ?>"
                               oninput="this.nextElementSibling.value = this.value + '%'">
                        <output class="badge bg-primary fs-6 mt-2"><?php echo round($project['avg_progress'] ?? 0); ?>%</output>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Update Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="planning" <?php echo $project['status'] == 'planning' ? 'selected' : ''; ?>>Planning</option>
                            <option value="ongoing" <?php echo $project['status'] == 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                            <option value="completed" <?php echo $project['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="on_hold" <?php echo $project['status'] == 'on_hold' ? 'selected' : ''; ?>>On Hold</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="progress_remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="progress_remarks" name="remarks" rows="3" 
                                  placeholder="Add any remarks about the progress..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Progress</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Units Modal -->
<div class="modal fade" id="addUnitsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Add Participating Units</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=add_project_units" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="project_id" value="<?php echo $project['project_id']; ?>">
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        You can add units from any division to this project.
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="filter_division" class="form-label">Filter by Division</label>
                            <select class="form-select" id="filter_division" onchange="filterUnitsByDivision()">
                                <option value="">All Divisions</option>
                                <option value="OSDS">OSDS</option>
                                <option value="CID">CID</option>
                                <option value="SGOD">SGOD</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary me-2" onclick="checkAllAvailableUnits()">Check All</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="uncheckAllAvailableUnits()">Uncheck All</button>
                        </div>
                    </div>
                    
                    <div class="row" id="available-units-container">
                        <!-- Units will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Add Selected Units</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Task to Project</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=create_task" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="project_id" value="<?php echo $project['project_id']; ?>">
                    <input type="hidden" name="from_project" value="yes">
                    <input type="hidden" name="functional_division" value="<?php echo $project['functional_division']; ?>">
                    
                    <div class="mb-3">
                        <label for="task_details" class="form-label">Task Details <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="task_details" name="task_details" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="unit_id" class="form-label">Assign to Unit <span class="text-danger">*</span></label>
                            <select class="form-select" id="unit_id" name="unit_id" required>
                                <option value="">Select Unit</option>
                                <?php 
                                // Get all units that are part of this project
                                require_once 'models/Unit.php';
                                $unitModel = new Unit();
                                $projectUnits = $unitModel->getUnitsByProject($project['project_id']);
                                foreach($projectUnits as $unit): 
                                ?>
                                <option value="<?php echo $unit['id']; ?>">
                                    [<?php echo $unit['functional_division']; ?>] <?php echo htmlspecialchars($unit['unit_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="target_completion_date" class="form-label">Target Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="target_completion_date" name="target_completion_date" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        This task will be associated with the project "<?php echo htmlspecialchars($project['project_name']); ?>"
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Load available units when modal opens
document.getElementById('addUnitsModal').addEventListener('show.bs.modal', function() {
    loadAvailableUnits();
});

function loadAvailableUnits() {
    var container = document.getElementById('available-units-container');
    container.innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Get all units
    var allUnits = <?php echo json_encode(array_map(function($unit) {
        return [
            'id' => $unit['id'],
            'name' => $unit['unit_name'],
            'division' => $unit['functional_division'],
            'pic' => $unit['person_in_charge'] ?? 'N/A'
        ];
    }, $units)); ?>;
    
    // Get already assigned unit IDs
    var assignedUnitIds = <?php echo json_encode(array_column($project_units, 'id')); ?>;
    
    // Filter out already assigned units
    var availableUnits = allUnits.filter(function(unit) {
        return !assignedUnitIds.includes(unit.id);
    });
    
    displayAvailableUnits(availableUnits);
}

function displayAvailableUnits(units) {
    var container = document.getElementById('available-units-container');
    container.innerHTML = '';
    
    if (units.length === 0) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-info">All units are already assigned to this project.</div></div>';
        return;
    }
    
    units.forEach(function(unit) {
        var col = document.createElement('div');
        col.className = 'col-md-4 mb-2 unit-item';
        col.setAttribute('data-division', unit.division);
        
        var div = document.createElement('div');
        div.className = 'form-check';
        
        var checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'form-check-input available-unit-checkbox';
        checkbox.name = 'unit_ids[]';
        checkbox.value = unit.id;
        checkbox.id = 'available_unit_' + unit.id;
        
        var label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = 'available_unit_' + unit.id;
        label.innerHTML = '<strong>' + unit.name + '</strong><br>' +
                         '<small class="text-muted">[' + unit.division + '] PIC: ' + unit.pic + '</small>';
        
        div.appendChild(checkbox);
        div.appendChild(label);
        col.appendChild(div);
        container.appendChild(col);
    });
}

function filterUnitsByDivision() {
    var division = document.getElementById('filter_division').value;
    var unitItems = document.getElementsByClassName('unit-item');
    
    for(var i = 0; i < unitItems.length; i++) {
        if (division === '' || unitItems[i].getAttribute('data-division') === division) {
            unitItems[i].style.display = '';
        } else {
            unitItems[i].style.display = 'none';
        }
    }
}

function checkAllAvailableUnits() {
    var checkboxes = document.getElementsByClassName('available-unit-checkbox');
    for(var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = true;
    }
}

function uncheckAllAvailableUnits() {
    var checkboxes = document.getElementsByClassName('available-unit-checkbox');
    for(var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = false;
    }
}

function removeUnit(projectId, unitId) {
    if(confirm('Are you sure you want to remove this unit from the project?')) {
        window.location.href = 'index.php?action=remove_project_unit&project_id=' + projectId + '&unit_id=' + unitId;
    }
}
</script>

<?php require_once 'views/layout/footer.php'; ?>
