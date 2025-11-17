<?php
/**
 * User Model
 * Handles database operations for users
 */

require_once __DIR__ . '/../Config/Database.php';

class User {
    private $connection;
    private $table = 'users';

    public function __construct() {
        $database = new Database();
        $this->connection = $database->connect();
    }

    /**
     * Get user by username
     */
    public function getUserByUsername($username) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Get user by ID
     */
    public function getById($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = ?";
        $stmt = $this->connection->prepare($query);
        
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Verify password
     */
    public function verifyPassword($password, $password_hash) {
        // The password in setup.sql appears to be MD5 hash
        // Comparing with MD5 for compatibility
        return md5($password) === $password_hash;
    }

    /**
     * Get user role name
     */
    public function getUserRole($role_id) {
        $query = "SELECT role_name FROM roles WHERE role_id = ?";
        $stmt = $this->connection->prepare($query);
        
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("i", $role_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row ? $row['role_name'] : null;
    }

    /**
     * Log user activity
     */
    public function logUserActivity($user_id, $action, $ip_address) {
        $query = "INSERT INTO user_logs (user_id, action, ip_address) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("sss", $user_id, $action, $ip_address);
        return $stmt->execute();
    }

    /**
     * Get all users
     */
    public function getAll() {
        $query = "SELECT u.user_id, u.fullname, u.username, u.email, u.role_id, r.role_name, u.status 
                  FROM " . $this->table . " u 
                  LEFT JOIN roles r ON u.role_id = r.role_id 
                  ORDER BY u.fullname ASC";
        $result = $this->connection->query($query);

        if (!$result) {
            return false;
        }

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;
    }

    /**
     * Generate unique user ID with format U001
     */
    private function generateUserId() {
        $query = "SELECT user_id FROM " . $this->table . " WHERE user_id LIKE 'U%' ORDER BY user_id DESC LIMIT 1";
        $result = $this->connection->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = $row['user_id'];
            $number = (int)substr($lastId, 1) + 1;
        } else {
            $number = 1;
        }

        return 'U' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Create new user
     */
    public function create($fullname, $username, $email, $password, $role_id, $status = 'active') {
        try {
            // Check for duplicate username
            $checkUsernameQuery = "SELECT * FROM " . $this->table . " WHERE username = ?";
            $stmt = $this->connection->prepare($checkUsernameQuery);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Username already exists. Please use a different username.',
                    'error_type' => 'duplicate_username'
                ];
            }

            // Check for duplicate email
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

            // Generate user ID
            $user_id = $this->generateUserId();

            // Hash password using MD5 (for compatibility with existing setup)
            $password_hash = md5($password);

            // Insert user
            $query = "INSERT INTO " . $this->table . " (user_id, fullname, username, email, password_hash, role_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);

            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }

            $stmt->bind_param("sssssis", $user_id, $fullname, $username, $email, $password_hash, $role_id, $status);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'User created successfully!',
                    'user_id' => $user_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error creating user: ' . $stmt->error,
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
     * Update user
     */
    public function update($user_id, $fullname, $username, $email, $role_id, $status = 'active') {
        try {
            // Check for duplicate username (excluding current record)
            $checkUsernameQuery = "SELECT * FROM " . $this->table . " WHERE username = ? AND user_id != ?";
            $stmt = $this->connection->prepare($checkUsernameQuery);
            $stmt->bind_param("ss", $username, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Username already exists. Please use a different username.',
                    'error_type' => 'duplicate_username'
                ];
            }

            // Check for duplicate email (excluding current record)
            $checkEmailQuery = "SELECT * FROM " . $this->table . " WHERE email = ? AND user_id != ?";
            $stmt = $this->connection->prepare($checkEmailQuery);
            $stmt->bind_param("ss", $email, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Email already exists. Please use a different email.',
                    'error_type' => 'duplicate_email'
                ];
            }

            $query = "UPDATE " . $this->table . " SET fullname = ?, username = ?, email = ?, role_id = ?, status = ? WHERE user_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("sssiss", $fullname, $username, $email, $role_id, $status, $user_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'User updated successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error updating user: ' . $stmt->error,
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
     * Change user status
     */
    public function changeStatus($user_id, $status) {
        try {
            // Validate status value
            if (!in_array($status, ['active', 'inactive'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid status value. Must be active or inactive.',
                    'error_type' => 'invalid_status'
                ];
            }

            $query = "UPDATE " . $this->table . " SET status = ? WHERE user_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ss", $status, $user_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'User status changed to ' . $status . ' successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error changing user status: ' . $stmt->error,
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
     * Delete user
     */
    public function delete($user_id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE user_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("s", $user_id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'User deleted successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error deleting user: ' . $stmt->error,
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
