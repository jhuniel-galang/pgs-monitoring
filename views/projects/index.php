<?php require_once 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Core Area</h2>
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
    <?php if($_SESSION['role'] == 'admin'): ?>
        <?php foreach($project_summary as $summary): ?>
        <div class="col-md-3 mb-3">
            <?php 
            $division = $summary['functional_division'];
            $bgColor = 'secondary'; // default
            
            if($division == 'OSDS') {
                $bgColor = 'primary';
            } elseif($division == 'CID') {
                $bgColor = 'success';
            } elseif($division == 'SGOD') {
                $bgColor = 'info';
            } elseif($division == 'Schools') {
                $bgColor = 'secondary';
            }
            ?>
            <div class="card text-white bg-<?php echo $bgColor; ?>">
                <div class="card-header"><?php echo $division; ?> Division</div>
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
    <?php else: ?>
        <!-- For encoder: Show only their division summary -->
        <div class="col-md-12">
            <?php 
            $division = $_SESSION['functional_division'];
            $bgColor = 'secondary';
            
            if($division == 'OSDS') {
                $bgColor = 'primary';
            } elseif($division == 'CID') {
                $bgColor = 'success';
            } elseif($division == 'SGOD') {
                $bgColor = 'info';
            } elseif($division == 'Schools') {
                $bgColor = 'secondary';
            }
            ?>
            <div class="card text-white bg-<?php echo $bgColor; ?>">
                <div class="card-header"><?php echo $division; ?> Division - Your Projects</div>
                <div class="card-body">
                    <h5 class="card-title">Total Projects: <?php echo $project_summary[0]['total_projects'] ?? 0; ?></h5>
                    <p class="card-text">
                        Ongoing: <?php echo $project_summary[0]['ongoing_projects'] ?? 0; ?> | 
                        Completed: <?php echo $project_summary[0]['completed_projects'] ?? 0; ?> | 
                        Planning: <?php echo $project_summary[0]['planning_projects'] ?? 0; ?>
                    </p>
                    <div class="progress bg-light" style="height: 5px;">
                        <div class="progress-bar bg-white" style="width: <?php echo $project_summary[0]['avg_progress'] ?? 0; ?>%"></div>
                    </div>
                    <small>Avg Progress: <?php echo round($project_summary[0]['avg_progress'] ?? 0, 1); ?>%</small>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Filter Projects</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="action" value="projects">
            
            <div class="col-md-<?php echo $_SESSION['role'] == 'admin' ? '4' : '6'; ?>">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search by project name..." 
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            
            <?php if($_SESSION['role'] == 'admin'): ?>
            <div class="col-md-3">
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
            
            <div class="col-md-3">
                <label for="priority" class="form-label">Priority</label>
                <select class="form-select" id="priority" name="priority">
                    <option value="">All Priorities</option>
                    <option value="low" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
                    <option value="critical" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'critical') ? 'selected' : ''; ?>>Critical</option>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
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
        <?php if($_SESSION['role'] == 'encoder'): ?>
        <small class="text-muted d-block">Showing only projects from <?php echo $_SESSION['functional_division']; ?> division</small>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Project Name</th>
                        <th>Division</th>
                        <th>Project Lead</th>
                        <th>Priority</th>
                        <th>Progress</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($projects)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No projects found matching your filters.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($projects as $project): ?>
                        <tr>
                            <td><strong><?php echo $project['project_id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($project['project_name']); ?></td>
                            <td>
                                <?php 
                                $division = $project['functional_division'] ?? '';
                                $badgeColor = 'secondary';
                                
                                if($division == 'OSDS') {
                                    $badgeColor = 'primary';
                                } elseif($division == 'CID') {
                                    $badgeColor = 'success';
                                } elseif($division == 'SGOD') {
                                    $badgeColor = 'info';
                                } elseif($division == 'Schools') {
                                    $badgeColor = 'secondary';
                                }
                                ?>
                                <span class="badge bg-<?php echo $badgeColor; ?>">
                                    <?php echo !empty($division) ? htmlspecialchars($division) : 'N/A'; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($project['project_lead'] ?? 'N/A'); ?></td>
                            <td>
                                <?php 
                                $priority = $project['priority'] ?? 'medium';
                                $priorityColor = 'secondary';
                                
                                if($priority == 'critical') {
                                    $priorityColor = 'danger';
                                } elseif($priority == 'high') {
                                    $priorityColor = 'warning';
                                } elseif($priority == 'medium') {
                                    $priorityColor = 'info';
                                }
                                ?>
                                <span class="badge bg-<?php echo $priorityColor; ?>">
                                    <?php echo ucfirst($priority); ?>
                                </span>
                            </td>
                            <td style="min-width: 120px;">
                                <div class="progress" style="height: 20px;">
                                    <?php $progress = $project['progress_percentage'] ?? 0; ?>
                                    <div class="progress-bar bg-<?php 
                                        echo $progress >= 100 ? 'success' : 
                                            ($progress >= 50 ? 'info' : 'warning'); 
                                    ?>" style="width: <?php echo $progress; ?>%">
                                        <?php echo $progress; ?>%
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
                    <a class="page-link" href="?action=projects&page=<?php echo ($page-1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>">
                        Previous
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?action=projects&page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
                
                <?php if($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?action=projects&page=<?php echo ($page+1); ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>&division=<?php echo urlencode($_GET['division'] ?? ''); ?>&priority=<?php echo urlencode($_GET['priority'] ?? ''); ?>">
                        Next
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Create Project Modal - Only for admin -->
<?php if($_SESSION['role'] == 'admin'): ?>
<div class="modal fade" id="createProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Create New Project</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=store_project" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="project_name" class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="project_name" name="project_name" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="project_description" class="form-label">Description</label>
                            <textarea class="form-control" id="project_description" name="project_description" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="functional_division" class="form-label">Lead Division <span class="text-danger">*</span></label>
                            <select class="form-select" id="functional_division" name="functional_division" required>
                                <option value="">Select Lead Division</option>
                                <option value="OSDS">OSDS</option>
                                <option value="CID">CID</option>
                                <option value="SGOD">SGOD</option>
                                <option value="Schools">Schools</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="project_lead" class="form-label">Project Lead</label>
                            <input type="text" class="form-control" id="project_lead" name="project_lead">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
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
<?php endif; ?>

<!-- Edit Project Modals - Only for admin -->
<?php if($_SESSION['role'] == 'admin'): ?>
    <?php foreach($projects as $project): ?>
    <div class="modal fade" id="editProjectModal<?php echo $project['project_id']; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
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
                                <label for="edit_project_name_<?php echo $project['project_id']; ?>" class="form-label">Project Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_project_name_<?php echo $project['project_id']; ?>" 
                                       name="project_name" value="<?php echo htmlspecialchars($project['project_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="edit_project_description_<?php echo $project['project_id']; ?>" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_project_description_<?php echo $project['project_id']; ?>" 
                                          name="project_description" rows="3"><?php echo htmlspecialchars($project['project_description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="edit_division_<?php echo $project['project_id']; ?>" class="form-label">Division <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_division_<?php echo $project['project_id']; ?>" 
                                        name="functional_division" required>
                                    <option value="OSDS" <?php echo $project['functional_division'] == 'OSDS' ? 'selected' : ''; ?>>OSDS</option>
                                    <option value="CID" <?php echo $project['functional_division'] == 'CID' ? 'selected' : ''; ?>>CID</option>
                                    <option value="SGOD" <?php echo $project['functional_division'] == 'SGOD' ? 'selected' : ''; ?>>SGOD</option>
                                    <option value="Schools" <?php echo $project['functional_division'] == 'Schools' ? 'selected' : ''; ?>>Schools</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="edit_project_lead_<?php echo $project['project_id']; ?>" class="form-label">Project Lead</label>
                                <input type="text" class="form-control" id="edit_project_lead_<?php echo $project['project_id']; ?>" 
                                       name="project_lead" value="<?php echo htmlspecialchars($project['project_lead'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="edit_priority_<?php echo $project['project_id']; ?>" class="form-label">Priority</label>
                                <select class="form-select" id="edit_priority_<?php echo $project['project_id']; ?>" name="priority">
                                    <option value="low" <?php echo $project['priority'] == 'low' ? 'selected' : ''; ?>>Low</option>
                                    <option value="medium" <?php echo $project['priority'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="high" <?php echo $project['priority'] == 'high' ? 'selected' : ''; ?>>High</option>
                                    <option value="critical" <?php echo $project['priority'] == 'critical' ? 'selected' : ''; ?>>Critical</option>
                                </select>
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
<?php endif; ?>

<?php require_once 'views/layout/footer.php'; ?>