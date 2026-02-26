<?php require_once 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h3>PGS Monitoring Dashboard</h3>
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



<!-- Projects Carousel -->
<?php if(isset($projects) && !empty($projects)): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-grid-3x3-gap-fill"></i> Core Area & Commitments Overview</h5>
                <div>
                    <span class="badge bg-light text-dark me-2" id="carousel-status">Core Area Overview</span>
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
                    <!-- Slide 1: All Projects Overview (4 per row) -->
                    <div class="carousel-slide active" data-slide="0" data-type="overview">
                        <div class="row">
                            <?php 
                            $projectCount = 0;
                            foreach($projects as $project): 
                                if($projectCount % 4 == 0 && $projectCount > 0) {
                                    echo '</div><div class="row mt-3">';
                                }
                            ?>
                            <div class="col-md-3 mb-3">
                                <div class="card h-100 border-<?php 
                                    echo $project['functional_division'] == 'OSDS' ? 'primary' : 
                                        ($project['functional_division'] == 'CID' ? 'success' : 
                                        ($project['functional_division'] == 'SGOD' ? 'info' : 'secondary')); 
                                ?>">
                                    <div class="card-header bg-<?php 
                                        echo $project['functional_division'] == 'OSDS' ? 'primary' : 
                                            ($project['functional_division'] == 'CID' ? 'success' : 
                                            ($project['functional_division'] == 'SGOD' ? 'info' : 'secondary')); 
                                    ?> text-white">
                                        <h6 class="mb-0 text-truncate"><?php echo htmlspecialchars($project['project_name']); ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Division</small>
                                            <strong><?php echo $project['functional_division']; ?></strong>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Progress</small>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-<?php 
                                                    echo $project['progress_percentage'] >= 100 ? 'success' : 
                                                        ($project['progress_percentage'] >= 50 ? 'info' : 'warning'); 
                                                ?>" style="width: <?php echo $project['progress_percentage']; ?>%">
                                                </div>
                                            </div>
                                            <small class="fw-bold"><?php echo $project['progress_percentage']; ?>%</small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Commitments</small>
                                            <strong><?php echo $project['completed_tasks'] ?? 0; ?>/<?php echo $project['total_tasks'] ?? 0; ?></strong>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary w-100 view-project-tasks" 
                                                onclick="showProjectTasks(<?php echo $project['project_id']; ?>)">
                                            View Commitment
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                $projectCount++;
                            endforeach; 
                            ?>
                        </div>
                    </div>

                    <!-- Individual Project Task Slides -->
                    <?php foreach($projects as $project): ?>
                    <div class="carousel-slide" data-slide="project_<?php echo $project['project_id']; ?>" data-type="project" data-project-id="<?php echo $project['project_id']; ?>">
                        <div class="card">
                            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-list-task"></i> 
                                    <?php echo htmlspecialchars($project['project_name']); ?> - Commitment
                                </h5>
                                <button class="btn btn-sm btn-light" onclick="goToSlide(0)">
                                    <i class="bi bi-arrow-left"></i> Back to Overview
                                </button>
                            </div>
                            <div class="card-body">
                                <?php
                                // Get tasks for this project
                                $project_tasks = array_filter($all_tasks ?? [], function($task) use ($project) {
                                    return ($task['project_id'] ?? 0) == $project['project_id'];
                                });
                                ?>
                                
                                <?php if(empty($project_tasks)): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> No tasks found for this project.
                                    <?php if($_SESSION['role'] == 'admin'): ?>
                                    <a href="index.php?action=create_task_page&project_id=<?php echo $project['project_id']; ?>" class="alert-link">
                                        Create a Commitment
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                <!-- Display tasks as cards in a 4x12 grid -->
                                <div class="row">
                                    <?php foreach($project_tasks as $task): 
                                        $percentage = $task['current_percentage'] ?? 0;
                                        $statusColor = $percentage >= 100 ? 'success' : ($percentage > 0 ? 'warning' : 'secondary');
                                        $statusText = $percentage >= 100 ? 'Completed' : ($percentage > 0 ? 'In Progress' : 'Not Started');
                                    ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-<?php echo $statusColor; ?>">
                                            <div class="card-header bg-<?php echo $statusColor; ?> text-white d-flex justify-content-between align-items-center">
                                                <span class="badge bg-light text-dark">Priority: <?php echo ucfirst($task['priority'] ?? 'medium'); ?></span>
                                                <small>ID: <?php echo $task['task_id']; ?></small>
                                            </div>
                                            <div class="card-body">
                                                <h6 class="card-title text-truncate" title="<?php echo htmlspecialchars($task['task_details']); ?>">
                                                    <?php echo htmlspecialchars(substr($task['task_details'], 0, 50)); ?>...
                                                </h6>
                                                
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Assigned Units</small>
                                                    <div>
                                                        <?php 
                                                        if(isset($task['unit_names']) && $task['unit_names']) {
                                                            $units = explode(', ', $task['unit_names']);
                                                            $displayUnits = array_slice($units, 0, 2);
                                                            foreach($displayUnits as $unit) {
                                                                echo '<span class="badge bg-info me-1">' . htmlspecialchars($unit) . '</span>';
                                                            }
                                                            if(count($units) > 2) {
                                                                echo '<span class="badge bg-secondary">+' . (count($units)-2) . '</span>';
                                                            }
                                                        } else {
                                                            echo '<span class="text-muted">No units assigned</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Progress</small>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-<?php echo $statusColor; ?>" 
                                                             style="width: <?php echo $percentage; ?>%">
                                                        </div>
                                                    </div>
                                                    <small class="fw-bold"><?php echo $percentage; ?>%</small>
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Target Date</small>
                                                    <strong><?php echo htmlspecialchars($task['target_completion_date'] ?? 'N/A'); ?></strong>
                                                </div>
                                                
                                                <?php if(isset($task['last_update']) && $task['last_update']): ?>
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Last Update</small>
                                                    <small><?php echo date('M d, Y', strtotime($task['last_update'])); ?></small>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <a href="index.php?action=view_task&id=<?php echo $task['task_id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary w-100">
                                                    View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="mt-3">
                                    <a href="index.php?action=view_project&id=<?php echo $project['project_id']; ?>" 
                                       class="btn btn-primary">
                                        View Project Details
                                    </a>
                                    <?php if($_SESSION['role'] == 'admin'): ?>
                                    <a href="index.php?action=create_task_page&project_id=<?php echo $project['project_id']; ?>" 
                                       class="btn btn-success">
                                        <i class="bi bi-plus-circle"></i> Add Commitment
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Carousel Indicators -->
                <div class="carousel-indicators mt-3">
                    <button type="button" class="indicator active" onclick="goToSlide(0)">Projects Overview</button>
                    <?php foreach($projects as $index => $project): ?>
                    <button type="button" class="indicator" onclick="goToSlide('project_<?php echo $project['project_id']; ?>')">
                        <?php echo htmlspecialchars(substr($project['project_name'], 0, 15)); ?>
                    </button>
                    <?php endforeach; ?>
                </div>
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




<!-- Recent Tasks -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Recent Tasks</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if(empty($recent_tasks)): ?>
                    <div class="col-12">
                        <p class="text-muted text-center">No recent tasks found.</p>
                    </div>
                    <?php else: ?>
                        <?php foreach($recent_tasks as $task): 
                            $percentage = $task['current_percentage'] ?? 0;
                            $statusColor = $percentage >= 100 ? 'success' : ($percentage > 0 ? 'warning' : 'secondary');
                        ?>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-<?php echo $statusColor; ?>">
                                <div class="card-header bg-<?php echo $statusColor; ?> text-white d-flex justify-content-between align-items-center">
                                    <small class="text-truncate"><?php echo htmlspecialchars($task['project_name'] ?? 'No Project'); ?></small>
                                    <span class="badge bg-light text-dark"><?php echo ucfirst($task['priority'] ?? 'medium'); ?></span>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title text-truncate" title="<?php echo htmlspecialchars($task['task_details']); ?>">
                                        <?php echo htmlspecialchars(substr($task['task_details'], 0, 40)); ?>...
                                    </h6>
                                    
                                    <div class="mb-2">
                                        <span class="badge bg-<?php 
                                            echo $task['functional_division'] == 'OSDS' ? 'primary' : 
                                                ($task['functional_division'] == 'CID' ? 'success' : 
                                                ($task['functional_division'] == 'SGOD' ? 'info' : 'secondary')); 
                                        ?>">
                                            <?php echo $task['functional_division']; ?>
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Progress</small>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-<?php echo $statusColor; ?>" 
                                                 style="width: <?php echo $percentage; ?>%">
                                            </div>
                                        </div>
                                        <small class="fw-bold"><?php echo $percentage; ?>%</small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="index.php?action=view_task&id=<?php echo $task['task_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary w-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.project-carousel {
    position: relative;
    min-height: 400px;
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
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
    position: relative;
    bottom: 0;
}

