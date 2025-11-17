<!DOCTYPE html>
<html lang="en">
    <?php
        // Include authentication protection
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        
        // Include RBAC protection (Admin and Staff)
        require_once __DIR__ . '/middleware/RBACProtect.php';
        
        include 'template/header.php';
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
                                <h1 class="mt-4">Student</h1>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="dashboard.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Faculty</li>
                                </ol>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStudentModal">
                                    <i class="fas fa-plus"></i> Add New Student
                                </button>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-user-graduate me-1"></i>
                                    Student List
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Birthdate</th>
                                            <th>Gender</th>
                                            <th>Address</th>
                                            <th>Contact Number</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                
                                        <tr>
                                            <td>STU004</td>
                                            <td>Sarah</td>
                                            <td>Johnson</td>
                                            <td>1995-04-12</td>
                                            <td>Female</td>
                                            <td>123 Main St, Springfield</td>
                                            <td>+1-555-1234</td>
                                            <td>sarah.johnson@msu.edu</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#updateStudentModal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteStudentModal">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
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

        <!-- Create Student Modal -->
        <div class="modal fade" id="createStudentModal" tabindex="-1" aria-labelledby="createStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createStudentModalLabel"><i class="fas fa-user-plus me-2"></i>Create New Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createStudentForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="birthdate" class="form-label">Birthdate <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="contact_number" name="contact_number" placeholder="e.g 09XXXXXXXXX" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Student Modal -->
        <div class="modal fade" id="updateStudentModal" tabindex="-1" aria-labelledby="updateStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateStudentModalLabel"><i class="fas fa-user-edit me-2"></i>Update Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateStudentForm">
                        <div class="modal-body">
                            <input type="hidden" id="student_id" name="student_id">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name_edit" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name_edit" name="first_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name_edit" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name_edit" name="last_name" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="birthdate_edit" class="form-label">Birthdate <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="birthdate_edit" name="birthdate" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender_edit" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select class="form-select" id="gender_edit" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address_edit" class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address_edit" name="address" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contact_number_edit" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="contact_number_edit" name="contact_number" placeholder="e.g 09XXXXXXXXX" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email_edit" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email_edit" name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Student Modal -->
        <div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="deleteStudentModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Delete Student</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteStudentForm">
                        <div class="modal-body">
                            <input type="hidden" id="delete_student_id" name="student_id">
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this student?
                            </div>
                            <div class="mb-3">
                                <label for="delete_student_name" class="form-label">Student to Delete:</label>
                                <input type="text" class="form-control" id="delete_student_name" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_delete_student" class="form-label">Type the student's full name to confirm: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="confirm_delete_student" name="confirm_delete" placeholder="Type full name here" required>
                                <small class="text-muted">This is to prevent accidental deletion.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Delete Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Logout Modal -->
        <?php include 'template/script.php'; ?>
    </body>
</html>