<?php
/**
 * Department Model
 * Handles database operations for departments
 */

require_once __DIR__ . '/../Config/Database.php';

class Department {
    private $connection;
    private $table = 'departments';

    public function __construct() {
        $database = new Database();
        $this->connection = $database->connect();
    }

    /**
     * Create a new department
     */
    public function create($department_id, $department_name) {
        try {
            // Check for duplicate department ID
            $checkIdQuery = "SELECT * FROM " . $this->table . " WHERE department_id = ?";
            $stmt = $this->connection->prepare($checkIdQuery);
            $stmt->bind_param("s", $department_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Department ID already exists. Please use a different ID.',
                    'error_type' => 'duplicate_id'
                ];
            }

            // Check for duplicate department name
            $checkNameQuery = "SELECT * FROM " . $this->table . " WHERE department_name = ?";
            $stmt = $this->connection->prepare($checkNameQuery);
            $stmt->bind_param("s", $department_name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Department name already exists. Please use a different name.',
                    'error_type' => 'duplicate_name'
                ];
            }

            // Insert department
            $query = "INSERT INTO " . $this->table . " (department_id, department_name) VALUES (?, ?)";
            $stmt = $this->connection->prepare($query);

            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }

            $stmt->bind_param("ss", $department_id, $department_name);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Department created successfully!',
                    'department_id' => $department_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error creating department: ' . $stmt->error,
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
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY department_name ASC";
        $result = $this->connection->query($query);

        if (!$result) {
            return false;
        }

        $departments = [];
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }

        return $departments;
    }

    /**
     * Get department by ID
     */
    public function getById($department_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE department_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $department_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Update department
     */
    public function update($department_id, $department_name) {
        try {
            // Check for duplicate name (excluding current record)
            $checkNameQuery = "SELECT * FROM " . $this->table . " WHERE department_name = ? AND department_id != ?";
            $stmt = $this->connection->prepare($checkNameQuery);
            $stmt->bind_param("ss", $department_name, $department_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Department name already exists. Please use a different name.',
                    'error_type' => 'duplicate'
                ];
            }

            $query = "UPDATE " . $this->table . " SET department_name = ? WHERE department_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ss", $department_name, $department_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Department updated successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error updating department: ' . $stmt->error,
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
     * Delete department
     */
    public function delete($department_id) {
        try {
            // Check if department has courses
            $checkQuery = "SELECT COUNT(*) as count FROM courses WHERE department_id = ?";
            $stmt = $this->connection->prepare($checkQuery);
            $stmt->bind_param("s", $department_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete department. It has associated courses.',
                    'error_type' => 'constraint'
                ];
            }

            $query = "DELETE FROM " . $this->table . " WHERE department_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("s", $department_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Department deleted successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error deleting department: ' . $stmt->error,
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
