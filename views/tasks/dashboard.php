<?php require_once 'views/layout/header.php'; ?>


<!-- Projects Carousel -->
<?php if(isset($projects) && !empty($projects)): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0"><i class="bi bi-grid-3x3-gap-fill"></i> Core Area & Commitments Overview</h5>
                <div class="d-flex align-items-center gap-3">
                    <!-- Year Filter inside header -->
                    <form method="GET" action="index.php" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="action" value="dashboard">
                        <select class="form-select form-select-sm bg-light text-dark" id="year" name="year" onchange="this.form.submit()" style="width: auto; min-width: 100px;">
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
                        <noscript>
                            <button type="submit" class="btn btn-sm btn-light">Apply</button>
                        </noscript>
                        <?php if(isset($selected_year) && $selected_year != ''): ?>
                        <a href="index.php?action=dashboard" class="btn btn-sm btn-light" title="Clear filter">
                            <i class="bi bi-x-circle"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                    
                    <!-- Filter status badge - simplified -->
                    <?php if(isset($selected_year) && $selected_year): ?>
                    <span class="badge bg-light text-dark">
                        <i class="bi bi-calendar-check"></i> <?php echo htmlspecialchars($selected_year); ?>
                    </span>
                    <?php endif; ?>
                    
                    <!-- Carousel controls -->
                    <span class="badge bg-light text-dark me-2" id="carousel-status">1/<?php echo ceil(count($projects)/8) + count($projects); ?></span>
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
                                        <h6 class="mb-0 text-truncate" title="<?php echo htmlspecialchars($project['project_name']); ?>">
                                            <?php 
                                            // Truncate long project names
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
        <?php 
        $progress = round($project['avg_progress'] ?? 0);
        $progress_color = $progress >= 100 ? 'success' : ($progress >= 50 ? 'info' : 'warning');
        ?>
        <div class="progress-bar bg-<?php echo $progress_color; ?>" 
             style="width: <?php echo $progress; ?>%">
        </div>
    </div>
    <small class="fw-bold"><?php echo $progress; ?>%</small>
