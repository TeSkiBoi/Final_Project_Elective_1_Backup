<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Main</div>
                <a class="nav-link" href="index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                <?php if($roleId == 1 || $roleId == 2): ?>
                    <div class="sb-sidenav-menu-heading">Records</div>
                    <a class="nav-link" href="students.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                        Residents
                    </a>
                    <a class="nav-link" href="certificate_generator.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-signature"></i></div>
                        Certificate Generator
                    </a>
                    <a class="nav-link" href="enrollment.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                        Households
                    </a>
                    <a class="nav-link" href="patient.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                        Seniors
                    </a>
                    <a class="nav-link" href="patient.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                        Adults
                    </a>
                    <a class="nav-link" href="patient.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                        Teenagers
                    </a>
                    <a class="nav-link" href="patient.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                        Children
                    </a>
                    <div class="sb-sidenav-menu-heading">Records</div>
                <?php endif; ?>

                <?php if($roleId == 2): ?>
                 <div class="sb-sidenav-menu-heading">Other</div>
                    <a class="nav-link" href="contact.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                        Contact Us
                    </a>
                <?php endif; ?>

                <?php if($roleId == 1): ?>
                    <div class="sb-sidenav-menu-heading">Maintenance</div>
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseMaintenance" aria-expanded="false" aria-controls="collapseMaintenance">
                        <div class="sb-nav-link-icon"><i class="fas fa-wrench"></i></div>
                        Maintenance
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-chevron-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseMaintenance" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="faculty.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                                faculty
                            </a>
                            <a class="nav-link" href="courses.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                                Courses
                            </a>
                            <a class="nav-link" href="department.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                                Department
                            </a>
                            <a class="nav-link" href="user.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                User
                            </a>
                            <a class="nav-link" href="role.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                Role
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