<!DOCTYPE html>
<html lang="en">
    <?php
        // Include authentication protection
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        
        // Include RBAC protection (Admin only)
        require_once __DIR__ . '/middleware/RBACProtect.php';

        include 'template/header.php';
        require_once __DIR__ . '/../Model/User.php';
        require_once __DIR__ . '/../Model/Role.php';
        
        // Initialize models
        $userModel = new User();
        $roleModel = new Role();
        
        // Get all users and roles
        $users = $userModel->getAll();
        $showRole = $roleModel->getAll();
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
                                <h1 class="mt-4">User</h1>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="dashboard.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active">User</li>
                                </ol>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                                    <i class="fas fa-plus"></i> Add New User
                                </button>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-user-graduate me-1"></i>
                                    User List
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Full Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                
                                        <?php if ($users && count($users) > 0): ?>
                                            <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                                                    <td>
                                                        <span class="badge <?php echo $user['status'] === 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                                            <?php echo htmlspecialchars($user['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info me-1 toggle-status-btn" title="Toggle Status">
                                                            <i class="fas fa-power-off"></i> Toggle
                                                        </button>
                                                        <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#updateUserModal">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                                            <i class="fas fa-trash-alt"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox me-2"></i>No users found. Click "Add New User" to create one.
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

        <!-- Create User Modal -->
        <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createUserModalLabel"><i class="fas fa-user"></i>Create New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createUserForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="fullname" name="fullname" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="role_id" name="role_id" required>
                                    <option value="">Select Role</option>
                                    <?php // if ($roles && count($roles) > 0): ?>
                                        <?php foreach ($showRole as $role): ?>
                                            <option value="<?php echo $role['role_id']; ?>">
                                                <?php echo $role['role_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php // endif; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status_create" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status_create" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update User Modal -->
        <div class="modal fade" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateUserModalLabel"><i class="fas fa-user-edit me-2"></i>Update User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateUserForm">
                        <div class="modal-body">
                            <input type="hidden" id="user_id" name="user_id">
                            <div class="mb-3">
                                <label for="fullname_edit" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="fullname_edit" name="fullname" required>
                            </div>
                            <div class="mb-3">
                                <label for="username_edit" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username_edit" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email_edit" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email_edit" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="role_id_edit" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="role_id_edit" name="role_id" required>
                                    <option value="">Select Role</option>
                                    <?//if ($showRole && count($roles) > 0): ?>
                                        <?php foreach ($showRole as $role): ?>
                                            <option value="<?php echo $role['role_id']; ?>">
                                                <?php echo htmlspecialchars($role['role_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php //endif; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status_edit" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status_edit" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete User Modal -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="deleteUserModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Delete User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteUserForm">
                        <div class="modal-body">
                            <input type="hidden" id="delete_user_id" name="user_id">
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this user?
                            </div>
                            <div class="mb-3">
                                <label for="delete_username" class="form-label">User to Delete:</label>
                                <input type="text" class="form-control" id="delete_username" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_delete" class="form-label">Type the username to confirm: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="confirm_delete" name="confirm_delete" placeholder="Type username here" required>
                                <small class="text-muted">This is to prevent accidental deletion.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Delete User</button>
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
            const API_URL = '../../App/Controller/UserController.php';

            /**
             * Create User Form Submission
             */
            document.getElementById('createUserForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const fullname = document.getElementById('fullname').value.trim();
                const username = document.getElementById('username').value.trim();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value.trim();
                const roleId = document.getElementById('role_id').value;
                const status = document.getElementById('status_create').value;

                // Validation
                if (!fullname || !username || !email || !password || !roleId || !status) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please fill all required fields.',
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
                            fullname: fullname,
                            username: username,
                            email: email,
                            password: password,
                            role_id: roleId,
                            status: status
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.message,
                            confirmButtonColor: '#6ec207'
                        }).then(() => {
                            document.getElementById('createUserForm').reset();
                            const modal = bootstrap.Modal.getInstance(document.getElementById('createUserModal'));
                            modal.hide();
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
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
             * Update User Form Submission
             */
            document.getElementById('updateUserForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const userId = document.getElementById('user_id').value;
                const fullname = document.getElementById('fullname_edit').value.trim();
                const username = document.getElementById('username_edit').value.trim();
                const email = document.getElementById('email_edit').value.trim();
                const roleId = document.getElementById('role_id_edit').value;
                const status = document.getElementById('status_edit').value;

                if (!userId || !fullname || !username || !email || !roleId || !status) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please fill all required fields.',
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
                            user_id: userId,
                            fullname: fullname,
                            username: username,
                            email: email,
                            role_id: roleId,
                            status: status
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: result.message,
                            confirmButtonColor: '#6ec207'
                        }).then(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('updateUserModal'));
                            modal.hide();
                            setTimeout(() => {
                                location.reload();
                            }, 500);
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
             * Delete User Form Submission
             */
            document.getElementById('deleteUserForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const userId = document.getElementById('delete_user_id').value;
                const username = document.getElementById('delete_username').value;
                const confirmDelete = document.getElementById('confirm_delete').value.trim();

                if (confirmDelete !== username) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirmation Failed',
                        text: 'The username does not match. Please type the correct username.',
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
                            user_id: userId
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: result.message,
                            confirmButtonColor: '#6ec207'
                        }).then(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteUserModal'));
                            modal.hide();
                            setTimeout(() => {
                                location.reload();
                            }, 500);
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
                if (e.target.closest('button[data-bs-target="#updateUserModal"]')) {
                    const row = e.target.closest('tr');
                    const userId = row.querySelector('td:nth-child(1)').textContent;
                    const fullname = row.querySelector('td:nth-child(2)').textContent;
                    const username = row.querySelector('td:nth-child(3)').textContent;
                    const email = row.querySelector('td:nth-child(4)').textContent;
                    const roleId = row.querySelector('td:nth-child(5)').textContent;
                    const status = row.querySelector('td:nth-child(6)').textContent.trim().toLowerCase();

                    document.getElementById('user_id').value = userId;
                    document.getElementById('fullname_edit').value = fullname;
                    document.getElementById('username_edit').value = username;
                    document.getElementById('email_edit').value = email;
                    document.getElementById('role_id_edit').value = Array.from(document.getElementById('role_id_edit').options)
                        .find(opt => opt.textContent.trim() === roleId.trim())?.value || '';
                    document.getElementById('status_edit').value = status;
                }

                if (e.target.closest('button[data-bs-target="#deleteUserModal"]')) {
                    const row = e.target.closest('tr');
                    const userId = row.querySelector('td:nth-child(1)').textContent;
                    const username = row.querySelector('td:nth-child(3)').textContent;

                    document.getElementById('delete_user_id').value = userId;
                    document.getElementById('delete_username').value = username;
                    document.getElementById('confirm_delete').value = '';
                }

                // Handle Toggle Status Button
                if (e.target.closest('button.toggle-status-btn')) {
                    const row = e.target.closest('tr');
                    const userId = row.querySelector('td:nth-child(1)').textContent;
                    const currentStatus = row.querySelector('td:nth-child(6) .badge').textContent.trim().toLowerCase();
                    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';

                    Swal.fire({
                        title: 'Change User Status',
                        text: `Change status from ${currentStatus} to ${newStatus}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#6ec207',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, change it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            toggleUserStatus(userId, newStatus);
                        }
                    });
                }
            });

            /**
             * Toggle User Status
             */
            async function toggleUserStatus(userId, newStatus) {
                try {
                    const response = await fetch(API_URL + '?action=changeStatus', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            status: newStatus
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Changed!',
                            text: result.message,
                            confirmButtonColor: '#6ec207'
                        }).then(() => {
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Status Change Failed',
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
                }
            }

            /**
             * Clear forms when modals are hidden
             */
            document.getElementById('createUserModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('createUserForm').reset();
            });

            document.getElementById('updateUserModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('updateUserForm').reset();
            });

            document.getElementById('deleteUserModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('deleteUserForm').reset();
            });
        </script>
    </body>
</html>