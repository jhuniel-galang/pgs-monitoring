<?php require_once 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Task Details</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Task Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="fw-bold">Task Details:</h6>
                        <p class="mb-4"><?php echo htmlspecialchars($task['task_details'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-md-4">
                        <?php if(isset($task['current_percentage'])): ?>
                        <div class="text-center mb-3">
                            <h6 class="fw-bold">Current Progress</h6>
                            <h1 class="display-4 <?php 
                                echo $task['current_percentage'] >= 100 ? 'text-success' : 
                                    ($task['current_percentage'] >= 50 ? 'text-info' : 'text-warning'); 
                            ?>">
                                <?php echo $task['current_percentage']; ?>%
                            </h1>
                            <div class="progress mt-2" style="height: 10px;">
                                <div class="progress-bar bg-<?php 
                                    echo $task['current_percentage'] >= 100 ? 'success' : 
                                        ($task['current_percentage'] >= 50 ? 'info' : 'warning'); 
                                ?>" style="width: <?php echo $task['current_percentage']; ?>%"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold">Division</h6>
                                <p>
                                    <?php if(isset($task['functional_division'])): ?>
                                    <span class="badge bg-<?php 
                                        echo $task['functional_division'] == 'OSDS' ? 'primary' : 
                                            ($task['functional_division'] == 'CID' ? 'success' : 'info'); 
                                    ?> p-2">
                                        <?php echo $task['functional_division']; ?>
                                    </span>
                                    <?php else: ?>
                                    N/A
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold">Units</h6>
                                <p>
                                    <?php 
                                    if(isset($task['unit_names']) && $task['unit_names']) {
                                        $units = explode(', ', $task['unit_names']);
                                        foreach($units as $unit) {
                                            echo '<span class="badge bg-info me-1 mb-1">' . htmlspecialchars($unit) . '</span>';
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </p>
                                <?php if(isset($task['person_in_charge'])): ?>
                                <small class="text-muted">PIC: <?php echo htmlspecialchars($task['person_in_charge']); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold">Target Date</h6>
                                <p><?php echo isset($task['target_completion_date']) ? htmlspecialchars($task['target_completion_date']) : 'N/A'; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold">Priority</h6>
                                <p>
                                    <?php if(isset($task['priority'])): ?>
                                    <span class="badge bg-<?php 
                                        echo $task['priority'] == 'critical' ? 'danger' : 
                                            ($task['priority'] == 'high' ? 'warning' : 
                                            ($task['priority'] == 'medium' ? 'info' : 'secondary')); 
                                    ?> p-2">
                                        <?php echo ucfirst($task['priority']); ?>
                                    </span>
                                    <?php else: ?>
                                    N/A
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status History -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Progress History</h5>
            </div>
            <div class="card-body">
                <?php if(isset($status_history) && !empty($status_history)): ?>
                <div class="timeline">
                    <?php foreach($status_history as $history): ?>
                    <div class="card mb-3 <?php echo ($history['percentage'] ?? 0) >= 100 ? 'border-success' : ''; ?>">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <strong><?php echo isset($history['percentage']) ? $history['percentage'] . '%' : 'N/A'; ?></strong>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-<?php 
                                            echo ($history['percentage'] ?? 0) >= 100 ? 'success' : 
                                                (($history['percentage'] ?? 0) >= 50 ? 'info' : 'warning'); 
                                        ?>" style="width: <?php echo $history['percentage'] ?? 0; ?>%"></div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <p class="mb-1"><?php echo htmlspecialchars($history['remarks'] ?? 'No remarks'); ?></p>
                                </div>
                                <div class="col-md-3 text-end">
                                    <small class="text-muted">
                                        <?php echo isset($history['created_at']) ? date('M d, Y h:i A', strtotime($history['created_at'])) : 'N/A'; ?>
                                    </small><br>
                                    <small class="text-muted">
                                        By: <?php echo htmlspecialchars($history['updated_by_name'] ?? 'Unknown'); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted">No status updates yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mt-4">
    <div class="col-md-12">
        <?php 
        $canUpdate = false;
        if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            $canUpdate = true;
        } elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'encoder' 
                && isset($_SESSION['functional_division']) 
                && isset($task['functional_division'])
                && $_SESSION['functional_division'] == $task['functional_division']) {
            $canUpdate = true;
        }
        
        if($canUpdate): 
        ?>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $task['task_id']; ?>">
            <i class="bi bi-pencil"></i> Update Progress
        </button>
        <?php endif; ?>
        <a href="index.php?action=tasks" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Tasks
        </a>
    </div>
</div>

<!-- Include the update modal for this task -->
<?php if(isset($task) && $task): ?>
<div class="modal fade" id="updateModal<?php echo $task['task_id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Update Task Progress</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=update_status" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                    
                    <!-- Task Details Summary -->
                    <div class="card mb-3 bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Task: <?php echo htmlspecialchars($task['task_details']); ?></h6>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Division:</small><br>
                                    <span class="badge bg-<?php 
                                        echo $task['functional_division'] == 'OSDS' ? 'primary' : 
                                            ($task['functional_division'] == 'CID' ? 'success' : 'info'); 
                                    ?>"><?php echo $task['functional_division']; ?></span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Units:</small><br>
                                    <?php 
                                    if(isset($task['unit_names']) && $task['unit_names']) {
                                        $units = explode(', ', $task['unit_names']);
                                        foreach($units as $unit) {
                                            echo '<span class="badge bg-info me-1">' . htmlspecialchars($unit) . '</span>';
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Target Date:</small><br>
                                    <strong><?php echo htmlspecialchars($task['target_completion_date'] ?? 'N/A'); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Progress</label>
                        <?php $current_percent = $task['current_percentage'] ?? 0; ?>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" style="width: <?php echo $current_percent; ?>%">
                                <?php echo $current_percent; ?>%
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="percentage<?php echo $task['task_id']; ?>" class="form-label fw-bold">
                            New Progress Percentage
                        </label>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="range" class="form-range" 
                                       id="percentage<?php echo $task['task_id']; ?>" 
                                       name="percentage"
                                       min="0" max="100" step="5" 
                                       value="<?php echo $current_percent; ?>"
                                       oninput="updateModalPercent(<?php echo $task['task_id']; ?>, this.value)">
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="number" class="form-control" 
                                           id="modalPercentInput<?php echo $task['task_id']; ?>"
                                           min="0" max="100" step="5"
                                           value="<?php echo $current_percent; ?>"
                                           onchange="updateModalPercentFromInput(<?php echo $task['task_id']; ?>, this.value)">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar bg-success" 
                                 id="modalPreviewBar<?php echo $task['task_id']; ?>"
                                 style="width: <?php echo $current_percent; ?>%">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Quick Set:</label>
                        <div class="btn-group flex-wrap" role="group">
                            <button type="button" class="btn btn-outline-warning btn-sm" 
                                    onclick="setTaskDeferred(<?php echo $task['task_id']; ?>)">
                                <i class="bi bi-hourglass-split"></i> Deferred/Postponed
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                    onclick="setModalPercent(<?php echo $task['task_id']; ?>, 0)">0%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                    onclick="setModalPercent(<?php echo $task['task_id']; ?>, 25)">25%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                    onclick="setModalPercent(<?php echo $task['task_id']; ?>, 50)">50%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                    onclick="setModalPercent(<?php echo $task['task_id']; ?>, 75)">75%</button>
                            <button type="button" class="btn btn-outline-success btn-sm" 
                                    onclick="setModalPercent(<?php echo $task['task_id']; ?>, 100)">100%</button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="modalRemarks<?php echo $task['task_id']; ?>" class="form-label fw-bold">
                            Remarks / Accomplishments
                        </label>
                        <textarea class="form-control" 
                                  id="modalRemarks<?php echo $task['task_id']; ?>" 
                                  name="remarks" 
                                  rows="3" 
                                  placeholder="Describe what was accomplished..." 
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Update Progress</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateModalPercent(taskId, value) {
    document.getElementById('modalPercentInput' + taskId).value = value;
    document.getElementById('modalPreviewBar' + taskId).style.width = value + '%';
}

function updateModalPercentFromInput(taskId, value) {
    if(value < 0) value = 0;
    if(value > 100) value = 100;
    document.getElementById('percentage' + taskId).value = value;
    document.getElementById('modalPreviewBar' + taskId).style.width = value + '%';
}

function setModalPercent(taskId, value) {
    document.getElementById('percentage' + taskId).value = value;
    document.getElementById('modalPercentInput' + taskId).value = value;
    document.getElementById('modalPreviewBar' + taskId).style.width = value + '%';
}

function setTaskDeferred(taskId) {
    // Set progress to 0%
    document.getElementById('percentage' + taskId).value = 0;
    document.getElementById('modalPercentInput' + taskId).value = 0;
    document.getElementById('modalPreviewBar' + taskId).style.width = '0%';
    
    // Set a default remark
    var remarksField = document.getElementById('modalRemarks' + taskId);
    if (remarksField && remarksField.value.trim() === '') {
        remarksField.value = 'Task deferred/postponed';
    }
}
</script>
<?php endif; ?>

<?php require_once 'views/layout/footer.php'; ?>