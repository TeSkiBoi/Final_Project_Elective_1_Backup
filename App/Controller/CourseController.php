<?php
/**
 * Course Controller
 * Handles course-related requests
 */

require_once __DIR__ . '/../Model/Course.php';

class CourseController {
    private $courseModel;

    public function __construct() {
        $this->courseModel = new Course();
    }

    /**
     * Create Course - API Endpoint
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
        if (!isset($data['course_code']) || empty(trim($data['course_code']))) {
            $this->sendResponse(false, 'Course code is required', null, 400);
            return;
        }

        if (!isset($data['course_name']) || empty(trim($data['course_name']))) {
            $this->sendResponse(false, 'Course name is required', null, 400);
            return;
        }

        if (!isset($data['units']) || empty(trim($data['units']))) {
            $this->sendResponse(false, 'Units is required', null, 400);
            return;
        }

        if (!isset($data['department_id']) || empty(trim($data['department_id']))) {
            $this->sendResponse(false, 'Department is required', null, 400);
            return;
        }

        $course_code = $data['course_code'];
        $course_name = $data['course_name'];
        $units = $data['units'];
        $department_id = $data['department_id'];

        // Call model to create course
        $result = $this->courseModel->create($course_code, $course_name, $units, $department_id);

        // Send response
        $this->sendResponse(
            $result['success'],
            $result['message'],
            isset($result['course_id']) ? ['id' => $result['course_id']] : null,
            $result['success'] ? 201 : 400
        );
    }

    /**
     * Update Course - API Endpoint
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['course_id']) || empty($data['course_id'])) {
            $this->sendResponse(false, 'Course ID is required', null, 400);
            return;
        }

        if (!isset($data['course_code']) || empty(trim($data['course_code']))) {
            $this->sendResponse(false, 'Course code is required', null, 400);
            return;
        }

        if (!isset($data['course_name']) || empty(trim($data['course_name']))) {
            $this->sendResponse(false, 'Course name is required', null, 400);
            return;
        }

        if (!isset($data['units']) || empty(trim($data['units']))) {
            $this->sendResponse(false, 'Units is required', null, 400);
            return;
        }

        if (!isset($data['department_id']) || empty(trim($data['department_id']))) {
            $this->sendResponse(false, 'Department is required', null, 400);
            return;
        }

        $course_id = trim($data['course_id']);
        $course_code = trim($data['course_code']);
        $course_name = trim($data['course_name']);
        $units = trim($data['units']);
        $department_id = trim($data['department_id']);

        $result = $this->courseModel->update($course_id, $course_code, $course_name, $units, $department_id);

        $this->sendResponse(
            $result['success'],
            $result['message'],
            null,
            $result['success'] ? 200 : 400
        );
    }

    /**
     * Delete Course - API Endpoint
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['course_id']) || empty($data['course_id'])) {
            $this->sendResponse(false, 'Course ID is required', null, 400);
            return;
        }

        $course_id = trim($data['course_id']);

        $result = $this->courseModel->delete($course_id);

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

    $controller = new CourseController();

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
?>
