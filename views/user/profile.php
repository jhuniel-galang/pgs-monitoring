<?php require_once 'views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>User Profile</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Username:</th>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                    </tr>
                    <tr>
                        <th>Full Name:</th>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Role:</th>
                        <td><?php echo ucfirst($user['role']); ?></td>
                    </tr>
                    <tr>
                        <th>Last Login:</th>
                        <td><?php echo date('F d, Y H:i:s', strtotime($user['last_login'])); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>