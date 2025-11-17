<!DOCTYPE html>
<html lang="en">
    <?php
        // Include authentication protection
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        
        // Include RBAC protection (Admin only)
        require_once __DIR__ . '/middleware/RBACProtect.php';
        // Only role_id 1 (Admin) can access this page
        requireRole(1);
        
        include 'template/header.php';
        require_once __DIR__ . '/../Model/Course.php';
        require_once __DIR__ . '/../Model/Department.php';
        
        // // Initialize models
        $courseModel = new Course();
        $departmentModel = new Department();
        
        // // Get all courses and departments
        $courses = $courseModel->getAll();
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
                                <h1 class="mt-4">Courses</h1>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="dashboard.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Courses</li>
                                </ol>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                                    <i class="fas fa-plus"></i> Add New Course
                                </button>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-book me-1"></i>
                                    Course List
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Course ID</th>
                                            <th>Course Code</th>
                                            <th>Course Name</th>
                                            <th>Units</th>
                                            <th>Department</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="courseTableBody">
                                    <?php if($courses && count($courses) > 0): ?>
                                        <?php foreach ($courses as $course): ?>
                                        <tr>
                                            <td><?php echo ($course['course_id']); ?></td>
                                            <td><?php echo ($course['course_code']); ?></td>
                                            <td><?php echo ($course['course_name']); ?></td>
                                            <td><?php echo ($course['units']); ?></td>
                                            <td><?php echo ($course['department_name']); ?></td>
                                            <td>
                                                 <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#updateCourseModal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCourseModal">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No courses found. Click "Add New Course" to create one.</td>
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

        <!-- Create Course Modal -->
        <div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createCourseModalLabel"><i class="fas fa-book"></i>Create New Course</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createCourseForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="course_code" class="form-label">Course Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="course_code" name="course_code" placeholder="e.g., CS101" required>
                            </div>
                            <div class="mb-3">
                                <label for="course_name" class="form-label">Course Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="course_name" name="course_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="units" class="form-label">Units <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="units" name="units" min="1" max="6" required>
                            </div>
                            <div class="mb-3">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    <?php if ($departments && is_array($departments)): ?>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo $dept['department_id']; ?>">
                                                <?php echo $dept['department_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Course Modal -->
        <div class="modal fade" id="updateCourseModal" tabindex="-1" aria-labelledby="updateCourseModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateCourseModalLabel"><i class="fas fa-book"></i>Update Course</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateCourseForm">
                        <div class="modal-body">
                            <input type="hidden" id="course_id" name="course_id">
                            <div class="mb-3">
                                <label for="course_code_edit" class="form-label">Course Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="course_code_edit" name="course_code" placeholder="e.g., CS101" required>
                            </div>
                            <div class="mb-3">
                                <label for="course_name_edit" class="form-label">Course Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="course_name_edit" name="course_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="units_edit" class="form-label">Units <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="units_edit" name="units" min="1" max="6" required>
                            </div>
                            <div class="mb-3">
                                <label for="department_id_edit" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select" id="department_id_edit" name="department_id" required>
                                    <option value="">Select Department</option>
                                    <?php if ($departments && is_array($departments)): ?>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo htmlspecialchars($dept['department_id']); ?>">
                                                <?php echo htmlspecialchars($dept['department_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Course Modal -->
        <div class="modal fade" id="deleteCourseModal" tabindex="-1" aria-labelledby="deleteCourseModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="deleteCourseModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Delete Course</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteCourseForm">
                        <div class="modal-body">
                            <input type="hidden" id="delete_course_id" name="course_id">
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this course?
                            </div>
                            <div class="mb-3">
                                <label for="delete_course_name" class="form-label">Course to Delete:</label>
                                <input type="text" class="form-control" id="delete_course_name" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_delete_course" class="form-label">Type the course code to confirm: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="confirm_delete_course" name="confirm_delete" placeholder="Type course code here" required>
                                <small class="text-muted">This is to prevent accidental deletion.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Delete Course</button>
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
            const API_URL = '../../App/Controller/CourseController.php';

            /**
             * Create Course Form Submission
             */
            document.getElementById('createCourseForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const courseCode = document.getElementById('course_code').value.trim();
                const courseName = document.getElementById('course_name').value.trim();
                const units = document.getElementById('units').value.trim();
                const departmentId = document.getElementById('department_id').value.trim();

                // Validation
                if (!courseCode) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter a Course Code.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!courseName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter a Course Name.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!units) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter Units.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!departmentId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please select a Department.',
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
                            course_code: courseCode,
                            course_name: courseName,
                            units: units,
                            department_id: departmentId
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
                                document.getElementById('createCourseForm').reset();

                                // Close modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById('createCourseModal'));
                                modal.hide();

                                // Reload page to show new course
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
             * Update Course Form Submission
             */
            document.getElementById('updateCourseForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const courseId = document.getElementById('course_id').value;
                const courseCode = document.getElementById('course_code_edit').value.trim();
                const courseName = document.getElementById('course_name_edit').value.trim();
                const units = document.getElementById('units_edit').value.trim();
                const departmentId = document.getElementById('department_id_edit').value.trim();

                if (!courseId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Course ID is missing.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!courseCode) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter a course code.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!courseName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter a course name.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!units) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter units.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!departmentId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please select a department.',
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
                            course_id: courseId,
                            course_code: courseCode,
                            course_name: courseName,
                            units: units,
                            department_id: departmentId
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
                                const modal = bootstrap.Modal.getInstance(document.getElementById('updateCourseModal'));
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
             * Delete Course Form Submission
             */
            document.getElementById('deleteCourseForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const courseId = document.getElementById('delete_course_id').value;
                const courseCode = document.getElementById('delete_course_name').value;
                const confirmDelete = document.getElementById('confirm_delete_course').value.trim();

                if (confirmDelete !== courseCode) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirmation Failed',
                        text: 'The course code does not match. Please type the correct code.',
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
                            course_id: courseId
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
                                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteCourseModal'));
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
                if (e.target.closest('button[data-bs-target="#updateCourseModal"]')) {
                    const row = e.target.closest('tr');
                    const courseId = row.querySelector('td:nth-child(1)').textContent.trim();
                    const courseCode = row.querySelector('td:nth-child(2)').textContent.trim();
                    const courseName = row.querySelector('td:nth-child(3)').textContent.trim();
                    const units = row.querySelector('td:nth-child(4)').textContent.trim();
                    const departmentName = row.querySelector('td:nth-child(5)').textContent.trim();

                    document.getElementById('course_id').value = courseId;
                    document.getElementById('course_code_edit').value = courseCode;
                    document.getElementById('course_name_edit').value = courseName;
                    document.getElementById('units_edit').value = units;
                    
                    // Find department ID from selected department name
                    const deptSelect = document.getElementById('department_id_edit');
                    for (let option of deptSelect.options) {
                        if (option.textContent.trim() === departmentName) {
                            deptSelect.value = option.value;
                            break;
                        }
                    }
                }

                if (e.target.closest('button[data-bs-target="#deleteCourseModal"]')) {
                    const row = e.target.closest('tr');
                    const courseId = row.querySelector('td:nth-child(1)').textContent.trim();
                    const courseCode = row.querySelector('td:nth-child(2)').textContent.trim();

                    document.getElementById('delete_course_id').value = courseId;
                    document.getElementById('delete_course_name').value = courseCode;
                    document.getElementById('confirm_delete_course').value = '';
                }
            });

            /**
             * Clear forms when modals are hidden
             */
            document.getElementById('createCourseModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('createCourseForm').reset();
            });

            document.getElementById('updateCourseModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('updateCourseForm').reset();
            });

            document.getElementById('deleteCourseModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('deleteCourseForm').reset();
            });
        </script>
    </body>
</html>