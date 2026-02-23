<?php require_once 'views/layout/header.php'; ?>

<?php if(isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php 
    echo $_SESSION['success']; 
    unset($_SESSION['success']);
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php 
    echo $_SESSION['error']; 
    unset($_SESSION['error']);
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Task Management</h2>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'encoder'): ?>
            <p class="text-muted">Viewing tasks for: <strong><?php echo $_SESSION['functional_division'] ?? 'N/A'; ?></strong></p>
        <?php endif; ?>
    </div>
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
    <div class="col-md-4 text-end">
        <a href="index.php?action=create_task" class="btn btn-primary">Create New Task</a>
    </div>
    <?php endif; ?>
</div>

<?php if(isset($_GET['msg'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($_GET['msg']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($_GET['error']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Division Summary Cards -->
<div class="row mb-4">
    <?php 
    // Ensure division_summary is an array and not empty
    if(isset($division_summary) && is_array($division_summary) && !empty($division_summary)): 
        foreach($division_summary as $summary): 
            // Skip if summary is not an array or doesn't have functional_division
            if(!is_array($summary) || !isset($summary['functional_division'])) continue;
    ?>
    <div class="col-md-4">
        <div class="card text-white bg-<?php 
            echo $summary['functional_division'] == 'OSDS' ? 'primary' : 
                ($summary['functional_division'] == 'CID' ? 'success' : 'info'); 
        ?> mb-3">
            <div class="card-header"><?php echo $summary['functional_division']; ?></div>
            <div class="card-body">
                <h5 class="card-title">Average Progress: <?php echo $summary['average_percentage'] ?? 0; ?>%</h5>
                <p class="card-text">
                    Total Tasks: <?php echo $summary['total_tasks'] ?? 0; ?><br>
                    Completed: <?php echo $summary['completed_tasks'] ?? 0; ?>
                </p>
            </div>
        </div>
    </div>
    <?php 
        endforeach; 
    else: 
    ?>
    <div class="col-12">
        <div class="alert alert-info">
            No division summary data available.
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Tasks Table -->
<div class="card">
    <div class="card-header">
        <h5>Task List</h5>
    </div>
    <div class="card-body">
        <?php 
        // Check if tasks is set and is an array
        if(!isset($tasks) || !is_array($tasks)) {
            $tasks = [];
        }
        
        // For encoders, just show all tasks (they're already filtered)
        if(isset($_SESSION['role']) && $_SESSION['role'] == 'encoder'): 
            $filtered_tasks = $tasks;
            include 'tasks_table.php';
        else: 
            // For admin, show tabs
        ?>
        <ul class="nav nav-tabs card-header-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#all">All Tasks</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#osds">OSDS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#cid">CID</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#sgod">SGOD</a>
            </li>
        </ul>
        
        <div class="tab-content">
            <div class="tab-pane active" id="all">
                <?php 
                $filtered_tasks = $tasks;
                include 'tasks_table.php'; 
                ?>
            </div>
            <div class="tab-pane" id="osds">
                <?php 
                $filtered_tasks = array_filter($tasks, function($t) { 
                    return is_array($t) && isset($t['functional_division']) && $t['functional_division'] == 'OSDS'; 
                });
                include 'tasks_table.php'; 
                ?>
            </div>
            <div class="tab-pane" id="cid">
                <?php 
                $filtered_tasks = array_filter($tasks, function($t) { 
                    return is_array($t) && isset($t['functional_division']) && $t['functional_division'] == 'CID'; 
                });
                include 'tasks_table.php'; 
                ?>
            </div>
            <div class="tab-pane" id="sgod">
                <?php 
                $filtered_tasks = array_filter($tasks, function($t) { 
                    return is_array($t) && isset($t['functional_division']) && $t['functional_division'] == 'SGOD'; 
                });
                include 'tasks_table.php'; 
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>