<!DOCTYPE html>
<html lang="en">
    <?php
        // Include authentication protection
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        include 'template/header.php';
        //FUNCTION TO GET ALL THE FUNCTIONS CREATED IN DASHBOARD MODEL
        require_once __DIR__ . '/../Model/dashboard.php';
        $dashboardModel = new Dashboard();

    ?>
    
    <body class="sb-nav-fixed">

        <?php include 'template/header_navigation.php'; ?>

        <div id="layoutSidenav">
            <?php include 'template/sidebar_navigation.php'; ?>
            <!--CONTENT OF THE PAGE -->
            <div id="layoutSidenav_content">

                <!-- CONTENT HERE -->
                 <?php include 'template/dashboard.php'; ?>   
                <!-- END CONTENT-->

                <?php include 'template/footer.php'; ?>
            </div>
        </div>
        <!-- Logout Modal -->
        <?php include 'template/script.php'; ?>
    </body>
</html>