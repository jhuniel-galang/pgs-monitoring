<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PGS Monitoring Report</title>
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
            font-size: 12px;
        }
        
        .report-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 5px;
        }
        
        .logo {
            width: 80px;
            height: auto;
            margin-right: 15px;
        }
        
        .logo img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .header-text {
            text-align: left;
        }
        
        .header-text h1 {
            font-size: 18px;
            margin-bottom: 2px;
            color: #333;
        }
        
        .header-text h2 {
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }
        
        .header-text h3 {
            font-size: 13px;
            color: #007bff;
            margin-top: 2px;
            font-weight: normal;
        }
        
        .report-filters {
            margin: 15px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .filter-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .filter-item {
            display: flex;
            align-items: center;
        }
        
        .filter-item strong {
            margin-right: 5px;
            color: #555;
        }
        
        .filter-item span {
            padding: 3px 8px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 3px;
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
            table-layout: fixed;
        }
        
        .commitments-table th {
            background-color: #343a40;
            color: white;
            padding: 8px 5px;
            text-align: left;
            border: 1px solid #454d55;
            font-weight: bold;
            font-size: 11px;
        }
        
        .commitments-table td {
            padding: 6px 5px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }
        
        .commitments-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .commitments-table tr:hover {
            background-color: #e9ecef;
        }
        
        /* Column widths */
        .col-id { width: 3%; }
        .col-core-area { width: 12%; }
        .col-commitment { width: 20%; }
        .col-unit { width: 10%; }
        .col-target { width: 6%; }
        .col-progress { width: 5%; }
        .col-status { width: 5%; }
        .col-priority { width: 5%; }
        .col-last-update { width: 6%; }
        .col-remarks { width: 28%; }
        
        .commitment-details {
            font-weight: bold;
            color: #333;
        }
        
        .unit-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .unit-list li {
            margin-bottom: 2px;
            font-size: 10px;
        }
        
        .status-badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
            font-size: 10px;
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
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
            font-size: 10px;
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
            width: 50px;
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            display: inline-block;
            margin-left: 5px;
        }
        
        .progress-bar-fill {
            height: 100%;
            background-color: #28a745;
            border-radius: 3px;
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
            margin-bottom: 6px;
            padding-bottom: 6px;
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
        }
        
        .remarks-text {
            margin: 2px 0;
            font-style: italic;
        }
        
        .remarks-meta {
            font-size: 9px;
            color: #666;
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
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        
        .summary-item {
            flex: 1;
            min-width: 150px;
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .summary-number {
            font-size: 24px;
            font-weight: bold;
        }
        
        @media print {
            .print-button {
                display: none;
            }
            
            body {
                padding: 10px;
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
        }
    </style>
    <!-- Bootstrap Icons (optional) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <button class="print-button" onclick="window.print()">
        <i class="bi bi-printer"></i> Print Report
    </button>
    
    <div class="report-container">
        <!-- Report Header with Logo -->
        <div class="report-header">
            <div class="logo-container">
                <div class="logo">
                    <img src="assets/images/5-removebg-preview.png" alt="DepEd Division of San Fernando Logo">
                </div>
                <div class="header-text">
                    <h1>Republic of the Philippines</h1>
                    <h2>Department of Education</h2>
                    <h3>SCHOOLS DIVISION OFFICE - CITY OF SAN FERNANDO, PAMPANGA</h3>
                </div>
            </div>
        </div>
        
        <!-- Report Filters -->
        <div class="report-filters">
            <div class="filter-grid">
                <div class="filter-item">
                    <strong>Year:</strong>
                    <span><?php echo !empty($selected_year) ? htmlspecialchars($selected_year) : 'All Years'; ?></span>
                </div>
                
                <div class="filter-item">
                    <strong>As of:</strong>
                    <span><?php echo $current_date; ?></span>
                </div>
                
                <div class="filter-item">
                    <strong>Division:</strong>
                    <span><?php echo !empty($selected_division) ? htmlspecialchars($selected_division) : 'All Divisions'; ?></span>
                </div>
                
                <?php if(!empty($selected_project)): 
                    $project_name = '';
                    foreach($projects as $p) {
                        if($p['id'] == $selected_project) {
                            $project_name = $p['project_name'];
                            break;
                        }
                    }
                ?>
                <div class="filter-item">
                    <strong>Core Area:</strong>
                    <span><?php echo htmlspecialchars($project_name); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($selected_status)): ?>
                <div class="filter-item">
                    <strong>Status:</strong>
                    <span><?php echo ucfirst(str_replace('_', ' ', $selected_status)); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($selected_priority)): ?>
                <div class="filter-item">
                    <strong>Priority:</strong>
                    <span><?php echo ucfirst($selected_priority); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Core Areas and Commitments Table -->
        <?php
        // Group tasks by project (core area)
        $tasks_by_project = [];
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
        
        if(empty($tasks)): ?>
            <div style="text-align: center; padding: 50px; background-color: #f8f9fa; border-radius: 5px;">
                <h3>No commitments found matching the selected filters.</h3>
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
                            <th class="col-commitment">Commitment / Task</th>
                            <th class="col-unit">Unit</th>
                            <th class="col-target">Target</th>
                            <th class="col-progress">Prog</th>
                            <th class="col-status">Status</th>
                            <th class="col-priority">Pri</th>
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
                                $status_text = 'In Prog';
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
                                <div class="commitment-details">
                                    <?php echo htmlspecialchars(substr($task['task_details'], 0, 60)); ?>
                                    <?php if(strlen($task['task_details']) > 60) echo '...'; ?>
                                </div>
                            </td>
                            <td>
                                <ul class="unit-list">
                                    <?php foreach(array_slice($unit_array, 0, 2) as $unit): ?>
                                    <li><?php echo htmlspecialchars(substr($unit, 0, 20) . (strlen($unit) > 20 ? '...' : '')); ?></li>
                                    <?php endforeach; ?>
                                    <?php if(count($unit_array) > 2): ?>
                                    <li>+<?php echo count($unit_array) - 2; ?> more</li>
                                    <?php endif; ?>
                                </ul>
                            </td>
                            <td><?php echo htmlspecialchars(substr($task['target_completion_date'] ?? '—', 0, 10)); ?></td>
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
                                    <?php echo strtoupper(substr($task['priority'] ?? 'MED', 0, 3)); ?>
                                </span>
                            </td>
                            <td><?php echo $last_update; ?></td>
                            <td class="remarks-cell">
                                <?php if(!empty($status_history)): ?>
                                <ul class="remarks-list">
                                    <?php foreach(array_slice($status_history, 0, 3) as $history): ?>
                                    <li class="remarks-item">
                                        <span class="remarks-percentage"><?php echo $history['percentage'] ?? 0; ?>%</span> - 
                                        <span class="remarks-text"><?php echo htmlspecialchars(substr($history['remarks'] ?? 'No remarks', 0, 40)); ?><?php echo strlen($history['remarks'] ?? '') > 40 ? '...' : ''; ?></span>
                                        <div class="remarks-meta">
                                            <?php 
                                            if(isset($history['created_at'])) {
                                                echo date('M d, Y', strtotime($history['created_at']));
                                            }
                                            ?> by <?php echo htmlspecialchars(substr($history['updated_by_name'] ?? 'Unknown', 0, 10)); ?>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                    <?php if(count($status_history) > 3): ?>
                                    <li>+<?php echo count($status_history) - 3; ?> more updates</li>
                                    <?php endif; ?>
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
                <h3 style="margin-bottom: 10px; font-size: 14px;">Report Summary</h3>
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
        <?php endif; ?>
        
        <div class="report-footer">
            <p>Report generated on <?php echo date('F d, Y \a\t h:i A'); ?></p>
            <p>PGS Monitoring System - Schools Division Office of San Fernando, Pampanga</p>
        </div>
    </div>
</body>
</html>