<?php
/**
 * Department Controller
 * Handles department-related requests
 */

require_once __DIR__ . '/../Model/Faculty.php';

class FacultyController {
    private $facultyModel;

    public function __construct() {
        $this->facultyModel = new Faculty();
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
        if (!isset($data['faculty_id']) || empty(trim($data['faculty_id']))) {
            $this->sendResponse(false, 'Faculty ID is required', null, 400);
            return;
        }

        if (!isset($data['faculty_name']) || empty(trim($data['faculty_name']))) {
            $this->sendResponse(false, 'Faculty name is required', null, 400);
            return;
        }

        $fid = $data['faculty_id'];
        $fname = $data['faculty_name'];
        
        // Call model to create faculty
        $result = $this->facultyModel->create($fid, $fname);

        // Send response
        $this->sendResponse(
            $result['success'],
            $result['message'],
            isset($result['faculty_id']) ? ['id' => $result['faculty_id']] : null,
            $result['success'] ? 201 : 400
        );
    }

    /**
     * Get all departments - API Endpoint
     */
    public function getAll() {
        $faculty = $this->facultyModel->getAll();

        if ($faculty === false) {
            $this->sendResponse(false, 'Error retrieving faculty', null, 500);
            return;
        }

        $this->sendResponse(true, 'Faculty retrieved successfully', $faculty, 200);
    }

    /**
     * Get department by ID - API Endpoint
     */
    public function getById($id) {
        if (empty($id)) {
            $this->sendResponse(false, 'Faculty ID is required', null, 400);
            return;
        }

        $faculty = $this->facultyModel->getById($id);

        if (!$faculty) {
            $this->sendResponse(false, 'Faculty not found', null, 404);
            return;
        }

        $this->sendResponse(true, 'Faculty retrieved successfully', $faculty, 200);
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

        if (!isset($data['faculty_id']) || empty($data['faculty_id'])) {
            $this->sendResponse(false, 'Faculty ID is required', null, 400);
            return;
        }

        if (!isset($data['faculty_name']) || empty(trim($data['faculty_name']))) {
            $this->sendResponse(false, 'Faculty name is required', null, 400);
            return; 
        }

        $result = $this->facultyModel->update($data['faculty_id'], trim($data['faculty_name']));

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

        if (!isset($data['faculty_id']) || empty($data['faculty_id'])) {
            $this->sendResponse(false, 'Faculty ID is required', null, 400);
            return;
        }

        $result = $this->facultyModel->delete($data['faculty_id']);

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

    $controller = new FacultyController();

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
}

else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $controller = new FacultyController();

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
