<!DOCTYPE html>
<html lang="en">
    <?php
        // Include authentication protection
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        
        // Include RBAC protection (Admin and Staff)
        require_once __DIR__ . '/middleware/RBACProtect.php';
        
        include 'template/header.php';
        require_once __DIR__ . '/../Model/Patient.php';
        
        // Initialize Patient model
        $PatientModel = new Patient();
        $Patients = $PatientModel->getAll();
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
                                <h1 class="mt-4">Patient Management</h1>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="dashboard.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Patients</li>
                                </ol>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPatientModal">
                                    <i class="fas fa-plus"></i> Add New Patient
                                </button>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-hospital-user me-1"></i>
                                    Patient List
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Patient ID</th>
                                            <th>First Name</th>
                                            <th>Middle Name</th>
                                            <th>Last Name</th>
                                            <th>Birthdate</th>
                                            <th>Gender</th>
                                            <th>Status</th>
                                            <th>Address</th>
                                            <th>Contact Number</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($Patients && count($Patients) > 0): ?>
                                            <?php foreach ($Patients as $patient): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($patient['patient_id']); ?></td>
                                                <td><?php echo htmlspecialchars($patient['firstname']); ?></td>
                                                <td><?php echo htmlspecialchars($patient['middlename'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($patient['lastname']); ?></td>
                                                <td><?php echo htmlspecialchars($patient['birthdate']); ?></td>
                                                <td><span class="badge bg-info"><?php echo htmlspecialchars($patient['gender']); ?></span></td>
                                                <td>
                                                    <?php 
                                                        $statusColor = '';
                                                        switch($patient['status']) {
                                                            case 'Single':
                                                                $statusColor = 'bg-success';
                                                                break;
                                                            case 'Married':
                                                                $statusColor = 'bg-primary';
                                                                break;
                                                            case 'Widowed':
                                                                $statusColor = 'bg-secondary';
                                                                break;
                                                            case 'Separated':
                                                                $statusColor = 'bg-warning';
                                                                break;
                                                            default:
                                                                $statusColor = 'bg-secondary';
                                                        }
                                                    ?>
                                                    <span class="badge <?php echo $statusColor; ?>"><?php echo htmlspecialchars($patient['status']); ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($patient['address']); ?></td>
                                                <td><?php echo htmlspecialchars($patient['contact_no']); ?></td>
                                                <td><?php echo htmlspecialchars($patient['email'] ?? ''); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#updatePatientModal" onclick="populateEditPatient(this)">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deletePatientModal" onclick="populateDeletePatient(this)">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="11" class="text-center py-4">
                                                    <i class="fas fa-inbox me-2"></i>No patients found. Click "Add New Patient" to register a patient.
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

        <!-- Create Patient Modal -->
        <div class="modal fade" id="createPatientModal" tabindex="-1" aria-labelledby="createPatientModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPatientModalLabel"><i class="fas fa-user-plus me-2"></i>Register New Patient</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createPatientForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="patient_id_create" class="form-label">Patient ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="patient_id_create" name="patient_id" placeholder="e.g., PAT001" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="firstname_create" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="firstname_create" name="firstname" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="middlename_create" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="middlename_create" name="middlename">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastname_create" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lastname_create" name="lastname" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="birthdate_create" class="form-label">Birthdate <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="birthdate_create" name="birthdate" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender_create" class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select class="form-select" id="gender_create" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status_create" class="form-label">Marital Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status_create" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address_create" class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address_create" name="address" placeholder="Street address" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contact_no_create" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="contact_no_create" name="contact_no" placeholder="e.g., +1-555-1234" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email_create" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_create" name="email" placeholder="patient@email.com">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create Patient</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Patient Modal -->
        <div class="modal fade" id="updatePatientModal" tabindex="-1" aria-labelledby="updatePatientModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updatePatientModalLabel"><i class="fas fa-user-edit me-2"></i>Update Patient Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updatePatientForm">
                        <div class="modal-body">
                            <input type="hidden" id="patient_id_hidden" name="patient_id_hidden">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="patient_id_edit" class="form-label">Patient ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="patient_id_edit" name="patient_id" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="firstname_edit" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="firstname_edit" name="firstname" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="middlename_edit" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="middlename_edit" name="middlename">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastname_edit" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lastname_edit" name="lastname" required>
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
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status_edit" class="form-label">Marital Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status_edit" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address_edit" class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address_edit" name="address" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contact_no_edit" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="contact_no_edit" name="contact_no" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email_edit" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_edit" name="email">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Patient</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Patient Modal -->
        <div class="modal fade" id="deletePatientModal" tabindex="-1" aria-labelledby="deletePatientModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="deletePatientModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Delete Patient</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deletePatientForm">
                        <div class="modal-body">
                            <input type="hidden" id="delete_patient_id" name="patient_id">
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Warning!</strong> This action cannot be undone. Are you sure you want to delete this patient's record?
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Patient Name:</strong></label>
                                <p id="delete_patient_name_display" class="form-control-plaintext bg-light p-2 rounded"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Patient ID:</strong></label>
                                <p id="delete_patient_id_display" class="form-control-plaintext bg-light p-2 rounded"></p>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_delete_patient" class="form-label">Type the patient's last name to confirm: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="confirm_delete_patient" name="confirm_delete" placeholder="Type last name here" required>
                                <small class="text-muted">This is to prevent accidental deletion.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Delete Patient</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <?php include 'template/script.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            const API_URL = '../../App/Controller/PatientController.php';

            /**
             * Create Patient Form Submission
             */
            document.getElementById('createPatientForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const patientId = document.getElementById('patient_id_create').value.trim();
                const firstname = document.getElementById('firstname_create').value.trim();
                const middlename = document.getElementById('middlename_create').value.trim();
                const lastname = document.getElementById('lastname_create').value.trim();
                const birthdate = document.getElementById('birthdate_create').value;
                const gender = document.getElementById('gender_create').value;
                const status = document.getElementById('status_create').value;
                const address = document.getElementById('address_create').value.trim();
                const contact_no = document.getElementById('contact_no_create').value.trim();
                const email = document.getElementById('email_create').value.trim();

                // Validation
                if (!patientId || !firstname || !lastname || !birthdate || !gender || !status || !address || !contact_no) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please fill in all required fields.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

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
                            patient_id: patientId,
                            firstname: firstname,
                            middlename: middlename,
                            lastname: lastname,
                            birthdate: birthdate,
                            gender: gender,
                            status: status,
                            address: address,
                            contact_no: contact_no,
                            email: email
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
                            document.getElementById('createPatientForm').reset();
                            bootstrap.Modal.getInstance(document.getElementById('createPatientModal')).hide();
                            setTimeout(() => location.reload(), 500);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
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
             * Update Patient Form Submission
             */
            document.getElementById('updatePatientForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const patientIdHidden = document.getElementById('patient_id_hidden').value;
                const patientId = document.getElementById('patient_id_edit').value.trim();
                const firstname = document.getElementById('firstname_edit').value.trim();
                const middlename = document.getElementById('middlename_edit').value.trim();
                const lastname = document.getElementById('lastname_edit').value.trim();
                const birthdate = document.getElementById('birthdate_edit').value;
                const gender = document.getElementById('gender_edit').value;
                const status = document.getElementById('status_edit').value;
                const address = document.getElementById('address_edit').value.trim();
                const contact_no = document.getElementById('contact_no_edit').value.trim();
                const email = document.getElementById('email_edit').value.trim();

                if (!patientId || !firstname || !lastname || !birthdate || !gender || !status || !address || !contact_no) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please fill in all required fields.',
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
                            patient_id_hidden: patientIdHidden,
                            patient_id: patientId,
                            firstname: firstname,
                            middlename: middlename,
                            lastname: lastname,
                            birthdate: birthdate,
                            gender: gender,
                            status: status,
                            address: address,
                            contact_no: contact_no,
                            email: email
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
                            document.getElementById('updatePatientForm').reset();
                            bootstrap.Modal.getInstance(document.getElementById('updatePatientModal')).hide();
                            setTimeout(() => location.reload(), 500);
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
             * Delete Patient Form Submission
             */
            document.getElementById('deletePatientForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const patientId = document.getElementById('delete_patient_id').value;
                const lastname = document.getElementById('delete_patient_name_display').textContent.split(' ').pop();
                const confirmDelete = document.getElementById('confirm_delete_patient').value.trim();

                if (confirmDelete !== lastname) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirmation Failed',
                        text: 'The last name does not match. Please type the correct last name.',
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
                            patient_id: patientId
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
                            document.getElementById('deletePatientForm').reset();
                            bootstrap.Modal.getInstance(document.getElementById('deletePatientModal')).hide();
                            setTimeout(() => location.reload(), 500);
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
             * Populate Edit Modal with Patient Data
             */
            function populateEditPatient(button) {
                const row = button.closest('tr');
                const patientId = row.querySelector('td:nth-child(1)').textContent.trim();
                const firstname = row.querySelector('td:nth-child(2)').textContent.trim();
                const middlename = row.querySelector('td:nth-child(3)').textContent.trim();
                const lastname = row.querySelector('td:nth-child(4)').textContent.trim();
                const birthdate = row.querySelector('td:nth-child(5)').textContent.trim();
                const gender = row.querySelector('td:nth-child(6)').textContent.trim();
                const status = row.querySelector('td:nth-child(7)').textContent.trim();
                const address = row.querySelector('td:nth-child(8)').textContent.trim();
                const contact_no = row.querySelector('td:nth-child(9)').textContent.trim();
                const email = row.querySelector('td:nth-child(10)').textContent.trim();

                document.getElementById('patient_id_hidden').value = patientId;
                document.getElementById('patient_id_edit').value = patientId;
                document.getElementById('firstname_edit').value = firstname;
                document.getElementById('middlename_edit').value = middlename;
                document.getElementById('lastname_edit').value = lastname;
                document.getElementById('birthdate_edit').value = birthdate;
                document.getElementById('gender_edit').value = gender;
                document.getElementById('status_edit').value = status;
                document.getElementById('address_edit').value = address;
                document.getElementById('contact_no_edit').value = contact_no;
                document.getElementById('email_edit').value = email;
            }

            /**
             * Populate Delete Modal with Patient Data
             */
            function populateDeletePatient(button) {
                const row = button.closest('tr');
                const patientId = row.querySelector('td:nth-child(1)').textContent.trim();
                const firstname = row.querySelector('td:nth-child(2)').textContent.trim();
                const lastname = row.querySelector('td:nth-child(4)').textContent.trim();

                document.getElementById('delete_patient_id').value = patientId;
                document.getElementById('delete_patient_id_display').textContent = patientId;
                document.getElementById('delete_patient_name_display').textContent = firstname + ' ' + lastname;
                document.getElementById('confirm_delete_patient').value = '';
            }

            /**
             * Clear forms when modals are hidden
             */
            document.getElementById('createPatientModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('createPatientForm').reset();
            });

            document.getElementById('updatePatientModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('updatePatientForm').reset();
            });

            document.getElementById('deletePatientModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('deletePatientForm').reset();
            });
        </script>
    </body>
</html>