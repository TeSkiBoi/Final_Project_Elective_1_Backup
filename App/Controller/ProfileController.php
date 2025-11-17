<?php
/**
 * Profile Controller
 * Handles user profile operations (password update, profile picture upload, account deletion)
 */

require_once __DIR__ . '/../Config/Auth.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Model/User.php';

class ProfileController {
    private $userModel;
    private $connection;
    private $uploadDir = __DIR__ . '/../../assets/uploads/profiles/';

    public function __construct() {
        $this->userModel = new User();
        $db = new Database();
        $this->connection = $db->connect();
        
        // Ensure upload directory exists
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Get current user profile
     */
    public function getUserProfile() {
        if (!isAuthenticated()) {
            $this->sendResponse(false, 'User not authenticated', null, 401);
            return;
        }

        $user_id = getCurrentUserId();
        $user = $this->userModel->getById($user_id);

        if (!$user) {
            $this->sendResponse(false, 'User not found', null, 404);
            return;
        }

        unset($user['password_hash']); // Don't send password hash
        $this->sendResponse(true, 'Profile retrieved', $user, 200);
    }

    /**
     * Update user password
     */
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        if (!isAuthenticated()) {
            $this->sendResponse(false, 'User not authenticated', null, 401);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        // Validate required fields
        if (!isset($data['current_password']) || empty($data['current_password'])) {
            $this->sendResponse(false, 'Current password is required', null, 400);
            return;
        }

        if (!isset($data['new_password']) || empty($data['new_password'])) {
            $this->sendResponse(false, 'New password is required', null, 400);
            return;
        }

        if (!isset($data['confirm_password']) || empty($data['confirm_password'])) {
            $this->sendResponse(false, 'Password confirmation is required', null, 400);
            return;
        }

        $current_password = $data['current_password'];
        $new_password = $data['new_password'];
        $confirm_password = $data['confirm_password'];

        // Validate password match
        if ($new_password !== $confirm_password) {
            $this->sendResponse(false, 'New passwords do not match', null, 400);
            return;
        }

        // Validate password strength (minimum 6 characters)
        if (strlen($new_password) < 6) {
            $this->sendResponse(false, 'Password must be at least 6 characters long', null, 400);
            return;
        }

        $user_id = getCurrentUserId();
        $user = $this->userModel->getById($user_id);

        if (!$user) {
            $this->sendResponse(false, 'User not found', null, 404);
            return;
        }

        // Verify current password
        if (!$this->userModel->verifyPassword($current_password, $user['password_hash'])) {
            $this->sendResponse(false, 'Current password is incorrect', null, 401);
            return;
        }

        // Update password
        $new_password_hash = md5($new_password);
        $query = "UPDATE users SET password_hash = ? WHERE user_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $new_password_hash, $user_id);

        if ($stmt->execute()) {
            $this->userModel->logUserActivity($user_id, 'User updated password', $this->getClientIpAddress());
            $this->sendResponse(true, 'Password updated successfully', null, 200);
        } else {
            $this->sendResponse(false, 'Failed to update password', null, 500);
        }
    }

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        if (!isAuthenticated()) {
            $this->sendResponse(false, 'User not authenticated', null, 401);
            return;
        }

        if (!isset($_FILES['profile_picture'])) {
            $this->sendResponse(false, 'No file uploaded', null, 400);
            return;
        }

        $file = $_FILES['profile_picture'];
        $user_id = getCurrentUserId();

        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->sendResponse(false, 'File upload failed', null, 400);
            return;
        }

        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            $this->sendResponse(false, 'Only JPEG, PNG, and GIF images are allowed', null, 400);
            return;
        }

        // Check file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->sendResponse(false, 'File size must not exceed 5MB', null, 400);
            return;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $user_id . '_' . time() . '.' . $extension;
        $filepath = $this->uploadDir . $filename;

        // Delete old profile picture
        $user = $this->userModel->getById($user_id);
        if ($user && $user['profile_picture']) {
            $old_file = $this->uploadDir . $user['profile_picture'];
            if (file_exists($old_file)) {
                unlink($old_file);
            }
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update database
            $query = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ss", $filename, $user_id);

            if ($stmt->execute()) {
                $this->userModel->logUserActivity($user_id, 'User uploaded profile picture', $this->getClientIpAddress());
                $this->sendResponse(true, 'Profile picture uploaded successfully', [
                    'profile_picture' => $filename,
                    'url' => '/assets/uploads/profiles/' . $filename
                ], 200);
            } else {
                unlink($filepath); // Delete uploaded file if database update fails
                $this->sendResponse(false, 'Failed to save profile picture', null, 500);
            }
        } else {
            $this->sendResponse(false, 'Failed to upload file', null, 500);
        }
    }

    /**
     * Delete user account permanently
     */
    public function deleteAccount() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        if (!isAuthenticated()) {
            $this->sendResponse(false, 'User not authenticated', null, 401);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        // Validate password confirmation
        if (!isset($data['password']) || empty($data['password'])) {
            $this->sendResponse(false, 'Password is required to delete account', null, 400);
            return;
        }

        $user_id = getCurrentUserId();
        $user = $this->userModel->getById($user_id);

        if (!$user) {
            $this->sendResponse(false, 'User not found', null, 404);
            return;
        }

        // Verify password
        if (!$this->userModel->verifyPassword($data['password'], $user['password_hash'])) {
            $this->sendResponse(false, 'Password is incorrect', null, 401);
            return;
        }

        try {
            // Log account deletion before deleting user
            $this->userModel->logUserActivity($user_id, 'User deleted account permanently', $this->getClientIpAddress());

            // Delete profile picture if exists
            if ($user['profile_picture']) {
                $old_file = $this->uploadDir . $user['profile_picture'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            // Delete user from database
            $deleteResult = $this->userModel->delete($user_id);

            if ($deleteResult) {
                // Logout the user
                session_unset();
                session_destroy();

                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                        $params["path"], $params["domain"],
                        $params["secure"], $params["httponly"]
                    );
                }

                $this->sendResponse(true, 'Account deleted successfully', null, 200);
            } else {
                $this->sendResponse(false, 'Failed to delete account', null, 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(false, 'Error deleting account: ' . $e->getMessage(), null, 500);
        }
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

    $controller = new ProfileController();

    switch ($action) {
        case 'getProfile':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller->getUserProfile();
            }
            break;
        case 'updatePassword':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->updatePassword();
            }
            break;
        case 'uploadProfilePicture':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->uploadProfilePicture();
            }
            break;
        case 'deleteAccount':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->deleteAccount();
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
