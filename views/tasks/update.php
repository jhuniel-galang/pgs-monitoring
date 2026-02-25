<?php require_once 'views/layout/header.php'; ?>



    <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php 
        echo $_SESSION['error']; 
        unset($_SESSION['error']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-edit me-1"></i>
                    Update Progress for Task #<?php echo $task['task_id']; ?>
                </div>
                <div class="card-body">
                    <!-- Task Summary -->
                    <div class="card mb-4 bg-light">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($task['task_details']); ?></h5>
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <small class="text-muted">Division:</small><br>
                                    <span class="badge bg-<?php 
                                        echo $task['functional_division'] == 'OSDS' ? 'primary' : 
                                            ($task['functional_division'] == 'CID' ? 'success' : 'info'); 
                                    ?> fs-6">
                                        <?php echo $task['functional_division']; ?>
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Unit:</small><br>
                                    <strong><?php echo htmlspecialchars($task['unit_names'] ?? 'N/A'); ?></strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Target Date:</small><br>
                                    <strong><?php echo htmlspecialchars($task['target_completion_date'] ?? 'N/A'); ?></strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Priority:</small><br>
                                    <span class="badge bg-<?php 
                                        echo $task['priority'] == 'critical' ? 'danger' : 
                                            ($task['priority'] == 'high' ? 'warning' : 
                                            ($task['priority'] == 'medium' ? 'info' : 'secondary')); 
                                    ?> fs-6">
                                        <?php echo ucfirst($task['priority'] ?? 'N/A'); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Form -->
                    <form action="index.php?action=update_status" method="POST">
                        <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">

                        <!-- Current Progress -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Current Progress</label>
                            <div class="progress" style="height: 30px;">
                                <?php $currentPercentage = $task['current_percentage'] ?? 0; ?>
                                <div class="progress-bar bg-info progress-bar-striped" 
                                     id="currentProgressBar"
                                     style="width: <?php echo $currentPercentage; ?>%">
                                    <?php echo $currentPercentage; ?>%
                                </div>
                            </div>
                        </div>

                        <!-- New Progress Slider -->
                        <div class="mb-4">
                            <label for="percentage" class="form-label fw-bold">
                                New Progress Percentage <span class="text-danger">*</span>
                            </label>
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="range" class="form-range" 
                                           id="percentage" 
                                           name="percentage"
                                           min="0" max="100" step="5" 
                                           value="<?php echo $currentPercentage; ?>"
                                           oninput="updatePercentValue(this.value)">
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="number" class="form-control" 
                                               id="percentInput"
                                               min="0" max="100" step="5"
                                               value="<?php echo $currentPercentage; ?>"
                                               onchange="updatePercentFromInput(this.value)">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 10px;">
                                <div class="progress-bar bg-success" 
                                     id="previewBar"
                                     style="width: <?php echo $currentPercentage; ?>%">
                                </div>
                            </div>
                        </div>

                        <!-- Quick Percentage Buttons -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Quick Set:</label>
                            <div class="btn-group flex-wrap" role="group">
                                <button type="button" class="btn btn-outline-warning btn-sm" 
                                        onclick="setDeferred()">
                                    <i class="bi bi-hourglass-split"></i> Deferred/Postponed
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="setQuickPercent(0)">0%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="setQuickPercent(25)">25%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="setQuickPercent(50)">50%</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="setQuickPercent(75)">75%</button>
                                <button type="button" class="btn btn-outline-success btn-sm" 
                                        onclick="setQuickPercent(100)">100%</button>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-4">
                            <label for="remarks" class="form-label fw-bold">
                                Remarks / Accomplishments <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="remarks" 
                                      name="remarks" 
                                      rows="4" 
                                      placeholder="Describe what was accomplished or any issues encountered..." 
                                      required></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Update Progress</button>
                            <a href="index.php?action=view_task&id=<?php echo $task['task_id']; ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Status History Sidebar -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Update History
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <?php if(isset($status_history) && !empty($status_history)): ?>
                        <?php foreach($status_history as $history): ?>
                        <div class="border-start border-4 border-<?php 
                            echo $history['percentage'] >= 100 ? 'success' : 
                                ($history['percentage'] >= 50 ? 'info' : 'warning'); 
                        ?> ps-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong><?php echo $history['percentage']; ?>% Complete</strong>
                                <small class="text-muted"><?php echo date('M d, Y', strtotime($history['update_date'])); ?></small>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars($history['remarks']); ?></p>
                            <small class="text-muted">Updated by: <?php echo htmlspecialchars($history['updated_by_name'] ?? 'Unknown'); ?></small>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No updates yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updatePercentValue(value) {
    document.getElementById('percentInput').value = value;
    document.getElementById('previewBar').style.width = value + '%';
}

function updatePercentFromInput(value) {
    if(value < 0) value = 0;
    if(value > 100) value = 100;
    document.getElementById('percentage').value = value;
    document.getElementById('previewBar').style.width = value + '%';
}

function setQuickPercent(value) {
    document.getElementById('percentage').value = value;
    document.getElementById('percentInput').value = value;
    document.getElementById('previewBar').style.width = value + '%';
}

function setDeferred() {
    setQuickPercent(0);
    let remarksField = document.getElementById('remarks');
    if (remarksField.value.trim() === '') {
        remarksField.value = 'Task deferred/postponed';
    }
}
</script>

<?php require_once 'views/layout/footer.php'; ?>