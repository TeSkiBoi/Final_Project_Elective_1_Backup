<?php
// Include auth functions to get user info
require_once __DIR__ . '/../../Config/Auth.php';

$currentUser = getCurrentUserFullName();
$currentUsername = getCurrentUsername();
$roleId = getCurrentUserRole();
?>
<nav class="sb-topnav navbar navbar-expand navbar-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="index.php">
        <img src="../../assets/img/MARSU LOGO.png" alt="MarSU Logo" height="40" class="me-2">
        MarSU Admin
    </a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
        <i class="fas fa-bars"></i>
    </button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
            <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
        </div>
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i> <?php echo htmlspecialchars($currentUsername); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><span class="dropdown-item-text"><small class="text-muted">Logged in as:</small><br><strong><?php echo htmlspecialchars($currentUser); ?></strong></span></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="profilesetting.php">Profile Settings</a></li>
                <li><a class="dropdown-item" href="activitylog.php">Activity Log</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="logoutModalLabel"><i class="fas fa-sign-out-alt me-2"></i>Confirm Logout</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to logout?</p>
                <small class="text-muted">You will be redirected to the login page.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmLogoutBtn"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Logout confirmation
    document.getElementById('confirmLogoutBtn').addEventListener('click', async function() {
        try {
            const response = await fetch('../../App/Controller/LogoutController.php?action=logout', {
                method: 'POST'
            });
            
            const result = await response.json();
            
            if (result.success) {
                window.location.href = '../../index.php?logout=success';
            }
        } catch (error) {
            console.error('Error:', error);
            // Force logout by going to index
            window.location.href = '../../index.php';
        }
    });
</script>