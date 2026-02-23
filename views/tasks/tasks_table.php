<?php if(empty($filtered_tasks)): ?>
    <p class="text-muted">No tasks found.</p>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Task</th>
                <th>Division</th>
                <th>Unit</th>
                <th>Target Date</th>
                <th>Priority</th>
                <th>Progress</th>
                <th>Last Update</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($filtered_tasks as $task): ?>
            <tr>
                <td><?php echo htmlspecialchars(substr($task['task_details'] ?? '', 0, 50)) . '...'; ?></td>
                <td>
                    <?php if(isset($task['functional_division'])): ?>
                    <span class="badge bg-<?php 
                        echo $task['functional_division'] == 'OSDS' ? 'primary' : 
                            ($task['functional_division'] == 'CID' ? 'success' : 'info'); 
                    ?>">
                        <?php echo $task['functional_division']; ?>
                    </span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($task['unit_name'] ?? 'N/A'); ?></td>
                <td><?php echo isset($task['target_completion_date']) ? date('M d, Y', strtotime($task['target_completion_date'])) : 'N/A'; ?></td>
                <td>
                    <span class="badge bg-<?php 
                        echo ($task['priority'] ?? '') == 'critical' ? 'danger' : 
                            (($task['priority'] ?? '') == 'high' ? 'warning' : 
                            (($task['priority'] ?? '') == 'medium' ? 'info' : 'secondary')); 
                    ?>">
                        <?php echo ucfirst($task['priority'] ?? 'N/A'); ?>
                    </span>
                </td>
                <td style="min-width: 150px;">
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
                        <a href="index.php?action=view_task&id=<?php echo $task['task_id'] ?? 0; ?>" 
                           class="btn btn-sm btn-info">View</a>
                        
                        <?php 
                        // Check if user can update this task
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
                        <button type="button" class="btn btn-sm btn-success" 
                                data-bs-toggle="modal" 
                                data-bs-target="#updateModal<?php echo $task['task_id']; ?>">
                            Update
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>

            <!-- Update Modal for each task -->
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
                                                <small class="text-muted">Unit:</small><br>
                                                <strong><?php echo htmlspecialchars($task['unit_name'] ?? 'N/A'); ?></strong>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Target Date:</small><br>
                                                <strong><?php echo date('M d, Y', strtotime($task['target_completion_date'])); ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Current Progress Display -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Current Progress</label>
                                    <div class="progress" style="height: 25px;">
                                        <?php $current_percent = $task['current_percentage'] ?? 0; ?>
                                        <div class="progress-bar bg-info" 
                                             style="width: <?php echo $current_percent; ?>%">
                                            <?php echo $current_percent; ?>%
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Update Percentage Slider -->
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
                                                   oninput="updatePercentValue(<?php echo $task['task_id']; ?>, this.value)">
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="number" class="form-control" 
                                                       id="percentInput<?php echo $task['task_id']; ?>"
                                                       min="0" max="100" step="5"
                                                       value="<?php echo $current_percent; ?>"
                                                       onchange="updatePercentFromInput(<?php echo $task['task_id']; ?>, this.value)">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 10px;">
                                        <div class="progress-bar bg-success" 
                                             id="previewBar<?php echo $task['task_id']; ?>"
                                             style="width: <?php echo $current_percent; ?>%">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Quick Percentage Buttons -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Quick Set:</label>
                                    <div class="btn-group flex-wrap" role="group">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                onclick="setQuickPercent(<?php echo $task['task_id']; ?>, 0)">0%</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                onclick="setQuickPercent(<?php echo $task['task_id']; ?>, 25)">25%</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                onclick="setQuickPercent(<?php echo $task['task_id']; ?>, 50)">50%</button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                onclick="setQuickPercent(<?php echo $task['task_id']; ?>, 75)">75%</button>
                                        <button type="button" class="btn btn-outline-success btn-sm" 
                                                onclick="setQuickPercent(<?php echo $task['task_id']; ?>, 100)">100%</button>
                                    </div>
                                </div>
                                
                                <!-- Remarks -->
                                <div class="mb-3">
                                    <label for="remarks<?php echo $task['task_id']; ?>" class="form-label fw-bold">
                                        Remarks / Accomplishments
                                    </label>
                                    <textarea class="form-control" 
                                              id="remarks<?php echo $task['task_id']; ?>" 
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
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Functions for the modal
function updatePercentValue(taskId, value) {
    document.getElementById('percentInput' + taskId).value = value;
    document.getElementById('previewBar' + taskId).style.width = value + '%';
}

function updatePercentFromInput(taskId, value) {
    if(value < 0) value = 0;
    if(value > 100) value = 100;
    document.getElementById('percentage' + taskId).value = value;
    document.getElementById('previewBar' + taskId).style.width = value + '%';
}

function setQuickPercent(taskId, value) {
    document.getElementById('percentage' + taskId).value = value;
    document.getElementById('percentInput' + taskId).value = value;
    document.getElementById('previewBar' + taskId).style.width = value + '%';
}
</script>

<?php endif; ?>