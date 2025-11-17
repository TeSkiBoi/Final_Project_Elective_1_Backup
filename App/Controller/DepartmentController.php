<?php
/**
 * Department Controller
 * Handles department-related requests
 */

require_once __DIR__ . '/../Model/Department.php';

class DepartmentController {
    private $departmentModel;

    public function __construct() {
        $this->departmentModel = new Department();
    }

    /**
     * Create Department - API Endpoint
     */
    public function create() {
        // Check if request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        // Get JSON data from request body
        $data = json_decode(file_get_contents("php://input"), true);

        // Validate required fields
        if (!isset($data['department_id']) || empty(trim($data['department_id']))) {
            $this->sendResponse(false, 'Department ID is required', null, 400);
            return;
        }

        if (!isset($data['department_name']) || empty(trim($data['department_name']))) {
            $this->sendResponse(false, 'Department name is required', null, 400);
            return;
        }

        $department_id = trim($data['department_id']);
        $department_name = trim($data['department_name']);

        // Call model to create department
        $result = $this->departmentModel->create($department_id, $department_name);

        // Send response
        $this->sendResponse(
            $result['success'],
            $result['message'],
            isset($result['department_id']) ? ['id' => $result['department_id']] : null,
            $result['success'] ? 201 : 400
        );
    }

    /**
     * Get all departments - API Endpoint
     */
    public function getAll() {
        $departments = $this->departmentModel->getAll();

        if ($departments === false) {
            $this->sendResponse(false, 'Error retrieving departments', null, 500);
            return;
        }

        $this->sendResponse(true, 'Departments retrieved successfully', $departments, 200);
    }

    /**
     * Get department by ID - API Endpoint
     */
    public function getById($id) {
        if (empty($id)) {
            $this->sendResponse(false, 'Department ID is required', null, 400);
            return;
        }

        $department = $this->departmentModel->getById($id);

        if (!$department) {
            $this->sendResponse(false, 'Department not found', null, 404);
            return;
        }

        $this->sendResponse(true, 'Department retrieved successfully', $department, 200);
    }

    /**
     * Update Department - API Endpoint
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['department_id']) || empty($data['department_id'])) {
            $this->sendResponse(false, 'Department ID is required', null, 400);
            return;
        }

        if (!isset($data['department_name']) || empty(trim($data['department_name']))) {
            $this->sendResponse(false, 'Department name is required', null, 400);
            return;
        }

        $result = $this->departmentModel->update($data['department_id'], trim($data['department_name']));

        $this->sendResponse(
            $result['success'],
            $result['message'],
            null,
            $result['success'] ? 200 : 400
        );
    }

    /**
     * Delete Department - API Endpoint
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['department_id']) || empty($data['department_id'])) {
            $this->sendResponse(false, 'Department ID is required', null, 400);
            return;
        }

        $result = $this->departmentModel->delete($data['department_id']);

        $this->sendResponse(
            $result['success'],
            $result['message'],
            null,
            $result['success'] ? 200 : 400
        );
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    $controller = new DepartmentController();

    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete();
            break;
        default:
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $controller = new DepartmentController();

    switch ($action) {
        case 'getAll':
            $controller->getAll();
            break;
        case 'getById':
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $controller->getById($id);
            break;
        default:
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}
?>
