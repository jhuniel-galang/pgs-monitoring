<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PGS Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        /* Navigation bar styles with centered logos - Light Blue */
        .navbar {
            padding: 10px 0;
            background-color: #5dade2 !important; /* Light blue color */
        }
        
        /* Override Bootstrap's navbar-dark class */
        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .navbar-dark .navbar-nav .nav-link:hover {
            color: #fff;
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            padding: 0;
            margin: 0 auto;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .navbar-brand .logo-group {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .navbar-brand .logo-item {
            display: flex;
            align-items: center;
        }
        
        .navbar-brand .logo-item img {
            max-height: 45px;
            width: auto;
            object-fit: contain;
        }
        
        /* Ensure the toggler and nav items don't overlap with centered logos */
        .navbar-toggler {
            position: relative;
            z-index: 10;
            background-color: rgba(255,255,255,0.2);
        }
        
        .navbar-collapse {
            position: relative;
            z-index: 5;
        }
        
        /* Black login button */
        .btn-black {
            background-color: #2c3e50 !important;
            color: white !important;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-black:hover {
            background-color: #1a2632 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Logout button also black */
        .btn-logout {
            background-color: #2c3e50 !important;
            color: white !important;
            border: none;
        }
        
        .btn-logout:hover {
            background-color: #1a2632 !important;
        }
        
        @media (max-width: 991px) {
            .navbar-brand {
                position: static;
                transform: none;
                margin: 0 auto;
            }
            .navbar-brand .logo-group {
                gap: 12px;
            }
            .navbar-brand .logo-item img {
                max-height: 35px;
            }
        }
        
        @media (max-width: 768px) {
            .navbar-brand .logo-group {
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
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

        /* Alert styling */
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.9rem;
        }

        .alert-danger i {
            font-size: 1rem;
        }

        /* Form input focus styling */
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Login button hover effect - removed old style */
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #5dade2;">
        <div class="container-fluid position-relative">
            <!-- Logo Group - Centered -->
            <a class="navbar-brand" href="#">
                <div class="logo-group">
                    <div class="logo-item">
                        <img src="assets/images/Seal_of_the_Department_of_Education_of_the_Philippines.png" alt="DepEd Logo">
                    </div>
                    <div class="logo-item">
                        <img src="assets/images/DepEd RO3 New Logo 50th.png" alt="DepEd RO3 Logo">
                    </div>
                    <div class="logo-item">
                        <img src="assets/images/DepEd CSFP.png" alt="SDO Logo">
                    </div>
                    <div class="logo-item">
                        <img src="assets/images/ISA LOGO.png" alt="ISA Logo">
                    </div>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>

                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=projects">Core Area</a>
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
                        <a class="nav-link btn btn-logout btn-sm text-white" href="index.php?action=logout">Logout</a>
                    </li>
                </ul>
                <?php else: ?>
                <!-- Public navigation - black login button -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button type="button" class="btn btn-black btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">
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
                        <?php if(isset($error) && !empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
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