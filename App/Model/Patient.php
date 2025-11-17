<?php
/**
 * Patient Model
 * Handles database operations for patients
 */

require_once __DIR__ . '/../Config/Database.php';

class Patient {
    private $connection;
    private $table = 'patients';

    public function __construct() {
        $database = new Database();
        $this->connection = $database->connect();
    }

    /**
     * Create a new patient
     */
    public function create($patient_id, $firstname, $middlename, $lastname, $birthdate, $gender, $status, $address, $contact_no, $email) {
        try {
            // Check for duplicate patient ID
            $checkIdQuery = "SELECT * FROM " . $this->table . " WHERE patient_id = ?";
            $stmt = $this->connection->prepare($checkIdQuery);
            $stmt->bind_param("s", $patient_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Patient ID already exists. Please use a different ID.',
                    'error_type' => 'duplicate_id'
                ];
            }

            // Check for duplicate email if provided
            if (!empty($email)) {
                $checkEmailQuery = "SELECT * FROM " . $this->table . " WHERE email = ?";
                $stmt = $this->connection->prepare($checkEmailQuery);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    return [
                        'success' => false,
                        'message' => 'Email already exists. Please use a different email.',
                        'error_type' => 'duplicate_email'
                    ];
                }
            }

            // Insert patient
            $query = "INSERT INTO " . $this->table . " (patient_id, firstname, middlename, lastname, birthdate, gender, status, address, contact_no, email) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);

            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }

            $stmt->bind_param("ssssssssss", $patient_id, $firstname, $middlename, $lastname, $birthdate, $gender, $status, $address, $contact_no, $email);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Patient registered successfully!',
                    'patient_id' => $patient_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error creating patient: ' . $stmt->error,
                    'error_type' => 'database'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
                'error_type' => 'exception'
            ];
        }
    }

    /**
     * Get all patients
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY patient_id ASC";
        $result = $this->connection->query($query);

        if (!$result) {
            return false;
        }

        $patients = [];
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }

        return $patients;
    }

    /**
     * Get patient by ID
     */
    public function getById($patient_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE patient_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Update patient
     */
    public function update($patient_id_hidden, $patient_id, $firstname, $middlename, $lastname, $birthdate, $gender, $status, $address, $contact_no, $email) {
        try {
            // Check for duplicate patient ID (excluding current record)
            if ($patient_id !== $patient_id_hidden) {
                $checkIdQuery = "SELECT * FROM " . $this->table . " WHERE patient_id = ? AND patient_id != ?";
                $stmt = $this->connection->prepare($checkIdQuery);
                $stmt->bind_param("ss", $patient_id, $patient_id_hidden);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    return [
                        'success' => false,
                        'message' => 'Patient ID already exists. Please use a different ID.',
                        'error_type' => 'duplicate_id'
                    ];
                }
            }

            // Check for duplicate email (excluding current record)
            if (!empty($email)) {
                $checkEmailQuery = "SELECT * FROM " . $this->table . " WHERE email = ? AND patient_id != ?";
                $stmt = $this->connection->prepare($checkEmailQuery);
                $stmt->bind_param("ss", $email, $patient_id_hidden);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    return [
                        'success' => false,
                        'message' => 'Email already exists. Please use a different email.',
                        'error_type' => 'duplicate_email'
                    ];
                }
            }

            $query = "UPDATE " . $this->table . " SET patient_id = ?, firstname = ?, middlename = ?, lastname = ?, birthdate = ?, gender = ?, status = ?, address = ?, contact_no = ?, email = ? WHERE patient_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("sssssssssss", $patient_id, $firstname, $middlename, $lastname, $birthdate, $gender, $status, $address, $contact_no, $email, $patient_id_hidden);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Patient information updated successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error updating patient: ' . $stmt->error,
                    'error_type' => 'database'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
                'error_type' => 'exception'
            ];
        }
    }

    /**
     * Delete patient
     */
    public function delete($patient_id) {
        try {
            // Check if patient has medical records (if you have a medical records table)
            // For now, we'll allow deletion
            // Uncomment below if you add a medical_records table
            /*
            $checkQuery = "SELECT COUNT(*) as count FROM medical_records WHERE patient_id = ?";
            $stmt = $this->connection->prepare($checkQuery);
            $stmt->bind_param("s", $patient_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete patient. It has associated medical records.',
                    'error_type' => 'constraint'
                ];
            }
            */

            $query = "DELETE FROM " . $this->table . " WHERE patient_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("s", $patient_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Patient deleted successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error deleting patient: ' . $stmt->error,
                    'error_type' => 'database'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
                'error_type' => 'exception'
            ];
        }
    }

    /**
     * Search patients by name or ID
     */
    public function search($searchTerm) {
        try {
            $searchTerm = "%{$searchTerm}%";
            $query = "SELECT * FROM " . $this->table . " WHERE patient_id LIKE ? OR firstname LIKE ? OR lastname LIKE ? OR email LIKE ? ORDER BY patient_id ASC";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();

            $patients = [];
            while ($row = $result->fetch_assoc()) {
                $patients[] = $row;
            }

            return $patients;
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
