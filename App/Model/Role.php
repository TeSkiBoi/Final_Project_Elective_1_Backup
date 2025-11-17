<?php
/**
 * Role Model
 * Handles database operations for roles
 */

require_once __DIR__ . '/../Config/Database.php';

class Role {
    private $connection;
    private $table = 'roles';

    public function __construct() {
        $database = new Database();
        $this->connection = $database->connect();
    }

    /**$
     * Create a new role
     */
    public function create($role_name) {
        try {
            // Check for duplicate role name
            $checkNameQuery = "SELECT * FROM " . $this->table . " WHERE role_name = ?";
            $stmt = $this->connection->prepare($checkNameQuery);
            $stmt->bind_param("s", $role_name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Role name already exists. Please use a different name.',
                    'error_type' => 'duplicate_name'
                ];
            }

            // Insert role
            $query = "INSERT INTO " . $this->table . " (role_name) VALUES (?)";
            $stmt = $this->connection->prepare($query);

            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }

            $stmt->bind_param("s", $role_name);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Role created successfully!',
                    'role_id' => $this->connection->insert_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error creating role: ' . $stmt->error,
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
     * Get all roles
     */
    public function getAll() {
        $query = "SELECT * FROM roles ORDER BY role_name ASC";
        $result = $this->connection->query($query);

        if (!$result) {
            return false;
        }

        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }

        return $roles;
    }

    /**
     * Get role by ID
     */
    public function getById($role_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE role_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $role_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Update role
     */
    public function update($role_id, $role_name) {
        try {
            // Check for duplicate name (excluding current record)
            $checkNameQuery = "SELECT * FROM " . $this->table . " WHERE role_name = ? AND role_id != ?";
            $stmt = $this->connection->prepare($checkNameQuery);
            $stmt->bind_param("si", $role_name, $role_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Role name already exists. Please use a different name.',
                    'error_type' => 'duplicate_name'
                ];
            }

            $query = "UPDATE " . $this->table . " SET role_name = ? WHERE role_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("si", $role_name, $role_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Role updated successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error updating role: ' . $stmt->error,
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
     * Delete role
     */
    public function delete($role_id) {
        try {
            // Check if role is assigned to any users
            $checkQuery = "SELECT COUNT(*) as count FROM users WHERE role_id = ?";
            $stmt = $this->connection->prepare($checkQuery);
            $stmt->bind_param("i", $role_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete role. It is assigned to ' . $row['count'] . ' user(s).',
                    'error_type' => 'constraint'
                ];
            }

            $query = "DELETE FROM " . $this->table . " WHERE role_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("i", $role_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Role deleted successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error deleting role: ' . $stmt->error,
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
