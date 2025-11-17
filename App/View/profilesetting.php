<!DOCTYPE html>
<html lang="en">
    <?php 
        // Include authentication protection
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        
        include 'template/header.php';
        require_once __DIR__ . '/../Model/User.php';
        require_once __DIR__ . '/../Config/Database.php';
        
        // Get current logged-in user
        $current_user_id = getCurrentUserId();
        
        // Initialize database connection
        $db = new Database();
        $connection = $db->connect();
        
        // Fetch current user profile
        $user = [];
        if ($current_user_id) {
            $userModel = new User();
            $user = $userModel->getById($current_user_id);
        }
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
                        <h1 class="mt-4">Profile Settings</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Profile Settings</li>
                        </ol>

                        <div class="row">
                            <!-- Profile Card -->
                            <div class="col-lg-4 mb-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <i class="fas fa-user-circle me-2"></i>Profile Information
                                    </div>
                                    <div class="card-body text-center">
                                        <div id="profilePictureContainer" class="mb-3">
                                            <?php if ($user && $user['profile_picture']): ?>
                                                <img id="profilePicturePreview" src="/assets/uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                                                     alt="Profile Picture" class="rounded-circle" width="150" height="150" style="object-fit: cover; border: 3px solid #6ec207;">
                                            <?php else: ?>
                                                    <h6 class="text-muted">No Profile Picture</h6>
                                                    <i class="fas fa-user-circle fa-7x text-muted"></i>
                                            <?php endif; ?>
                                        </div>

                                        <h5><?php echo htmlspecialchars($user['fullname'] ?? 'User'); ?></h5>
                                        <p class="text-muted"><?php echo htmlspecialchars($user['username'] ?? ''); ?></p>

                                        <div class="mb-3">
                                            <small class="d-block text-muted"><strong>Email:</strong></small>
                                            <small><?php echo htmlspecialchars($user['email'] ?? ''); ?></small>
                                        </div>

                                        <div class="mb-3">
                                            <small class="d-block text-muted"><strong>User ID:</strong></small>
                                            <small class="badge bg-warning"><?php echo htmlspecialchars($user['user_id'] ?? ''); ?></small>
                                        </div>

                                        <div class="mb-3">
                                            <small class="d-block text-muted"><strong>Status:</strong></small>
                                            <?php if ($user['status'] === 'active'): ?>
                                                <small class="badge bg-success">Active</small>
                                            <?php else: ?>
                                                <small class="badge bg-danger">Inactive</small>
                                            <?php endif; ?>
                                        </div>

                                        <hr>

                                        <button type="button" class="btn btn-sm btn-success w-100" data-bs-toggle="modal" data-bs-target="#uploadProfilePictureModal">
                                            <i class="fas fa-camera me-1"></i>Upload Picture
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings Panels -->
                            <div class="col-lg-8">
                                <!-- Update Password Card -->
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <i class="fas fa-key me-2"></i>Change Password
                                    </div>
                                    <div class="card-body">
                                        <form id="passwordForm">
                                            <div class="mb-3">
                                                <label for="currentPassword" class="form-label">Current Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="currentPassword" name="current_password" placeholder="Enter your current password" required>
                                                <small class="text-muted">Enter your current password to verify your identity.</small>
                                            </div>

                                            <hr>

                                            <div class="mb-3">
                                                <label for="newPassword" class="form-label">New Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="newPassword" name="new_password" placeholder="Enter your new password" required>
                                                <small class="text-muted">Must be at least 6 characters long.</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="confirmPassword" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="Re-enter your new password" required>
                                                <small class="text-muted">Must match the new password above.</small>
                                            </div>

                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save me-1"></i>Update Password
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Delete Account Card -->
                                <div class="card border-danger mb-4">
                                    <div class="card-header bg-danger text-white">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                                    </div>
                                    <div class="card-body">
                                        <h6 class="text-danger mb-3"><strong>Delete Your Account Permanently</strong></h6>
                                        <p class="text-muted mb-3">
                                            Once you delete your account, there is no going back. Please be certain.
                                        </p>
                                        <div class="alert alert-warning" role="alert">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <strong>Warning!</strong> This action is permanent and cannot be undone. All your data will be deleted.
                                        </div>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                            <i class="fas fa-trash-alt me-1"></i>Delete Account
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                <!-- END CONTENT-->
                <?php include 'template/footer.php'; ?>
            </div>
        </div>

        <!-- Upload Profile Picture Modal -->
        <div class="modal fade" id="uploadProfilePictureModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel"><i class="fas fa-camera me-2"></i>Upload Profile Picture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="uploadPictureForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="profilePictureInput" class="form-label">Select Image <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="profilePictureInput" name="profile_picture" accept="image/*" required>
                                <small class="text-muted">
                                    Accepted formats: JPEG, PNG, GIF. Max size: 5MB
                                </small>
                            </div>
                            <div id="previewContainer" class="text-center mb-3" style="display: none;">
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-2"></i>Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Account Modal -->
        <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="deleteAccountLabel">
                            <i class="fas fa-exclamation-triangle me-2"></i>Delete Account Permanently
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteAccountForm">
                        <div class="modal-body">
                            <div class="alert alert-danger" role="alert">
                                <strong>This action cannot be undone!</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Your account will be permanently deleted</li>
                                    <li>All your personal information will be removed</li>
                                    <li>You will be immediately logged out</li>
                                </ul>
                            </div>

                            <div class="mb-3">
                                <label for="deletePassword" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="deletePassword" name="password" 
                                       placeholder="Enter your password to confirm deletion" required>
                                <small class="text-muted">We need your password to confirm this action.</small>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmDelete" name="confirm" required>
                                <label class="form-check-label" for="confirmDelete">
                                    I understand and confirm that I want to delete my account permanently
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i>Delete My Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <?php include 'template/script.php'; ?>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            const API_URL = '../../App/Controller/ProfileController.php';

            // Image preview before upload
            document.getElementById('profilePictureInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('preview').src = event.target.result;
                        document.getElementById('previewContainer').style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });

            /**
             * Upload Profile Picture Form Submission
             */
            document.getElementById('uploadPictureForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const fileInput = document.getElementById('profilePictureInput');
                const file = fileInput.files[0];

                if (!file) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No File Selected',
                        text: 'Please select an image file to upload.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                const formData = new FormData();
                formData.append('profile_picture', file);

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

                try {
                    const response = await fetch(API_URL + '?action=uploadProfilePicture', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.message,
                            confirmButtonColor: '#6ec207'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('uploadPictureForm').reset();
                                document.getElementById('previewContainer').style.display = 'none';
                                const modal = bootstrap.Modal.getInstance(document.getElementById('uploadProfilePictureModal'));
                                modal.hide();
                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Failed',
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
             * Update Password Form Submission
             */
            document.getElementById('passwordForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const currentPassword = document.getElementById('currentPassword').value;
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                // Validation
                if (!currentPassword) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please enter your current password.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (newPassword.length < 6) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Weak Password',
                        text: 'New password must be at least 6 characters long.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (newPassword !== confirmPassword) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Password Mismatch',
                        text: 'New passwords do not match.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

                try {
                    const response = await fetch(API_URL + '?action=updatePassword', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            current_password: currentPassword,
                            new_password: newPassword,
                            confirm_password: confirmPassword
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
                            document.getElementById('passwordForm').reset();
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
             * Delete Account Form Submission
             */
            document.getElementById('deleteAccountForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const password = document.getElementById('deletePassword').value;
                const confirmCheckbox = document.getElementById('confirmDelete').checked;

                if (!password) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Password Required',
                        text: 'Please enter your password to confirm deletion.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                if (!confirmCheckbox) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Confirmation Required',
                        text: 'Please check the confirmation checkbox.',
                        confirmButtonColor: '#6ec207'
                    });
                    return;
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

                try {
                    const response = await fetch(API_URL + '?action=deleteAccount', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            password: password
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Account Deleted!',
                            text: 'Your account has been permanently deleted. You will be redirected to the login page.',
                            confirmButtonColor: '#6ec207',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then(() => {
                            setTimeout(() => {
                                window.location.href = '/index.php';
                            }, 1000);
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
        </script>
    </body>
</html>