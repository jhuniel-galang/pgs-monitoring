<?php require_once 'views/layout/header.php'; ?>

<!-- Year Filter -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="index.php" class="row align-items-end">
                    <input type="hidden" name="action" value="dashboard">
                    
                    <div class="col-md-3">
                        <label for="year" class="form-label fw-bold">Select Year</label>
                        <select class="form-select form-select-lg" id="year" name="year" onchange="this.form.submit()">
                            <option value="">All Years</option>
                            <option value="2026" <?php echo (isset($selected_year) && $selected_year == '2026') ? 'selected' : ''; ?>>2026</option>
                            <option value="2027" <?php echo (isset($selected_year) && $selected_year == '2027') ? 'selected' : ''; ?>>2027</option>
                            <?php 
                            // Dynamically add years from projects if available
                            if(isset($available_years) && !empty($available_years)):
                                foreach($available_years as $year):
                                    if($year != '2026' && $year != '2027'):
                            ?>
                            <option value="<?php echo htmlspecialchars($year); ?>" <?php echo (isset($selected_year) && $selected_year == $year) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($year); ?>
                            </option>
                            <?php 
                                    endif;
                                endforeach;
                            endif; 
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                        <?php if(isset($selected_year) && $selected_year != ''): ?>
                        <a href="index.php?action=dashboard" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6 text-end">
                        <?php if(isset($selected_year) && $selected_year): ?>
                        <div class="alert alert-info py-2 mb-0">
                            <i class="bi bi-calendar-check"></i> 
                            Showing data for <strong><?php echo htmlspecialchars($selected_year); ?></strong>
                            <?php if(isset($filtered_project_count) && isset($filtered_task_count)): ?>
                            (<?php echo $filtered_project_count; ?> core areas, <?php echo $filtered_task_count; ?> commitments)
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
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
                    <span class="badge bg-light text-dark me-2" id="carousel-status">Core Area Overview 1/<?php echo ceil(count($projects)/8) + count($projects); ?></span>
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
                    <!-- Project Overview Slides (8 projects per slide) -->
                    <?php 
                    $projectChunks = array_chunk($projects, 8);
                    $slideIndex = 0;
                    foreach($projectChunks as $chunkIndex => $projectChunk): 
                    ?>
                    <div class="carousel-slide <?php echo $chunkIndex === 0 ? 'active' : ''; ?>" data-slide="<?php echo $chunkIndex; ?>" data-type="overview">
                        <div class="row">
                            <?php 
                            $projectCount = 0;
                            foreach($projectChunk as $project): 
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
                                            <small class="text-muted d-block">Year</small>
                                            <span class="badge bg-dark"><?php echo htmlspecialchars($project['year'] ?? 'N/A'); ?></span>
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
                        <div class="text-center mt-2">
                            <small class="text-muted">Overview Page <?php echo $chunkIndex + 1; ?>/<?php echo count($projectChunks); ?></small>
                        </div>
                    </div>
                    <?php 
                        $slideIndex++;
                    endforeach; 
                    ?>

                    <!-- Individual Project Task Slides -->
                    <?php foreach($projects as $project): ?>
                    <div class="carousel-slide" data-slide="project_<?php echo $project['project_id']; ?>" data-type="project" data-project-id="<?php echo $project['project_id']; ?>">
                        <div class="card">
                            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-list-task"></i> 
                                    <?php echo htmlspecialchars($project['project_name']); ?> - Commitments
                                    <span class="badge bg-light text-dark ms-2">Year: <?php echo htmlspecialchars($project['year'] ?? 'N/A'); ?></span>
                                </h5>
                                <button class="btn btn-sm btn-light" onclick="goToSlide(0)">
                                    <i class="bi bi-arrow-left"></i> Back to Overview
                                </button>
                            </div>
                            <div class="card-body">
                                <?php
                                // Get tasks for this project (filtered by year if selected)
                                $project_tasks = array_filter($all_tasks ?? [], function($task) use ($project, $selected_year) {
                                    $task_match = ($task['project_id'] ?? 0) == $project['project_id'];
                                    // Apply year filter if selected
                                    if($selected_year && $task_match) {
                                        return isset($task['year']) && $task['year'] == $selected_year;
                                    }
                                    return $task_match;
                                });
                                ?>
                                
                                <?php if(empty($project_tasks)): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> No commitments found for this project 
                                    <?php if($selected_year): ?>in <strong><?php echo $selected_year; ?></strong><?php endif; ?>.
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
                                    ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-<?php echo $statusColor; ?>">
                                            <div class="card-header bg-<?php echo $statusColor; ?> text-white d-flex justify-content-between align-items-center">
                                                <span class="badge bg-light text-dark">Priority: <?php echo ucfirst($task['priority'] ?? 'medium'); ?></span>
                                                <small>Year: <?php echo htmlspecialchars($task['year'] ?? 'N/A'); ?></small>
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
                    <?php 
                    $totalOverviewSlides = count($projectChunks);
                    for($i = 0; $i < $totalOverviewSlides; $i++): 
                    ?>
                    <button type="button" class="indicator <?php echo $i === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $i; ?>)">
                        Overview <?php echo $i + 1; ?>
                    </button>
                    <?php endfor; ?>
                    
                    <?php foreach($projects as $index => $project): ?>
                    <button type="button" class="indicator" onclick="goToSlide('project_<?php echo $project['project_id']; ?>')">
                        <?php echo htmlspecialchars(substr($project['project_name'], 0, 10)); ?> (<?php echo htmlspecialchars($project['year'] ?? 'N/A'); ?>)
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
            <i class="bi bi-info-circle"></i> 
            <?php if(isset($selected_year) && $selected_year): ?>
                No projects found for year <strong><?php echo htmlspecialchars($selected_year); ?></strong>.
            <?php else: ?>
                No projects found.
            <?php endif; ?>
            <?php if($_SESSION['role'] == 'admin'): ?>
            <a href="index.php?action=projects" class="alert-link">Create your first project</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Division Summary Cards (Quick overview) -->
