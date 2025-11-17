<?php
/**
 * Role Controller
 * Handles API endpoints for role CRUD operations
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../Config/Auth.php';
require_once __DIR__ . '/../Model/Role.php';

// Check if user is authenticated
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Initialize Role model
$roleModel = new Role();
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
 * Handle Create Role
 */
function handleCreate() {
    global $roleModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($data['role_name'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Role name is required']);
        return;
    }

    $role_name = $data['role_name'];

    $result = $roleModel->create($role_name);
    echo json_encode($result);
}

/**
 * Handle Update Role
 */
function handleUpdate() {
    global $roleModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($data['role_id']) || empty($data['role_name'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Role ID and Role name are required']);
        return;
    }

    $role_id = $data['role_id'];
    $role_name = $data['role_name'];

    $result = $roleModel->update($role_id, $role_name);
    echo json_encode($result);
}

/**
 * Handle Delete Role
 */
function handleDelete() {
    global $roleModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($data['role_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Role ID is required']);
        return;
    }

    $role_id = $data['role_id'];
    $result = $roleModel->delete($role_id);
    echo json_encode($result);
}

/**
 * Handle Get All Roles
 */
function handleGetAll() {
    global $roleModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $roles = $roleModel->getAll();
    echo json_encode(['success' => true, 'data' => $roles]);
}

/**
 * Handle Get Role By ID
 */
function handleGetById() {
    global $roleModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    if (empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Role ID is required']);
        return;
    }

    $role_id = $_GET['id'];
    $role = $roleModel->getById($role_id);

    if ($role) {
        echo json_encode(['success' => true, 'data' => $role]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Role not found']);
    }
}
?>