.carousel-indicators .indicator {
    padding: 5px 10px;
    border-radius: 20px;
    border: 1px solid #ddd;
    background-color: #f8f9fa;
    color: #495057;
    cursor: pointer;
    font-size: 0.8rem;
    transition: all 0.3s;
}

.carousel-indicators .indicator.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
    transform: scale(1.05);
}

.carousel-indicators .indicator:hover {
    background-color: #e9ecef;
}

.carousel-indicators .indicator.active:hover {
    background-color: #0056b3;
}

/* Card hover effects */
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Progress bar animation */
.progress-bar {
    transition: width 0.3s ease;
}

/* Text truncation for long names */
.text-truncate {
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Card body spacing */
.card-body {
    padding: 1rem;
}

.card-body small {
    font-size: 0.75rem;
}

.card-footer {
    padding: 0.75rem 1rem;
}
</style>

<script>
let currentSlide = '0';
let autoPlayInterval;
let slideOrder = ['0'];

// Initialize slide order
<?php 
echo "slideOrder.push('" . implode("', '", array_map(function($p) { 
    return 'project_' . $p['project_id']; 
}, $projects)) . "');";
?>

function showSlide(slideId) {
    // Hide all slides
    document.querySelectorAll('.carousel-slide').forEach(slide => {
        slide.classList.remove('active');
    });
    
    // Show selected slide
    const targetSlide = document.querySelector(`.carousel-slide[data-slide="${slideId}"]`);
    if (targetSlide) {
        targetSlide.classList.add('active');
    }
    
    // Update indicators
    document.querySelectorAll('.indicator').forEach((indicator, i) => {
        const indicatorSlideId = i === 0 ? '0' : slideOrder[i];
        if (indicatorSlideId == slideId) {
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active');
        }
    });
    
    // Update status text
    const statusEl = document.getElementById('carousel-status');
    if (slideId === '0') {
        statusEl.textContent = 'Projects Overview';
    } else {
        const projectName = document.querySelector(`.carousel-slide[data-slide="${slideId}"] .card-header h5`).textContent;
        statusEl.textContent = projectName;
    }
    
    currentSlide = slideId;
}

function nextSlide() {
    const currentIndex = slideOrder.indexOf(currentSlide);
    let nextIndex = (currentIndex + 1) % slideOrder.length;
    showSlide(slideOrder[nextIndex]);
    resetAutoPlay();
}

function prevSlide() {
    const currentIndex = slideOrder.indexOf(currentSlide);
    let prevIndex = (currentIndex - 1 + slideOrder.length) % slideOrder.length;
    showSlide(slideOrder[prevIndex]);
    resetAutoPlay();
}

function goToSlide(slideId) {
    showSlide(slideId);
    resetAutoPlay();
}

function showProjectTasks(projectId) {
    goToSlide('project_' + projectId);
}

function startAutoPlay() {
    if (slideOrder.length <= 1) return;
    autoPlayInterval = setInterval(() => {
        nextSlide();
    }, 20000); 
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