<div class="row mb-4">
    <?php if(!empty($division_summary)): ?>
        <?php foreach($division_summary as $summary): ?>
        <?php if($summary && isset($summary['functional_division'])): ?>
        <div class="col-md-<?php echo $_SESSION['role'] == 'admin' ? '3' : '12'; ?> mb-3">
            <div class="card h-100 bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title"><?php echo $summary['functional_division']; ?> Division</h5>
                    <h2><?php echo $summary['total_projects']; ?></h2>
                    <p class="text-muted">Total Projects</p>
                    <?php if(isset($summary['total_tasks'])): ?>
                    <div class="small">
                        <span class="badge bg-success"><?php echo $summary['completed_tasks'] ?? 0; ?> completed tasks</span>
                        <span class="badge bg-info"><?php echo $summary['total_tasks'] ?? 0; ?> total tasks</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle"></i> No division summary data available.
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Recent Tasks -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Tasks</h5>
                <?php if(isset($selected_year) && $selected_year): ?>
                <span class="badge bg-info">Filtered by Year: <?php echo htmlspecialchars($selected_year); ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if(empty($recent_tasks)): ?>
                    <div class="col-12">
                        <p class="text-muted text-center">
                            <?php if(isset($selected_year) && $selected_year): ?>
                                No recent tasks found for year <strong><?php echo htmlspecialchars($selected_year); ?></strong>.
                            <?php else: ?>
                                No recent tasks found.
                            <?php endif; ?>
                        </p>
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
                                    <span class="badge bg-light text-dark">Year: <?php echo htmlspecialchars($task['year'] ?? 'N/A'); ?></span>
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
    min-height: 500px;
}

.carousel-slide {
    display: none;
    opacity: 0;
    transition: opacity 0.8s ease-in-out;
}

.carousel-slide.active {
    display: block;
    opacity: 1;
}

.carousel-slide.fade-out {
    opacity: 0;
}