</div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Commitments</small>
                                            <strong><?php echo $project['completed_tasks'] ?? 0; ?>/<?php echo $project['total_tasks'] ?? 0; ?></strong>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary w-100 view-project-tasks" 
                                                onclick="showProjectTasks(<?php echo $project['project_id']; ?>)">
                                            View
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
                            <small class="text-muted">Page <?php echo $chunkIndex + 1; ?>/<?php echo count($projectChunks); ?></small>
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
                                <h5 class="mb-0 text-truncate" style="max-width: 70%;" title="<?php echo htmlspecialchars($project['project_name']); ?>">
                                    <i class="bi bi-list-task"></i> 
                                    <?php 
                                    // Truncate long project names
                                    $project_name = htmlspecialchars($project['project_name']);
                                    echo strlen($project_name) > 50 ? substr($project_name, 0, 47) . '...' : $project_name;
                                    ?>
                                    <span class="badge bg-light text-dark ms-2"><?php echo htmlspecialchars($project['year'] ?? 'N/A'); ?></span>
                                </h5>

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
                                <div class="alert alert-info py-2 mb-3">
                                    <i class="bi bi-info-circle"></i> No commitments for <?php echo $selected_year ?? 'this year'; ?>.
                                    <?php if($_SESSION['role'] == 'admin'): ?>
                                    <a href="index.php?action=create_task_page&project_id=<?php echo $project['project_id']; ?>" class="alert-link">Add</a>
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
                                                            $displayUnits = array_slice($units, 0, 1);
                                                            foreach($displayUnits as $unit) {
                                                                $short_unit = strlen($unit) > 15 ? substr($unit, 0, 12) . '...' : $unit;
                                                                echo '<span class="badge bg-info me-1" title="' . htmlspecialchars($unit) . '">' . htmlspecialchars($short_unit) . '</span>';
                                                            }
                                                            if(count($units) > 1) {
                                                                echo '<span class="badge bg-secondary" title="' . htmlspecialchars(implode(', ', $units)) . '">+' . (count($units)-1) . '</span>';
                                                            }
                                                        } else {
                                                            echo '<span class="text-muted">—</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-2">
    <small class="text-muted d-block">Progress</small>
    <div class="progress" style="height: 8px;">
        <div class="progress-bar bg-<?php 
            // Calculate progress from avg_progress or tasks
            $progress = isset($project['avg_progress']) ? round($project['avg_progress']) : 0;
            echo $progress >= 100 ? 'success' : ($progress >= 50 ? 'info' : 'warning'); 
        ?>" style="width: <?php echo $progress; ?>%">
        </div>
    </div>
    <small class="fw-bold"><?php echo $progress; ?>%</small>
</div>
                                                
                                                <div class="mb-1">
                                                    <small class="text-muted d-block">Target</small>
                                                    <small><?php 
                                                        $date = $task['target_completion_date'] ?? 'N/A';
                                                        echo strlen($date) > 10 ? substr($date, 0, 7) . '…' : $date;
                                                    ?></small>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-transparent p-1">
                                                <a href="index.php?action=view_task&id=<?php echo $task['task_id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary w-100 py-0">
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="mt-2 d-flex gap-2">
                                    <a href="index.php?action=view_project&id=<?php echo $project['project_id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        View Project
                                    </a>
                                    <?php if($_SESSION['role'] == 'admin'): ?>
                                    <a href="index.php?action=create_task_page&project_id=<?php echo $project['project_id']; ?>" 
                                       class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle"></i> Add
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Carousel Indicators - Simplified -->
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
            <?php if($_SESSION['role'] == 'admin'): ?>
            <a href="index.php?action=projects" class="alert-link">Create your first project</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Division Summary Cards (Quick overview) - Simplified -->
<div class="row mb-4">
    <?php if(!empty($division_summary)): ?>
        <?php foreach($division_summary as $summary): ?>
        <?php if($summary && isset($summary['functional_division'])): ?>
        <div class="col-md-<?php echo $_SESSION['role'] == 'admin' ? '3' : '12'; ?> mb-3">
            <div class="card h-100 bg-light">
                <div class="card-body text-center p-3">
                    <h6 class="card-title"><?php echo $summary['functional_division']; ?></h6>
                    <h3><?php echo $summary['total_projects']; ?></h3>
                    <small class="text-muted">Projects</small>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Recent Tasks - Simplified -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-2">
                <h6 class="mb-0">Recent Tasks</h6>
            </div>
            <div class="card-body p-2">
                <div class="row">
                    <?php if(empty($recent_tasks)): ?>
                    <div class="col-12">
                        <p class="text-muted text-center small my-2">
                            <?php if(isset($selected_year) && $selected_year): ?>
                                No tasks for <?php echo $selected_year; ?>
                            <?php else: ?>
                                No tasks found
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php else: ?>
                        <?php foreach($recent_tasks as $task): 
                            $percentage = $task['current_percentage'] ?? 0;
                        ?>
                        <div class="col-md-3 mb-2">
                            <div class="card h-100">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-<?php echo $percentage >= 100 ? 'success' : ($percentage > 0 ? 'warning' : 'secondary'); ?> text-white" style="font-size: 0.6rem;">
                                            <?php echo $percentage; ?>%
                                        </span>
                                        <small><?php echo htmlspecialchars($task['year'] ?? 'N/A'); ?></small>
                                    </div>
                                    <p class="card-text small text-truncate mt-1" title="<?php echo htmlspecialchars($task['task_details']); ?>">
                                        <?php echo htmlspecialchars(substr($task['task_details'], 0, 25)); ?>...
                                    </p>
                                    <a href="index.php?action=view_task&id=<?php echo $task['task_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary w-100 py-0" style="font-size: 0.7rem;">
                                        View
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

<!-- Music Player Control -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card bg-light border-0 shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-music-note-beamed text-primary me-2" style="font-size: 1.2rem;"></i>
                        <span class="fw-semibold me-3">Background Music</span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary me-3" id="music-status">Paused</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" id="playPauseBtn" onclick="togglePlayPause()">
                            <i class="bi bi-play-fill" id="playPauseIcon"></i> <span id="playPauseText">Play</span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="muteBtn" onclick="toggleMute()">
                            <i class="bi bi-volume-up-fill" id="muteIcon"></i> <span id="muteText">Mute</span>
                        </button>
                        <div class="d-flex align-items-center ms-2">
                            <i class="bi bi-volume-down me-1"></i>
                            <input type="range" class="form-range" id="volumeSlider" min="0" max="100" value="50" style="width: 100px;" onchange="changeVolume(this.value)" oninput="updateVolumePreview(this.value)">
                            <i class="bi bi-volume-up ms-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Audio Element -->
<audio id="backgroundMusic" loop preload="auto" style="display: none;">
    <source src="assets/music/stepup.mp3" type="audio/mpeg">
    <source src="assets/music/step up final m ix one (1).ogg" type="audio/ogg">
    <source src="assets/music/step up final m ix one (1).wav" type="audio/wav">
    Your browser does not support the audio element.
</audio>

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
    gap: 5px;
    margin-top: 10px;
    position: relative;
    bottom: 0;
}

