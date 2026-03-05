<?php require_once 'views/layout/header.php'; ?>

<?php 
// Add public-view class for any additional styling if needed
echo '<div class="public-view">';
?>

<!-- Projects Carousel - Public View (Read Only) -->
<?php if(isset($projects) && !empty($projects)): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0"><i class="bi bi-grid-3x3-gap-fill"></i> Core Area & Commitments Overview</h5>
                <div class="d-flex align-items-center gap-3">
                    <!-- Year Filter inside header -->
                    <form method="GET" action="index.php" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="action" value="login">
                        <select class="form-select form-select-sm bg-light text-dark" id="year" name="year" onchange="this.form.submit()" style="width: auto; min-width: 100px;">
                            <option value="2026" <?php echo (isset($selected_year) && $selected_year == '2026') ? 'selected' : ''; ?>>2026</option>
                            <option value="2027" <?php echo (isset($selected_year) && $selected_year == '2027') ? 'selected' : ''; ?>>2027</option>
                            <?php 
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
                        <noscript>
                            <button type="submit" class="btn btn-sm btn-light">Apply</button>
                        </noscript>
                    </form>
                    
                    <?php if(isset($selected_year) && $selected_year): ?>
                    <span class="badge bg-light text-dark">
                        <i class="bi bi-calendar-check"></i> <?php echo htmlspecialchars($selected_year); ?>
                    </span>
                    <?php endif; ?>
                    
                    <!-- Carousel controls -->
                    <span class="badge bg-light text-dark me-2" id="carousel-status">1/<?php echo ceil(count($projects)/8) + count($projects); ?></span>
                    <button class="btn btn-sm btn-light carousel-control" onclick="prevSlide()">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="btn btn-sm btn-light carousel-control" onclick="nextSlide()">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="projectCarousel" class="project-carousel">
                    <!-- Project Overview Slides -->
                    <?php 
                    $projectChunks = array_chunk($projects, 8);
                    foreach($projectChunks as $chunkIndex => $projectChunk): 
                    ?>
                    <div class="carousel-slide <?php echo $chunkIndex === 0 ? 'active' : ''; ?>" data-slide="<?php echo $chunkIndex; ?>">
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
                                        <h6 class="mb-0 text-truncate" title="<?php echo htmlspecialchars($project['project_name']); ?>">
                                            <?php 
                                            $project_name = htmlspecialchars($project['project_name']);
                                            echo strlen($project_name) > 30 ? substr($project_name, 0, 27) . '...' : $project_name;
                                            ?>
                                        </h6>
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
                                        <!-- View button removed -->
                                    </div>
                                </div>
                            </div>
                            <?php 
                                $projectCount++;
                            endforeach; 
                            ?>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">Page <?php echo $chunkIndex + 1; ?>/<?php echo count($projectChunks); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Individual Project Task Slides -->
                    <?php foreach($projects as $project): ?>
                    <div class="carousel-slide" data-slide="project_<?php echo $project['project_id']; ?>">
                        <div class="card">
                            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-truncate" style="max-width: 70%;" title="<?php echo htmlspecialchars($project['project_name']); ?>">
                                    <i class="bi bi-list-task"></i> 
                                    <?php 
                                    $project_name = htmlspecialchars($project['project_name']);
                                    echo strlen($project_name) > 50 ? substr($project_name, 0, 47) . '...' : $project_name;
                                    ?>
                                    <span class="badge bg-light text-dark ms-2"><?php echo htmlspecialchars($project['year'] ?? 'N/A'); ?></span>
                                </h5>
                                <button class="btn btn-sm btn-light carousel-control" onclick="goToSlide(0)">
                                    <i class="bi bi-arrow-left"></i> Back
                                </button>
                            </div>
                            <div class="card-body">
                                <?php
                                $project_tasks = array_filter($all_tasks ?? [], function($task) use ($project, $selected_year) {
                                    $task_match = ($task['project_id'] ?? 0) == $project['project_id'];
                                    if($selected_year && $task_match) {
                                        return isset($task['year']) && $task['year'] == $selected_year;
                                    }
                                    return $task_match;
                                });
                                ?>
                                
                                <?php if(empty($project_tasks)): ?>
                                <div class="alert alert-info py-2 mb-3">
                                    <i class="bi bi-info-circle"></i> No commitments for <?php echo $selected_year ?? 'this year'; ?>.
                                </div>
                                <?php else: ?>
                                <div class="row">
                                    <?php foreach($project_tasks as $task): 
                                        $percentage = $task['current_percentage'] ?? 0;
                                        $statusColor = $percentage >= 100 ? 'success' : ($percentage > 0 ? 'warning' : 'secondary');
                                    ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-<?php echo $statusColor; ?>">
                                            <div class="card-header bg-<?php echo $statusColor; ?> text-white d-flex justify-content-between align-items-center py-1">
                                                <span class="badge bg-light text-dark"><?php echo ucfirst($task['priority'] ?? 'med'); ?></span>
                                                <small><?php echo htmlspecialchars($task['year'] ?? 'N/A'); ?></small>
                                            </div>
                                            <div class="card-body p-2">
                                                <h6 class="card-title text-truncate small" title="<?php echo htmlspecialchars($task['task_details']); ?>">
                                                    <?php 
                                                    $task_details = htmlspecialchars($task['task_details']);
                                                    echo strlen($task_details) > 40 ? substr($task_details, 0, 37) . '...' : $task_details;
                                                    ?>
                                                </h6>
                                                
                                                <div class="mb-1">
                                                    <small class="text-muted d-block">Units</small>
                                                    <div class="small">
                                                        <?php 
                                                        if(isset($task['unit_names']) && $task['unit_names']) {
                                                            $units = explode(', ', $task['unit_names']);
                                                            echo '<span class="badge bg-info me-1" title="' . htmlspecialchars($units[0]) . '">' . 
                                                                 (strlen($units[0]) > 10 ? substr($units[0], 0, 7) . '...' : $units[0]) . '</span>';
                                                            if(count($units) > 1) {
                                                                echo '<span class="badge bg-secondary">+' . (count($units)-1) . '</span>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-1">
                                                    <small class="text-muted d-block">Progress</small>
                                                    <div class="progress" style="height: 5px;">
                                                        <div class="progress-bar bg-<?php echo $statusColor; ?>" 
                                                             style="width: <?php echo $percentage; ?>%">
                                                        </div>
                                                    </div>
                                                    <small class="fw-bold"><?php echo $percentage; ?>%</small>
                                                </div>
                                            </div>
                                            <!-- Card footer with view button removed -->
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Carousel Indicators -->
                <div class="carousel-indicators mt-2">
                    <?php 
                    $totalOverviewSlides = count($projectChunks);
                    for($i = 0; $i < $totalOverviewSlides; $i++): 
                    ?>
                    <button type="button" class="indicator <?php echo $i === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $i; ?>)">
                        P<?php echo $i + 1; ?>
                    </button>
                    <?php endfor; ?>
                    
                    <?php foreach($projects as $index => $project): ?>
                    <button type="button" class="indicator" onclick="goToSlide('project_<?php echo $project['project_id']; ?>')" 
                            title="<?php echo htmlspecialchars($project['project_name']); ?>">
                        <?php 
                        $short_name = substr($project['project_name'], 0, 8);
                        echo htmlspecialchars($short_name) . (strlen($project['project_name']) > 8 ? '…' : '');
                        ?>
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
        </div>
    </div>
</div>
<?php endif; ?>



</div> <!-- end public-view -->

<script>
let currentSlide = '0';
let autoPlayInterval;
let slideOrder = [];
let isTransitioning = false;

// Initialize slide order
<?php 
$totalOverviewSlides = count($projectChunks);
for($i = 0; $i < $totalOverviewSlides; $i++) {
    echo "slideOrder.push('$i');\n";
}
foreach($projects as $p) {
    echo "slideOrder.push('project_" . $p['project_id'] . "');\n";
}
?>

function showSlide(slideId) {
    if (isTransitioning) return;
    isTransitioning = true;
    
    const currentActiveSlide = document.querySelector('.carousel-slide.active');
    const targetSlide = document.querySelector(`.carousel-slide[data-slide="${slideId}"]`);
    
    if (!targetSlide) {
        isTransitioning = false;
        return;
    }
    
    if (currentActiveSlide) {
        currentActiveSlide.classList.remove('active');
        currentActiveSlide.classList.add('fade-out');
        
        setTimeout(() => {
            currentActiveSlide.classList.remove('fade-out');
            currentActiveSlide.style.display = 'none';
            
            targetSlide.style.display = 'block';
            targetSlide.classList.add('active', 'fade-in');
            
            setTimeout(() => {
                targetSlide.classList.remove('fade-in');
                isTransitioning = false;
            }, 800);
        }, 400);
    } else {
        targetSlide.style.display = 'block';
        targetSlide.classList.add('active', 'fade-in');
        
        setTimeout(() => {
            targetSlide.classList.remove('fade-in');
            isTransitioning = false;
        }, 800);
    }
    
    document.querySelectorAll('.indicator').forEach((indicator) => {
        if (indicator.getAttribute('onclick').includes(slideId)) {
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active');
        }
    });
    
    const statusEl = document.getElementById('carousel-status');
    const currentIndex = slideOrder.indexOf(slideId) + 1;
    const totalSlides = slideOrder.length;
    statusEl.textContent = `${currentIndex}/${totalSlides}`;
    
    currentSlide = slideId;
}

function nextSlide() {
    if (isTransitioning) return;
    const currentIndex = slideOrder.indexOf(currentSlide);
    let nextIndex = (currentIndex + 1) % slideOrder.length;
    showSlide(slideOrder[nextIndex]);
    resetAutoPlay();
}

function prevSlide() {
    if (isTransitioning) return;
    const currentIndex = slideOrder.indexOf(currentSlide);
    let prevIndex = (currentIndex - 1 + slideOrder.length) % slideOrder.length;
    showSlide(slideOrder[prevIndex]);
    resetAutoPlay();
}

function goToSlide(slideId) {
    if (isTransitioning) return;
    showSlide(slideId);
    resetAutoPlay();
}

function startAutoPlay() {
    if (slideOrder.length <= 1) return;
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
    }
    autoPlayInterval = setInterval(() => {
        if (!isTransitioning) {
            nextSlide();
        }
    }, 10000);
}

function resetAutoPlay() {
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
    }
    startAutoPlay();
}

// Initialize slides
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.carousel-slide').forEach(slide => {
        slide.style.display = 'none';
    });
    
    const firstSlide = document.querySelector('.carousel-slide[data-slide="0"]');
    if (firstSlide) {
        firstSlide.style.display = 'block';
        firstSlide.classList.add('active');
    }
    
    startAutoPlay();
    
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

// Show login modal if there's an error
<?php if(isset($error) && $error): ?>
document.addEventListener('DOMContentLoaded', function() {
    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
});
<?php endif; ?>
</script>

<?php require_once 'views/layout/footer.php'; ?>