.carousel-slide.fade-in {
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
let slideOrder = [];
let isTransitioning = false;

// Initialize slide order - first all overview slides, then all project task slides
<?php 
$totalOverviewSlides = count($projectChunks);
// Add overview slides
for($i = 0; $i < $totalOverviewSlides; $i++) {
    echo "slideOrder.push('$i');\n";
}
// Add project task slides
foreach($projects as $p) {
    echo "slideOrder.push('project_" . $p['project_id'] . "');\n";
}
?>

function showSlide(slideId) {
    if (isTransitioning) return; // Prevent multiple transitions at once
    isTransitioning = true;
    
    const currentActiveSlide = document.querySelector('.carousel-slide.active');
    const targetSlide = document.querySelector(`.carousel-slide[data-slide="${slideId}"]`);
    
    if (!targetSlide) {
        isTransitioning = false;
        return;
    }
    
    // Fade out current slide if exists
    if (currentActiveSlide) {
        currentActiveSlide.classList.remove('active');
        currentActiveSlide.classList.add('fade-out');
        
        // After fade out, hide it completely and fade in new slide
        setTimeout(() => {
            currentActiveSlide.classList.remove('fade-out');
            currentActiveSlide.style.display = 'none';
            
            // Show and fade in new slide
            targetSlide.style.display = 'block';
            targetSlide.classList.add('active', 'fade-in');
            
            setTimeout(() => {
                targetSlide.classList.remove('fade-in');
                isTransitioning = false;
            }, 800); // Match transition duration
        }, 400); // Half of the transition time
    } else {
        // No current slide, just show new one
        targetSlide.style.display = 'block';
        targetSlide.classList.add('active', 'fade-in');
        
        setTimeout(() => {
            targetSlide.classList.remove('fade-in');
            isTransitioning = false;
        }, 800);
    }
    
    // Update indicators
    document.querySelectorAll('.indicator').forEach((indicator, i) => {
        if (indicator.getAttribute('onclick').includes(slideId)) {
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active');
        }
    });
    
    // Update status text
    const statusEl = document.getElementById('carousel-status');
    const currentIndex = slideOrder.indexOf(slideId) + 1;
    const totalSlides = slideOrder.length;
    
    if (slideId === '0') {
        statusEl.textContent = `Core Area Overview 1/${totalSlides}`;
    } else if (!isNaN(parseInt(slideId))) {
        statusEl.textContent = `Core Area Overview ${parseInt(slideId) + 1}/${totalSlides}`;
    } else {
        const projectHeader = document.querySelector(`.carousel-slide[data-slide="${slideId}"] .card-header h5`);
        if (projectHeader) {
            statusEl.textContent = `${projectHeader.textContent} ${currentIndex}/${totalSlides}`;
        }
    }
    
    currentSlide = slideId;
}

function nextSlide() {
    if (isTransitioning) return; // Don't allow next slide during transition
    const currentIndex = slideOrder.indexOf(currentSlide);
    let nextIndex = (currentIndex + 1) % slideOrder.length;
    showSlide(slideOrder[nextIndex]);
    resetAutoPlay();
}

function prevSlide() {
    if (isTransitioning) return; // Don't allow previous slide during transition
    const currentIndex = slideOrder.indexOf(currentSlide);
    let prevIndex = (currentIndex - 1 + slideOrder.length) % slideOrder.length;
    showSlide(slideOrder[prevIndex]);
    resetAutoPlay();
}

function goToSlide(slideId) {
    if (isTransitioning) return; // Don't allow navigation during transition
    showSlide(slideId);
    resetAutoPlay();
}

function showProjectTasks(projectId) {
    goToSlide('project_' + projectId);
}

function startAutoPlay() {
    if (slideOrder.length <= 1) return;
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
    }
    autoPlayInterval = setInterval(() => {
        if (!isTransitioning) { // Only auto-advance if not transitioning
            nextSlide();
        }
    }, 10000); // 10 seconds
}

function resetAutoPlay() {
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
    }
    startAutoPlay();
}

// Initialize slides - make sure only the first slide is visible
document.addEventListener('DOMContentLoaded', function() {
    // Hide all slides first
    document.querySelectorAll('.carousel-slide').forEach(slide => {
        slide.style.display = 'none';
    });
    
    // Show the first slide
    const firstSlide = document.querySelector('.carousel-slide[data-slide="0"]');
    if (firstSlide) {
        firstSlide.style.display = 'block';
        firstSlide.classList.add('active');
    }
    
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
    
    // Also pause when clicking indicators
    document.querySelectorAll('.indicator').forEach(indicator => {
        indicator.addEventListener('click', () => {
            resetAutoPlay();
        });
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>