.carousel-indicators .indicator {
    padding: 2px 6px;
    border-radius: 12px;
    border: 1px solid #ddd;
    background-color: #f8f9fa;
    color: #495057;
    cursor: pointer;
    font-size: 0.7rem;
    transition: all 0.3s;
}

.carousel-indicators .indicator.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
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

/* Smaller text for compact display */
.small {
    font-size: 0.7rem;
}

/* Card body padding reduction */
.card-body {
    padding: 0.75rem;
}

.card-footer {
    padding: 0.25rem;
}

/* Button text size */
.btn-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Badge adjustments */
.badge {
    font-size: 0.6rem;
}

/* Volume slider styling */
.form-range {
    height: 0.5rem;
}

.form-range::-webkit-slider-thumb {
    background: #007bff;
}

.form-range::-moz-range-thumb {
    background: #007bff;
}

.form-range::-ms-thumb {
    background: #007bff;
}
</style>

<script>
// Music player controls
let audio = document.getElementById('backgroundMusic');
let isPlaying = false;
let isMuted = false;
let userActivated = false;  // becomes true after first interaction

// Initialize audio
document.addEventListener('DOMContentLoaded', function() {
    if (!audio) {
        console.error('Audio element not found!');
        return;
    }

    // Set initial volume to 50%
    audio.volume = 0.5;
    audio.loop = true;

    // Load saved preferences
    const savedVolume = localStorage.getItem('musicVolume');
    const savedMuted = localStorage.getItem('musicMuted');

    if (savedVolume !== null) {
        audio.volume = savedVolume / 100;
        document.getElementById('volumeSlider').value = savedVolume;
    }

    if (savedMuted === 'true') {
        audio.muted = true;
        isMuted = true;
        document.getElementById('muteIcon').className = 'bi bi-volume-mute-fill';
        document.getElementById('muteText').textContent = 'Unmute';
    }

    // Show hint that interaction is needed
    document.getElementById('music-status').textContent = 'Move mouse or click to play';
    document.getElementById('music-status').className = 'badge bg-warning bg-opacity-10 text-warning me-3';

    // Wait for first user interaction to start playback
    const startOnInteraction = () => {
        if (!userActivated) {
            userActivated = true;
            audio.play()
                .then(() => {
                    isPlaying = true;
                    updatePlayPauseUI();
                    console.log('Playback started after user interaction');
                })
                .catch(e => console.error('Playback failed:', e));
        }
        // Remove all interaction listeners after first activation
        document.removeEventListener('mousemove', startOnInteraction);
        document.removeEventListener('click', startOnInteraction);
        document.removeEventListener('keydown', startOnInteraction);
        document.removeEventListener('touchstart', startOnInteraction);
    };

    document.addEventListener('mousemove', startOnInteraction, { once: true });
    document.addEventListener('click', startOnInteraction, { once: true });
    document.addEventListener('keydown', startOnInteraction, { once: true });
    document.addEventListener('touchstart', startOnInteraction, { once: true });

    // Audio event listeners
    audio.addEventListener('play', () => {
        isPlaying = true;
        updatePlayPauseUI();
    });

    audio.addEventListener('pause', () => {
        isPlaying = false;
        updatePlayPauseUI();
    });

    audio.addEventListener('volumechange', () => {
        if (audio.volume === 0 && !audio.muted) {
            audio.muted = true;
        }
    });

    audio.addEventListener('error', (e) => {
        console.error('Audio error:', e);
        document.getElementById('music-status').textContent = 'Error';
        document.getElementById('music-status').className = 'badge bg-danger bg-opacity-10 text-danger me-3';
    });
});

