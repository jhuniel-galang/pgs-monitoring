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
                <th>Budget</th>
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
            ?>
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
                <td>
                    <?php 
                    if(isset($task['unit_names']) && $task['unit_names']) {
                        $units = explode(', ', $task['unit_names']);
                        foreach($units as $unit) {
                            echo '<span class="badge bg-info me-1">' . htmlspecialchars($unit) . '</span>';
                        }
                    } elseif(isset($task['unit_name']) && $task['unit_name']) {
                        echo '<span class="badge bg-info">' . htmlspecialchars($task['unit_name']) . '</span>';
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($task['target_completion_date'] ?? 'N/A'); ?></td>
                <td>₱ <?php echo number_format($task['budget_allocation'] ?? 0, 2); ?></td>
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
                        <a href="index.php?action=view_task&id=<?php echo $taskId; ?>" 
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
                        <!-- Use a single update button that opens a modal with AJAX -->
                        <button type="button" class="btn btn-sm btn-success" 
                                onclick="openUpdateModal(<?php echo $taskId; ?>)">
                            Update
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Single Update Modal (instead of multiple modals) -->
<div class="modal fade" id="updateTaskModal" tabindex="-1" aria-labelledby="updateTaskModalLabel" aria-hidden="true">
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
// Global variable to store current task data
let currentTaskData = null;

// Function to open update modal with task data
function openUpdateModal(taskId) {
    console.log('Opening update modal for task:', taskId);
    
    // Fetch task data via AJAX
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
            
            // Set form values
            document.getElementById('modal_task_id').value = task.id || task.task_id;
            document.getElementById('modal_task_details').textContent = task.task_details || 'No details';
            
            // Set division badge
            let division = task.functional_division || 'N/A';
            let badge = document.getElementById('modal_task_division');
            badge.textContent = division;
            badge.className = 'badge bg-' + (division == 'OSDS' ? 'primary' : (division == 'CID' ? 'success' : 'info'));
            
            document.getElementById('modal_task_unit').textContent = task.unit_name || 'N/A';
            document.getElementById('modal_task_target_date').textContent = task.target_completion_date || 'N/A';
            
            // Set progress
            let currentPercent = parseInt(task.current_percentage) || 0;
            document.getElementById('modal_current_progress').style.width = currentPercent + '%';
            document.getElementById('modal_current_progress').textContent = currentPercent + '%';
            
            // Set slider and input
            document.getElementById('modal_percentage').value = currentPercent;
            document.getElementById('modal_percent_input').value = currentPercent;
            document.getElementById('modal_preview_bar').style.width = currentPercent + '%';
            
            // Clear remarks
            document.getElementById('modal_remarks').value = '';
            
            // Show the modal
            let updateModal = new bootstrap.Modal(document.getElementById('updateTaskModal'));
            updateModal.show();
        })
        .catch(error => {
            console.error('Error fetching task:', error);
            alert('Error loading task data. Please check console for details.');
        });
}

// Modal helper functions
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

// Form submission handler
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

// Debug: Check if the script loaded
console.log('Tasks table script loaded');
</script>

<?php endif; ?>