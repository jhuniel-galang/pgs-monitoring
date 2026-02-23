<?php require_once 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2>PGS Monitoring Dashboard</h2>
        <p class="text-muted">
            Welcome back, <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>!
            <?php if($_SESSION['role'] == 'encoder'): ?>
                <span class="badge bg-info">Encoder - <?php echo $_SESSION['functional_division']; ?></span>
            <?php else: ?>
                <span class="badge bg-primary">Administrator</span>
            <?php endif; ?>
        </p>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <?php foreach($division_summary as $summary): ?>
    <?php if($summary && $summary['functional_division']): ?>
    <div class="col-md-<?php echo $_SESSION['role'] == 'admin' ? '4' : '12'; ?>">
        <div class="card">
            <div class="card-header bg-<?php 
                echo $summary['functional_division'] == 'OSDS' ? 'primary' : 
                    ($summary['functional_division'] == 'CID' ? 'success' : 'info'); 
            ?> text-white">
                <h5 class="mb-0"><?php echo $summary['functional_division']; ?> Division</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 text-center">
                        <h3><?php echo $summary['average_percentage']; ?>%</h3>
                        <small>Average Progress</small>
                    </div>
                    <div class="col-6 text-center">
                        <h3><?php echo $summary['completed_tasks']; ?>/<?php echo $summary['total_tasks']; ?></h3>
                        <small>Completed Tasks</small>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 25px;">
                    <div class="progress-bar bg-success" 
                         style="width: <?php echo $summary['average_percentage']; ?>%">
                        Overall: <?php echo $summary['average_percentage']; ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="index.php?action=tasks" class="btn btn-info me-2">
                    <i class="bi bi-list-task"></i> View All Tasks
                </a>
                <?php if($_SESSION['role'] == 'admin'): ?>
                <a href="index.php?action=create_task" class="btn btn-primary me-2">
                    <i class="bi bi-plus-circle"></i> Create New Task
                </a>
                <a href="index.php?action=units" class="btn btn-secondary">
                    <i class="bi bi-building"></i> Manage Units
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tasks -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Recent Tasks</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Division</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_tasks as $task): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(substr($task['task_details'], 0, 50)); ?>...</td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $task['functional_division'] == 'OSDS' ? 'primary' : 
                                            ($task['functional_division'] == 'CID' ? 'success' : 'info'); 
                                    ?>">
                                        <?php echo $task['functional_division']; ?>
                                    </span>
                                </td>
                                <td style="width: 200px;">
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $task['current_percentage'] ?? 0; ?>%">
                                            <?php echo $task['current_percentage'] ?? 0; ?>%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if(($task['current_percentage'] ?? 0) >= 100): ?>
                                        <span class="badge bg-success">Completed</span>
                                    <?php elseif(($task['current_percentage'] ?? 0) > 0): ?>
                                        <span class="badge bg-warning">In Progress</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Not Started</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?action=view_task&id=<?php echo $task['task_id']; ?>" 
                                       class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>