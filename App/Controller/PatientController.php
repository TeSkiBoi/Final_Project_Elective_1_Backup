<?php
/**
 * Patient Controller
 * Handles patient-related requests
 */

require_once __DIR__ . '/../Model/Patient.php';

class PatientController {
    private $patientModel;

    public function __construct() {
        $this->patientModel = new Patient();
    }

    /**
     * Create Patient - API Endpoint
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
        if (!isset($data['patient_id']) || empty(trim($data['patient_id']))) {
            $this->sendResponse(false, 'Patient ID is required', null, 400);
            return;
        }

        if (!isset($data['firstname']) || empty(trim($data['firstname']))) {
            $this->sendResponse(false, 'First name is required', null, 400);
            return;
        }

        if (!isset($data['lastname']) || empty(trim($data['lastname']))) {
            $this->sendResponse(false, 'Last name is required', null, 400);
            return;
        }

        if (!isset($data['birthdate']) || empty($data['birthdate'])) {
            $this->sendResponse(false, 'Birthdate is required', null, 400);
            return;
        }

        if (!isset($data['gender']) || empty(trim($data['gender']))) {
            $this->sendResponse(false, 'Gender is required', null, 400);
            return;
        }

        if (!isset($data['status']) || empty(trim($data['status']))) {
            $this->sendResponse(false, 'Marital status is required', null, 400);
            return;
        }

        if (!isset($data['address']) || empty(trim($data['address']))) {
            $this->sendResponse(false, 'Address is required', null, 400);
            return;
        }

        if (!isset($data['contact_no']) || empty(trim($data['contact_no']))) {
            $this->sendResponse(false, 'Contact number is required', null, 400);
            return;
        }

        $patient_id = trim($data['patient_id']);
        $firstname = trim($data['firstname']);
        $middlename = isset($data['middlename']) ? trim($data['middlename']) : '';
        $lastname = trim($data['lastname']);
        $birthdate = $data['birthdate'];
        $gender = trim($data['gender']);
        $status = trim($data['status']);
        $address = trim($data['address']);
        $contact_no = trim($data['contact_no']);
        $email = isset($data['email']) && !empty($data['email']) ? trim($data['email']) : null;

        // Call model to create patient
        $result = $this->patientModel->create($patient_id, $firstname, $middlename, $lastname, $birthdate, $gender, $status, $address, $contact_no, $email);

        // Send response
        $this->sendResponse(
            $result['success'],
            $result['message'],
            isset($result['patient_id']) ? ['id' => $result['patient_id']] : null,
            $result['success'] ? 201 : 400
        );
    }

    /**
     * Get All Patients - API Endpoint
     */
    public function getAll() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        $patients = $this->patientModel->getAll();

        if ($patients === false) {
            $this->sendResponse(false, 'Error retrieving patients', null, 500);
            return;
        }

        $this->sendResponse(true, 'Patients retrieved successfully', $patients, 200);
    }

    /**
     * Get Patient by ID - API Endpoint
     */
    public function getById() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->sendResponse(false, 'Patient ID is required', null, 400);
            return;
        }

        $patient_id = trim($_GET['id']);
        $patient = $this->patientModel->getById($patient_id);

        if (!$patient) {
            $this->sendResponse(false, 'Patient not found', null, 404);
            return;
        }

        $this->sendResponse(true, 'Patient retrieved successfully', $patient, 200);
    }

    /**
     * Update Patient - API Endpoint
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        // Validate required fields
        if (!isset($data['patient_id_hidden']) || empty($data['patient_id_hidden'])) {
            $this->sendResponse(false, 'Original Patient ID is required', null, 400);
            return;
        }

        if (!isset($data['patient_id']) || empty(trim($data['patient_id']))) {
            $this->sendResponse(false, 'Patient ID is required', null, 400);
            return;
        }

        if (!isset($data['firstname']) || empty(trim($data['firstname']))) {
            $this->sendResponse(false, 'First name is required', null, 400);
            return;
        }

        if (!isset($data['lastname']) || empty(trim($data['lastname']))) {
            $this->sendResponse(false, 'Last name is required', null, 400);
            return;
        }

        if (!isset($data['birthdate']) || empty($data['birthdate'])) {
            $this->sendResponse(false, 'Birthdate is required', null, 400);
            return;
        }

        if (!isset($data['gender']) || empty(trim($data['gender']))) {
            $this->sendResponse(false, 'Gender is required', null, 400);
            return;
        }

        if (!isset($data['status']) || empty(trim($data['status']))) {
            $this->sendResponse(false, 'Marital status is required', null, 400);
            return;
        }

        if (!isset($data['address']) || empty(trim($data['address']))) {
            $this->sendResponse(false, 'Address is required', null, 400);
            return;
        }

        if (!isset($data['contact_no']) || empty(trim($data['contact_no']))) {
            $this->sendResponse(false, 'Contact number is required', null, 400);
            return;
        }

        $patient_id_hidden = trim($data['patient_id_hidden']);
        $patient_id = trim($data['patient_id']);
        $firstname = trim($data['firstname']);
        $middlename = isset($data['middlename']) ? trim($data['middlename']) : '';
        $lastname = trim($data['lastname']);
        $birthdate = $data['birthdate'];
        $gender = trim($data['gender']);
        $status = trim($data['status']);
        $address = trim($data['address']);
        $contact_no = trim($data['contact_no']);
        $email = isset($data['email']) && !empty($data['email']) ? trim($data['email']) : null;

        $result = $this->patientModel->update($patient_id_hidden, $patient_id, $firstname, $middlename, $lastname, $birthdate, $gender, $status, $address, $contact_no, $email);

        $this->sendResponse(
            $result['success'],
            $result['message'],
            null,
            $result['success'] ? 200 : 400
        );
    }

    /**
     * Delete Patient - API Endpoint
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['patient_id']) || empty($data['patient_id'])) {
            $this->sendResponse(false, 'Patient ID is required', null, 400);
            return;
        }

        $patient_id = trim($data['patient_id']);

        $result = $this->patientModel->delete($patient_id);

        $this->sendResponse(
            $result['success'],
            $result['message'],
            null,
            $result['success'] ? 200 : 400
        );
    }

    /**
     * Search Patients - API Endpoint
     */
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->sendResponse(false, 'Invalid request method', null, 405);
            return;
        }

        if (!isset($_GET['q']) || empty($_GET['q'])) {
            $this->sendResponse(false, 'Search term is required', null, 400);
            return;
        }

        $searchTerm = trim($_GET['q']);
        $patients = $this->patientModel->search($searchTerm);

        $this->sendResponse(true, 'Search results', $patients, 200);
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

    $controller = new PatientController();

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
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    $controller = new PatientController();

    switch ($action) {
        case 'getAll':
            $controller->getAll();
            break;
        case 'getById':
            $controller->getById();
            break;
        case 'search':
            $controller->search();
            break;
        default:
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}
?>
