<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Create New Task</h1>


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
            <i class="fas fa-plus-circle me-1"></i>
            Task Details
        </div>
        <div class="card-body">
            <form action="index.php?action=create_task" method="POST" id="taskForm">
                <div class="mb-3">
                    <label for="task_details" class="form-label">Task Details <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="task_details" name="task_details" rows="4" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="project_id" class="form-label">Project (Optional)</label>
                        <select class="form-select" id="project_id" name="project_id" onchange="loadProjectDetails()">
                            <option value="">No Project</option>
                            <?php if(isset($projects) && !empty($projects)): ?>
                                <?php foreach($projects as $project): ?>
                                <option value="<?php echo $project['id']; ?>" 
                                        data-division="<?php echo $project['functional_division']; ?>"
                                        data-target-date="<?php echo $project['target_end_date']; ?>"
                                        data-budget="<?php echo $project['budget_allocation']; ?>">
                                    <?php echo htmlspecialchars($project['project_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Selecting a project will auto-fill the division</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="functional_division" class="form-label">Division <span class="text-danger">*</span></label>
                        <select class="form-select" id="functional_division" name="functional_division" required>
                            <option value="">Select Lead Division</option>
                            <option value="OSDS">OSDS</option>
                            <option value="CID">CID</option>
                            <option value="SGOD">SGOD</option>
                            <option value="Schools">Schools</option>
                        </select>
                        <small class="text-muted">This is the main division responsible for the task</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="target_completion_date" class="form-label">Target Date <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="target_completion_date" name="target_completion_date" 
                               placeholder="e.g., Quarterly, Annually, or specific date" required>
                    </div>

                    <div class="col-md-6 mb-3">
    <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="year" name="year" 
           placeholder="e.g., 2024, 2025, SY 2024-2025" 
           value="<?php echo date('Y'); ?>" required>
    <small class="text-muted">Enter the year for this commitment</small>
</div>

                    <div class="col-md-6 mb-3">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="planning">Planning</option>
                            <option value="ongoing" selected>Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="on_hold">On Hold</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="budget_allocation" class="form-label">Budget Allocation</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="budget_allocation" name="budget_allocation" 
                                   step="0.01" min="0" value="0">
                        </div>
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
                    <button type="submit" class="btn btn-primary">Create Task</button>
                    <a href="index.php?action=tasks" class="btn btn-secondary">Cancel</a>
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

// Load project details when a project is selected
function loadProjectDetails() {
    var projectSelect = document.getElementById('project_id');
    var selectedOption = projectSelect.options[projectSelect.selectedIndex];
    
    if (projectSelect.value) {
        // Get project data from data attributes
        var division = selectedOption.getAttribute('data-division');
        var targetDate = selectedOption.getAttribute('data-target-date');
        var budget = selectedOption.getAttribute('data-budget');
        
        // Auto-fill the division field
        if (division) {
            document.getElementById('functional_division').value = division;
        }
        
        // Optionally auto-fill other fields (commented out as per your request to remove them)
        // if (targetDate) {
        //     document.getElementById('target_completion_date').value = targetDate;
        // }
        // if (budget) {
        //     document.getElementById('budget_allocation').value = budget;
        // }
    }
}

// Display all units as checkboxes
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
    
    // Check if there's a pre-selected project from URL parameter
    var urlParams = new URLSearchParams(window.location.search);
    var projectId = urlParams.get('project_id');
    if (projectId) {
        var projectSelect = document.getElementById('project_id');
        for(var i = 0; i < projectSelect.options.length; i++) {
            if (projectSelect.options[i].value == projectId) {
                projectSelect.selectedIndex = i;
                loadProjectDetails();
                break;
            }
        }
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