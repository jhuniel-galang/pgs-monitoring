<?php require_once 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Update Task Progress</h2>
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
                <h5 class="mb-0">Task Progress Update Form</h5>
            </div>
            <div class="card-body">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form action="index.php?action=update_status" method="POST" id="updateTaskForm">
                    <input type="hidden" name="task_id" value="<?php echo $task['task_id'] ?? 0; ?>">
                    
                    <!-- Task Details -->
                    <div class="card mb-4 bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Task Information</h6>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <strong>Task:</strong>
                                    <p><?php echo htmlspecialchars($task['task_details'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Division:</strong>
                                    <p>
                                        <?php if(isset($task['functional_division'])): ?>
                                        <span class="badge bg-<?php 
                                            echo $task['functional_division'] == 'OSDS' ? 'primary' : 
                                                ($task['functional_division'] == 'CID' ? 'success' : 'info'); 
                                        ?> fs-6">
                                            <?php echo $task['functional_division']; ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">N/A</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Unit:</strong>
                                    <p><?php echo htmlspecialchars($task['unit_name'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Target Date:</strong>
                                    <p><?php echo isset($task['target_completion_date']) ? date('F d, Y', strtotime($task['target_completion_date'])) : 'N/A'; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Priority:</strong>
                                    <p>
                                        <?php if(isset($task['priority'])): ?>
                                        <span class="badge bg-<?php 
                                            echo $task['priority'] == 'critical' ? 'danger' : 
                                                ($task['priority'] == 'high' ? 'warning' : 
                                                ($task['priority'] == 'medium' ? 'info' : 'secondary')); 
                                        ?> fs-6">
                                            <?php echo ucfirst($task['priority']); ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">N/A</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current Progress -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Current Progress</label>
                        <?php $current_percentage = $task['current_percentage'] ?? 0; ?>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-info" 
                                 style="width: <?php echo $current_percentage; ?>%">
                                <?php echo $current_percentage; ?>%
                            </div>
                        </div>
                    </div>
                    
                    <!-- Update Progress -->
                    <div class="mb-4">
                        <label for="percentage" class="form-label fw-bold">Update Completion Percentage</label>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="range" class="form-range" min="0" max="100" step="5" 
                                       id="percentage" name="percentage" 
                                       value="<?php echo $current_percentage; ?>"
                                       oninput="updatePercentage(this.value)">
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="percentageInput" 
                                           min="0" max="100" step="5"
                                           value="<?php echo $current_percentage; ?>"
                                           onchange="document.getElementById('percentage').value = this.value; updatePercentage(this.value);">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar bg-success" id="previewBar" 
                                 style="width: <?php echo $current_percentage; ?>%">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Percentage Buttons -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Quick Set:</label>
                        <div class="btn-group flex-wrap" role="group">
                            <button type="button" class="btn btn-outline-secondary" onclick="setPercentage(0)">0%</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setPercentage(25)">25%</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setPercentage(50)">50%</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setPercentage(75)">75%</button>
                            <button type="button" class="btn btn-outline-success" onclick="setPercentage(100)">100% Complete</button>
                        </div>
                    </div>
                    
                    <!-- Remarks -->
                    <div class="mb-4">
                        <label for="remarks" class="form-label fw-bold">Remarks / Accomplishments</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="5" 
                                  placeholder="Please provide detailed information about the progress, accomplishments, challenges, and next steps..." 
                                  required></textarea>
                        <div class="form-text">
                            <ul class="mt-2">
                                <li>What specific accomplishments were made?</li>
                                <li>Any challenges encountered?</li>
                                <li>What are the next steps?</li>
                                <li>Do you need any support?</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Previous Updates -->
                    <?php if(isset($status_history) && !empty($status_history)): ?>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Previous Updates</label>
                        <div class="list-group">
                            <?php foreach($status_history as $history): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <?php echo isset($history['created_at']) ? date('M d, Y h:i A', strtotime($history['created_at'])) : 'N/A'; ?>
                                    </small>
                                    <span class="badge bg-primary"><?php echo $history['percentage'] ?? 0; ?>%</span>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($history['remarks'] ?? 'No remarks'); ?></p>
                                <small class="text-muted">
                                    Updated by: <?php echo htmlspecialchars($history['updated_by_name'] ?? 'Unknown'); ?>
                                </small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Update Task Progress</button>
                        <a href="index.php?action=tasks" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updatePercentage(value) {
    document.getElementById('percentage').value = value;
    document.getElementById('percentageInput').value = value;
    document.getElementById('previewBar').style.width = value + '%';
}

function setPercentage(value) {
    document.getElementById('percentage').value = value;
    document.getElementById('percentageInput').value = value;
    document.getElementById('previewBar').style.width = value + '%';
}

// Form validation
document.getElementById('updateTaskForm').addEventListener('submit', function(e) {
    const remarks = document.getElementById('remarks');
    if (!remarks.value.trim()) {
        e.preventDefault();
        alert('Please enter remarks about the accomplishment.');
    }
});
</script>

<?php require_once 'views/layout/footer.php'; ?>