function updatePlayPauseUI() {
    const playPauseIcon = document.getElementById('playPauseIcon');
    const playPauseText = document.getElementById('playPauseText');
    const musicStatus = document.getElementById('music-status');

    if (isPlaying) {
        playPauseIcon.className = 'bi bi-pause-fill';
        playPauseText.textContent = 'Pause';
        musicStatus.textContent = 'Playing';
        musicStatus.className = 'badge bg-primary bg-opacity-10 text-primary me-3';
    } else {
        playPauseIcon.className = 'bi bi-play-fill';
        playPauseText.textContent = 'Play';
        musicStatus.textContent = 'Paused';
        musicStatus.className = 'badge bg-secondary bg-opacity-10 text-secondary me-3';
    }

    localStorage.setItem('musicPlaying', isPlaying);
}

function togglePlayPause() {
    if (isPlaying) {
        audio.pause();
        // isPlaying will be updated by the 'pause' event listener
    } else {
        audio.play()
            .then(() => {
                // isPlaying will be updated by the 'play' event listener
            })
            .catch(error => {
                console.log('Play failed:', error);
                // If still blocked (rare), show hint again
                document.getElementById('music-status').textContent = 'Click to enable music';
                document.getElementById('music-status').className = 'badge bg-warning bg-opacity-10 text-warning me-3';
            });
    }
}

function toggleMute() {
    if (isMuted) {
        audio.muted = false;
        document.getElementById('muteIcon').className = 'bi bi-volume-up-fill';
        document.getElementById('muteText').textContent = 'Mute';
    } else {
        audio.muted = true;
        document.getElementById('muteIcon').className = 'bi bi-volume-mute-fill';
        document.getElementById('muteText').textContent = 'Unmute';
    }
    isMuted = !isMuted;
    localStorage.setItem('musicMuted', isMuted);
}

function changeVolume(value) {
    const volume = value / 100;
    audio.volume = volume;

    // Update mute state based on volume
    if (value == 0 && !isMuted) {
        audio.muted = true;
        document.getElementById('muteIcon').className = 'bi bi-volume-mute-fill';
        document.getElementById('muteText').textContent = 'Unmute';
        isMuted = true;
        localStorage.setItem('musicMuted', true);
    } else if (value > 0 && isMuted) {
        audio.muted = false;
        document.getElementById('muteIcon').className = 'bi bi-volume-up-fill';
        document.getElementById('muteText').textContent = 'Mute';
        isMuted = false;
        localStorage.setItem('musicMuted', false);
    }

    localStorage.setItem('musicVolume', value);
}

function updateVolumePreview(value) {
    const muteIcon = document.getElementById('muteIcon');
    if (value == 0) {
        muteIcon.className = 'bi bi-volume-off-fill';
    } else if (value < 30) {
        muteIcon.className = 'bi bi-volume-down-fill';
    } else {
        muteIcon.className = 'bi bi-volume-up-fill';
    }
}

// Add error handling for audio
audio.addEventListener('error', function(e) {
    console.error('Audio error:', e);
    document.getElementById('music-status').textContent = 'Error';
    document.getElementById('music-status').className = 'badge bg-danger bg-opacity-10 text-danger me-3';
});

// When audio can play, update status if playing
audio.addEventListener('canplay', function() {
    if (isPlaying) {
        document.getElementById('music-status').textContent = 'Playing';
    }
});

// Handle when audio is paused by browser (e.g., when another audio starts)
audio.addEventListener('pause', function() {
    if (isPlaying) {
        isPlaying = false;
        updatePlayPauseUI();
    }
});

// Handle when audio plays
audio.addEventListener('play', function() {
    if (!isPlaying) {
        isPlaying = true;
        updatePlayPauseUI();
    }
});

function changeVolume(value) {
    audio.volume = value / 100;
    
    // Update mute state if volume is 0
    if (value == 0) {
        if (!isMuted) {
            audio.muted = true;
            document.getElementById('muteIcon').className = 'bi bi-volume-mute-fill';
            document.getElementById('muteText').textContent = 'Unmute';
            isMuted = true;
        }
    } else {
        if (isMuted) {
            audio.muted = false;
            document.getElementById('muteIcon').className = 'bi bi-volume-up-fill';
            document.getElementById('muteText').textContent = 'Mute';
            isMuted = false;
        }
    }
}

// Carousel variables and functions
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

function showProjectTasks(projectId) {
    goToSlide('project_' + projectId);
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
    // Initialize carousel
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
    
    document.querySelectorAll('.indicator').forEach(indicator => {
        indicator.addEventListener('click', () => {
            resetAutoPlay();
        });
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>