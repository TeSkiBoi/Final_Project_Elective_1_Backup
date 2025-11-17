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
                                <h1 class="mt-4">Contact US</h1>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="dashboard.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Enrollment</li>
                                </ol>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Enrollment
                                </button>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-book me-1"></i>
                                    Enrollment List
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Enrollment ID</th>
                                            <th>Student ID</th>
                                            <th>Course ID</th>
                                            <th>Semester</th>
                                            <th>Year Level</th>
                                            <th>Academic Year</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                
                                        <tr>
                                            <td>ENR001</td>
                                            <td>STU001</td>
                                            <td>CRS001</td>
                                            <td>1</td>
                                            <td>1</td>
                                            <td>2023-2024</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-1">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger">
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
        <!-- Logout Modal -->
        <?php include 'template/script.php'; ?>
    </body>
</html>