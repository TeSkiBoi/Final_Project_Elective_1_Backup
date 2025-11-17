<!DOCTYPE html>
<html lang="en">
    <?php 
        // Include authentication protection
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        
        // Include RBAC protection (Admin only)
        require_once __DIR__ . '/middleware/RBACProtect.php';
        
        include 'template/header.php';
        require_once __DIR__ . '/../Model/Role.php';
        
        // Initialize Role model
        $roleModel = new Role();
        $roles = $roleModel->getAll();
    ?>
    
    <body class="sb-nav-fixed">

        <?php include 'template/header_navigation.php'; ?>

        <div id="layoutSidenav">
            <?php include 'template/sidebar_navigation.php'; ?>
            <!--CONTENT OF THE PAGE -->
            <div id="layoutSidenav_content">

                <!-- CONTENT HERE -->
                 <main>
                    <div class="container-fluid px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="mt-4">Role</h1>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="dashboard.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Role</li>
                                </ol>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                                    <i class="fas fa-plus"></i> Add New Role
                                </button>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-users me-1"></i>
                                    Role List
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Role ID</th>
                                            <th>Role Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                
                                        <?php if ($roles && count($roles) > 0): ?>
                                            <?php foreach ($roles as $role): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($role['role_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($role['role_name']); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#updateRoleModal">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal">
                                                            <i class="fas fa-trash-alt"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox me-2"></i>No roles found. Click "Add New Role" to create one.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <!-- END CONTENT-->
                <?php include 'template/footer.php'; ?>
            </div>
        </div>

        <!-- Create Role Modal -->
        <div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createRoleModalLabel"><i class="fas fa-user-shield me-2"></i>Create New Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createRoleForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="role_name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="role_name" name="role_name" placeholder="e.g., Admin, Staff" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Role Modal -->
        <div class="modal fade" id="updateRoleModal" tabindex="-1" aria-labelledby="updateRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateRoleModalLabel"><i class="fas fa-user-shield me-2"></i>Update Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateRoleForm">
                        <div class="modal-body">
                            <input type="hidden" id="role_id_edit" name="role_id">
                            <div class="mb-3">
                                <label for="role_id_display" class="form-label">Role ID</label>
                                <input type="text" class="form-control" id="role_id_display" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="role_name_edit" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="role_name_edit" name="role_name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Role Modal -->
        <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="deleteRoleModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Delete Role</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteRoleForm">
                        <div class="modal-body">
                            <input type="hidden" id="delete_role_id" name="role_id">
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this role?
                            </div>
                            <div class="mb-3">
                                <label for="delete_role_name" class="form-label">Role to Delete:</label>
                                <input type="text" class="form-control" id="delete_role_name" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_delete_role" class="form-label">Type the role name to confirm: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="confirm_delete_role" name="confirm_delete" placeholder="Type role name here" required>
                                <small class="text-muted">This is to prevent accidental deletion.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Delete Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Logout Modal -->
        <?php include 'template/script.php'; ?>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // API Base URL
            const API_URL = '../../App/Controller/RoleController.php';

            /**
             * Create Role Form Submission
             */
            document.getElementById('createRoleForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const roleName = document.getElementById('role_name').value.trim();

                // Validation
                if (!roleName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter a Role Name.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

                try {
                    const response = await fetch(API_URL + '?action=create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            role_name: roleName
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Success Alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.message,
                            confirmButtonColor: '#6ec207'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Reset form
                                document.getElementById('createRoleForm').reset();

                                // Close modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById('createRoleModal'));
                                modal.hide();

                                // Reload page to show new role
                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            }
                        });
                    } else {
                        // Error handling based on error type
                        let errorTitle = 'Error';
                        let errorMessage = result.message;

                        if (result.message.includes('already exists')) {
                            errorTitle = 'Duplicate Entry';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: errorTitle,
                            text: errorMessage,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Failed to connect to the server. Please check your connection and try again.',
                        confirmButtonColor: '#dc3545'
                    });
                } finally {
                    // Restore button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });

            /**
             * Update Role Form Submission
             */
            document.getElementById('updateRoleForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const roleId = document.getElementById('role_id_edit').value;
                const roleName = document.getElementById('role_name_edit').value.trim();

                if (!roleId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Role ID is missing.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!roleName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter a role name.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

                try {
                    const response = await fetch(API_URL + '?action=update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            role_id: roleId,
                            role_name: roleName
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: result.message,
                            confirmButtonColor: '#6ec207'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('updateRoleModal'));
                                modal.hide();
                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: result.message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Failed to connect to the server.',
                        confirmButtonColor: '#dc3545'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });

            /**
             * Delete Role Form Submission
             */
            document.getElementById('deleteRoleForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const roleId = document.getElementById('delete_role_id').value;
                const roleName = document.getElementById('delete_role_name').value;
                const confirmDelete = document.getElementById('confirm_delete_role').value.trim();

                if (confirmDelete !== roleName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirmation Failed',
                        text: 'The role name does not match. Please type the correct name.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

                try {
                    const response = await fetch(API_URL + '?action=delete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            role_id: roleId
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: result.message,
                            confirmButtonColor: '#6ec207'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteRoleModal'));
                                modal.hide();
                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Deletion Failed',
                            text: result.message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Failed to connect to the server.',
                        confirmButtonColor: '#dc3545'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });

            /**
             * Handle Edit Button Click - Populate Update Modal
             */
            document.addEventListener('click', function(e) {
                if (e.target.closest('button[data-bs-target="#updateRoleModal"]')) {
                    const row = e.target.closest('tr');
                    const roleId = row.querySelector('td:first-child').textContent;
                    const roleName = row.querySelector('td:nth-child(2)').textContent;

                    document.getElementById('role_id_edit').value = roleId;
                    document.getElementById('role_id_display').value = roleId;
                    document.getElementById('role_name_edit').value = roleName;
                }

                if (e.target.closest('button[data-bs-target="#deleteRoleModal"]')) {
                    const row = e.target.closest('tr');
                    const roleId = row.querySelector('td:first-child').textContent;
                    const roleName = row.querySelector('td:nth-child(2)').textContent;

                    document.getElementById('delete_role_id').value = roleId;
                    document.getElementById('delete_role_name').value = roleName;
                    document.getElementById('confirm_delete_role').value = '';
                }
            });

            /**
             * Clear forms when modals are hidden
             */
            document.getElementById('createRoleModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('createRoleForm').reset();
            });

            document.getElementById('updateRoleModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('updateRoleForm').reset();
            });

            document.getElementById('deleteRoleModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('deleteRoleForm').reset();
            });
        </script>
    </body>
</html>