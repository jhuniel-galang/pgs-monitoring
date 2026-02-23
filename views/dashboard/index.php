<?php require_once 'views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2>Dashboard</h2>
        <div class="alert alert-success">
            Welcome to PGS Monitoring System, <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>!
        </div>
    </div>
</div>


<?php require_once 'views/layout/footer.php'; ?>