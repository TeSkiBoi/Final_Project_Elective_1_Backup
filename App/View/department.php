<!DOCTYPE html>
<html lang="en">
    <?php 
        // Include authentication protection
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        // Include RBAC protection (Admin only)
        require_once __DIR__ . '/middleware/RBACProtect.php';
        
        include 'template/header.php';
        require_once __DIR__ . '/../Model/Department.php';
        
        // Initialize Department model
        $departmentModel = new Department();
        $departments = $departmentModel->getAll();
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
                                <h1 class="mt-4">Department</h1>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="dashboard.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Department</li>
                                </ol>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                                    <i class="fas fa-plus"></i> Add New Department
                                </button>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-user-graduate me-1"></i>
                                    Department List
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Department ID</th>
                                            <th>Department Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($departments && count($departments) > 0): ?>
                                            <?php foreach ($departments as $dept): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($dept['department_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($dept['department_name']); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#updateDepartmentModal">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDepartmentModal">
                                                            <i class="fas fa-trash-alt"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox me-2"></i>No departments found. Click "Add New Department" to create one.
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

        <!-- Create Department Modal -->
        <div class="modal fade" id="createDepartmentModal" tabindex="-1" aria-labelledby="createDepartmentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createDepartmentModalLabel"><i class="fas fa-building me-2"></i>Create New Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createDepartmentForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="department_id" class="form-label">Department ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="department_id" name="department_id" placeholder="e.g., D001" required>
                            </div>
                            <div class="mb-3">
                                <label for="department_name" class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="department_name" name="department_name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create Department</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Department Modal -->
        <div class="modal fade" id="updateDepartmentModal" tabindex="-1" aria-labelledby="updateDepartmentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateDepartmentModalLabel"><i class="fas fa-building me-2"></i>Update Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateDepartmentForm">
                        <div class="modal-body">
                            <input type="hidden" id="department_id_edit" name="department_id">
                            <div class="mb-3">
                                <label for="department_id_display" class="form-label">Department ID</label>
                                <input type="text" class="form-control" id="department_id_display" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="department_name_edit" class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="department_name_edit" name="department_name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Department</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Department Modal -->
        <div class="modal fade" id="deleteDepartmentModal" tabindex="-1" aria-labelledby="deleteDepartmentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="deleteDepartmentModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Delete Department</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteDepartmentForm">
                        <div class="modal-body">
                            <input type="hidden" id="delete_department_id" name="department_id">
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this department?
                            </div>
                            <div class="mb-3">
                                <label for="delete_department_name" class="form-label">Department to Delete:</label>
                                <input type="text" class="form-control" id="delete_department_name" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_delete_department" class="form-label">Type the department name to confirm: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="confirm_delete_department" name="confirm_delete" placeholder="Type department name here" required>
                                <small class="text-muted">This is to prevent accidental deletion.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Delete Department</button>
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
            const API_URL = '../../App/Controller/DepartmentController.php';

            /**
             * Create Department Form Submission
             */
            document.getElementById('createDepartmentForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const departmentId = document.getElementById('department_id').value.trim();
                const departmentName = document.getElementById('department_name').value.trim();

                // Validation
                if (!departmentId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter a Department ID.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!departmentName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter a Department Name.',
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
                            department_id: departmentId,
                            department_name: departmentName
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
                                document.getElementById('createDepartmentForm').reset();

                                // Close modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById('createDepartmentModal'));
                                modal.hide();

                                // Reload page to show new department
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
             * Update Department Form Submission
             */
            document.getElementById('updateDepartmentForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const departmentId = document.getElementById('department_id_edit').value;
                const departmentName = document.getElementById('department_name_edit').value.trim();

                if (!departmentId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Department ID is missing.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!departmentName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter a department name.',
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
                            department_id: departmentId,
                            department_name: departmentName
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
                                const modal = bootstrap.Modal.getInstance(document.getElementById('updateDepartmentModal'));
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
             * Delete Department Form Submission
             */
            document.getElementById('deleteDepartmentForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const departmentId = document.getElementById('delete_department_id').value;
                const departmentName = document.getElementById('delete_department_name').value;
                const confirmDelete = document.getElementById('confirm_delete_department').value.trim();

                if (confirmDelete !== departmentName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirmation Failed',
                        text: 'The department name does not match. Please type the correct name.',
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
                            department_id: departmentId
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
                                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteDepartmentModal'));
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
                if (e.target.closest('button[data-bs-target="#updateDepartmentModal"]')) {
                    const row = e.target.closest('tr');
                    const departmentId = row.querySelector('td:first-child').textContent;
                    const departmentName = row.querySelector('td:nth-child(2)').textContent;

                    document.getElementById('department_id_edit').value = departmentId;
                    document.getElementById('department_id_display').value = departmentId;
                    document.getElementById('department_name_edit').value = departmentName;
                }

                if (e.target.closest('button[data-bs-target="#deleteDepartmentModal"]')) {
                    const row = e.target.closest('tr');
                    const departmentId = row.querySelector('td:first-child').textContent;
                    const departmentName = row.querySelector('td:nth-child(2)').textContent;

                    document.getElementById('delete_department_id').value = departmentId;
                    document.getElementById('delete_department_name').value = departmentName;
                    document.getElementById('confirm_delete_department').value = '';
                }
            });

            /**
             * Clear forms when modals are hidden
             */
            document.getElementById('createDepartmentModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('createDepartmentForm').reset();
            });

            document.getElementById('updateDepartmentModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('updateDepartmentForm').reset();
            });

            document.getElementById('deleteDepartmentModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('deleteDepartmentForm').reset();
            });
        </script>
    </body>
</html>