<?php require_once 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Create New Task</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="index.php?action=tasks" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Tasks
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">New Task Information</h5>
            </div>
            <div class="card-body">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="index.php?action=create_task" method="POST">
                    <div class="mb-3">
                        <label for="task_details" class="form-label">Task Details <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="task_details" name="task_details" rows="4" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="functional_division" class="form-label">Division <span class="text-danger">*</span></label>
                            <select class="form-select" id="functional_division" name="functional_division" required onchange="loadUnits()">
                                <option value="">Select Division</option>
                                <option value="OSDS">OSDS</option>
                                <option value="CID">CID</option>
                                <option value="SGOD">SGOD</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                            <select class="form-select" id="unit_id" name="unit_id" required>
                                <option value="">Select Division First</option>
                                <?php foreach($units as $unit): ?>
                                <option value="<?php echo $unit['id']; ?>" data-division="<?php echo $unit['functional_division']; ?>">
                                    <?php echo htmlspecialchars($unit['unit_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="target_completion_date" class="form-label">Target Completion Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="target_completion_date" name="target_completion_date" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
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
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Create Task</button>
                        <a href="index.php?action=tasks" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function loadUnits() {
    var division = document.getElementById('functional_division').value;
    var unitSelect = document.getElementById('unit_id');
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

// Initialize on page load
window.onload = function() {
    var division = document.getElementById('functional_division').value;
    if(division) {
        loadUnits();
    }
};
</script>

<?php require_once 'views/layout/footer.php'; ?>