<!DOCTYPE html>
<html lang="en">
    <?php 
        // Include authentication protection
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        
        // Include RBAC protection (Admin and Staff)
        require_once __DIR__ . '/middleware/RBACProtect.php';
        
        include 'template/header.php';
        require_once __DIR__ . '/../Model/Household.php';
        
        // Initialize Household model
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
                                <table id="table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Household ID</th>
                                            <th>Family No</th>
                                            <th>Full Name (Head)</th>
                                            <th>Address</th>
                                            <th>Income</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($households && count($households) > 0): ?>
                                            <?php foreach ($households as $household): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($household['household_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($household['family_no']); ?></td>
                                                    <td><?php echo htmlspecialchars($household['full_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($household['address']); ?></td>
                                                    <td><?php echo number_format($household['income'] ?? 0, 2); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info me-1 view-members-btn" 
                                                                data-household-id="<?php echo htmlspecialchars($household['household_id']); ?>"
                                                                data-household-name="<?php echo htmlspecialchars($household['full_name']); ?>"
                                                                title="View Members">
                                                            <i class="fas fa-eye"></i> View
                                                        </button>
                                                        <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#updateHouseholdModal">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteHouseholdModal">
                                                            <i class="fas fa-trash-alt"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox me-2"></i>No households found. Click "Add New Household" to create one.
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
        <!-- Logout Modal -->
        <!-- Create / Update / Delete Modals -->
        <div class="modal fade" id="createHouseholdModal" tabindex="-1" aria-labelledby="createHouseholdModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createHouseholdModalLabel"><i class="fas fa-plus me-2"></i>Create Household with Members</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createHouseholdForm">
                        <div class="modal-body">
                            <h6 class="mb-3 text-primary"><i class="fas fa-home me-2"></i>Household Information</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="family_no" class="form-label">Family No <span class="text-danger">*</span></label>
                                    <input type="number" id="family_no" name="family_no" class="form-control" required placeholder="e.g., 1">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="income" class="form-label">Household Income</label>
                                    <input type="number" id="income" name="income" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name (Household Head) <span class="text-danger">*</span></label>
                                <input type="text" id="full_name" name="full_name" class="form-control" required placeholder="Enter full name of household head">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" id="address" name="address" class="form-control" required placeholder="Enter complete address">
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> After creating the household, you can add members by clicking the "Edit" button.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create Household</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="updateHouseholdModal" tabindex="-1" aria-labelledby="updateHouseholdModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateHouseholdModalLabel"><i class="fas fa-edit me-2"></i>Update Household</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateHouseholdForm">
                        <div class="modal-body">
                            <input type="hidden" id="household_id_edit" name="household_id">
                            
                            <h6 class="mb-3 text-primary"><i class="fas fa-home me-2"></i>Household Information</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="household_id_display" class="form-label">Household ID</label>
                                    <input type="text" id="household_id_display" class="form-control" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="family_no_edit" class="form-label">Family No <span class="text-danger">*</span></label>
                                    <input type="number" id="family_no_edit" name="family_no" class="form-control" required placeholder="e.g., 1">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="full_name_edit" class="form-label">Full Name (Household Head) <span class="text-danger">*</span></label>
                                <input type="text" id="full_name_edit" name="full_name" class="form-control" required placeholder="Enter full name of household head">
                            </div>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="address_edit" class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" id="address_edit" name="address" class="form-control" required placeholder="Enter complete address">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="income_edit" class="form-label">Household Income</label>
                                    <input type="number" id="income_edit" name="income" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-primary"><i class="fas fa-users me-2"></i>Household Members</h6>
                                <button type="button" class="btn btn-sm btn-success" id="addMemberBtnEdit">
                                    <i class="fas fa-plus me-1"></i>Add Member
                                </button>
                            </div>

                            <div id="membersContainerEdit">
                                <!-- Existing and new member forms will be added here -->
                                <div class="text-center py-3" id="loadingMembersEdit">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading members...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Household</button>
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
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Warning!</strong> This action cannot be undone.
                            </div>
                            <div class="mb-3">
                                <label for="delete_household_no" class="form-label">Household ID</label>
                                <input type="text" id="delete_household_no" class="form-control" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_delete_household" class="form-label">
                                    Type the household ID to confirm <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="confirm_delete_household" class="form-control" required placeholder="Type household ID to confirm">
                                <small class="form-text text-muted">This is a safety measure to prevent accidental deletion.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Delete Household</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Members Modal -->
        <div class="modal fade" id="viewMembersModal" tabindex="-1" aria-labelledby="viewMembersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="viewMembersModalLabel">
                            <i class="fas fa-users me-2"></i>Household Members - <span id="modalHouseholdName"></span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="membersLoadingSpinner" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading members...</p>
                        </div>

                        <div id="membersContent" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Resident ID</th>
                                            <th>Full Name</th>
                                            <th>Birth Date</th>
                                            <th>Age</th>
                                            <th>Gender</th>
                                            <th>Contact No</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody id="membersTableBody">
                                        <!-- Members will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="noMembersMessage" class="alert alert-info" style="display: none;">
                                <i class="fas fa-info-circle me-2"></i>No members found for this household.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'template/script.php'; ?>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // API Base URL
            const API_URL = '../../App/Controller/HouseholdController.php';

            // Member counter for unique IDs
            let memberCount = 0;
            let editMemberCount = 0;

            /**
             * Calculate Age from Birth Date
             */
            function calculateAge(birthDate) {
                const today = new Date();
                const birth = new Date(birthDate);
                let age = today.getFullYear() - birth.getFullYear();
                const monthDiff = today.getMonth() - birth.getMonth();
                
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                    age--;
                }
                
                return age >= 0 ? age : 0;
            }

            /**
             * Add Member Form Fields (Edit Modal)
             */
            document.getElementById('addMemberBtnEdit').addEventListener('click', function() {
                editMemberCount++;
                const memberHtml = `
                    <div class="member-form border rounded p-3 mb-3 bg-light" id="edit-member-new-${editMemberCount}" data-member-type="new">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-success"><i class="fas fa-user-plus me-2"></i>New Member #${editMemberCount}</h6>
                            <button type="button" class="btn btn-sm btn-danger remove-member-btn-edit" data-member-id="edit-member-new-${editMemberCount}">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="member_first_name_edit[]" class="form-control form-control-sm" required placeholder="First name">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="member_middle_name_edit[]" class="form-control form-control-sm" placeholder="Middle name (optional)">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="member_last_name_edit[]" class="form-control form-control-sm" required placeholder="Last name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Birth Date <span class="text-danger">*</span></label>
                                <input type="date" name="member_birth_date_edit[]" class="form-control form-control-sm birth-date-input" required>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Age</label>
                                <input type="number" name="member_age_edit[]" class="form-control form-control-sm age-display" readonly placeholder="Auto">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="member_gender_edit[]" class="form-select form-select-sm" required>
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Contact No</label>
                                <input type="text" name="member_contact_edit[]" class="form-control form-control-sm" placeholder="09XXXXXXXXX">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Email</label>
                                <input type="email" name="member_email_edit[]" class="form-control form-control-sm" placeholder="email@example.com">
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('membersContainerEdit').insertAdjacentHTML('beforeend', memberHtml);
            });

            /**
             * Remove Member Form Fields (Edit Modal) - Handles both existing and new members
             */
            document.getElementById('membersContainerEdit').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-member-btn-edit') || e.target.closest('.remove-member-btn-edit')) {
                    const btn = e.target.closest('.remove-member-btn-edit');
                    const memberId = btn.getAttribute('data-member-id');
                    const memberForm = document.getElementById(memberId);
                    
                    if (memberForm) {
                        const memberType = memberForm.getAttribute('data-member-type');
                        
                        if (memberType === 'existing') {
                            // Check if already marked for deletion
                            if (memberForm.getAttribute('data-deleted') === 'true') {
                                // Undo deletion
                                memberForm.classList.remove('border-danger', 'opacity-50');
                                memberForm.removeAttribute('data-deleted');
                                btn.innerHTML = '<i class="fas fa-times"></i> Remove';
                                btn.classList.remove('btn-warning');
                                btn.classList.add('btn-danger');
                            } else {
                                // Mark for deletion with visual feedback
                                memberForm.classList.add('border-danger', 'opacity-50');
                                memberForm.setAttribute('data-deleted', 'true');
                                btn.innerHTML = '<i class="fas fa-undo"></i> Undo';
                                btn.classList.remove('btn-danger');
                                btn.classList.add('btn-warning');
                            }
                        } else {
                            // Just remove new members
                            memberForm.remove();
                        }
                    }
                }
            });

            /**
             * Load Existing Members for Edit Modal
             */
            async function loadExistingMembers(householdId) {
                const container = document.getElementById('membersContainerEdit');
                const loading = document.getElementById('loadingMembersEdit');
                
                // Show loading
                loading.style.display = 'block';
                container.innerHTML = '<div class="text-center py-3" id="loadingMembersEdit"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading members...</span></div></div>';
                
                try {
                    const response = await fetch(API_URL + `?action=getMembers&household_id=${householdId}`);
                    const result = await response.json();
                    
                    // Clear loading
                    container.innerHTML = '';
                    
                    if (result.success && result.data && result.data.length > 0) {
                        result.data.forEach((member, index) => {
                            const memberHtml = `
                                <div class="member-form border rounded p-3 mb-3" id="edit-member-${member.resident_id}" data-member-type="existing" data-resident-id="${member.resident_id}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-secondary"><i class="fas fa-user me-2"></i>Member: ${member.first_name} ${member.last_name}</h6>
                                        <button type="button" class="btn btn-sm btn-danger remove-member-btn-edit" data-member-id="edit-member-${member.resident_id}">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="existing_member_first_name[]" class="form-control form-control-sm" required value="${member.first_name}">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Middle Name</label>
                                            <input type="text" name="existing_member_middle_name[]" class="form-control form-control-sm" value="${member.middle_name || ''}">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="existing_member_last_name[]" class="form-control form-control-sm" required value="${member.last_name}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Birth Date <span class="text-danger">*</span></label>
                                            <input type="date" name="existing_member_birth_date[]" class="form-control form-control-sm birth-date-input" required value="${member.birth_date}">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Age</label>
                                            <input type="number" name="existing_member_age[]" class="form-control form-control-sm age-display" readonly value="${member.age || calculateAge(member.birth_date)}">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                                            <select name="existing_member_gender[]" class="form-select form-select-sm" required>
                                                <option value="">Select</option>
                                                <option value="Male" ${member.gender === 'Male' ? 'selected' : ''}>Male</option>
                                                <option value="Female" ${member.gender === 'Female' ? 'selected' : ''}>Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Contact No</label>
                                            <input type="text" name="existing_member_contact[]" class="form-control form-control-sm" value="${member.contact_no || ''}">
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="existing_member_email[]" class="form-control form-control-sm" value="${member.email || ''}">
                                        </div>
                                    </div>
                                    <input type="hidden" name="existing_member_id[]" value="${member.resident_id}">
                                </div>
                            `;
                            container.insertAdjacentHTML('beforeend', memberHtml);
                        });
                    } else {
                        container.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No members found. Click "Add Member" to add new members.</div>';
                    }
                } catch (error) {
                    console.error('Error loading members:', error);
                    container.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Failed to load members.</div>';
                }
            }

            /**
             * Auto-calculate age when birth date changes (using event delegation)
             */
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('birth-date-input')) {
                    const birthDate = e.target.value;
                    if (birthDate) {
                        const age = calculateAge(birthDate);
                        const memberForm = e.target.closest('.member-form');
                        if (memberForm) {
                            const ageInput = memberForm.querySelector('.age-display');
                            if (ageInput) {
                                ageInput.value = age;
                            }
                        }
                    }
                }
            });

            /**
             * Create Household Form Submission
             */
            document.getElementById('createHouseholdForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const family_no = document.getElementById('family_no').value.trim();
                const full_name = document.getElementById('full_name').value.trim();
                const address = document.getElementById('address').value.trim();
                const income = document.getElementById('income').value;

                // Validation
                if (!family_no || !full_name || !address) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please fill all required fields (Family No, Full Name, and Address).',
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
                            family_no: parseInt(family_no),
                            full_name: full_name,
                            address: address,
                            income: income ? parseFloat(income) : 0.00
                        }),
                        signal: AbortSignal.timeout(30000) // 30 second timeout
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        // Close modal first to prevent focus issues
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createHouseholdModal'));
                        modal.hide();
                        
                        // Reset form
                        document.getElementById('createHouseholdForm').reset();
                        
                        // Wait for modal to fully close before showing alert
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: result.message,
                                confirmButtonColor: '#6ec207'
                            }).then(() => {
                                location.reload();
                            });
                        }, 300);
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
                    
                    let errorMessage = 'Failed to connect to the server. Please check your connection and try again.';
                    if (error.name === 'TimeoutError') {
                        errorMessage = 'Request timed out. The server is taking too long to respond.';
                    } else if (error.message.includes('HTTP error')) {
                        errorMessage = 'Server error: ' + error.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: errorMessage,
                        confirmButtonColor: '#dc3545'
                    });
                } finally {
                    // Restore button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });

            /**
             * Update Household Form Submission with Member Management
             */
            document.getElementById('updateHouseholdForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const householdId = document.getElementById('household_id_edit').value;
                const family_no = document.getElementById('family_no_edit').value.trim();
                const full_name = document.getElementById('full_name_edit').value.trim();
                const address = document.getElementById('address_edit').value.trim();
                const income = document.getElementById('income_edit').value;

                if (!householdId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Household ID is missing.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!family_no || !full_name || !address) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please fill all required fields (Family No, Full Name, and Address).',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                // Collect member operations
                const memberOperations = {
                    add: [],
                    update: [],
                    delete: []
                };

                // Collect existing members to update
                const existingMembers = document.querySelectorAll('.member-form[data-member-type="existing"]');
                existingMembers.forEach(form => {
                    const residentId = form.getAttribute('data-resident-id');
                    const isDeleted = form.getAttribute('data-deleted') === 'true';

                    if (isDeleted) {
                        memberOperations.delete.push(residentId);
                    } else {
                        const firstNames = form.querySelectorAll('input[name="existing_member_first_name[]"]');
                        const middleNames = form.querySelectorAll('input[name="existing_member_middle_name[]"]');
                        const lastNames = form.querySelectorAll('input[name="existing_member_last_name[]"]');
                        const birthDates = form.querySelectorAll('input[name="existing_member_birth_date[]"]');
                        const genders = form.querySelectorAll('select[name="existing_member_gender[]"]');
                        const contacts = form.querySelectorAll('input[name="existing_member_contact[]"]');
                        const emails = form.querySelectorAll('input[name="existing_member_email[]"]');
                        const ages = form.querySelectorAll('input[name="existing_member_age[]"]');

                        if (firstNames.length > 0) {
                            const birthDate = birthDates[0].value;
                            const age = ages.length > 0 && ages[0].value ? parseInt(ages[0].value) : calculateAge(birthDate);
                            
                            memberOperations.update.push({
                                resident_id: residentId,
                                first_name: firstNames[0].value.trim(),
                                middle_name: middleNames[0]?.value.trim() || '',
                                last_name: lastNames[0].value.trim(),
                                birth_date: birthDate,
                                gender: genders[0].value,
                                age: age,
                                contact_no: contacts[0]?.value.trim() || '',
                                email: emails[0]?.value.trim() || ''
                            });
                        }
                    }
                });

                // Collect new members to add
                const newMembers = document.querySelectorAll('.member-form[data-member-type="new"]');
                newMembers.forEach(form => {
                    const firstName = form.querySelector('input[name="member_first_name_edit[]"]')?.value.trim();
                    const middleName = form.querySelector('input[name="member_middle_name_edit[]"]')?.value.trim();
                    const lastName = form.querySelector('input[name="member_last_name_edit[]"]')?.value.trim();
                    const birthDate = form.querySelector('input[name="member_birth_date_edit[]"]')?.value;
                    const gender = form.querySelector('select[name="member_gender_edit[]"]')?.value;
                    const contact = form.querySelector('input[name="member_contact_edit[]"]')?.value.trim();
                    const email = form.querySelector('input[name="member_email_edit[]"]')?.value.trim();
                    const age = form.querySelector('input[name="member_age_edit[]"]')?.value;

                    if (firstName && lastName && birthDate && gender) {
                        memberOperations.add.push({
                            first_name: firstName,
                            middle_name: middleName || '',
                            last_name: lastName,
                            birth_date: birthDate,
                            gender: gender,
                            age: age ? parseInt(age) : calculateAge(birthDate),
                            contact_no: contact || '',
                            email: email || ''
                        });
                    }
                });

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

                // Log member operations for debugging
                console.log('Member Operations:', memberOperations);

                try {
                    const response = await fetch(API_URL + '?action=updateWithMembers', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            household_id: householdId,
                            family_no: parseInt(family_no),
                            full_name: full_name,
                            address: address,
                            income: income ? parseFloat(income) : 0.00,
                            memberOperations: memberOperations
                        }),
                        signal: AbortSignal.timeout(30000) // 30 second timeout
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        // Close modal first to prevent focus issues
                        const modal = bootstrap.Modal.getInstance(document.getElementById('updateHouseholdModal'));
                        modal.hide();
                        
                        // Wait for modal to fully close before showing alert
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: result.message,
                                confirmButtonColor: '#6ec207'
                            }).then(() => {
                                location.reload();
                            });
                        }, 300);
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
                    
                    let errorMessage = 'Failed to connect to the server. Please check your connection and try again.';
                    if (error.name === 'AbortError') {
                        errorMessage = 'Request timed out. The server is taking too long to respond.';
                    } else if (error.message.includes('HTTP error')) {
                        errorMessage = 'Server error: ' + error.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: errorMessage,
                        confirmButtonColor: '#dc3545'
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });

            /**
             * Delete Household Form Submission
             */
            document.getElementById('deleteHouseholdForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const householdId = document.getElementById('delete_household_id').value;
                const householdNo = document.getElementById('delete_household_no').value;
                const confirmDelete = document.getElementById('confirm_delete_household').value.trim();

                if (confirmDelete !== householdNo) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirmation Failed',
                        text: 'The household ID does not match. Please type the correct household ID.',
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
                            household_id: householdId
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
                                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteHouseholdModal'));
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
             * Handle Edit Button Click - Populate Update Modal and Load Members
             */
            document.addEventListener('click', function(e) {
                if (e.target.closest('button[data-bs-target="#updateHouseholdModal"]')) {
                    const row = e.target.closest('tr');
                    const householdId = row.querySelector('td:nth-child(1)').textContent;
                    const family_no = row.querySelector('td:nth-child(2)').textContent;
                    const full_name = row.querySelector('td:nth-child(3)').textContent;
                    const address = row.querySelector('td:nth-child(4)').textContent;
                    const income = row.querySelector('td:nth-child(5)').textContent;

                    document.getElementById('household_id_edit').value = householdId;
                    document.getElementById('household_id_display').value = householdId;
                    document.getElementById('family_no_edit').value = family_no;
                    document.getElementById('full_name_edit').value = full_name;
                    document.getElementById('address_edit').value = address;
                    document.getElementById('income_edit').value = parseFloat(income.replace(/,/g, ''));
                    
                    // Load existing members
                    loadExistingMembers(householdId);
                }

                if (e.target.closest('button[data-bs-target="#deleteHouseholdModal"]')) {
                    const row = e.target.closest('tr');
                    const householdId = row.querySelector('td:nth-child(1)').textContent;

                    document.getElementById('delete_household_id').value = householdId;
                    document.getElementById('delete_household_no').value = householdId;
                    document.getElementById('confirm_delete_household').value = '';
                }
            });

            /**
             * Clear forms when modals are hidden
             */
            document.getElementById('createHouseholdModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('createHouseholdForm').reset();
            });

            document.getElementById('updateHouseholdModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('updateHouseholdForm').reset();
                document.getElementById('membersContainerEdit').innerHTML = '<div class="text-center py-3" id="loadingMembersEdit"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading members...</span></div></div>';
                editMemberCount = 0;
            });

            document.getElementById('deleteHouseholdModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('deleteHouseholdForm').reset();
            });

            /**
             * Handle View Members Button Click
             */
            document.addEventListener('click', async function(e) {
                if (e.target.closest('.view-members-btn')) {
                    const btn = e.target.closest('.view-members-btn');
                    const householdId = btn.getAttribute('data-household-id');
                    const householdName = btn.getAttribute('data-household-name');

                    // Set modal title
                    document.getElementById('modalHouseholdName').textContent = householdName;

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('viewMembersModal'));
                    modal.show();

                    // Show loading state
                    document.getElementById('membersLoadingSpinner').style.display = 'block';
                    document.getElementById('membersContent').style.display = 'none';

                    try {
                        const response = await fetch(API_URL + `?action=getMembers&household_id=${householdId}`);
                        const result = await response.json();

                        if (result.success && result.data && result.data.length > 0) {
                            // Populate members table
                            const tbody = document.getElementById('membersTableBody');
                            tbody.innerHTML = '';

                            result.data.forEach(member => {
                                const row = `
                                    <tr>
                                        <td>${member.resident_id}</td>
                                        <td>${member.first_name} ${member.middle_name || ''} ${member.last_name}</td>
                                        <td>${member.birth_date}</td>
                                        <td>${member.age || 'N/A'}</td>
                                        <td>${member.gender}</td>
                                        <td>${member.contact_no || 'N/A'}</td>
                                        <td>${member.email || 'N/A'}</td>
                                    </tr>
                                `;
                                tbody.insertAdjacentHTML('beforeend', row);
                            });

                            document.getElementById('noMembersMessage').style.display = 'none';
                        } else {
                            // Show no members message
                            document.getElementById('membersTableBody').innerHTML = '';
                            document.getElementById('noMembersMessage').style.display = 'block';
                        }

                        // Hide loading, show content
                        document.getElementById('membersLoadingSpinner').style.display = 'none';
                        document.getElementById('membersContent').style.display = 'block';

                    } catch (error) {
                        console.error('Error:', error);
                        document.getElementById('membersLoadingSpinner').style.display = 'none';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load household members.',
                            confirmButtonColor: '#dc3545'
                        });
                        modal.hide();
                    }
                }
            });

            /**
             * Clear member list when modal closes
             */
            document.getElementById('viewMembersModal').addEventListener('hide.bs.modal', function() {
                document.getElementById('membersTableBody').innerHTML = '';
                document.getElementById('noMembersMessage').style.display = 'none';
            });
        </script>
    </body>
</html>