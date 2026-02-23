<?php require_once 'views/layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Unit Management</h2>
    </div>
    <div class="col-md-4 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUnitModal">
            <i class="bi bi-plus-circle"></i> Create New Unit
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

<!-- Division Summary Cards -->
<div class="row mb-4">
    <?php foreach($division_summary as $summary): ?>
    <div class="col-md-4">
        <div class="card text-white bg-<?php 
            echo $summary['functional_division'] == 'OSDS' ? 'primary' : 
                ($summary['functional_division'] == 'CID' ? 'success' : 'info'); 
        ?>">
            <div class="card-header"><?php echo $summary['functional_division']; ?> Division</div>
            <div class="card-body">
                <h5 class="card-title">Total Units: <?php echo $summary['total_units']; ?></h5>
                <p class="card-text">
                    Active Units: <?php echo $summary['active_units']; ?>
                </p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Units Table -->
<div class="card">
    <div class="card-header">
        <h5>Unit List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Division</th>
                        <th>Unit Name</th>
                        <th>Person In Charge</th>
                        <th>Designation</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($units as $unit): ?>
                    <tr>
                        <td><?php echo $unit['id']; ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $unit['functional_division'] == 'OSDS' ? 'primary' : 
                                    ($unit['functional_division'] == 'CID' ? 'success' : 'info'); 
                            ?>">
                                <?php echo $unit['functional_division']; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($unit['unit_name']); ?></td>
                        <td><?php echo htmlspecialchars($unit['person_in_charge'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($unit['designation'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($unit['email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($unit['contact_number'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $unit['status'] == 'active' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($unit['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editUnitModal<?php echo $unit['id']; ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteUnitModal<?php echo $unit['id']; ?>">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>

                            <!-- Edit Unit Modal -->
                            <div class="modal fade" id="editUnitModal<?php echo $unit['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit Unit: <?php echo htmlspecialchars($unit['unit_name']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="index.php?action=update_unit" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $unit['id']; ?>">
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_division<?php echo $unit['id']; ?>" class="form-label">Division <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="edit_division<?php echo $unit['id']; ?>" 
                                                                name="functional_division" required>
                                                            <option value="OSDS" <?php echo $unit['functional_division'] == 'OSDS' ? 'selected' : ''; ?>>OSDS</option>
                                                            <option value="CID" <?php echo $unit['functional_division'] == 'CID' ? 'selected' : ''; ?>>CID</option>
                                                            <option value="SGOD" <?php echo $unit['functional_division'] == 'SGOD' ? 'selected' : ''; ?>>SGOD</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_unit_name<?php echo $unit['id']; ?>" class="form-label">Unit Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="edit_unit_name<?php echo $unit['id']; ?>" 
                                                               name="unit_name" value="<?php echo htmlspecialchars($unit['unit_name']); ?>" required>
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_pic<?php echo $unit['id']; ?>" class="form-label">Person In Charge</label>
                                                        <input type="text" class="form-control" id="edit_pic<?php echo $unit['id']; ?>" 
                                                               name="person_in_charge" value="<?php echo htmlspecialchars($unit['person_in_charge'] ?? ''); ?>">
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_designation<?php echo $unit['id']; ?>" class="form-label">Designation</label>
                                                        <input type="text" class="form-control" id="edit_designation<?php echo $unit['id']; ?>" 
                                                               name="designation" value="<?php echo htmlspecialchars($unit['designation'] ?? ''); ?>">
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_email<?php echo $unit['id']; ?>" class="form-label">Email</label>
                                                        <input type="email" class="form-control" id="edit_email<?php echo $unit['id']; ?>" 
                                                               name="email" value="<?php echo htmlspecialchars($unit['email'] ?? ''); ?>">
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_contact<?php echo $unit['id']; ?>" class="form-label">Contact Number</label>
                                                        <input type="text" class="form-control" id="edit_contact<?php echo $unit['id']; ?>" 
                                                               name="contact_number" value="<?php echo htmlspecialchars($unit['contact_number'] ?? ''); ?>">
                                                    </div>
                                                    
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_status<?php echo $unit['id']; ?>" class="form-label">Status</label>
                                                        <select class="form-select" id="edit_status<?php echo $unit['id']; ?>" name="status">
                                                            <option value="active" <?php echo $unit['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                                            <option value="inactive" <?php echo $unit['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning">Update Unit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Unit Modal -->
                            <div class="modal fade" id="deleteUnitModal<?php echo $unit['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirm Delete</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete unit <strong><?php echo htmlspecialchars($unit['unit_name']); ?></strong>?</p>
                                            <p class="text-danger">This action cannot be undone!</p>
                                        </div>
                                        <div class="modal-footer">
                                            <form action="index.php?action=delete_unit" method="POST">
                                                <input type="hidden" name="id" value="<?php echo $unit['id']; ?>">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Delete Unit</button>
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

<!-- Create Unit Modal -->
<div class="modal fade" id="createUnitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Create New Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=store_unit" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_division" class="form-label">Division <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_division" name="functional_division" required>
                                <option value="">Select Division</option>
                                <option value="OSDS">OSDS</option>
                                <option value="CID">CID</option>
                                <option value="SGOD">SGOD</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="create_unit_name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_unit_name" name="unit_name" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="create_pic" class="form-label">Person In Charge</label>
                            <input type="text" class="form-control" id="create_pic" name="person_in_charge">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="create_designation" class="form-label">Designation</label>
                            <input type="text" class="form-control" id="create_designation" name="designation">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="create_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="create_email" name="email">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="create_contact" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="create_contact" name="contact_number">
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
                    <button type="submit" class="btn btn-primary">Create Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>