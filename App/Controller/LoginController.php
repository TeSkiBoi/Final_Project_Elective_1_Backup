<?php
/**
 * Login Controller
 * Handles user authentication and login logic
 */

require_once __DIR__ . '/../Config/Auth.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Model/User.php';

class LoginController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Handle login request
     */
    public function login() {
        // Check if request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        // Get JSON data from request body
        $data = json_decode(file_get_contents("php://input"), true);

        // Validate required fields
        if (!isset($data['username']) || empty(trim($data['username']))) {
            $this->sendResponse(false, 'Username is required', null, 400);
            return;
        }

        if (!isset($data['password']) || empty($data['password'])) {
            $this->sendResponse(false, 'Password is required', null, 400);
            return;
        }

        $username = trim($data['username']);
        $password = $data['password'];

        // Get user from database
        $user = $this->userModel->getUserByUsername($username);

        // Check if user exists
        if (!$user) {
            $this->sendResponse(false, 'Invalid username or password', null, 401);
            return;
        }

        // Check if user is active
        if ($user['status'] !== 'active') {
            $this->sendResponse(false, 'Your account has been deactivated. Please contact the administrator.', null, 403);
            return;
        }

        // Verify password
        if (!$this->userModel->verifyPassword($password, $user['password_hash'])) {
            $this->sendResponse(false, 'Invalid username or password', null, 401);
            return;
        }

        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['status'] = $user['status'];
        $_SESSION['login_time'] = time();

        // Log user activity
        $ip_address = $this->getClientIpAddress();
        $this->userModel->logUserActivity($user['user_id'], 'User logged in', $ip_address);

        // Get role name
        $role_name = $this->userModel->getUserRole($user['role_id']);

        $this->sendResponse(true, 'Login successful', [
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'fullname' => $user['fullname'],
            'role' => $role_name,
            'status' => $user['status']
        ], 200);
    }

    /**
     * Handle logout request
     */
    public function logout() {
        if (isAuthenticated()) {
            $user_id = getCurrentUserId();
            $ip_address = $this->getClientIpAddress();
            $this->userModel->logUserActivity($user_id, 'User logged out', $ip_address);
        }

        logout();
    }

    /**
     * Get current session info
     */
    public function getCurrentUser() {
        if (!isAuthenticated()) {
            $this->sendResponse(false, 'User not authenticated', null, 401);
            return;
        }

        $this->sendResponse(true, 'User retrieved', [
            'user_id' => getCurrentUserId(),
            'username' => getCurrentUsername(),
            'fullname' => getCurrentUserFullName(),
            'role_id' => getCurrentUserRole()
        ], 200);
    }

    /**
     * Get client IP address
     */
    private function getClientIpAddress() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Send JSON response
     */
    private function sendResponse($success, $message, $data = null, $status_code = 200) {
        header('Content-Type: application/json');
        http_response_code($status_code);

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    $controller = new LoginController();

    switch ($action) {
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login();
            }
            break;
        case 'logout':
            $controller->logout();
            break;
        case 'current':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller->getCurrentUser();
            }
            break;
        default:
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}
?>
