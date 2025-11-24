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
                                    <h1 class="mt-4">Households</h1>
                                    <ol class="breadcrumb mb-4">
                                        <li class="breadcrumb-item"><a href="dashboard.html">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Households</li>
                                    </ol>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHouseholdModal">
                                        <i class="fas fa-plus"></i> Add New Household
                                    </button>
                                </div>
                            </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-home me-1"></i>
                                    Households List
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                    // Use Household model to fetch records
                                    require_once __DIR__ . '/../Model/Household.php';
                                    $householdModel = new Household();
                                    $households = $householdModel->getAll();
                                    if ($households === false) $households = [];
                                ?>

                                <table id="table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>NAME/MIDDLE/SURNAME</th>
                                            <th>BIRTHDAY</th>
                                            <th>AGE</th>
                                            <th>OCCUPATION</th>
                                            <th>INCOME</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($households)): ?>
                                            <?php foreach ($households as $row): ?>
                                                <tr data-id="<?php echo htmlspecialchars($row['household_id']); ?>">
                                                    <td><?php echo htmlspecialchars((isset($row['firstname']) ? $row['firstname'] : '') . ' ' . (isset($row['middlename']) ? $row['middlename'] : '') . ' ' . (isset($row['lastname']) ? $row['lastname'] : '')); ?></td>
                                                    <td><?php echo htmlspecialchars(isset($row['birthday']) ? $row['birthday'] : ''); ?></td>
                                                    <td><?php echo htmlspecialchars(isset($row['age']) ? $row['age'] : ''); ?></td>
                                                    <td><?php echo htmlspecialchars(isset($row['occupation']) ? $row['occupation'] : ''); ?></td>
                                                    <td><?php echo htmlspecialchars(isset($row['income']) ? $row['income'] : ''); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning me-1 edit-household-btn" data-bs-toggle="modal" data-bs-target="#updateHouseholdModal">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger delete-household-btn" data-bs-toggle="modal" data-bs-target="#deleteHouseholdModal">
                                                            <i class="fas fa-trash-alt"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">No households found.</td>
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
        <!-- Logout Modal -->
        <!-- Create / Update / Delete Modals -->
        <div class="modal fade" id="createHouseholdModal" tabindex="-1" aria-labelledby="createHouseholdModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createHouseholdModalLabel"><i class="fas fa-plus me-2"></i>Create Household</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createHouseholdForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" id="firstname" name="firstname" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" id="middlename" name="middlename" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" id="lastname" name="lastname" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Birthday</label>
                                <input type="date" id="birthday" name="birthday" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Age</label>
                                <input type="number" id="age" name="age" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Occupation</label>
                                <input type="text" id="occupation" name="occupation" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Income</label>
                                <input type="text" id="income" name="income" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create Household</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="updateHouseholdModal" tabindex="-1" aria-labelledby="updateHouseholdModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateHouseholdModalLabel"><i class="fas fa-edit me-2"></i>Update Household</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateHouseholdForm">
                        <div class="modal-body">
                            <input type="hidden" id="update_household_id" name="household_id">
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" id="update_firstname" name="firstname" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" id="update_middlename" name="middlename" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" id="update_lastname" name="lastname" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Birthday</label>
                                <input type="date" id="update_birthday" name="birthday" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Age</label>
                                <input type="number" id="update_age" name="age" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Occupation</label>
                                <input type="text" id="update_occupation" name="occupation" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Income</label>
                                <input type="text" id="update_income" name="income" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Household</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteHouseholdModal" tabindex="-1" aria-labelledby="deleteHouseholdModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteHouseholdModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Delete Household</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteHouseholdForm">
                        <div class="modal-body">
                            <input type="hidden" id="delete_household_id" name="household_id">
                            <p>Are you sure you want to delete this household?</p>
                            <p class="text-muted small">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php include 'template/script.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            const API_URL = '../../App/Controller/HouseholdController.php';

            // Create
            document.getElementById('createHouseholdForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const data = {
                    firstname: document.getElementById('firstname').value.trim(),
                    middlename: document.getElementById('middlename').value.trim(),
                    lastname: document.getElementById('lastname').value.trim(),
                    birthday: document.getElementById('birthday').value,
                    age: document.getElementById('age').value,
                    occupation: document.getElementById('occupation').value.trim(),
                    income: document.getElementById('income').value.trim()
                };

                const submitBtn = this.querySelector('button[type="submit"]');
                const orig = submitBtn.innerHTML;
                submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

                try {
                    const res = await fetch(API_URL + '?action=create', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(data)
                    });
                    const result = await res.json();
                    if (result.success) {
                        Swal.fire({icon:'success', title:'Created', text: result.message, confirmButtonColor:'#6ec207'})
                            .then(() => location.reload());
                    } else {
                        Swal.fire({icon:'error', title:'Error', text: result.message || 'Create failed', confirmButtonColor:'#dc3545'});
                    }
                } catch (err) {
                    console.error(err);
                    Swal.fire({icon:'error', title:'Network Error', text:'Failed to connect', confirmButtonColor:'#dc3545'});
                } finally {
                    submitBtn.disabled = false; submitBtn.innerHTML = orig;
                }
            });

            // Edit button click - populate update modal
            document.addEventListener('click', async function(e){
                const editBtn = e.target.closest('.edit-household-btn');
                if (!editBtn) return;
                const row = editBtn.closest('tr');
                const id = row?.getAttribute('data-id');
                if (!id) return;

                try {
                    const res = await fetch(API_URL + '?action=getById&id=' + encodeURIComponent(id));
                    const result = await res.json();
                    if (result.success && result.data) {
                        const d = result.data;
                        document.getElementById('update_household_id').value = d.household_id || id;
                        document.getElementById('update_firstname').value = d.firstname || '';
                        document.getElementById('update_middlename').value = d.middlename || '';
                        document.getElementById('update_lastname').value = d.lastname || '';
                        document.getElementById('update_birthday').value = d.birthday || '';
                        document.getElementById('update_age').value = d.age || '';
                        document.getElementById('update_occupation').value = d.occupation || '';
                        document.getElementById('update_income').value = d.income || '';
                    } else {
                        Swal.fire({icon:'error', title:'Error', text: result.message || 'Failed to load record', confirmButtonColor:'#dc3545'});
                    }
                } catch(err) {
                    console.error(err);
                }
            });

            // Update
            document.getElementById('updateHouseholdForm').addEventListener('submit', async function(e){
                e.preventDefault();
                const data = {
                    household_id: document.getElementById('update_household_id').value,
                    firstname: document.getElementById('update_firstname').value.trim(),
                    middlename: document.getElementById('update_middlename').value.trim(),
                    lastname: document.getElementById('update_lastname').value.trim(),
                    birthday: document.getElementById('update_birthday').value,
                    age: document.getElementById('update_age').value,
                    occupation: document.getElementById('update_occupation').value.trim(),
                    income: document.getElementById('update_income').value.trim()
                };

                const submitBtn = this.querySelector('button[type="submit"]');
                const orig = submitBtn.innerHTML;
                submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

                try {
                    const res = await fetch(API_URL + '?action=update', {
                        method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)
                    });
                    const result = await res.json();
                    if (result.success) {
                        Swal.fire({icon:'success', title:'Updated', text: result.message, confirmButtonColor:'#6ec207'})
                            .then(() => location.reload());
                    } else {
                        Swal.fire({icon:'error', title:'Error', text: result.message || 'Update failed', confirmButtonColor:'#dc3545'});
                    }
                } catch(err) {
                    console.error(err);
                    Swal.fire({icon:'error', title:'Network Error', text:'Failed to connect', confirmButtonColor:'#dc3545'});
                } finally {
                    submitBtn.disabled = false; submitBtn.innerHTML = orig;
                }
            });

            // Delete - set id when opening modal
            document.addEventListener('click', function(e){
                const delBtn = e.target.closest('.delete-household-btn');
                if (!delBtn) return;
                const row = delBtn.closest('tr');
                const id = row?.getAttribute('data-id');
                if (!id) return;
                document.getElementById('delete_household_id').value = id;
            });

            // Delete form submit
            document.getElementById('deleteHouseholdForm').addEventListener('submit', async function(e){
                e.preventDefault();
                const household_id = document.getElementById('delete_household_id').value;

                const submitBtn = this.querySelector('button[type="submit"]');
                const orig = submitBtn.innerHTML;
                submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

                try {
                    const res = await fetch(API_URL + '?action=delete', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({household_id}) });
                    const result = await res.json();
                    if (result.success) {
                        Swal.fire({icon:'success', title:'Deleted', text: result.message, confirmButtonColor:'#6ec207'})
                            .then(() => location.reload());
                    } else {
                        Swal.fire({icon:'error', title:'Error', text: result.message || 'Delete failed', confirmButtonColor:'#dc3545'});
                    }
                } catch(err) {
                    console.error(err);
                    Swal.fire({icon:'error', title:'Network Error', text:'Failed to connect', confirmButtonColor:'#dc3545'});
                } finally {
                    submitBtn.disabled = false; submitBtn.innerHTML = orig;
                }
            });
        </script>
    </body>
</html>