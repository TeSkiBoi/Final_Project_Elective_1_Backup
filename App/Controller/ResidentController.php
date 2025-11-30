<?php
/**
 * Resident Controller
 * API endpoints for resident CRUD
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../Config/Auth.php';
require_once __DIR__ . '/../Model/Resident.php';

// Authentication
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$residentModel = new Resident();
$action = isset($_GET['action']) ? $_GET['action'] : null;

switch ($action) {
    case 'create': handleCreate(); break;
    case 'update': handleUpdate(); break;
    case 'delete': handleDelete(); break;
    case 'getAll': handleGetAll(); break;
    case 'getById': handleGetById(); break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function handleCreate() {
    global $residentModel;
    
    // Only Admin can create residents
    if (getCurrentUserRole() != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access Denied. Only Admin can create residents.']);
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        return;
    }

    $result = $residentModel->create($data);
    echo json_encode($result);
    exit;
}

function handleUpdate() {
    global $residentModel;
    
    // Only Admin can update residents
    if (getCurrentUserRole() != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access Denied. Only Admin can update residents.']);
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['resident_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Resident ID is required']);
        return;
    }

    $resident_id = $data['resident_id'];
    $result = $residentModel->update($resident_id, $data);
    echo json_encode($result);
    exit;
}

function handleDelete() {
    global $residentModel;
    
    // Only Admin can delete residents
    if (getCurrentUserRole() != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access Denied. Only Admin can delete residents.']);
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['resident_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Resident ID is required']);
        return;
    }

    $resident_id = $data['resident_id'];
    $result = $residentModel->delete($resident_id);
    echo json_encode($result);
    exit;
}

function handleGetAll() {
    global $residentModel;
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $rows = $residentModel->getAll();
    echo json_encode(['success' => true, 'data' => $rows]);
}

function handleGetById() {
    global $residentModel;
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    if (empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Resident ID is required']);
        return;
    }

    $id = $_GET['id'];
    $row = $residentModel->getById($id);
    if ($row) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Resident not found']);
    }
}

?>
