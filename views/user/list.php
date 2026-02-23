<?php require_once 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>User Management</h2>
    </div>
    <div class="col-md-4 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-plus-circle"></i> Create New User
        </button>
    </div>
</div>

<?php if(isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php 
    echo $_SESSION['success']; 
    unset($_SESSION['success']);
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php 
    echo $_SESSION['error']; 
    unset($_SESSION['error']);
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5>User List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Division</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $user['role'] == 'admin' ? 'danger' : 
                                    ($user['role'] == 'encoder' ? 'info' : 
                                    ($user['role'] == 'supervisor' ? 'warning' : 'secondary')); 
                            ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if($user['functional_division']): ?>
                                <span class="badge bg-<?php 
                                    echo $user['functional_division'] == 'OSDS' ? 'primary' : 
                                        ($user['functional_division'] == 'CID' ? 'success' : 'info'); 
                                ?>">
                                    <?php echo $user['functional_division']; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $user['status'] == 'active' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'Never'; ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editUserModal<?php echo $user['id']; ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                
                                <?php if($user['id'] != $_SESSION['user_id']): ?>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal<?php echo $user['id']; ?>">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                                <?php endif; ?>
                            </div>

                            <!-- Edit User Modal -->
                            <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit User: <?php echo htmlspecialchars($user['username']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="index.php?action=update_user" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_username<?php echo $user['id']; ?>" class="form-label">Username <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="edit_username<?php echo $user['id']; ?>" 
                                                               name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_password<?php echo $user['id']; ?>" class="form-label">New Password</label>
                                                        <input type="password" class="form-control" id="edit_password<?php echo $user['id']; ?>" 
                                                               name="password" placeholder="Leave blank to keep current">
                                                        <small class="text-muted">Only fill if changing password</small>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_full_name<?php echo $user['id']; ?>" class="form-label">Full Name</label>
                                                        <input type="text" class="form-control" id="edit_full_name<?php echo $user['id']; ?>" 
                                                               name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_email<?php echo $user['id']; ?>" class="form-label">Email</label>
                                                        <input type="email" class="form-control" id="edit_email<?php echo $user['id']; ?>" 
                                                               name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_role<?php echo $user['id']; ?>" class="form-label">Role <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="edit_role<?php echo $user['id']; ?>" 
                                                                name="role" required onchange="toggleEditDivision(<?php echo $user['id']; ?>)">
                                                            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                            <option value="encoder" <?php echo $user['role'] == 'encoder' ? 'selected' : ''; ?>>Encoder</option>
                                                            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                                            <option value="supervisor" <?php echo $user['role'] == 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3" id="edit_division_field<?php echo $user['id']; ?>" 
                                                         style="<?php echo $user['role'] == 'encoder' ? 'display: block;' : 'display: none;'; ?>">
                                                        <label for="edit_functional_division<?php echo $user['id']; ?>" class="form-label">Functional Division</label>
                                                        <select class="form-select" id="edit_functional_division<?php echo $user['id']; ?>" 
                                                                name="functional_division">
                                                            <option value="">Select Division</option>
                                                            <option value="OSDS" <?php echo ($user['functional_division'] ?? '') == 'OSDS' ? 'selected' : ''; ?>>OSDS</option>
                                                            <option value="CID" <?php echo ($user['functional_division'] ?? '') == 'CID' ? 'selected' : ''; ?>>CID</option>
                                                            <option value="SGOD" <?php echo ($user['functional_division'] ?? '') == 'SGOD' ? 'selected' : ''; ?>>SGOD</option>
                                                        </select>
                                                        <small class="text-muted">Required for encoder role</small>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_status<?php echo $user['id']; ?>" class="form-label">Status</label>
                                                        <select class="form-select" id="edit_status<?php echo $user['id']; ?>" name="status">
                                                            <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                                            <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning">Update User</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal<?php echo $user['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirm Delete</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete user <strong><?php echo htmlspecialchars($user['username']); ?></strong>?</p>
                                            <p class="text-danger">This action cannot be undone!</p>
                                        </div>
                                        <div class="modal-footer">
                                            <form action="index.php?action=delete_user" method="POST">
                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Delete User</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Create New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=store_user" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_username" name="username" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="create_password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="create_password" name="password" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="create_full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="create_full_name" name="full_name">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="create_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="create_email" name="email">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="create_role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_role" name="role" required onchange="toggleCreateDivision()">
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="encoder">Encoder</option>
                                <option value="user">User</option>
                                <option value="supervisor">Supervisor</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3" id="create_division_field" style="display: none;">
                            <label for="create_functional_division" class="form-label">Functional Division</label>
                            <select class="form-select" id="create_functional_division" name="functional_division">
                                <option value="">Select Division</option>
                                <option value="OSDS">OSDS</option>
                                <option value="CID">CID</option>
                                <option value="SGOD">SGOD</option>
                            </select>
                            <small class="text-muted">Required for encoder role</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="create_status" class="form-label">Status</label>
                            <select class="form-select" id="create_status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle division field for create modal
function toggleCreateDivision() {
    var role = document.getElementById('create_role').value;
    var divisionField = document.getElementById('create_division_field');
    
    if(role === 'encoder') {
        divisionField.style.display = 'block';
        document.getElementById('create_functional_division').required = true;
    } else {
        divisionField.style.display = 'none';
        document.getElementById('create_functional_division').required = false;
    }
}

// Toggle division field for edit modals
function toggleEditDivision(userId) {
    var role = document.getElementById('edit_role' + userId).value;
    var divisionField = document.getElementById('edit_division_field' + userId);
    
    if(role === 'encoder') {
        divisionField.style.display = 'block';
        document.getElementById('edit_functional_division' + userId).required = true;
    } else {
        divisionField.style.display = 'none';
        document.getElementById('edit_functional_division' + userId).required = false;
    }
}
</script>

<?php require_once 'views/layout/footer.php'; ?>