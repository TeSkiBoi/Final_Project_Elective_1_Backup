<!DOCTYPE html>
<html lang="en">
    <?php 
        // Include authentication protection
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        // Include RBAC protection (Admin only)
        require_once __DIR__ . '/middleware/RBACProtect.php';
        
        include 'template/header.php';
        require_once __DIR__ . '/../Model/Faculty.php';
        
        // // Initialize Faculty model
        $FacultyModel = new Faculty();
        $Faculties = $FacultyModel->getAllFaculty();
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
                                <h1 class="mt-4">Faculty</h1>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="dashboard.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Faculty</li>
                                </ol>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFacultyModal">
                                    <i class="fas fa-plus"></i> Add New Faculty
                                </button>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-user-graduate me-1"></i>
                                    Faculty List
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Faculty ID</th>
                                            <th>Faculty Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($Faculties as $faculty): ?>
                                        <tr>
                                            <td><?php echo $faculty['faculty_id']; ?></td>
                                            <td><?php echo $faculty['faculty_name']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editFacultyModal">
                                                    <i class="fas fa-edit"></i> Edit
                                                    
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteFacultyModal">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
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


        <!-- Create Faculty Modal -->
        <div class="modal fade" id="createFacultyModal" tabindex="-1" aria-labelledby="createFacultyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createFacultyModalLabel">Create New Faculty</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createFacultyForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="createFacultyId" class="form-label">Faculty ID</label>
                                <input type="text" class="form-control" id="faculty_id" name="faculty_id" placeholder="Enter Faculty ID" required>
                            </div>
                            <div class="mb-3">
                                <label for="createFacultyName" class="form-label">Faculty Name</label>
                                <input type="text" class="form-control" id="faculty_name" name="faculty_name" placeholder="Enter Faculty Name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Faculty
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Faculty Modal -->
        <div class="modal fade" id="editFacultyModal" tabindex="-1" aria-labelledby="editFacultyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFacultyModalLabel">Edit Faculty</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editFacultyForm">
                        <div class="modal-body">
                            <input type="hidden" id="editFacultyIdHidden" name="faculty_id_hidden">
                            <div class="mb-3">
                                <label for="editFacultyId" class="form-label">Faculty ID</label>
                                <input type="text" disabled class="form-control" id="editFacultyId" name="faculty_id" placeholder="Enter Faculty ID" required>
                            </div>
                            <div class="mb-3">
                                <label for="editFacultyName" class="form-label">Faculty Name</label>
                                <input type="text" class="form-control" id="editFacultyName" name="faculty_name" placeholder="Enter Faculty Name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Faculty
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Faculty Modal -->
        <div class="modal fade" id="deleteFacultyModal" tabindex="-1" aria-labelledby="deleteDepartmentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="deleteFacultyModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Delete Faculty</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteFacultyForm">
                        <div class="modal-body">
                            <input type="hidden" id="delete_faculty_id" name="faculty_id">
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this faculty?
                            </div>
                            <div class="mb-3">
                                <label for="delete_faculty_name" class="form-label">Faculty to Delete:</label>
                                <input type="text" class="form-control" id="delete_faculty_name" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_delete_faculty" class="form-label">Type the faculty name to confirm: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="confirm_delete_faculty" name="confirm_delete" placeholder="Type faculty name here" required>
                                <small class="text-muted">This is to prevent accidental deletion.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Delete Faculty</button>
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
            const API_URL = '../../App/Controller/FacultyController.php';

            /**
             * Create Faculty Form Submission
             */
            document.getElementById('createFacultyForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const facultyId = document.getElementById('faculty_id').value.trim();
                const facultyName = document.getElementById('faculty_name').value.trim();
                // Validation
                if (!facultyId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter a Faculty ID.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!facultyName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter a Faculty Name.',
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
                            faculty_id: facultyId,  //DATABASE : FORM
                            faculty_name: facultyName
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
                                document.getElementById('createFacultyForm').reset();

                                // Close modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById('createFacultyModal'));
                                modal.hide();

                                // Reload page to show new faculty
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
            document.getElementById('editFacultyForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const facultyId = document.getElementById('editFacultyId').value;
                const facultyName = document.getElementById('editFacultyName').value.trim();
                if (!facultyId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Faculty ID is missing.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!facultyName) {
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
                            faculty_id: facultyId,
                            faculty_name: facultyName
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
                                const modal = bootstrap.Modal.getInstance(document.getElementById('editFacultyModal'));
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
            document.getElementById('deleteFacultyForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const facultyId = document.getElementById('delete_faculty_id').value;
                const facultyName = document.getElementById('delete_faculty_name').value;
                const confirmDelete = document.getElementById('confirm_delete_faculty').value.trim();

                if (confirmDelete !== facultyName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirmation Failed',
                        text: 'The faculty name does not match. Please type the correct name.',
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
                            faculty_id: facultyId
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
                                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteFacultyModal'));
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
                if (e.target.closest('button[data-bs-target="#editFacultyModal"]')) {
                    const row = e.target.closest('tr');
                    const facultyId = row.querySelector('td:first-child').textContent;
                    const facultyName = row.querySelector('td:nth-child(2)').textContent;

                    document.getElementById('editFacultyIdHidden').value = facultyId;
                    document.getElementById('editFacultyId').value = facultyId;
                    document.getElementById('editFacultyName').value = facultyName;
                }

                if (e.target.closest('button[data-bs-target="#deleteFacultyModal"]')) {
                    const row = e.target.closest('tr');
                    const facultyId = row.querySelector('td:first-child').textContent;
                    const facultyName = row.querySelector('td:nth-child(2)').textContent;

                    document.getElementById('delete_faculty_id').value = facultyId;
                    document.getElementById('delete_faculty_name').value = facultyName;
                    document.getElementById('confirm_delete_faculty').value = '';
                }
            });

            /**
             * Clear forms when modals are hidden
             */
            document.getElementById('createFacultyModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('createFacultyForm').reset();
            });

            document.getElementById('editFacultyModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('editFacultyForm').reset();
            });

            document.getElementById('deleteFacultyModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('deleteFacultyForm').reset();
            });
        </script>
       
    </body>
</html>