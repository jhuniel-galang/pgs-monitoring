<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PGS Monitoring Report - Public View</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: white;
            font-size: 11px;
        }
        
        .report-container {
            max-width: 1600px;
            margin: 0 auto;
        }
        
        .report-header {
            position: relative;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            min-height: 100px;
        }
        
        .logo-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
            height: auto;
        }
        
        .logo-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 80px;
            height: auto;
        }
        
        .logo-left img, .logo-right img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .header-text {
            text-align: center;
            padding: 0 100px;
            color: #000;
        }
        
        .header-text h1 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #000;
            font-weight: bold;
        }
        
        .header-text h2 {
            font-size: 16px;
            color: #000;
            font-weight: normal;
            margin-bottom: 5px;
        }
        
        .header-text h3 {
            font-size: 14px;
            color: #000;
            margin-top: 5px;
            font-weight: bold;
        }
        
        .header-text .pgs-title {
            font-size: 18px;
            color: #000;
            font-weight: bold;
            margin-top: 5px;
            letter-spacing: 1px;
        }
        
        .report-filters {
            margin: 15px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .filter-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: flex-end;
        }
        
        .filter-item {
            display: flex;
            flex-direction: column;
            min-width: 150px;
        }
        
        .filter-item label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            font-size: 12px;
        }
        
        .filter-item select, .filter-item input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 12px;
            background-color: white;
        }
        
        .filter-item span {
            padding: 8px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: inline-block;
        }
        
        .filter-actions {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .btn-info:hover {
            background-color: #138496;
        }
        
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 8px 15px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .back-button:hover {
            background-color: #5a6268;
        }
        
        .core-area-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .core-area-header {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            margin: 15px 0 10px 0;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        
        /* Main Commitments Table */
        .commitments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
            table-layout: auto;
        }
        
        .commitments-table th {
            background-color: #343a40;
            color: white;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #454d55;
            font-weight: bold;
            font-size: 11px;
        }
        
        .commitments-table td {
            padding: 8px 10px;
            border: 1px solid #dee2e6;
            vertical-align: top;
            word-wrap: break-word;
        }
        
        .commitments-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .commitments-table tr:hover {
            background-color: #e9ecef;
        }
        
        .col-id { width: 3%; }
        .col-commitment { min-width: 250px; }
        .col-unit { min-width: 150px; }
        .col-target { width: 8%; }
        .col-progress { width: 6%; }
        .col-status { width: 6%; }
        .col-priority { width: 5%; }
        .col-last-update { width: 8%; }
        .col-remarks { min-width: 300px; }
        
        .commitment-details {
            font-weight: bold;
            color: #333;
            white-space: normal;
            word-wrap: break-word;
        }
        
        .commitment-full-text {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .unit-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .unit-list li {
            margin-bottom: 3px;
            font-size: 11px;
            white-space: normal;
            word-wrap: break-word;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
            font-size: 11px;
            text-align: center;
            width: 100%;
        }
        
        .status-completed {
            background-color: #28a745;
            color: white;
        }
        
        .status-in-progress {
            background-color: #ffc107;
            color: #333;
        }
        
        .status-not-started {
            background-color: #dc3545;
            color: white;
        }
        
        .priority-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
            font-size: 11px;
            text-align: center;
            width: 100%;
        }
        
        .priority-critical {
            background-color: #dc3545;
            color: white;
        }
        
        .priority-high {
            background-color: #fd7e14;
            color: white;
        }
        
        .priority-medium {
            background-color: #ffc107;
            color: #333;
        }
        
        .priority-low {
            background-color: #6c757d;
            color: white;
        }
        
        .progress-bar-container {
            width: 60px;
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
            margin-left: 5px;
        }
        
        .progress-bar-fill {
            height: 100%;
            background-color: #28a745;
            border-radius: 4px;
        }
        
        .remarks-cell {
            max-width: 100%;
            word-wrap: break-word;
        }
        
        .remarks-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .remarks-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dotted #ccc;
        }
        
        .remarks-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .remarks-percentage {
            font-weight: bold;
            color: #007bff;
            display: inline-block;
            min-width: 45px;
        }
        
        .remarks-text {
            margin: 5px 0;
            font-style: italic;
            line-height: 1.3;
            white-space: normal;
            word-wrap: break-word;
        }
        
        .remarks-meta {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }
        
        .report-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #333;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        
        .print-button:hover {
            background-color: #0056b3;
        }
        
        .summary-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .summary-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .summary-item {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .summary-number {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .division-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.2s;
        }
        
        .division-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .division-card.filtered {
            border: 3px solid #ffc107;
            transform: scale(1.02);
        }
        
        .division-header {
            padding: 12px 15px;
            color: white;
        }
        
        .division-header h4 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        
        .division-stats {
            padding: 15px;
        }
        
        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #555;
        }
        
        .stat-value {
            font-weight: bold;
        }
        
        .stat-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        
        .stat-total .label {
            font-weight: bold;
            color: #555;
        }
        
        .stat-total .value {
            font-size: 24px;
            font-weight: bold;
        }
        
        .progress-container {
            margin-top: 10px;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 12px;
            color: #666;
        }
        
        .progress-bar-bg {
            width: 100%;
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
        }
        
        .legend-box {
            margin-top: 15px;
            padding: 10px;
            background-color: #fff3cd;
            border-radius: 5px;
            font-size: 12px;
            color: #856404;
        }
        
        .division-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
            margin-right: 10px;
        }
        
        .division-osds {
            background-color: #007bff;
            color: white;
        }
        
        .division-cid {
            background-color: #28a745;
            color: white;
        }
        
        .division-sgod {
            background-color: #17a2b8;
            color: white;
        }
        
        .division-schools {
            background-color: #6c757d;
            color: white;
        }
        
        .div-info {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
            padding-top: 3px;
            border-top: 1px dotted #ccc;
        }
        
        .public-note {
            margin: 10px 0;
            padding: 10px;
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            border-radius: 4px;
            font-size: 12px;
            color: #004085;
        }
        
        @media (max-width: 768px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            [style*="grid-template-columns: repeat(2, 1fr)"] {
                grid-template-columns: 1fr !important;
            }
        }
        
        @media print {
            .print-button, .filter-actions, .btn, .back-button, .public-note {
                display: none;
            }
            
            body {
                padding: 10px;
            }
            
            .report-filters {
                background-color: white;
                border: 1px solid #ddd;
            }
            
            .core-area-header {
                background-color: #007bff !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .commitments-table th {
                background-color: #343a40 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .status-completed {
                background-color: #28a745 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .status-in-progress {
                background-color: #ffc107 !important;
                color: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .status-not-started {
                background-color: #dc3545 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .priority-critical {
                background-color: #dc3545 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .priority-high {
                background-color: #fd7e14 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .priority-medium {
                background-color: #ffc107 !important;
                color: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .priority-low {
                background-color: #6c757d !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .division-osds {
                background-color: #007bff !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .division-cid {
                background-color: #28a745 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .division-sgod {
                background-color: #17a2b8 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .division-schools {
                background-color: #6c757d !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php
    // Get current year for default
    $current_year = date('Y');
    ?>
    
    <!-- Back button to return to public view -->
    <a href="index.php?action=login&year=<?php echo urlencode($selected_year); ?>" class="back-button">
        <i class="bi bi-arrow-left"></i> Back to Public View
    </a>
    
    <button class="print-button" onclick="window.print()">
        <i class="bi bi-printer"></i> Print Report
    </button>
    
    <div class="report-container">
        <!-- Public View Note -->
        <div class="public-note">
            <i class="bi bi-info-circle"></i> 
            <strong>Public Report:</strong> This is a read-only view of the PGS Monitoring data for transparency and public access.
        </div>
        
        <!-- Report Header with Two Logos -->
        <div class="report-header">
            <!-- Left Logo -->
            <div class="logo-left">
                <img src="assets/images/7.jpeg" alt="DepEd Division of San Fernando Logo">
            </div>
            
            <!-- Right Logo -->
            <div class="logo-right">
                <img src="assets/images/5-removebg-preview.png" alt="Republic of the Philippines Logo">
            </div>
            
            <!-- Centered Header Text - Black -->
            <div class="header-text">
                <h1>Republic of the Philippines</h1>
                <h2>Department of Education</h2>
                <h3>SCHOOLS DIVISION OFFICE - CITY OF SAN FERNANDO, PAMPANGA</h3>
                <div class="pgs-title">PGS MONITORING REPORT (PUBLIC VIEW)</div>
            </div>
        </div>
        
        <!-- Report Filters with Division Selector -->
        <div class="report-filters">
            <form method="GET" action="index.php" id="reportForm">
                <input type="hidden" name="action" value="public_report">
                
                <div class="filter-grid">
                    <div class="filter-item">
                        <label for="year">Year:</label>
                        <input type="text" id="year" name="year" placeholder="e.g., 2024" 
                               value="<?php echo htmlspecialchars($_GET['year'] ?? $selected_year); ?>">
                    </div>
                    
                    <div class="filter-item">
                        <label for="division">Functional Division:</label>
                        <select id="division" name="division">
                            <option value="">All Divisions</option>
                            <option value="OSDS" <?php echo (isset($_GET['division']) && $_GET['division'] == 'OSDS') ? 'selected' : ''; ?>>OSDS</option>
                            <option value="CID" <?php echo (isset($_GET['division']) && $_GET['division'] == 'CID') ? 'selected' : ''; ?>>CID</option>
                            <option value="SGOD" <?php echo (isset($_GET['division']) && $_GET['division'] == 'SGOD') ? 'selected' : ''; ?>>SGOD</option>
                            <option value="Schools" <?php echo (isset($_GET['division']) && $_GET['division'] == 'Schools') ? 'selected' : ''; ?>>Schools</option>
                        </select>
                    </div>
                    
                    <div class="filter-item">
                        <label for="status">Status:</label>
                        <select id="status" name="status">
                            <option value="">All Status</option>
                            <option value="not_started" <?php echo (isset($_GET['status']) && $_GET['status'] == 'not_started') ? 'selected' : ''; ?>>Not Started</option>
                            <option value="in_progress" <?php echo (isset($_GET['status']) && $_GET['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                    
                    <div class="filter-item">
                        <label for="priority">Priority:</label>
                        <select id="priority" name="priority">
                            <option value="">All Priorities</option>
                            <option value="low" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
                            <option value="critical" <?php echo (isset($_GET['priority']) && $_GET['priority'] == 'critical') ? 'selected' : ''; ?>>Critical</option>
                        </select>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i> Apply Filters
                        </button>
                        <a href="index.php?action=public_report&year=<?php echo urlencode($selected_year); ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Current Filter Display -->
        <div style="margin-bottom: 15px; padding: 10px; background-color: #e9ecef; border-radius: 5px;">
            <div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center;">
                <div><strong>Year:</strong> <?php echo !empty($selected_year) ? htmlspecialchars($selected_year) : $current_year; ?></div>
                <div><strong>As of:</strong> <?php echo $current_date; ?></div>
                <div>
                    <strong>Division:</strong> 
                    <?php if(!empty($selected_division)): ?>
                        <span class="division-badge division-<?php echo strtolower($selected_division); ?>">
                            <?php echo htmlspecialchars($selected_division); ?>
                        </span>
                    <?php else: ?>
                        <span>All Divisions</span>
                    <?php endif; ?>
                </div>
                <?php if(!empty($selected_status)): ?>
                <div><strong>Status:</strong> <?php echo ucfirst(str_replace('_', ' ', $selected_status)); ?></div>
                <?php endif; ?>
                <?php if(!empty($selected_priority)): ?>
                <div><strong>Priority:</strong> <?php echo ucfirst($selected_priority); ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Core Areas and Commitments Table -->
        <?php
        // Group tasks by project (core area)
        $tasks_by_project = [];
        if(isset($tasks) && !empty($tasks)) {
            foreach($tasks as $task) {
                $project_id = $task['project_id'] ?? 0;
                $project_name = $task['project_name'] ?? 'Unassigned Core Area';
                
                if(!isset($tasks_by_project[$project_id])) {
                    $tasks_by_project[$project_id] = [
                        'project_name' => $project_name,
                        'tasks' => []
                    ];
                }
                
                $tasks_by_project[$project_id]['tasks'][] = $task;
            }
            
            // Sort projects by name
            ksort($tasks_by_project);
        }
        
        if(empty($tasks)): ?>
            <div style="text-align: center; padding: 50px; background-color: #f8f9fa; border-radius: 5px;">
                <h3>No commitments found for <?php echo !empty($selected_year) ? htmlspecialchars($selected_year) : $current_year; ?>.</h3>
                <p style="margin-top: 10px; color: #666;">Try adjusting your filter criteria or check other years.</p>
            </div>
        <?php else: ?>
            <?php foreach($tasks_by_project as $project_id => $project_data): 
                $project_tasks = $project_data['tasks'];
            ?>
            <div class="core-area-section">
                <div class="core-area-header">
                    Core Area: <?php echo htmlspecialchars($project_data['project_name'] ?: 'Unassigned Core Area'); ?> 
                    (<?php echo count($project_tasks); ?> commitments)
                </div>
                
                <table class="commitments-table">
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-commitment">Commitments</th>
                            <th class="col-unit">Unit</th>
                            <th class="col-target">Target</th>
                            <th class="col-progress">Progress</th>
                            <th class="col-status">Status</th>
                            <th class="col-priority">Priority</th>
                            <th class="col-last-update">Last Update</th>
                            <th class="col-remarks">Remarks History</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($project_tasks as $task): 
                            $percentage = $task['current_percentage'] ?? 0;
                            
                            // Determine status class
                            if($percentage >= 100) {
                                $status_class = 'status-completed';
                                $status_text = 'Completed';
                            } elseif($percentage > 0) {
                                $status_class = 'status-in-progress';
                                $status_text = 'In Progress';
                            } else {
                                $status_class = 'status-not-started';
                                $status_text = 'Not Started';
                            }
                            
                            // Get unit names as array
                            $unit_names = $task['unit_names'] ?? 'N/A';
                            $unit_array = explode(', ', $unit_names);
                            
                            // Get status history
                            $status_history = $task['status_history'] ?? [];
                            
                            // Format last update
                            $last_update = !empty($task['last_update']) ? date('M d, Y', strtotime($task['last_update'])) : '—';
                        ?>
                        <tr>
                            <td><?php echo $task['task_id']; ?></td>
                            <td>
                                <div class="commitment-full-text">
                                    <?php echo htmlspecialchars($task['task_details']); ?>
                                </div>
                                <div class="div-info">
                                    <strong>Division:</strong> <?php echo $task['functional_division'] ?? 'N/A'; ?>
                                </div>
                            </td>
                            <td>
                                <ul class="unit-list">
                                    <?php foreach($unit_array as $unit): ?>
                                    <?php if(!empty(trim($unit))): ?>
                                    <li>• <?php echo htmlspecialchars($unit); ?></li>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td><?php echo htmlspecialchars($task['target_completion_date'] ?? '—'); ?></td>
                            <td>
                                <?php echo $percentage; ?>%
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </td>
                            <td>
                                <span class="priority-badge priority-<?php echo $task['priority'] ?? 'medium'; ?>">
                                    <?php echo ucfirst($task['priority'] ?? 'Medium'); ?>
                                </span>
                            </td>
                            <td><?php echo $last_update; ?></td>
                            <td class="remarks-cell">
                                <?php if(!empty($status_history)): ?>
                                <ul class="remarks-list">
                                    <?php foreach($status_history as $history): ?>
                                    <li class="remarks-item">
                                        <div>
                                            <span class="remarks-percentage"><?php echo $history['percentage'] ?? 0; ?>%</span>
                                            <span style="color: #666; font-size: 10px;">
                                                (<?php 
                                                if(isset($history['created_at'])) {
                                                    echo date('M d, Y h:i A', strtotime($history['created_at']));
                                                }
                                                ?>)
                                            </span>
                                        </div>
                                        <div class="remarks-text">
                                            <?php echo nl2br(htmlspecialchars($history['remarks'] ?? 'No remarks')); ?>
                                        </div>
                                        <div class="remarks-meta">
                                            Updated by: <?php echo htmlspecialchars($history['updated_by_name'] ?? 'Unknown'); ?>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php else: ?>
                                <span style="color: #999; font-style: italic;">No remarks</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; ?>
            
            <!-- Summary Section -->
            <div class="summary-section">
                <div class="summary-title">Report Summary (<?php echo !empty($selected_year) ? htmlspecialchars($selected_year) : $current_year; ?>)</div>
                
                <!-- Overall Summary Cards -->
                <div style="margin-bottom: 30px;">
                    <div style="font-size: 14px; font-weight: bold; margin-bottom: 15px; color: #333;">
                        <i class="bi bi-pie-chart"></i> Overall Summary
                    </div>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="summary-number"><?php echo count($tasks); ?></div>
                            <div>Total Commitments</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-number" style="color: #28a745;">
                                <?php 
                                $completed = array_filter($tasks, function($t) {
                                    return ($t['current_percentage'] ?? 0) >= 100;
                                });
                                echo count($completed);
                                ?>
                            </div>
                            <div>Completed</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-number" style="color: #ffc107;">
                                <?php 
                                $in_progress = array_filter($tasks, function($t) {
                                    $p = $t['current_percentage'] ?? 0;
                                    return $p > 0 && $p < 100;
                                });
                                echo count($in_progress);
                                ?>
                            </div>
                            <div>In Progress</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-number" style="color: #dc3545;">
                                <?php 
                                $not_started = array_filter($tasks, function($t) {
                                    return ($t['current_percentage'] ?? 0) == 0;
                                });
                                echo count($not_started);
                                ?>
                            </div>
                            <div>Not Started</div>
                        </div>
                    </div>
                </div>
                
                <!-- Division-wise Summary - Each division as card style -->
                <div>
                    <div style="font-size: 14px; font-weight: bold; margin-bottom: 15px; color: #333;">
                        <i class="bi bi-diagram-3"></i> Breakdown by Functional Division
                    </div>
                    
                    <?php
                    // Calculate division-wise statistics
                    $divisions = ['OSDS', 'CID', 'SGOD', 'Schools'];
                    $division_stats = [];
                    $division_colors = [
                        'OSDS' => '#007bff',
                        'CID' => '#28a745',
                        'SGOD' => '#17a2b8',
                        'Schools' => '#6c757d'
                    ];
                    
                    foreach($divisions as $division) {
                        $division_tasks = array_filter($tasks, function($t) use ($division) {
                            return ($t['functional_division'] ?? '') == $division;
                        });
                        
                        $division_stats[$division] = [
                            'total' => count($division_tasks),
                            'completed' => count(array_filter($division_tasks, function($t) {
                                return ($t['current_percentage'] ?? 0) >= 100;
                            })),
                            'in_progress' => count(array_filter($division_tasks, function($t) {
                                $p = $t['current_percentage'] ?? 0;
                                return $p > 0 && $p < 100;
                            })),
                            'not_started' => count(array_filter($division_tasks, function($t) {
                                return ($t['current_percentage'] ?? 0) == 0;
                            }))
                        ];
                    }
                    ?>
                    
                    <!-- Division Cards Grid -->
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 15px;">
                        <?php foreach($division_stats as $division => $stats): 
                            $bg_color = $division_colors[$division];
                            $is_filtered = (!empty($selected_division) && $selected_division == $division);
                            $card_style = $is_filtered ? 'border: 3px solid #ffc107; transform: scale(1.02);' : '';
                        ?>
                        <div style="background-color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; <?php echo $card_style; ?> transition: all 0.2s;">
                            <!-- Division Header -->
                            <div style="background-color: <?php echo $bg_color; ?>; color: white; padding: 12px 15px;">
                                <h4 style="margin: 0; font-size: 16px; font-weight: bold;"><?php echo $division; ?> Division</h4>
                            </div>
                            
                            <!-- Statistics -->
                            <div style="padding: 15px;">
                                <!-- Total -->
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
                                    <span style="font-weight: bold; color: #555;">Total Commitments</span>
                                    <span style="font-size: 24px; font-weight: bold; color: <?php echo $bg_color; ?>;"><?php echo $stats['total']; ?></span>
                                </div>
                                
                                <!-- Completed -->
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <span style="color: #555;">Completed</span>
                                    <span style="font-weight: bold; color: #28a745;"><?php echo $stats['completed']; ?></span>
                                </div>
                                
                                <!-- In Progress -->
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <span style="color: #555;">In Progress</span>
                                    <span style="font-weight: bold; color: #ffc107;"><?php echo $stats['in_progress']; ?></span>
                                </div>
                                
                                <!-- Not Started -->
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                    <span style="color: #555;">Not Started</span>
                                    <span style="font-weight: bold; color: #dc3545;"><?php echo $stats['not_started']; ?></span>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div style="margin-top: 10px;">
                                    <?php 
                                    $completion_rate = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0;
                                    ?>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span style="font-size: 12px; color: #666;">Completion Rate</span>
                                        <span style="font-size: 12px; font-weight: bold;"><?php echo $completion_rate; ?>%</span>
                                    </div>
                                    <div style="width: 100%; height: 8px; background-color: #e9ecef; border-radius: 4px; overflow: hidden;">
                                        <div style="width: <?php echo $completion_rate; ?>%; height: 100%; background-color: <?php echo $bg_color; ?>; border-radius: 4px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Legend for filtered division -->
                    <?php if(!empty($selected_division)): ?>
                    <div style="margin-top: 15px; padding: 10px; background-color: #fff3cd; border-radius: 5px; font-size: 12px; color: #856404;">
                        <i class="bi bi-info-circle"></i> 
                        The <strong><?php echo htmlspecialchars($selected_division); ?></strong> card is highlighted as it matches your current filter.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="report-footer">
            <p>Report generated on <?php echo date('F d, Y \a\t h:i A'); ?></p>
            <p>PGS Monitoring System - Schools Division Office of San Fernando, Pampanga</p>
            <p style="margin-top: 5px; font-style: italic;">This is a public report. For official use only data is restricted.</p>
        </div>
    </div>
</body>
</html>