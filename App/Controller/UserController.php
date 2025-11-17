<?php
/**
 * User Controller
 * Handles API endpoints for user CRUD operations
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../Config/Auth.php';
require_once __DIR__ . '/../Model/User.php';

// Check if user is authenticated
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Initialize User model
$userModel = new User();
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Handle different actions
switch ($action) {
    case 'create':
        handleCreate();
        break;
    case 'update':
        handleUpdate();
        break;
    case 'delete':
        handleDelete();
        break;
    case 'changeStatus':
        handleChangeStatus();
        break;
    case 'getAll':
        handleGetAll();
        break;
    case 'getById':
        handleGetById();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

/**
 * Handle Create User
 */
function handleCreate() {
    global $userModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($data['fullname']) || empty($data['username']) || empty($data['email']) || 
        empty($data['password']) || empty($data['role_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }

    $fullname = $data['fullname'];
    $username = $data['username'];
    $email = $data['email'];
    $password = $data['password'];
    $role_id = $data['role_id'];
    $status = $data['status'] ?? 'active';

    $result = $userModel->create($fullname, $username, $email, $password, $role_id, $status);
    echo json_encode($result);
}

/**
 * Handle Update User
 */
function handleUpdate() {
    global $userModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($data['user_id']) || empty($data['fullname']) || empty($data['username']) || 
        empty($data['email']) || empty($data['role_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }

    $user_id = $data['user_id'];
    $fullname = $data['fullname'];
    $username = $data['username'];
    $email = $data['email'];
    $role_id = $data['role_id'];
    $status = $data['status'] ?? 'active';

    $result = $userModel->update($user_id, $fullname, $username, $email, $role_id, $status);
    echo json_encode($result);
}

/**
 * Handle Delete User
 */
function handleDelete() {
    global $userModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($data['user_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
        return;
    }

    $user_id = $data['user_id'];
    $result = $userModel->delete($user_id);
    echo json_encode($result);
}

/**
 * Handle Get All Users
 */
function handleGetAll() {
    global $userModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $users = $userModel->getAll();
    echo json_encode(['success' => true, 'data' => $users]);
}

/**
 * Handle Get User By ID
 */
function handleGetById() {
    global $userModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    if (empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
        return;
    }

    $user_id = $_GET['id'];
    $user = $userModel->getById($user_id);

    if ($user) {
        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
}

/**
 * Handle Change User Status
 */
function handleChangeStatus() {
    global $userModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($data['user_id']) || empty($data['status'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID and Status are required']);
        return;
    }

    $user_id = $data['user_id'];
    $status = $data['status'];

    $result = $userModel->changeStatus($user_id, $status);
    echo json_encode($result);
}
?>
