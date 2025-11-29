<!DOCTYPE html>
<html lang="en">
    <?php 
        // Include authentication protection
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        
        // Include RBAC protection (Admin and Staff)
        require_once __DIR__ . '/middleware/RBACProtect.php';
        
        include 'template/header.php';
        require_once __DIR__ . '/../Model/Resident.php';
        require_once __DIR__ . '/../Model/Household.php';
        
        // Initialize models
        $residentModel = new Resident();
        $residents = $residentModel->getAll();
        
        $householdModel = new Household();
        $households = $householdModel->getAll();
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
                                <h1 class="mt-4">Residents</h1>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="dashboard.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Residents</li>
                                </ol>
                            </div>
                            <div>
                                <!-- Add New Resident button removed - Residents are now added through Household creation -->
                                <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.9rem;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    To add residents, please create or update a household with its members.
                                    <a href="household.php" class="alert-link ms-2">
                                        <i class="fas fa-arrow-right me-1"></i>Go to Households
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-users me-1"></i>
                                    Residents List
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Resident ID</th>
                                            <th>First Name</th>
                                            <th>Middle Name</th>
                                            <th>Last Name</th>
                                            <th>Birth Date</th>
                                            <th>Gender</th>
                                            <th>Age</th>
                                            <th>Contact No</th>
                                            <th>Email</th>
                                            <th>Household ID</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($residents && count($residents) > 0): ?>
                                            <?php foreach ($residents as $resident): ?>
                                                <tr data-household-id="<?php echo htmlspecialchars($resident['household_id']); ?>">
                                                    <td><?php echo htmlspecialchars($resident['resident_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($resident['first_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($resident['middle_name'] ?? 'N/A'); ?></td>
                                                    <td><?php echo htmlspecialchars($resident['last_name']); ?></td>
                                                    <td><?php echo $resident['birth_date'] ? date('M d, Y', strtotime($resident['birth_date'])) : 'N/A'; ?></td>
                                                    <td><?php echo htmlspecialchars($resident['gender'] ?? 'N/A'); ?></td>
                                                    <td><?php echo htmlspecialchars($resident['age']); ?></td>
                                                    <td><?php echo htmlspecialchars($resident['contact_no'] ?? 'N/A'); ?></td>
                                                    <td><?php echo htmlspecialchars($resident['email'] ?? 'N/A'); ?></td>
                                                    <td><?php echo htmlspecialchars($resident['household_id']); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#updateResidentModal">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteResidentModal">
                                                            <i class="fas fa-trash-alt"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="11" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox me-2"></i>No residents found. Click "Add New Resident" to create one.
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

        <!-- Create Resident Modal -->
        <div class="modal fade" id="createResidentModal" tabindex="-1" aria-labelledby="createResidentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createResidentModalLabel"><i class="fas fa-plus me-2"></i>Create Resident</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createResidentForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" id="first_name" name="first_name" class="form-control" required placeholder="e.g., Juan">
                            </div>
                            <div class="mb-3">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" id="middle_name" name="middle_name" class="form-control" placeholder="e.g., Santos">
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" id="last_name" name="last_name" class="form-control" required placeholder="e.g., Dela Cruz">
                            </div>
                            <div class="mb-3">
                                <label for="birth_date" class="form-label">Birth Date</label>
                                <input type="date" id="birth_date" name="birth_date" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select id="gender" name="gender" class="form-select">
                                    <option value="">-- Select Gender --</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="age" class="form-label">Age <span class="text-danger">*</span></label>
                                <input type="number" id="age" name="age" class="form-control" required min="1" max="150" placeholder="e.g., 25">
                            </div>
                            <div class="mb-3">
                                <label for="contact_no" class="form-label">Contact Number</label>
                                <input type="text" id="contact_no" name="contact_no" class="form-control" placeholder="e.g., 09171234567">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="e.g., juan@gmail.com">
                            </div>
                            <div class="mb-3">
                                <label for="household_id" class="form-label">Household <span class="text-danger">*</span></label>
                                <select id="household_id" name="household_id" class="form-select" required>
                                    <option value="">-- Select Household --</option>
                                    <?php foreach ($households as $household): ?>
                                        <option value="<?php echo $household['household_id']; ?>">
                                            <?php echo htmlspecialchars($household['household_id'] . ' - ' . $household['address']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create Resident</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Resident Modal -->
        <div class="modal fade" id="updateResidentModal" tabindex="-1" aria-labelledby="updateResidentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateResidentModalLabel"><i class="fas fa-edit me-2"></i>Update Resident</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateResidentForm">
                        <div class="modal-body">
                            <input type="hidden" id="resident_id_edit" name="resident_id">
                            <div class="mb-3">
                                <label for="resident_id_display" class="form-label">Resident ID</label>
                                <input type="text" id="resident_id_display" class="form-control" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="first_name_edit" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" id="first_name_edit" name="first_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="middle_name_edit" class="form-label">Middle Name</label>
                                <input type="text" id="middle_name_edit" name="middle_name" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="last_name_edit" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" id="last_name_edit" name="last_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="birth_date_edit" class="form-label">Birth Date</label>
                                <input type="date" id="birth_date_edit" name="birth_date" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="gender_edit" class="form-label">Gender</label>
                                <select id="gender_edit" name="gender" class="form-select">
                                    <option value="">-- Select Gender --</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="age_edit" class="form-label">Age</label>
                                <input type="number" id="age_edit" name="age" class="form-control" readonly placeholder="Auto-calculated from birth date">
                            </div>
                            <div class="mb-3">
                                <label for="contact_no_edit" class="form-label">Contact Number</label>
                                <input type="text" id="contact_no_edit" name="contact_no" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="email_edit" class="form-label">Email</label>
                                <input type="email" id="email_edit" name="email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="household_id_edit" class="form-label">Household <span class="text-danger">*</span></label>
                                <select id="household_id_edit" name="household_id" class="form-select" required>
                                    <option value="">-- Select Household --</option>
                                    <?php foreach ($households as $household): ?>
                                        <option value="<?php echo $household['household_id']; ?>">
                                            <?php echo htmlspecialchars($household['household_id'] . ' - ' . $household['address']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Resident</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Resident Modal -->
        <div class="modal fade" id="deleteResidentModal" tabindex="-1" aria-labelledby="deleteResidentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteResidentModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Delete Resident</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteResidentForm">
                        <div class="modal-body">
                            <input type="hidden" id="delete_resident_id" name="resident_id">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Warning!</strong> This action cannot be undone.
                            </div>
                            <div class="mb-3">
                                <label for="delete_resident_name" class="form-label">Resident Name</label>
                                <input type="text" id="delete_resident_name" class="form-control" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_delete_resident" class="form-label">
                                    Type the full name to confirm <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="confirm_delete_resident" class="form-control" required placeholder="Type full name to confirm">
                                <small class="form-text text-muted">This is a safety measure to prevent accidental deletion.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Delete Resident</button>
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
            const API_URL = '../../App/Controller/ResidentController.php';

            /**
             * Create Resident Form Submission
             */
            document.getElementById('createResidentForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const first_name = document.getElementById('first_name').value.trim();
                const middle_name = document.getElementById('middle_name').value.trim();
                const last_name = document.getElementById('last_name').value.trim();
                const birth_date = document.getElementById('birth_date').value;
                const gender = document.getElementById('gender').value;
                const age = document.getElementById('age').value;
                const contact_no = document.getElementById('contact_no').value.trim();
                const email = document.getElementById('email').value.trim();
                const household_id = document.getElementById('household_id').value;

                // Validation
                if (!first_name || !last_name || !age || !household_id) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please fill all required fields (First Name, Last Name, Age, Household).',
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
                            first_name: first_name,
                            middle_name: middle_name || null,
                            last_name: last_name,
                            birth_date: birth_date || null,
                            gender: gender || null,
                            age: parseInt(age),
                            contact_no: contact_no || null,
                            email: email || null,
                            household_id: household_id
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
                                document.getElementById('createResidentForm').reset();

                                // Close modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById('createResidentModal'));
                                modal.hide();

                                // Reload page to show new resident
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
             * Update Resident Form Submission
             */
            document.getElementById('updateResidentForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const residentId = document.getElementById('resident_id_edit').value;
                const first_name = document.getElementById('first_name_edit').value.trim();
                const middle_name = document.getElementById('middle_name_edit').value.trim();
                const last_name = document.getElementById('last_name_edit').value.trim();
                const birth_date = document.getElementById('birth_date_edit').value;
                const gender = document.getElementById('gender_edit').value;
                const age = document.getElementById('age_edit').value;
                const contact_no = document.getElementById('contact_no_edit').value.trim();
                const email = document.getElementById('email_edit').value.trim();
                const household_id = document.getElementById('household_id_edit').value;

                if (!residentId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Resident ID is missing.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!first_name || !last_name || !age || !household_id) {
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
                            resident_id: residentId,
                            first_name: first_name,
                            middle_name: middle_name || null,
                            last_name: last_name,
                            birth_date: birth_date || null,
                            gender: gender || null,
                            age: parseInt(age),
                            contact_no: contact_no || null,
                            email: email || null,
                            household_id: household_id
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
                                const modal = bootstrap.Modal.getInstance(document.getElementById('updateResidentModal'));
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
             * Delete Resident Form Submission
             */
            document.getElementById('deleteResidentForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const residentId = document.getElementById('delete_resident_id').value;
                const residentName = document.getElementById('delete_resident_name').value;
                const confirmDelete = document.getElementById('confirm_delete_resident').value.trim();

                if (confirmDelete !== residentId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirmation Failed',
                        text: 'The resident ID does not match. Please type the correct resident ID.',
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
                            resident_id: residentId
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
                                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteResidentModal'));
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
                if (e.target.closest('button[data-bs-target="#updateResidentModal"]')) {
                    const row = e.target.closest('tr');
                    const residentId = row.querySelector('td:nth-child(1)').textContent;
                    const first_name = row.querySelector('td:nth-child(2)').textContent;
                    const middle_name = row.querySelector('td:nth-child(3)').textContent;
                    const last_name = row.querySelector('td:nth-child(4)').textContent;
                    const birth_date_text = row.querySelector('td:nth-child(5)').textContent;
                    const gender = row.querySelector('td:nth-child(6)').textContent;
                    const age = row.querySelector('td:nth-child(7)').textContent;
                    const contact_no = row.querySelector('td:nth-child(8)').textContent;
                    const email = row.querySelector('td:nth-child(9)').textContent;
                    const household_id = row.querySelector('td:nth-child(10)').textContent;

                    // Convert birth date from "Mon DD, YYYY" to "YYYY-MM-DD" format
                    let birth_date = '';
                    if (birth_date_text !== 'N/A') {
                        const dateObj = new Date(birth_date_text);
                        if (!isNaN(dateObj.getTime())) {
                            birth_date = dateObj.toISOString().split('T')[0];
                        }
                    }

                    document.getElementById('resident_id_edit').value = residentId;
                    document.getElementById('resident_id_display').value = residentId;
                    document.getElementById('first_name_edit').value = first_name;
                    document.getElementById('middle_name_edit').value = middle_name === 'N/A' ? '' : middle_name;
                    document.getElementById('last_name_edit').value = last_name;
                    document.getElementById('birth_date_edit').value = birth_date;
                    document.getElementById('gender_edit').value = gender === 'N/A' ? '' : gender;
                    document.getElementById('age_edit').value = age;
                    document.getElementById('contact_no_edit').value = contact_no === 'N/A' ? '' : contact_no;
                    document.getElementById('email_edit').value = email === 'N/A' ? '' : email;
                    document.getElementById('household_id_edit').value = household_id;
                }

                if (e.target.closest('button[data-bs-target="#deleteResidentModal"]')) {
                    const row = e.target.closest('tr');
                    const residentId = row.querySelector('td:nth-child(1)').textContent;

                    document.getElementById('delete_resident_id').value = residentId;
                    document.getElementById('delete_resident_name').value = residentId;
                    document.getElementById('confirm_delete_resident').value = '';
                }
            });

            /**
             * Clear forms when modals are hidden
             */
            document.getElementById('createResidentModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('createResidentForm').reset();
            });

            document.getElementById('updateResidentModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('updateResidentForm').reset();
            });

            document.getElementById('deleteResidentModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('deleteResidentForm').reset();
            });

            /**
             * Auto-calculate age when birth date changes in edit form
             */
            document.getElementById('birth_date_edit').addEventListener('change', function() {
                const birthDate = this.value;
                if (birthDate) {
                    const birth = new Date(birthDate);
                    const today = new Date();
                    let age = today.getFullYear() - birth.getFullYear();
                    const monthDiff = today.getMonth() - birth.getMonth();
                    
                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                        age--;
                    }
                    
                    document.getElementById('age_edit').value = age >= 0 ? age : 0;
                }
            });
        </script>
    </body>
</html>
