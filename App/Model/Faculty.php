<?php
/**
 * Department Model
 * Handles database operations for departments
 */

require_once __DIR__ . '/../Config/Database.php';

class Faculty {
    private $connection;
    private $table = 'faculty';

    public function __construct() {
        $database = new Database();
        $this->connection = $database->connect();
    }


    // THIS IS TO SHOW ALL THE DATA OF THE FACULTY 
    public function getAllFaculty() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY faculty_name ASC";
        $result = $this->connection->query($query);

        if (!$result) {
            return false;
        }

        $faculties = [];
        while ($row = $result->fetch_assoc()) {
            $faculties[] = $row;
        }

        return $faculties;
    }


    /**
     * Create a new department
     */
    public function create($faculty_id, $faculty_name) {
        try {
            // Check for duplicate department ID
            $checkIdQuery = "SELECT * FROM " . $this->table . " WHERE faculty_id = ?";
            $stmt = $this->connection->prepare($checkIdQuery);
            $stmt->bind_param("s", $faculty_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Faculty ID already exists. Please use a different ID.',
                    'error_type' => 'duplicate_id'
                ];
            }

            // Check for duplicate faculty name
            $checkNameQuery = "SELECT * FROM " . $this->table . " WHERE faculty_name = ?";
            $stmt = $this->connection->prepare($checkNameQuery);
            $stmt->bind_param("s", $faculty_name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Faculty name already exists. Please use a different name.',
                    'error_type' => 'duplicate_name'
                ];
            }

            // Insert department
            $query = "INSERT INTO " . $this->table . " (faculty_id, faculty_name) VALUES (?, ?)";
            $stmt = $this->connection->prepare($query);

            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }

            $stmt->bind_param("ss", $faculty_id, $faculty_name);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Faculty created successfully!',
                    'faculty_id' => $faculty_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error creating faculty: ' . $stmt->error,
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
     * Get all departments
     */
    

    /**
     * Get Faculty by ID
     */
    public function getById($faculty_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE faculty_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $faculty_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Update department
     */
    public function update($faculty_id, $faculty_name) {
        try {
            // Check for duplicate name (excluding current record)
            $checkNameQuery = "SELECT * FROM " . $this->table . " WHERE faculty_name = ? AND faculty_id != ?";
            $stmt = $this->connection->prepare($checkNameQuery);
            $stmt->bind_param("ss", $faculty_name, $faculty_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Faculty name already exists. Please use a different name.',
                    'error_type' => 'duplicate'
                ];
            }

            $query = "UPDATE " . $this->table . " SET faculty_name = ? WHERE faculty_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ss", $faculty_name, $faculty_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Faculty updated successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error updating faculty: ' . $stmt->error,
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
     * Delete Faculty
     */
    public function delete($faculty_id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE faculty_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("s", $faculty_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Faculty deleted successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error deleting faculty: ' . $stmt->error,
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

}
?>
