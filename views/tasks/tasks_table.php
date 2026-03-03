<?php if(empty($filtered_tasks)): ?>
    <p class="text-muted">No commitments found.</p>
<?php else: ?>
<div class="table-responsive">
    
    <table class="table table-striped table-hover">
        <thead>
    <tr>
        <th>Commitments</th>
        <th>Division</th>
        <th>Unit</th>
        <th>Year</th>
        <th>Target Date</th>
        <th>Priority</th>
        <th>Progress</th>
        <th>Last Update</th>
        <th>Actions</th>
    </tr>
</thead>
        <tbody>
            <?php foreach($filtered_tasks as $task): 
                // Determine the correct task ID field
                $taskId = $task['task_id'] ?? $task['id'] ?? 0;
                if(!$taskId) continue; // Skip if no ID
                
                // Double-check encoder access (extra safety)
                if($_SESSION['role'] == 'encoder' && isset($task['functional_division']) && $task['functional_division'] != $_SESSION['functional_division']) {
                    continue; // Skip tasks not in encoder's division
                }
                
                // Determine permissions
                $canUpdate = false; // For progress updates
                $canEdit = false;    // For editing task details
                $canDelete = false;   // For deleting tasks
                
                if($_SESSION['role'] == 'admin') {
                    $canUpdate = true;
                    $canEdit = true;
                    $canDelete = true;
                } elseif($_SESSION['role'] == 'encoder' && isset($task['functional_division']) && $task['functional_division'] == $_SESSION['functional_division']) {
                    $canUpdate = true; // Encoders can update progress
                    // Encoders cannot edit details or delete
                }
            ?>
            <tr>
                <td><?php echo htmlspecialchars($task['task_details'] ?? ''); ?></td>
                <td>
                    <?php if(isset($task['functional_division'])): ?>
                    <span class="badge bg-<?php 
                        echo $task['functional_division'] == 'OSDS' ? 'primary' : 
                            ($task['functional_division'] == 'CID' ? 'success' : 
                            ($task['functional_division'] == 'SGOD' ? 'info' : 'secondary')); 
                    ?>">
                        <?php echo $task['functional_division']; ?>
                    </span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    if(isset($task['unit_names']) && $task['unit_names']) {
                        $units = explode(', ', $task['unit_names']);
                        echo htmlspecialchars(implode(', ', $units));
                    } elseif(isset($task['unit_name']) && $task['unit_name']) {
                        echo htmlspecialchars($task['unit_name']);
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>

                <td>
    <?php if(!empty($task['year'])): ?>
        <span class="badge bg-dark"><?php echo htmlspecialchars($task['year']); ?></span>
    <?php else: ?>
        <span class="text-muted">—</span>
    <?php endif; ?>
</td>
                <td><?php echo htmlspecialchars($task['target_completion_date'] ?? 'N/A'); ?></td>
                <td>
                    <span class="badge bg-<?php 
                        echo ($task['priority'] ?? '') == 'critical' ? 'danger' : 
                            (($task['priority'] ?? '') == 'high' ? 'warning' : 
                            (($task['priority'] ?? '') == 'medium' ? 'info' : 'secondary')); 
                    ?>">
                        <?php echo ucfirst($task['priority'] ?? 'N/A'); ?>
                    </span>
                </td>
                <td style="min-width: 120px;">
                    <div class="progress" style="height: 20px;">
                        <?php $percentage = $task['current_percentage'] ?? 0; ?>
                        <div class="progress-bar bg-<?php 
                            echo $percentage >= 100 ? 'success' : 
                                ($percentage >= 50 ? 'info' : 'warning'); 
                        ?>" style="width: <?php echo $percentage; ?>%">
                            <?php echo $percentage; ?>%
                        </div>
                    </div>
                    <small class="text-muted">
                        <?php echo $percentage; ?>%
                    </small>
                </td>
                <td>
                    <?php 
                    if(isset($task['last_update']) && $task['last_update']) {
                        echo date('M d, Y', strtotime($task['last_update']));
                    } else {
                        echo '<span class="text-muted">No updates</span>';
                    }
                    ?>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="index.php?action=view_task&id=<?php echo $taskId; ?>" 
                           class="btn btn-sm btn-info">View</a>
                        
                        <?php if($canUpdate): ?>
                        <a href="index.php?action=update_task_page&id=<?php echo $taskId; ?>" 
                           class="btn btn-sm btn-success">Update</a>
                        <?php endif; ?>
                        
                        <?php if($canEdit): ?>
                        <a href="index.php?action=edit_task_page&id=<?php echo $taskId; ?>" 
                           class="btn btn-sm btn-warning">Edit</a>
                        <?php endif; ?>
                        
                        <?php if($canDelete): ?>
                        <a href="index.php?action=delete_task_direct&id=<?php echo $taskId; ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Delete this task?');">Delete</a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
/* Simple styles matching projects page */
.table td {
    vertical-align: middle;
}

/* Make the task column wrap properly */
.table td:first-child {
    word-break: break-word;
    white-space: normal;
    min-width: 250px;
}

/* Button group spacing */
.btn-group {
    gap: 2px;
}
</style>

<!-- Single Update Modal (keep this for update functionality) -->
<div class="modal fade" id="updateTaskModal" tabindex="-1" aria-labelledby="updateTaskModalLabel" aria-hidden="true">
    <!-- Your existing update modal content -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="updateTaskModalLabel">Update Task Progress</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?action=update_status" method="POST" id="updateTaskForm">
                <div class="modal-body">
                    <input type="hidden" name="task_id" id="modal_task_id">
                    
                    <!-- Task Details Summary -->
                    <div class="card mb-3 bg-light" id="taskSummary">
                        <div class="card-body">
                            <h6 class="card-title" id="modal_task_details">Loading...</h6>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <small class="text-muted">Division:</small><br>
                                    <span class="badge" id="modal_task_division">-</span>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Unit:</small><br>
                                    <strong id="modal_task_unit">-</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Target Date:</small><br>
                                    <strong id="modal_task_target_date">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current Progress Display -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Progress</label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" id="modal_current_progress" style="width: 0%">0%</div>
                        </div>
                    </div>
                    
                    <!-- Update Percentage Slider -->
                    <div class="mb-3">
                        <label for="modal_percentage" class="form-label fw-bold">
                            New Progress Percentage
                        </label>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="range" class="form-range" 
                                       id="modal_percentage" 
                                       name="percentage"
                                       min="0" max="100" step="5" 
                                       value="0"
                                       oninput="updateModalPercentValue(this.value)">
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="number" class="form-control" 
                                           id="modal_percent_input"
                                           min="0" max="100" step="5"
                                           value="0"
                                           onchange="updateModalPercentFromInput(this.value)">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar bg-success" 
                                 id="modal_preview_bar"
                                 style="width: 0%">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Percentage Buttons -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Quick Set:</label>
                        <div class="btn-group flex-wrap" role="group">
                            <button type="button" class="btn btn-outline-warning btn-sm" 
                                    onclick="setModalDeferred()">
                                <i class="bi bi-hourglass-split"></i> Deferred/Postponed
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                    onclick="setModalQuickPercent(0)">0%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                    onclick="setModalQuickPercent(25)">25%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                    onclick="setModalQuickPercent(50)">50%</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                    onclick="setModalQuickPercent(75)">75%</button>
                            <button type="button" class="btn btn-outline-success btn-sm" 
                                    onclick="setModalQuickPercent(100)">100%</button>
                        </div>
                    </div>
                    
                    <!-- Remarks -->
                    <div class="mb-3">
                        <label for="modal_remarks" class="form-label fw-bold">
                            Remarks / Accomplishments
                        </label>
                        <textarea class="form-control" 
                                  id="modal_remarks" 
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
// Keep your existing JavaScript for the update modal
let currentTaskData = null;

function openUpdateModal(taskId) {
    console.log('Opening update modal for task:', taskId);
    
    fetch(`index.php?action=get_task&id=${taskId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(task => {
            console.log('Task data received:', task);
            currentTaskData = task;
            
            document.getElementById('modal_task_id').value = task.id || task.task_id;
            document.getElementById('modal_task_details').textContent = task.task_details || 'No details';
            
            let division = task.functional_division || 'N/A';
            let badge = document.getElementById('modal_task_division');
            badge.textContent = division;
            badge.className = 'badge bg-' + (division == 'OSDS' ? 'primary' : (division == 'CID' ? 'success' : (division == 'SGOD' ? 'info' : 'secondary')));
            
            document.getElementById('modal_task_unit').textContent = task.unit_name || 'N/A';
            document.getElementById('modal_task_target_date').textContent = task.target_completion_date || 'N/A';
            
            let currentPercent = parseInt(task.current_percentage) || 0;
            document.getElementById('modal_current_progress').style.width = currentPercent + '%';
            document.getElementById('modal_current_progress').textContent = currentPercent + '%';
            
            document.getElementById('modal_percentage').value = currentPercent;
            document.getElementById('modal_percent_input').value = currentPercent;
            document.getElementById('modal_preview_bar').style.width = currentPercent + '%';
            
            document.getElementById('modal_remarks').value = '';
            
            let updateModal = new bootstrap.Modal(document.getElementById('updateTaskModal'));
            updateModal.show();
        })
        .catch(error => {
            console.error('Error fetching task:', error);
            alert('Error loading task data. Please check console for details.');
        });
}

function updateModalPercentValue(value) {
    document.getElementById('modal_percent_input').value = value;
    document.getElementById('modal_preview_bar').style.width = value + '%';
}

function updateModalPercentFromInput(value) {
    if(value < 0) value = 0;
    if(value > 100) value = 100;
    document.getElementById('modal_percentage').value = value;
    document.getElementById('modal_preview_bar').style.width = value + '%';
}

function setModalQuickPercent(value) {
    document.getElementById('modal_percentage').value = value;
    document.getElementById('modal_percent_input').value = value;
    document.getElementById('modal_preview_bar').style.width = value + '%';
}

function setModalDeferred() {
    setModalQuickPercent(0);
    let remarksField = document.getElementById('modal_remarks');
    if (remarksField.value.trim() === '') {
        remarksField.value = 'Task deferred/postponed';
    }
}

document.getElementById('updateTaskForm')?.addEventListener('submit', function(e) {
    let remarks = document.getElementById('modal_remarks').value.trim();
    if (!remarks) {
        e.preventDefault();
        alert('Please enter remarks for this update.');
        return false;
    }
    
    let percentage = document.getElementById('modal_percentage').value;
    console.log('Submitting update:', {
        task_id: document.getElementById('modal_task_id').value,
        percentage: percentage,
        remarks: remarks
    });
});

console.log('Tasks table script loaded');
</script>

<?php endif; ?>