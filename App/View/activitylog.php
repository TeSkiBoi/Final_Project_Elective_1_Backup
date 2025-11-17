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
        
        // Fetch user logs for current user
        $userLogs = [];
        if ($current_user_id) {
            $query = "SELECT log_id, user_id, action, log_time, ip_address 
                      FROM user_logs 
                      WHERE user_id = ? 
                      ORDER BY log_time DESC";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("s", $current_user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $userLogs[] = $row;
            }
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
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="mt-4">Activity Log</h1>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Activity Log</li>
                                </ol>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-history me-1"></i>
                                    My Activity Log
                                </div>
                                <div>
                                    <small class="text-muted">User ID: <?php echo htmlspecialchars($current_user_id); ?></small>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="activityTable" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Log ID</th>
                                            <th>User ID</th>
                                            <th>Action</th>
                                            <th>Log Time</th>
                                            <th>IP Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                
                                        <?php if ($userLogs && count($userLogs) > 0): ?>
                                            <?php foreach ($userLogs as $log): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($log['log_id']); ?></td>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            <?php echo htmlspecialchars($log['user_id']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            $action = htmlspecialchars($log['action']);
                                                            if (strpos($action, 'logged in') !== false) {
                                                                echo '<span class="badge bg-success"><i class="fas fa-sign-in-alt me-1"></i>' . $action . '</span>';
                                                            } elseif (strpos($action, 'logged out') !== false) {
                                                                echo '<span class="badge bg-warning"><i class="fas fa-sign-out-alt me-1"></i>' . $action . '</span>';
                                                            } else {
                                                                echo '<span class="badge bg-info"><i class="fas fa-tasks me-1"></i>' . $action . '</span>';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($log['log_time']); ?></td>
                                                    <td>
                                                        <code><?php echo htmlspecialchars($log['ip_address']); ?></code>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox me-2"></i>No activity logs found.
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
        <?php include 'template/script.php'; ?>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Initialize DataTable for activity logs
            document.addEventListener('DOMContentLoaded', function() {
                // Check if DataTable is available
                if (typeof SimpleDatatables !== 'undefined') {
                    const dataTable = new SimpleDatatables.DataTable("#activityTable", {
                        searchable: true,
                        sortable: true,
                        perPage: 10,
                        labels: {
                            placeholder: "Search activity logs...",
                            perPage: "{select} entries per page",
                            noRows: "No entries found",
                            info: "Showing {start} to {end} of {rows} entries"
                        }
                    });
                }
            });
        </script>
    </body>
</html>