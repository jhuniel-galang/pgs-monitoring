<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Task</h1>


    <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php 
        echo $_SESSION['error']; 
        unset($_SESSION['error']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Edit Task Details
        </div>
        <div class="card-body">
            <form action="index.php?action=update_task" method="POST">
                <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                
                <div class="mb-3">
                    <label for="task_details" class="form-label">Task Details <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="task_details" name="task_details" rows="4" required><?php echo htmlspecialchars($task['task_details']); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="functional_division" class="form-label">Division <span class="text-danger">*</span></label>
                        <select class="form-select" id="functional_division" name="functional_division" required onchange="loadUnits()">
                            <option value="">Select Division</option>
                            <option value="OSDS" <?php echo ($task['functional_division'] == 'OSDS') ? 'selected' : ''; ?>>OSDS</option>
                            <option value="CID" <?php echo ($task['functional_division'] == 'CID') ? 'selected' : ''; ?>>CID</option>
                            <option value="SGOD" <?php echo ($task['functional_division'] == 'SGOD') ? 'selected' : ''; ?>>SGOD</option>
                            <option value="Schools" <?php echo ($task['functional_division'] == 'Schools') ? 'selected' : ''; ?>>Schools</option>
                        </select>
                        <small class="text-muted">This is the main division responsible for the task</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="target_completion_date" class="form-label">Target Date <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="target_completion_date" name="target_completion_date" 
                               placeholder="e.g., Quarterly, Annually, or specific date" 
                               value="<?php echo htmlspecialchars($task['target_completion_date'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="low" <?php echo ($task['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo ($task['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo ($task['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
                            <option value="critical" <?php echo ($task['priority'] == 'critical') ? 'selected' : ''; ?>>Critical</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="budget_allocation" class="form-label">Budget Allocation</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="budget_allocation" name="budget_allocation" 
                                   step="0.01" min="0" value="<?php echo $task['budget_allocation'] ?? 0; ?>">
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="project_id" class="form-label">Project (Optional)</label>
                        <select class="form-select" id="project_id" name="project_id">
                            <option value="">No Project</option>
                            <?php if(isset($projects) && !empty($projects)): ?>
                                <?php foreach($projects as $project): ?>
                                <option value="<?php echo $project['id']; ?>" 
                                    <?php echo ($task['project_id'] == $project['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($project['project_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Units Selection Section -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Assigned Units <span class="text-danger">*</span></label>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            Select the units that will be responsible for this task.
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-light" id="unit-header">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-select form-select-sm" id="filter_division" onchange="filterUnitsByDivision()">
                                            <option value="">All Divisions</option>
                                            <option value="OSDS">OSDS</option>
                                            <option value="CID">CID</option>
                                            <option value="SGOD">SGOD</option>
                                            <option value="Schools">Schools</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkAllUnits()">Check All</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="uncheckAllUnits()">Uncheck All</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <div class="row" id="units-container">
                                    <!-- Units will be loaded here via JavaScript -->
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <span id="selected-count">0</span> unit(s) selected
                            </div>
                        </div>
                        <small class="text-muted">You can select multiple units to assign to this task</small>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-warning">Update Task</button>
                    <a href="index.php?action=view_task&id=<?php echo $task['task_id']; ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Get all units data from PHP
var allUnits = <?php echo json_encode(array_map(function($unit) {
    return [
        'id' => $unit['id'],
        'name' => $unit['unit_name'],
        'division' => $unit['functional_division'],
        'pic' => $unit['person_in_charge'] ?? 'N/A'
    ];
}, $units)); ?>;

// Get currently selected unit IDs from the task
var selectedUnitIds = <?php 
    $unit_ids = [];
    if(isset($task['unit_ids']) && $task['unit_ids']) {
        $unit_ids = explode(',', $task['unit_ids']);
    }
    echo json_encode($unit_ids); 
?>;

// Display all units as checkboxes with pre-selected ones checked
function displayAllUnits() {
    var container = document.getElementById('units-container');
    
    if (!container) return;
    
    // Clear container
    container.innerHTML = '';
    
    if (allUnits.length === 0) {
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
        container.appendChild(createDivisionSection('OSDS', 'primary', osdsUnits));
    }
    
    if (cidUnits.length > 0) {
        container.appendChild(createDivisionSection('CID', 'success', cidUnits));
    }
    
    if (sgodUnits.length > 0) {
        container.appendChild(createDivisionSection('SGOD', 'info', sgodUnits));
    }
    
    if (schoolsUnits.length > 0) {
        container.appendChild(createDivisionSection('Schools', 'secondary', schoolsUnits));
    }
    
    updateSelectedCount();
}

// Helper function to create division section
function createDivisionSection(division, color, units) {
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
        col.className = 'col-md-4 mb-2 unit-item';
        col.setAttribute('data-division', unit.division);
        
        var div = document.createElement('div');
        div.className = 'form-check';
        
        var checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'form-check-input unit-checkbox';
        checkbox.name = 'unit_ids[]';
        checkbox.value = unit.id;
        checkbox.id = 'unit_' + unit.id;
        
        // Check if this unit is already selected
        if (selectedUnitIds.includes(unit.id.toString())) {
            checkbox.checked = true;
        }
        
        checkbox.addEventListener('change', updateSelectedCount);
        
        var label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = 'unit_' + unit.id;
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

// Filter units by division
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

// Check all units
function checkAllUnits() {
    var checkboxes = document.getElementsByClassName('unit-checkbox');
    for(var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = true;
    }
    updateSelectedCount();
}

// Uncheck all units
function uncheckAllUnits() {
    var checkboxes = document.getElementsByClassName('unit-checkbox');
    for(var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = false;
    }
    updateSelectedCount();
}

// Update selected count
function updateSelectedCount() {
    var checkboxes = document.getElementsByClassName('unit-checkbox');
    var count = 0;
    for(var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) count++;
    }
    document.getElementById('selected-count').innerText = count;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    displayAllUnits();
    
    // Load units based on selected division
    var selectedDivision = document.getElementById('functional_division').value;
    if(selectedDivision) {
        filterUnitsByDivision();
    }
});

// Form validation - ensure at least one unit is selected
document.querySelector('form').addEventListener('submit', function(e) {
    var checkboxes = document.getElementsByClassName('unit-checkbox');
    var checked = false;
    
    for(var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            checked = true;
            break;
        }
    }
    
    if (!checked) {
        e.preventDefault();
        alert('Please select at least one unit for this task.');
    }
});

// Load units when division changes
document.getElementById('functional_division').addEventListener('change', function() {
    filterUnitsByDivision();
});
</script>

<style>
/* Add some styling for the unit checkboxes */
.unit-checkbox {
    margin-right: 8px;
    transform: scale(1.1);
}

.form-check-label {
    cursor: pointer;
}

.form-check-label strong {
    display: block;
    margin-bottom: 2px;
}

.unit-item {
    padding: 8px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.unit-item:hover {
    background-color: #f8f9fa;
}

/* Division badge styling */
.badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    margin-bottom: 0.5rem;
}

/* Different colors for Schools division */
.bg-secondary {
    background-color: #6c757d !important;
}
.text-secondary {
    color: #6c757d !important;
}
</style>

<?php require_once 'views/layout/footer.php'; ?>