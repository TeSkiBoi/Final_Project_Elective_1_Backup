<?php
/**
 * Logout API Endpoint
 */

require_once __DIR__ . '/../Config/Auth.php';
require_once __DIR__ . '/../Model/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($action === 'logout') {
        if (isAuthenticated()) {
            // Log logout activity
            $user_id = getCurrentUserId();
            $userModel = new User();
            $ip_address = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : 
                         (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
            $userModel->logUserActivity($user_id, 'User logged out', $ip_address);
        }

        // Destroy session
        session_unset();
        session_destroy();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
        exit;
    }
}

header('Content-Type: application/json');
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
