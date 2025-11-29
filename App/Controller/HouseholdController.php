<?php
/**
 * Household Controller
 * API endpoints for household CRUD operations
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../Config/Auth.php';
require_once __DIR__ . '/../Model/Household.php';

// require authenticated user
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$householdModel = new Household();
$action = isset($_GET['action']) ? $_GET['action'] : null;

switch ($action) {
    case 'create':
        handleCreate();
        break;
    case 'createWithMembers':
        handleCreateWithMembers();
        break;
    case 'update':
        handleUpdate();
        break;
    case 'updateWithMembers':
        handleUpdateWithMembers();
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
    case 'getMembers':
        handleGetMembers();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function handleCreate() {
    global $householdModel;
    
    error_log('handleCreate called at ' . date('Y-m-d H:i:s'));

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    error_log('Create data: ' . json_encode($data));

    // Required fields: family_no, full_name, address
    if (empty($data['family_no']) || empty($data['full_name']) || empty($data['address'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Family No, Full Name, and Address are required']);
        return;
    }

    $family_no = $data['family_no'];
    $full_name = $data['full_name'];
    $address = $data['address'];
    $income = $data['income'] ?? 0.00;

    $result = $householdModel->create($family_no, $full_name, $address, $income);
    error_log('Create result: ' . json_encode($result));
    echo json_encode($result);
    exit;
}

function handleUpdate() {
    global $householdModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['household_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Household ID is required']);
        return;
    }

    $household_id = $data['household_id'];
    $family_no = $data['family_no'] ?? 0;
    $full_name = $data['full_name'] ?? '';
    $address = $data['address'] ?? '';
    $income = $data['income'] ?? 0.00;

    $result = $householdModel->update($household_id, $family_no, $full_name, $address, $income);
    echo json_encode($result);
    exit;
}

function handleDelete() {
    global $householdModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['household_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Household ID is required']);
        return;
    }

    $household_id = $data['household_id'];
    $result = $householdModel->delete($household_id);
    echo json_encode($result);
}

function handleUpdateWithMembers() {
    global $householdModel;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    // Log request for debugging
    error_log('UpdateWithMembers request: ' . json_encode($data));
    
    $household_id = $data['household_id'] ?? null;
    $family_no = $data['family_no'] ?? null;
    $full_name = $data['full_name'] ?? null;
    $address = $data['address'] ?? null;
    $income = $data['income'] ?? 0.00;
    $memberOperations = $data['memberOperations'] ?? [];

    if (!$household_id || !$family_no || !$full_name || !$address) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }

    $result = $householdModel->updateWithMembers($household_id, $family_no, $full_name, $address, $income, $memberOperations);
    
    // Log result for debugging
    error_log('UpdateWithMembers result: ' . json_encode($result));
    
    echo json_encode($result);
}

function handleGetAll() {
    global $householdModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $rows = $householdModel->getAll();
    echo json_encode(['success' => true, 'data' => $rows]);
}

function handleGetById() {
    global $householdModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    if (empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Household ID is required']);
        return;
    }

    $household_id = $_GET['id'];
    $row = $householdModel->getById($household_id);
    if ($row) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Household not found']);
    }
}

function handleCreateWithMembers() {
    global $householdModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Log request for debugging
    error_log('CreateWithMembers request: ' . json_encode($data));

    // Required fields: family_no, full_name, address
    if (empty($data['family_no']) || empty($data['full_name']) || empty($data['address'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Family No, Full Name, and Address are required']);
        return;
    }

    $family_no = $data['family_no'];
    $full_name = $data['full_name'];
    $address = $data['address'];
    $income = $data['income'] ?? 0.00;
    $members = $data['members'] ?? [];

    $result = $householdModel->createWithMembers($family_no, $full_name, $address, $income, $members);
    
    // Log result for debugging
    error_log('CreateWithMembers result: ' . json_encode($result));
    
    echo json_encode($result);
    exit;
}

function handleGetMembers() {
    global $householdModel;

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    if (empty($_GET['household_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Household ID is required']);
        return;
    }

    $household_id = $_GET['household_id'];
    $members = $householdModel->getMembers($household_id);
    echo json_encode(['success' => true, 'data' => $members]);
}

?>
