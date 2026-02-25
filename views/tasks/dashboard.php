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

<!-- Division Summary Cards (Quick overview) -->
<div class="row mb-4">
    <?php foreach($division_summary as $summary): ?>
    <?php if($summary && $summary['functional_division']): ?>
    <div class="col-md-<?php echo $_SESSION['role'] == 'admin' ? '3' : '12'; ?> mb-3">
        <div class="card h-100 bg-light">
            <div class="card-body text-center">
                <h5 class="card-title"><?php echo $summary['functional_division']; ?> Division</h5>
                <h2><?php echo $summary['total_projects']; ?></h2>
                <p class="text-muted">Total Projects</p>
                <div class="small">
                    <span class="badge bg-success"><?php echo $summary['completed_tasks'] ?? 0; ?> completed tasks</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Projects Carousel -->
<?php if(isset($projects) && !empty($projects)): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-grid-3x3-gap-fill"></i> Project Progress Overview</h5>
                <div>
                    <span class="badge bg-light text-dark me-2" id="carousel-status">Showing 1-2 of <?php echo count($projects); ?></span>
                    <button class="btn btn-sm btn-light" onclick="prevSlide()">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="btn btn-sm btn-light" onclick="nextSlide()">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="projectCarousel" class="project-carousel">
                    <?php 
                    // Group projects into slides of 2
                    $project_chunks = array_chunk($projects, 2);
                    foreach($project_chunks as $index => $chunk): 
                    ?>
                    <div class="carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>">
                        <div class="row">
                            <?php foreach($chunk as $project): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-<?php 
                                    echo $project['functional_division'] == 'OSDS' ? 'primary' : 
                                        ($project['functional_division'] == 'CID' ? 'success' : 
                                        ($project['functional_division'] == 'SGOD' ? 'info' : 'secondary')); 
                                ?>">
                                    <div class="card-header bg-<?php 
                                        echo $project['functional_division'] == 'OSDS' ? 'primary' : 
                                            ($project['functional_division'] == 'CID' ? 'success' : 
                                            ($project['functional_division'] == 'SGOD' ? 'info' : 'secondary')); 
                                    ?> text-white d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($project['project_name']); ?></h6>
                                        <span class="badge bg-light text-dark">ID: <?php echo $project['project_id']; ?></span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Division</small>
                                                <strong><?php echo $project['functional_division']; ?></strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Project Lead</small>
                                                <strong><?php echo htmlspecialchars($project['project_lead'] ?? 'N/A'); ?></strong>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Priority</small>
                                                <span class="badge bg-<?php 
                                                    echo $project['priority'] == 'critical' ? 'danger' : 
                                                        ($project['priority'] == 'high' ? 'warning' : 
                                                        ($project['priority'] == 'medium' ? 'info' : 'secondary')); 
                                                ?>">
                                                    <?php echo ucfirst($project['priority'] ?? 'medium'); ?>
                                                </span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Tasks</small>
                                                <strong><?php echo $project['completed_tasks'] ?? 0; ?>/<?php echo $project['total_tasks'] ?? 0; ?></strong>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span>Progress</span>
                                                <span class="fw-bold"><?php echo $project['progress_percentage']; ?>%</span>
                                            </div>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-<?php 
                                                    echo $project['progress_percentage'] >= 100 ? 'success' : 
                                                        ($project['progress_percentage'] >= 50 ? 'info' : 'warning'); 
                                                ?>" style="width: <?php echo $project['progress_percentage']; ?>%">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if(isset($project['latest_task'])): ?>
                                        <div class="mt-3 p-2 bg-light rounded">
                                            <small class="text-muted d-block">Latest Task:</small>
                                            <small><?php echo htmlspecialchars(substr($project['latest_task'], 0, 50)); ?>...</small>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="mt-3">
                                            <a href="index.php?action=view_project&id=<?php echo $project['project_id']; ?>" 
                                               class="btn btn-sm btn-outline-primary w-100">
                                                View Project Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <!-- If only one project in this slide, add an empty placeholder -->
                            <?php if(count($chunk) == 1): ?>
                            <div class="col-md-6 mb-3">
                                <!-- Empty placeholder for layout balance -->
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Carousel Indicators -->
                <?php if(count($project_chunks) > 1): ?>
                <div class="carousel-indicators mt-3">
                    <?php for($i = 0; $i < count($project_chunks); $i++): ?>
                    <button type="button" class="indicator <?php echo $i === 0 ? 'active' : ''; ?>" 
                            onclick="goToSlide(<?php echo $i; ?>)"></button>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No projects found. 
            <?php if($_SESSION['role'] == 'admin'): ?>
            <a href="index.php?action=projects" class="alert-link">Create your first project</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="index.php?action=projects" class="btn btn-info me-2">
                    <i class="bi bi-folder"></i> View All Projects
                </a>
                <a href="index.php?action=tasks" class="btn btn-primary me-2">
                    <i class="bi bi-list-task"></i> View All Tasks
                </a>
                <?php if($_SESSION['role'] == 'admin'): ?>
                <a href="index.php?action=create_task_page" class="btn btn-success me-2">
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
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Project</th>
                                <th>Division</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($recent_tasks)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No recent tasks found.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($recent_tasks as $task): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(substr($task['task_details'], 0, 50)); ?>...</td>
                                    <td>
                                        <?php 
                                        if(isset($task['project_name'])) {
                                            echo htmlspecialchars(substr($task['project_name'], 0, 30));
                                        } else {
                                            echo '<span class="text-muted">No Project</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $task['functional_division'] == 'OSDS' ? 'primary' : 
                                                ($task['functional_division'] == 'CID' ? 'success' : 
                                                ($task['functional_division'] == 'SGOD' ? 'info' : 'secondary')); 
                                        ?>">
                                            <?php echo $task['functional_division']; ?>
                                        </span>
                                    </td>
                                    <td style="width: 200px;">
                                        <?php $percentage = $task['current_percentage'] ?? 0; ?>
                                        <div class="progress">
                                            <div class="progress-bar bg-<?php 
                                                echo $percentage >= 100 ? 'success' : 
                                                    ($percentage >= 50 ? 'info' : 'warning'); 
                                            ?>" style="width: <?php echo $percentage; ?>%">
                                                <?php echo $percentage; ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($percentage >= 100): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php elseif($percentage > 0): ?>
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
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.project-carousel {
    position: relative;
    min-height: 300px;
}

.carousel-slide {
    display: none;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.carousel-slide.active {
    display: block;
    opacity: 1;
}

.carousel-indicators {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.carousel-indicators .indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: none;
    background-color: #ccc;
    cursor: pointer;
    padding: 0;
    transition: background-color 0.3s;
}

.carousel-indicators .indicator.active {
    background-color: #007bff;
    transform: scale(1.2);
}

.carousel-indicators .indicator:hover {
    background-color: #999;
}
</style>

<script>
let currentSlide = 0;
let totalSlides = <?php echo isset($project_chunks) ? count($project_chunks) : 0; ?>;
let autoPlayInterval;

function showSlide(index) {
    if (totalSlides === 0) return;
    
    // Hide all slides
    document.querySelectorAll('.carousel-slide').forEach(slide => {
        slide.classList.remove('active');
    });
    
    // Show selected slide
    document.querySelector(`.carousel-slide[data-slide="${index}"]`).classList.add('active');
    
    // Update indicators
    document.querySelectorAll('.indicator').forEach((indicator, i) => {
        if (i === index) {
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active');
        }
    });
    
    // Update status text
    const start = index * 2 + 1;
    const end = Math.min((index + 1) * 2, <?php echo count($projects ?? []); ?>);
    document.getElementById('carousel-status').textContent = `Showing ${start}-${end} of <?php echo count($projects ?? []); ?>`;
    
    currentSlide = index;
}

function nextSlide() {
    if (totalSlides === 0) return;
    let next = (currentSlide + 1) % totalSlides;
    showSlide(next);
    resetAutoPlay();
}

function prevSlide() {
    if (totalSlides === 0) return;
    let prev = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(prev);
    resetAutoPlay();
}

function goToSlide(index) {
    if (totalSlides === 0) return;
    showSlide(index);
    resetAutoPlay();
}

function startAutoPlay() {
    if (totalSlides <= 1) return;
    autoPlayInterval = setInterval(() => {
        nextSlide();
    }, 30000); // 30 seconds
}

function resetAutoPlay() {
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
    }
    startAutoPlay();
}

// Start autoplay when page loads
document.addEventListener('DOMContentLoaded', function() {
    startAutoPlay();
    
    // Pause autoplay when user hovers over carousel
    const carousel = document.getElementById('projectCarousel');
    if (carousel) {
        carousel.addEventListener('mouseenter', () => {
            if (autoPlayInterval) {
                clearInterval(autoPlayInterval);
            }
        });
        
        carousel.addEventListener('mouseleave', () => {
            startAutoPlay();
        });
    }
});
</script>

<?php require_once 'views/layout/footer.php'; ?>