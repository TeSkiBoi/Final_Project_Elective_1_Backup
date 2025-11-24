<?php
/**
 * Household Model
 * Handles database operations for households
 */

require_once __DIR__ . '/../Config/Database.php';

class Household {
    private $connection;
    private $table = 'households';

    public function __construct() {
        $database = new Database();
        $this->connection = $database->connect();
    }

    /**
     * Get all households
     */
    public function getAll() {
        $query = "SELECT household_id, firstname, middlename, lastname, birthday, age, occupation, income FROM " . $this->table . " ORDER BY lastname, firstname ASC";
        $result = $this->connection->query($query);

        if (!$result) {
            return false;
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Get household by ID
     */
    public function getById($household_id) {
        $query = "SELECT household_id, firstname, middlename, lastname, birthday, age, occupation, income FROM " . $this->table . " WHERE household_id = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return null;
        $stmt->bind_param('s', $household_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    /**
     * Generate a new household id (HHD001)
     */
    private function generateHouseholdId() {
        $query = "SELECT household_id FROM " . $this->table . " WHERE household_id LIKE 'HHD%' ORDER BY household_id DESC LIMIT 1";
        $result = $this->connection->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = $row['household_id'];
            $number = (int)substr($lastId, 3) + 1;
        } else {
            $number = 1;
        }

        return 'HHD' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Create household
     */
    public function create($firstname, $middlename, $lastname, $birthday, $age, $occupation, $income) {
        try {
            $household_id = $this->generateHouseholdId();
            $query = "INSERT INTO " . $this->table . " (household_id, firstname, middlename, lastname, birthday, age, occupation, income) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                return ['success' => false, 'message' => 'DB prepare error: ' . $this->connection->error];
            }
            $stmt->bind_param('ssssisss', $household_id, $firstname, $middlename, $lastname, $birthday, $age, $occupation, $income);
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Household created', 'household_id' => $household_id];
            } else {
                return ['success' => false, 'message' => 'Error creating household: ' . $stmt->error];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Update household
     */
    public function update($household_id, $firstname, $middlename, $lastname, $birthday, $age, $occupation, $income) {
        try {
            $query = "UPDATE " . $this->table . " SET firstname = ?, middlename = ?, lastname = ?, birthday = ?, age = ?, occupation = ?, income = ? WHERE household_id = ?";
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                return ['success' => false, 'message' => 'DB prepare error: ' . $this->connection->error];
            }
            $stmt->bind_param('sssisiss', $firstname, $middlename, $lastname, $birthday, $age, $occupation, $income, $household_id);
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Household updated'];
            } else {
                return ['success' => false, 'message' => 'Error updating household: ' . $stmt->error];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Delete household
     */
    public function delete($household_id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE household_id = ?";
            $stmt = $this->connection->prepare($query);
            if (!$stmt) return ['success' => false, 'message' => 'DB prepare error: ' . $this->connection->error];
            $stmt->bind_param('s', $household_id);
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Household deleted'];
            } else {
                return ['success' => false, 'message' => 'Error deleting household: ' . $stmt->error];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }
}
?>
