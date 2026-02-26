<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PGS Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php?action=dashboard">PGS Monitoring</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=dashboard">Dashboard</a>
                    </li>
                    
                    <!-- Tasks link - visible to ALL logged-in users -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=tasks">Commitments</a>
                    </li>
                    
                    <!-- Admin only links -->
                    <?php if($_SESSION['role'] == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=users">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=units">Units</a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item">
    <a class="nav-link" href="index.php?action=projects">Core Area</a>
</li>
                    
                    <!-- Profile link for all users -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=profile">Profile</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">
                            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 
                            (<?php echo ucfirst($_SESSION['role']); ?>)
                            <?php if($_SESSION['role'] == 'encoder'): ?>
                                - <?php echo $_SESSION['functional_division']; ?>
                            <?php endif; ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger btn-sm text-white" href="index.php?action=logout">Logout</a>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container mt-4">