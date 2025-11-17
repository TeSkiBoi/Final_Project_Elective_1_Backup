<?php
/**
 * Department Model
 * Handles database operations for departments
 */

require_once __DIR__ . '/../Config/Database.php';

class Course {
    private $connection;
    private $table = 'courses';

    public function __construct() {
        $database = new Database();
        $this->connection = $database->connect();
    }

    /**
     * Generate unique course ID
     */
    public function generateCourseId() {
        $query = "SELECT course_id FROM " . $this->table . " ORDER BY course_id DESC LIMIT 1";
        $result = $this->connection->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = $row['course_id'];
            $number = (int)substr($lastId, 1) + 1;
        } else {
            $number = 1;
        }

        return 'C' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new course
     */
    public function create($course_code, $course_name, $units, $department_id) {
        try {
            // Check for duplicate course code
            $checkCodeQuery = "SELECT * FROM " . $this->table . " WHERE course_code = ?";
            $stmt = $this->connection->prepare($checkCodeQuery);
            $stmt->bind_param("s", $course_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Course code already exists. Please use a different code.',
                    'error_type' => 'duplicate_code'
                ];
            }

            // Check for duplicate course name
            $checkNameQuery = "SELECT * FROM " . $this->table . " WHERE course_name = ?";
            $stmt = $this->connection->prepare($checkNameQuery);
            $stmt->bind_param("s", $course_name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Course name already exists. Please use a different name.',
                    'error_type' => 'duplicate_name'
                ];
            }

            // Generate unique course ID
            $generatedCourseId = $this->generateCourseId();

            // Insert course
            $query = "INSERT INTO " . $this->table . " (course_id, course_code, course_name, units, department_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);

            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }

            $stmt->bind_param("sssss", $generatedCourseId, $course_code, $course_name, $units, $department_id);
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Course created successfully!',
                    'course_id' => $generatedCourseId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error creating course: ' . $stmt->error,
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
     * Get all courses
     */
    public function getAll() {
        $query = "SELECT c.*, d.department_name 
                    FROM " . $this->table . " c 
                    INNER JOIN departments d ON c.department_id = d.department_id 
                    ORDER BY c.course_id ASC";
        $result = $this->connection->query($query);

        if (!$result) {
            return false;
        }

        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }

        return $courses;
    }

    /**
     * Get course by ID
     */
    public function getById($course_id) {
        $query = "SELECT c.*, d.department_name FROM " . $this->table . " c INNER JOIN departments d ON c.department_id = d.department_id WHERE c.course_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Update course
     */
    public function update($course_id, $course_code, $course_name, $units, $department_id) {
        try {
            // Check for duplicate code (excluding current record)
            $checkCodeQuery = "SELECT * FROM " . $this->table . " WHERE course_code = ? AND course_id != ?";
            $stmt = $this->connection->prepare($checkCodeQuery);
            $stmt->bind_param("ss", $course_code, $course_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Course code already exists. Please use a different code.',
                    'error_type' => 'duplicate'
                ];
            }

            // Check for duplicate name (excluding current record)
            $checkNameQuery = "SELECT * FROM " . $this->table . " WHERE course_name = ? AND course_id != ?";
            $stmt = $this->connection->prepare($checkNameQuery);
            $stmt->bind_param("ss", $course_name, $course_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Course name already exists. Please use a different name.',
                    'error_type' => 'duplicate'
                ];
            }

            $query = "UPDATE " . $this->table . " SET course_code = ?, course_name = ?, units = ?, department_id = ? WHERE course_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("sssss", $course_code, $course_name, $units, $department_id, $course_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Course updated successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error updating course: ' . $stmt->error,
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
     * Delete course
     */
    public function delete($course_id) {
        try {
            // Check if course has enrollments
            $checkQuery = "SELECT COUNT(*) as count FROM enrollments WHERE course_id = ?";
            $stmt = $this->connection->prepare($checkQuery);
            $stmt->bind_param("s", $course_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete course. It has associated enrollments.',
                    'error_type' => 'constraint'
                ];
            }

            $query = "DELETE FROM " . $this->table . " WHERE course_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("s", $course_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Course deleted successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error deleting course: ' . $stmt->error,
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
