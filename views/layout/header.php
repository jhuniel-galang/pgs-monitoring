<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PGS Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .navbar-brand {
            display: flex;
            align-items: center;
            padding: 0;
            margin-right: 2rem;
        }
        .navbar-brand img {
            max-height: 50px;
            width: auto;
            margin-right: 10px;
        }
        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        .brand-text .top {
            font-size: 0.8rem;
            color: #ffc107;
        }
        .brand-text .bottom {
            font-size: 0.7rem;
            color: #adb5bd;
        }
        @media (max-width: 991px) {
            .navbar-brand {
                margin-right: 0;
            }
        }
        
        /* Public view styles - disable interactions */
        .public-view .btn:not(.carousel-control):not(.modal .btn),
        .public-view a:not(.navbar-brand):not(.modal a),
        .public-view button:not(.carousel-control):not(.modal button) {
            pointer-events: none;
            opacity: 0.6;
            cursor: default;
        }
        
        .public-view .card {
            user-select: none;
        }
        
        .public-view .indicator {
            cursor: pointer !important;
            pointer-events: auto !important;
        }
        
        .public-view .carousel-control {
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        
        /* Project carousel styles */
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php?action=login">
                <img src="assets/images/5.jpeg" alt="SDO Logo">
                <div class="brand-text">
                    <span class="top">SCHOOLS DIVISION OFFICE</span>
                    <span class="bottom">CITY OF SAN FERNANDO, PAMPANGA</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <!-- Logged in navigation -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=tasks">Commitments</a>
                    </li>
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=profile">Profile</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">
                            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 
                            (<?php echo ucfirst($_SESSION['role']); ?>)
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger btn-sm text-white" href="index.php?action=logout">Logout</a>
                    </li>
                </ul>
                <?php else: ?>
                <!-- Public navigation - only login button -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="loginModalLabel">
                        <i class="bi bi-box-arrow-in-right"></i> Login to PGS Monitoring System
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="index.php?action=authenticate" id="loginForm">
                    <div class="modal-body">
                        <?php if(isset($error) && $error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="container mt-4">