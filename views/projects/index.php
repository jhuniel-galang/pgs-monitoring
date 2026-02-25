<?php require_once 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Project Management</h2>
    </div>
    <?php if($_SESSION['role'] == 'admin'): ?>
    <div class="col-md-4 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
            <i class="bi bi-plus-circle"></i> Create New Project
        </button>
    </div>
    <?php endif; ?>
</div>

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

<!-- Project Summary Cards -->
<div class="row mb-4">
    <?php foreach($project_summary as $summary): ?>
    <div class="col-md-4">
        <div class="card text-white bg-<?php 
            echo $summary['functional_division'] == 'OSDS' ? 'primary' : 
                ($summary['functional_division'] == 'CID' ? 'success' : 
                ($summary['functional_division'] == 'SGOD' ? 'info' : 'secondary')); 
        ?>">
            <div class="card-header"><?php echo $summary['functional_division']; ?> Division</div>
            <div class="card-body">
                <h5 class="card-title">Total Projects: <?php echo $summary['total_projects']; ?></h5>
                <p class="card-text">
                    Ongoing: <?php echo $summary['ongoing_projects']; ?> | 
                    Completed: <?php echo $summary['completed_projects']; ?> | 
                    Planning: <?php echo $summary['planning_projects']; ?>
                </p>
                <div class="progress bg-light" style="height: 5px;">
                    <div class="progress-bar bg-white" style="width: <?php echo $summary['avg_progress'] ?? 0; ?>%"></div>
                </div>
                <small>Avg Progress: <?php echo round($summary['avg_progress'] ?? 0, 1); ?>%</small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Filter Projects</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="action" value="projects">
            
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search by name or code..." 
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            
            <?php if($_SESSION['role'] == 'admin'): ?>
            <div class="col-md-2">
                <label for="division" class="form-label">Division</label>
                <select class="form-select" id="division" name="division">
                    <option value="">All Divisions</option>
                    <option value="OSDS" <?php echo (isset($_GET['division']) && $_GET['division'] == 'OSDS') ? 'selected' : ''; ?>>OSDS</option>
                    <option value="CID" <?php echo (isset($_GET['division']) && $_GET['division'] == 'CID') ? 'selected' : ''; ?>>CID</option>
                    <option value="SGOD" <?php echo (isset($_GET['division']) && $_GET['division'] == 'SGOD') ? 'selected' : ''; ?>>SGOD</option>
                    <option value="Schools" <?php echo (isset($_GET['division']) && $_GET['division'] == 'Schools') ? 'selected' : ''; ?>>Schools</option>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="planning" <?php echo (isset($_GET['status']) && $_GET['status'] == 'planning') ? 'selected' : ''; ?>>Planning</option>
                    <option value="ongoing" <?php echo (isset($_GET['status']) && $_GET['status'] == 'ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="on_hold" <?php echo (isset($_GET['status']) && $_GET['status'] == 'on_hold') ? 'selected' : ''; ?>>On Hold</option>
                    <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="priority" class="form-label">Priority</label>
                <select class="form-select" id="priority" name="priority">
                    <option value="">All Priorities</option>
                    <option value="low" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
                    <option value="critical" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'critical') ? 'selected' : ''; ?>>Critical</option>
                </select>
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                <a href="index.php?action=projects" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Projects Table -->
<div class="card">
    <div class="card-header">
        <h5>Project List (<?php echo $total_projects; ?> projects found)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Project Name</th>
                        <th>Division</th>
                        <th>Project Lead</th>
                        <th>Target Completion</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($projects)): ?>
                    <tr>
                        <td colspan="9" class="text-center">No projects found matching your filters.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($projects as $project): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($project['project_code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($project['project_name']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $project['functional_division'] == 'OSDS' ? 'primary' : 
                                        ($project['functional_division'] == 'CID' ? 'success' : 
                                        ($project['functional_division'] == 'SGOD' ? 'info' : 'secondary')); 
                                ?>">
                                    <?php echo $project['functional_division']; ?>
                                </span>
                                    <?php echo $project['functional_division']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($project['project_lead'] ?? 'N/A'); ?></td>
                            <td>
    <?php 
    echo $project['target_end_date'] ? htmlspecialchars($project['target_end_date']) : 'N/A';
    ?>
</td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $project['priority'] == 'critical' ? 'danger' : 
                                        ($project['priority'] == 'high' ? 'warning' : 
                                        ($project['priority'] == 'medium' ? 'info' : 'secondary')); 
                                ?>">
                                    <?php echo ucfirst($project['priority']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $project['status'] == 'completed' ? 'success' : 
                                        ($project['status'] == 'ongoing' ? 'primary' : 
                                        ($project['status'] == 'planning' ? 'info' : 
                                        ($project['status'] == 'on_hold' ? 'warning' : 'secondary'))); 
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?>
                                </span>
                            </td>
                            <td style="min-width: 120px;">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-<?php 
                                        echo $project['progress_percentage'] >= 100 ? 'success' : 
                                            ($project['progress_percentage'] >= 50 ? 'info' : 'warning'); 
                                    ?>" style="width: <?php echo $project['progress_percentage']; ?>%">
                                        <?php echo $project['progress_percentage']; ?>%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    Tasks: <?php echo $project['completed_tasks'] ?? 0; ?>/<?php echo $project['total_tasks'] ?? 0; ?>
                                </small>
                            </td>
                            <td>
    <div class="btn-group" role="group">
        <a href="index.php?action=view_project&id=<?php echo $project['project_id']; ?>" class="btn btn-sm btn-info">
            View
        </a>
        
        <?php if($_SESSION['role'] == 'admin'): ?>
        <button type="button" class="btn btn-sm btn-warning" 
                data-bs-toggle="modal" 
                data-bs-target="#editProjectModal<?php echo $project['project_id']; ?>">
            Edit
        </button>
        <button type="button" class="btn btn-sm btn-danger" 
                data-bs-toggle="modal" 
                data-bs-target="#deleteProjectModal<?php echo $project['project_id']; ?>">
            Delete
        </button>
        <?php endif; ?>
    </div>
</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav aria-label="Project pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?action=projects&page=<?php echo ($page-1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>">
                        Previous
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?action=projects&page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
                
                <?php if($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?action=projects&page=<?php echo ($page+1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&status=<?php echo urlencode($_GET['status'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>">
                        Next
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Create Project Modal -->
<!-- Create Project Modal -->
<div class="modal fade" id="createProjectModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Create New Project</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=store_project" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="project_code" class="form-label">Project Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="project_code" name="project_code" 
                                   placeholder="e.g., OSDS-ADMIN-2024-01" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="project_name" class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="project_name" name="project_name" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="project_description" class="form-label">Description</label>
                            <textarea class="form-control" id="project_description" name="project_description" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="functional_division" class="form-label">Lead Division <span class="text-danger">*</span></label>
                            <select class="form-select" id="functional_division" name="functional_division" required>
                                <option value="">Select Lead Division</option>
                                <option value="OSDS">OSDS</option>
                                <option value="CID">CID</option>
                                <option value="SGOD">SGOD</option>
                                <option value="Schools">Schools</option>
                            </select>
                            <small class="text-muted">This is the main division overseeing the project</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="project_lead" class="form-label">Project Lead</label>
                            <input type="text" class="form-control" id="project_lead" name="project_lead">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="lead_designation" class="form-label">Lead Designation</label>
                            <input type="text" class="form-control" id="lead_designation" name="lead_designation">
                        </div>
                        
                        
                        
                        <div class="col-md-3 mb-3">
    <label for="target_end_date" class="form-label">Target End Date</label>
    <input type="text" class="form-control" id="target_end_date" name="target_end_date" 
           placeholder="e.g., December 2025, Quarterly, Annually, etc.">
    <small class="text-muted">You can enter a specific date or a timeframe like "Quarterly 2025"</small>
</div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="budget_allocation" class="form-label">Budget Allocation</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="budget_allocation" name="budget_allocation" step="0.01">
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="planning">Planning</option>
                                <option value="ongoing" selected>Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="on_hold">On Hold</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Participating Units <span class="text-danger">*</span></label>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                You can select units from any division to participate in this project.
                            </div>
                            
                            



                        <div class="card">
    <div class="card-header bg-light" id="create-unit-header">
        <!-- Filter will be added by JavaScript -->
    </div>
    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
        <div class="row" id="units-container-create">
            <!-- Units will be loaded here via JavaScript -->
        </div>
    </div>
    <div class="card-footer text-muted">
        <span id="selected-count-create">0</span> unit(s) selected
    </div>
</div>



                            <small class="text-muted">Select all units that will participate in this project</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Project Modals -->
<?php foreach($projects as $project): ?>
<div class="modal fade" id="editProjectModal<?php echo $project['project_id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Project: <?php echo htmlspecialchars($project['project_name']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=update_project" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="project_id" value="<?php echo $project['project_id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_project_code_<?php echo $project['project_id']; ?>" class="form-label">Project Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_project_code_<?php echo $project['project_id']; ?>" 
                                   name="project_code" value="<?php echo htmlspecialchars($project['project_code']); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_project_name_<?php echo $project['project_id']; ?>" class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_project_name_<?php echo $project['project_id']; ?>" 
                                   name="project_name" value="<?php echo htmlspecialchars($project['project_name']); ?>" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="edit_project_description_<?php echo $project['project_id']; ?>" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_project_description_<?php echo $project['project_id']; ?>" 
                                      name="project_description" rows="3"><?php echo htmlspecialchars($project['project_description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="edit_division_<?php echo $project['project_id']; ?>" class="form-label">Division <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_division_<?php echo $project['project_id']; ?>" 
                                    name="functional_division" required onchange="displayEditUnitCheckboxes(<?php echo $project['project_id']; ?>, '<?php echo $project['unit_ids'] ?? ''; ?>')">
                                <option value="OSDS" <?php echo $project['functional_division'] == 'OSDS' ? 'selected' : ''; ?>>OSDS</option>
                                <option value="CID" <?php echo $project['functional_division'] == 'CID' ? 'selected' : ''; ?>>CID</option>
                                <option value="SGOD" <?php echo $project['functional_division'] == 'SGOD' ? 'selected' : ''; ?>>SGOD</option>
                                <option value="Schools" <?php echo $project['functional_division'] == 'Schools' ? 'selected' : ''; ?>>Schools</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="edit_project_lead_<?php echo $project['project_id']; ?>" class="form-label">Project Lead</label>
                            <input type="text" class="form-control" id="edit_project_lead_<?php echo $project['project_id']; ?>" 
                                   name="project_lead" value="<?php echo htmlspecialchars($project['project_lead'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="edit_lead_designation_<?php echo $project['project_id']; ?>" class="form-label">Lead Designation</label>
                            <input type="text" class="form-control" id="edit_lead_designation_<?php echo $project['project_id']; ?>" 
                                   name="lead_designation" value="<?php echo htmlspecialchars($project['lead_designation'] ?? ''); ?>">
                        </div>
                        
                        
                        
                        <div class="col-md-3 mb-3">
    <label for="edit_target_end_date_<?php echo $project['project_id']; ?>" class="form-label">Target End Date</label>
    <input type="text" class="form-control" id="edit_target_end_date_<?php echo $project['project_id']; ?>" 
           name="target_end_date" value="<?php echo htmlspecialchars($project['target_end_date'] ?? ''); ?>"
           placeholder="e.g., December 2025, Quarterly, Annually, etc.">
    <small class="text-muted">You can enter a specific date or a timeframe</small>
</div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="edit_budget_allocation_<?php echo $project['project_id']; ?>" class="form-label">Budget Allocation</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="edit_budget_allocation_<?php echo $project['project_id']; ?>" 
                                       name="budget_allocation" step="0.01" value="<?php echo $project['budget_allocation'] ?? 0; ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="edit_priority_<?php echo $project['project_id']; ?>" class="form-label">Priority</label>
                            <select class="form-select" id="edit_priority_<?php echo $project['project_id']; ?>" name="priority">
                                <option value="low" <?php echo $project['priority'] == 'low' ? 'selected' : ''; ?>>Low</option>
                                <option value="medium" <?php echo $project['priority'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="high" <?php echo $project['priority'] == 'high' ? 'selected' : ''; ?>>High</option>
                                <option value="critical" <?php echo $project['priority'] == 'critical' ? 'selected' : ''; ?>>Critical</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="edit_status_<?php echo $project['project_id']; ?>" class="form-label">Status</label>
                            <select class="form-select" id="edit_status_<?php echo $project['project_id']; ?>" name="status">
                                <option value="planning" <?php echo $project['status'] == 'planning' ? 'selected' : ''; ?>>Planning</option>
                                <option value="ongoing" <?php echo $project['status'] == 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                <option value="completed" <?php echo $project['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="on_hold" <?php echo $project['status'] == 'on_hold' ? 'selected' : ''; ?>>On Hold</option>
                                <option value="cancelled" <?php echo $project['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
    <label class="form-label fw-bold">Participating Units</label>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> 
        You can select units from any division to participate in this project.
    </div>
    
    <div class="card">
        <div class="card-header bg-light" id="edit-unit-header-<?php echo $project['project_id']; ?>">
            <!-- Filter will be added by JavaScript -->
        </div>
        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            <div class="row" id="edit_units_container_<?php echo $project['project_id']; ?>">
                <!-- Units will be loaded here via JavaScript -->
            </div>
        </div>
        <div class="card-footer text-muted">
            <span id="selected-count-edit_<?php echo $project['project_id']; ?>">0</span> unit(s) selected
        </div>
    </div>
</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Project</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Delete Project Modals -->
<?php foreach($projects as $project): ?>
<div class="modal fade" id="deleteProjectModal<?php echo $project['project_id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete project <strong><?php echo htmlspecialchars($project['project_name']); ?></strong>?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <form action="index.php?action=delete_project" method="POST">
                    <input type="hidden" name="project_id" value="<?php echo $project['project_id']; ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Project</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>





<script>


// Helper functions for colors
function getDivisionColor(division) {
    return division === 'OSDS' ? 'primary' : (division === 'CID' ? 'success' : 'info');
}

function getStatusColor(status) {
    switch(status) {
        case 'completed': return 'success';
        case 'ongoing': return 'primary';
        case 'planning': return 'info';
        case 'on_hold': return 'warning';
        default: return 'secondary';
    }
}

function getProgressColor(progress) {
    return progress >= 100 ? 'success' : (progress >= 50 ? 'info' : 'warning');
}

function formatStatus(status) {
    return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
}

// Display project details in modal
function displayProjectDetails(project) {
    var html = `
        <div class="row">
            <div class="col-md-6 mb-3">
                <h6 class="fw-bold">Project Code</h6>
                <p>${project.project_code}</p>
            </div>
            <div class="col-md-6 mb-3">
                <h6 class="fw-bold">Project Name</h6>
                <p>${project.project_name}</p>
            </div>
            <div class="col-md-12 mb-3">
                <h6 class="fw-bold">Description</h6>
                <p>${project.project_description || 'No description provided'}</p>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold">Division</h6>
                <p><span class="badge bg-${getDivisionColor(project.functional_division)}">${project.functional_division}</span></p>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold">Project Lead</h6>
                <p>${project.project_lead || 'Not assigned'}</p>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold">Status</h6>
                <p><span class="badge bg-${getStatusColor(project.status)}">${formatStatus(project.status)}</span></p>
            </div>
            <div class="col-md-6 mb-3">
    <h6 class="fw-bold">Target End Date</h6>
    <p>${project.target_end_date || 'Not set'}</p>
</div>
            <div class="col-md-6 mb-3">
                <h6 class="fw-bold">Target End Date</h6>
                <p>${project.target_end_date || 'Not set'}</p>
            </div>
            <div class="col-md-12 mb-3">
                <h6 class="fw-bold">Progress</h6>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-${getProgressColor(project.progress_percentage)}" 
                         style="width: ${project.progress_percentage}%">
                        ${project.progress_percentage}%
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('viewProjectModalBody').innerHTML = html;
}

// Helper functions for colors
function getDivisionColor(division) {
    return division === 'OSDS' ? 'primary' : (division === 'CID' ? 'success' : 'info');
}

function getStatusColor(status) {
    switch(status) {
        case 'completed': return 'success';
        case 'ongoing': return 'primary';
        case 'planning': return 'info';
        case 'on_hold': return 'warning';
        default: return 'secondary';
    }
}

function getProgressColor(progress) {
    return progress >= 100 ? 'success' : (progress >= 50 ? 'info' : 'warning');
}

function formatStatus(status) {
    return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
}
</script>





<script>
// Get all units data for JavaScript
var allUnits = <?php echo json_encode(array_map(function($unit) {
    return [
        'id' => $unit['id'],
        'name' => $unit['unit_name'],
        'division' => $unit['functional_division'],
        'pic' => $unit['person_in_charge'] ?? 'N/A'
    ];
}, $units)); ?>;

// Function to display all units as checkboxes (for create modal)
function displayAllUnits() {
    var container = document.getElementById('units-container-create');
    var header = document.getElementById('create-unit-header');
    
    if (!container) return;
    
    // Add filter controls to header if not already added
    if (header && header.children.length === 0) {
        var filterRow = document.createElement('div');
        filterRow.className = 'row';
        filterRow.innerHTML = `
            <div class="col-md-4">
                <select class="form-select form-select-sm" id="filter_division_create" onchange="filterUnitsByDivision('create')">
                    <option value="">All Divisions</option>
                    <option value="OSDS">OSDS</option>
                    <option value="CID">CID</option>
                    <option value="SGOD">SGOD</option>
                    <option value="Schools">Schools</option>
                </select>
            </div>
            <div class="col-md-8 text-end">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkAllUnits('create')">Check All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="uncheckAllUnits('create')">Uncheck All</button>
            </div>
        `;
        header.appendChild(filterRow);
    }
    
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
        container.appendChild(createDivisionSection('OSDS', 'primary', osdsUnits, 'create'));
    }
    
    if (cidUnits.length > 0) {
        container.appendChild(createDivisionSection('CID', 'success', cidUnits, 'create'));
    }
    
    if (sgodUnits.length > 0) {
        container.appendChild(createDivisionSection('SGOD', 'info', sgodUnits, 'create'));
    }

    if (schoolsUnits.length > 0) {
    container.appendChild(createDivisionSection('Schools', 'secondary', schoolsUnits, 'create'));
}
    
    updateSelectedCount('create');
}

// Helper function to create division section
function createDivisionSection(division, color, units, modal) {
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
        col.className = 'col-md-4 mb-2 unit-item-' + modal;
        col.setAttribute('data-division', unit.division);
        
        var div = document.createElement('div');
        div.className = 'form-check';
        
        var checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'form-check-input unit-checkbox-' + modal;
        checkbox.name = 'unit_ids[]';
        checkbox.value = unit.id;
        checkbox.id = 'unit_' + modal + '_' + unit.id;
        checkbox.addEventListener('change', function() {
            updateSelectedCount(modal);
        });
        
        var label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = 'unit_' + modal + '_' + unit.id;
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
function filterUnitsByDivision(modal) {
    var division = document.getElementById('filter_division_' + modal).value;
    var unitItems = document.getElementsByClassName('unit-item-' + modal);
    
    for(var i = 0; i < unitItems.length; i++) {
        if (division === '' || unitItems[i].getAttribute('data-division') === division) {
            unitItems[i].style.display = '';
        } else {
            unitItems[i].style.display = 'none';
        }
    }
}

// Check all units in modal
function checkAllUnits(modal) {
    var checkboxes = document.getElementsByClassName('unit-checkbox-' + modal);
    for(var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = true;
    }
    updateSelectedCount(modal);
}

// Uncheck all units in modal
function uncheckAllUnits(modal) {
    var checkboxes = document.getElementsByClassName('unit-checkbox-' + modal);
    for(var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = false;
    }
    updateSelectedCount(modal);
}

// Update selected count
function updateSelectedCount(modal) {
    var checkboxes = document.getElementsByClassName('unit-checkbox-' + modal);
    var count = 0;
    for(var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) count++;
    }
    var countElement = document.getElementById('selected-count-' + modal);
    if (countElement) {
        countElement.innerText = count;
    }
}

// Initialize create modal
document.getElementById('createProjectModal').addEventListener('show.bs.modal', function() {
    displayAllUnits();
});

// For edit modals
function displayEditUnits(modalId, selectedUnitIds) {
    var container = document.getElementById('edit_units_container_' + modalId);
    var header = document.getElementById('edit-unit-header-' + modalId);
    
    if (!container) return;
    
    // Add filter controls to header if not already added
    if (header && header.children.length === 0) {
        var filterRow = document.createElement('div');
        filterRow.className = 'row';
        filterRow.innerHTML = `
            <div class="col-md-4">
                <select class="form-select form-select-sm" id="filter_division_edit_${modalId}" onchange="filterEditUnitsByDivision(${modalId})">
                    <option value="">All Divisions</option>
                    <option value="OSDS">OSDS</option>
                    <option value="CID">CID</option>
                    <option value="SGOD">SGOD</option>
                    <option value="Schools">Schools</option>
                </select>
            </div>
            <div class="col-md-8 text-end">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkAllUnits('edit_${modalId}')">Check All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="uncheckAllUnits('edit_${modalId}')">Uncheck All</button>
            </div>
        `;
        header.appendChild(filterRow);
    }
    
    // Clear container
    container.innerHTML = '';
    
    // Convert selectedUnitIds to array
    var selectedArray = [];
    if (selectedUnitIds) {
        if (typeof selectedUnitIds === 'string') {
            selectedArray = selectedUnitIds.split(',').map(Number);
        } else if (Array.isArray(selectedUnitIds)) {
            selectedArray = selectedUnitIds;
        }
    }
    
    if (allUnits.length === 0) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-warning">No units available.</div></div>';
        return;
    }
    
    // Group units by division
    var osdsUnits = allUnits.filter(u => u.division === 'OSDS');
    var cidUnits = allUnits.filter(u => u.division === 'CID');
    var sgodUnits = allUnits.filter(u => u.division === 'SGOD');
    var schoolsUnits = allUnits.filter(u => u.division === 'Schools');
    
    // Create sections for each division with pre-selected units
    if (osdsUnits.length > 0) {
        container.appendChild(createEditDivisionSection(modalId, 'OSDS', 'primary', osdsUnits, selectedArray));
    }
    
    if (cidUnits.length > 0) {
        container.appendChild(createEditDivisionSection(modalId, 'CID', 'success', cidUnits, selectedArray));
    }
    
    if (sgodUnits.length > 0) {
        container.appendChild(createEditDivisionSection(modalId, 'SGOD', 'info', sgodUnits, selectedArray));
    }
    if (schoolsUnits.length > 0) {
    container.appendChild(createEditDivisionSection(modalId, 'Schools', 'secondary', schoolsUnits, selectedArray));
}
    
    updateSelectedCount('edit_' + modalId);
}

// Helper function for edit modal division section
function createEditDivisionSection(modalId, division, color, units, selectedArray) {
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
        col.className = 'col-md-4 mb-2 unit-item-edit_' + modalId;
        col.setAttribute('data-division', unit.division);
        
        var div = document.createElement('div');
        div.className = 'form-check';
        
        var checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'form-check-input unit-checkbox-edit_' + modalId;
        checkbox.name = 'unit_ids[]';
        checkbox.value = unit.id;
        checkbox.id = 'unit_edit_' + modalId + '_' + unit.id;
        
        // Check if this unit is already selected
        if (selectedArray.includes(unit.id)) {
            checkbox.checked = true;
        }
        
        checkbox.addEventListener('change', function() {
            updateSelectedCount('edit_' + modalId);
        });
        
        var label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = 'unit_edit_' + modalId + '_' + unit.id;
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

// Filter function for edit modal
function filterEditUnitsByDivision(modalId) {
    var division = document.getElementById('filter_division_edit_' + modalId).value;
    var unitItems = document.getElementsByClassName('unit-item-edit_' + modalId);
    
    for(var i = 0; i < unitItems.length; i++) {
        if (division === '' || unitItems[i].getAttribute('data-division') === division) {
            unitItems[i].style.display = '';
        } else {
            unitItems[i].style.display = 'none';
        }
    }
}

// Initialize edit modals
<?php foreach($projects as $project): ?>
document.getElementById('editProjectModal<?php echo $project['project_id']; ?>').addEventListener('show.bs.modal', function() {
    var modalId = <?php echo $project['project_id']; ?>;
    var selectedUnits = '<?php echo $project['unit_ids'] ?? ''; ?>';
    displayEditUnits(modalId, selectedUnits);
});
<?php endforeach; ?>
</script>

<?php require_once 'views/layout/footer.php'; ?>