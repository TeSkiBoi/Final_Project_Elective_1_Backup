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
    case 'addResident':
        handleAddResident();
        break;
    case 'deleteResident':
        handleDeleteResident();
        break;
    case 'getMembersWithHead':
        handleGetMembersWithHead();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function handleCreate() {
    global $householdModel;
    
    // Only Admin can create households
    if (getCurrentUserRole() != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access Denied. Only Admin can create households.']);
        return;
    }
    
    error_log('handleCreate called at ' . date('Y-m-d H:i:s'));

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    error_log('Create data: ' . json_encode($data));

    // Required fields: family_no, address
    if (empty($data['family_no']) || empty($data['address'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Family No and Address are required']);
        return;
    }

    $family_no = $data['family_no'];
    $address = $data['address'];
    $income = $data['income'] ?? 0.00;

    $result = $householdModel->create($family_no, $address, $income);
    error_log('Create result: ' . json_encode($result));
    echo json_encode($result);
    exit;
}

function handleUpdate() {
    global $householdModel;
    
    // Only Admin can update households
    if (getCurrentUserRole() != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access Denied. Only Admin can update households.']);
        return;
    }

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
    $address = $data['address'] ?? '';
    $income = $data['income'] ?? 0.00;
    $household_head_id = $data['household_head_id'] ?? null;

    $result = $householdModel->update($household_id, $family_no, $address, $income, $household_head_id);
    echo json_encode($result);
    exit;
}

function handleDelete() {
    global $householdModel;
    
    // Only Admin can delete households
    if (getCurrentUserRole() != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access Denied. Only Admin can delete households.']);
        return;
    }

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
    
    // Only Admin can update households
    if (getCurrentUserRole() != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access Denied. Only Admin can update households.']);
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    // Log request for debugging
    error_log('UpdateWithMembers request: ' . json_encode($data));
    
    $household_id = $data['household_id'] ?? null;
    $family_no = $data['family_no'] ?? null;
    $address = $data['address'] ?? null;
    $income = $data['income'] ?? 0.00;
    $household_head_id = $data['household_head_id'] ?? null;
    $memberOperations = $data['memberOperations'] ?? [];

    if (!$household_id || !$family_no || !$address) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }

    $result = $householdModel->updateWithMembers($household_id, $family_no, $address, $income, $household_head_id, $memberOperations);
    
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
    
    // Only Admin can create households
    if (getCurrentUserRole() != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access Denied. Only Admin can create households.']);
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Log request for debugging
    error_log('CreateWithMembers request: ' . json_encode($data));

    // Required fields: family_no, address
    if (empty($data['family_no']) || empty($data['address'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Family No and Address are required']);
        return;
    }

    $family_no = $data['family_no'];
    $address = $data['address'];
    $income = $data['income'] ?? 0.00;
    $members = $data['members'] ?? [];

    $result = $householdModel->createWithMembers($family_no, $address, $income, $members);
    
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

function handleAddResident() {
    global $householdModel;
    
    // Only Admin can add residents
    if (getCurrentUserRole() != 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access Denied. Only Admin can add residents.']);
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    error_log('AddResident request: ' . json_encode($data));
    
    if (empty($data['household_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Household ID is required']);
        return;
    }
    
    require_once __DIR__ . '/../Model/Resident.php';
    $residentModel = new Resident();
    
    // Create the resident
    $residentResult = $residentModel->create($data);
    
    if (!$residentResult['success']) {
        echo json_encode($residentResult);
        return;
    }
    
    // Auto-assign household head based on member count
    $household_id = $data['household_id'];
    $headAssignResult = $householdModel->autoAssignHouseholdHead($household_id);
    
    // Fetch updated members list
    $members = $householdModel->getMembers($household_id);
    
    // Get updated household info
    $household = $householdModel->getById($household_id);
    
    echo json_encode([
        'success' => true,
        'message' => $residentResult['message'],
        'resident_id' => $residentResult['resident_id'],
        'members' => $members,
        'household' => $household,
        'head_assignment' => $headAssignResult
    ]);
}

function handleDeleteResident() {
    global $householdModel;
    
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
    error_log('DeleteResident request: ' . json_encode($data));
    
    if (empty($data['resident_id']) || empty($data['household_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Resident ID and Household ID are required']);
        return;
    }
    
    require_once __DIR__ . '/../Model/Resident.php';
    $residentModel = new Resident();
    
    // Delete the resident
    $deleteResult = $residentModel->delete($data['resident_id']);
    
    if (!$deleteResult['success']) {
        echo json_encode($deleteResult);
        return;
    }
    
    // Auto-reassign household head (if deleted resident was head, reassign to next)
    $household_id = $data['household_id'];
    $headAssignResult = $householdModel->autoAssignHouseholdHead($household_id);
    
    // Fetch updated members list
    $members = $householdModel->getMembers($household_id);
    
    // Get updated household info
    $household = $householdModel->getById($household_id);
    
    echo json_encode([
        'success' => true,
        'message' => $deleteResult['message'],
        'members' => $members,
        'household' => $household,
        'head_assignment' => $headAssignResult
    ]);
}

function handleGetMembersWithHead() {
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
    $household = $householdModel->getById($household_id);
    
    echo json_encode([
        'success' => true,
        'members' => $members,
        'household' => $household,
        'household_head_id' => $household['household_head_id'] ?? null
    ]);
}

?>
