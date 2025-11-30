<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Main/Records</div>
                <a class="nav-link" href="index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                
                <?php if($roleId == 1): ?>
                    <!-- ADMIN VIEW -->
                    <div class="sb-sidenav-menu-heading">Records</div>
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFeatures2" aria-expanded="false" aria-controls="collapseFeatures2">
                        <div class="sb-nav-link-icon"><i class="fas fa-th-large"></i></div>
                        Records
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-chevron-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseFeatures2">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="household.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                Household
                            </a>
                            <a class="nav-link" href="Resident.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Resident
                            </a>
                            <a class="nav-link" href="children.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-child"></i></div>
                                Children
                            </a>
                            <a class="nav-link" href="senior.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-clock"></i></div>
                                Seniors
                            </a>
                            <a class="nav-link" href="adult.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                Adult
                            </a>
                        </nav>
                    </div>
                    
                    <div class="sb-sidenav-menu-heading">Features</div>
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFeatures" aria-expanded="false" aria-controls="collapseFeatures">
                        <div class="sb-nav-link-icon"><i class="fas fa-th-large"></i></div>
                        Features
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-chevron-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseFeatures">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="certificate_generator.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-signature"></i></div>
                                Certificate Generator
                            </a>
                            <a class="nav-link" href="financial.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                Financial Management
                            </a>
                            <a class="nav-link" href="projects.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-project-diagram"></i></div>
                                Barangay Projects
                            </a>
                            <a class="nav-link" href="blotter.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                                Blotter & Incident Recording
                            </a>
                        </nav>
                    </div>
                <?php endif; ?>

                <?php if($roleId == 2): ?>
                    <!-- STAFF VIEW - View-Only Records, Edit-Enabled Projects & Financial -->
                    <div class="sb-sidenav-menu-heading">Records (View Only)</div>
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseRecordsStaff" aria-expanded="false" aria-controls="collapseRecordsStaff">
                        <div class="sb-nav-link-icon"><i class="fas fa-th-large"></i></div>
                        Records
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-chevron-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseRecordsStaff">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="household.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                Household
                            </a>
                            <a class="nav-link" href="Resident.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Resident
                            </a>
                            <a class="nav-link" href="children.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-child"></i></div>
                                Children
                            </a>
                            <a class="nav-link" href="senior.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-clock"></i></div>
                                Seniors
                            </a>
                            <a class="nav-link" href="adult.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                Adult
                            </a>
                        </nav>
                    </div>
                    
                    <div class="sb-sidenav-menu-heading">Features</div>
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFeaturesStaff" aria-expanded="false" aria-controls="collapseFeaturesStaff">
                        <div class="sb-nav-link-icon"><i class="fas fa-th-large"></i></div>
                        Features
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-chevron-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseFeaturesStaff">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="projects.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-project-diagram"></i></div>
                                Barangay Projects
                            </a>
                            <a class="nav-link" href="financial.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                                Financial Management
                            </a>
                        </nav>
                    </div>
                <?php endif; ?>

                <?php if($roleId == 1): ?>
                    <div class="sb-sidenav-menu-heading">Maintenance</div>
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseMaintenanceAdmin" aria-expanded="false" aria-controls="collapseMaintenanceAdmin">
                        <div class="sb-nav-link-icon"><i class="fas fa-wrench"></i></div>
                        Maintenance
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-chevron-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseMaintenanceAdmin">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="user.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                User
                            </a>
                            <a class="nav-link" href="role.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-tag"></i></div>
                                Role
                            </a>
                            <a class="nav-link" href="barangay_officials.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-sitemap"></i></div>
                                Barangay Official Org Chart
                            </a>
                        </nav>
                    </div>
                <?php endif; ?>
                
                
                
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            <?php echo htmlspecialchars($currentUsername); ?>
        </div>
    </nav>
</div>