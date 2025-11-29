<?php
/**
 * Resident Model
 * Handles database operations for residents
 */

require_once __DIR__ . '/../Config/Database.php';

class Resident {
    private $connection;
    private $table = 'residents';

    public function __construct($existingConnection = null) {
        if ($existingConnection !== null) {
            $this->connection = $existingConnection;
        } else {
            $database = new Database();
            $this->connection = $database->connect();
        }
    }

    public function getAll() {
        $query = "SELECT r.resident_id, r.household_id, r.first_name, r.middle_name, r.last_name, r.birth_date, r.gender, r.age, r.contact_no, r.email, r.created_at, h.household_id as hh_id 
                  FROM " . $this->table . " r 
                  LEFT JOIN households h ON r.household_id = h.household_id 
                  ORDER BY r.last_name ASC, r.first_name ASC";
        $result = $this->connection->query($query);

        if (!$result) return false;

        $rows = [];
        while ($r = $result->fetch_assoc()) {
            $rows[] = $r;
        }
        return $rows;
    }

    public function getById($resident_id) {
        $query = "SELECT r.*, h.household_id as hh_id FROM " . $this->table . " r LEFT JOIN households h ON r.household_id = h.household_id WHERE r.resident_id = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return null;
        $stmt->bind_param('s', $resident_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Generate next resident ID (e.g., R001, R002)
     */
    public function generateNextId() {
        $query = "SELECT resident_id FROM " . $this->table . " ORDER BY resident_id DESC LIMIT 1";
        $result = $this->connection->query($query);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = $row['resident_id'];
            $number = intval(substr($lastId, 1)) + 1;
            return 'R' . str_pad($number, 3, '0', STR_PAD_LEFT);
        }
        
        return 'R001';
    }

    public function create($data) {
        try {
            // Log incoming data for debugging
            error_log('Resident create called with data: ' . json_encode($data));
            
            // Required fields validation
            $required = ['first_name', 'last_name', 'age', 'household_id'];
            foreach ($required as $f) {
                if (empty($data[$f])) {
                    $errorMsg = ucfirst(str_replace('_', ' ', $f)) . ' is required';
                    error_log('Resident create validation failed: ' . $errorMsg);
                    return [
                        'success' => false,
                        'message' => $errorMsg,
                        'error_type' => 'validation'
                    ];
                }
            }

            $resident_id = $this->generateNextId();
            $household_id = $data['household_id'];
            $first_name = $data['first_name'];
            $middle_name = $data['middle_name'] ?? null;
            $last_name = $data['last_name'];
            $birth_date = $data['birth_date'] ?? null;
            $gender = $data['gender'] ?? null;
            $age = $data['age'];
            $contact_no = $data['contact_no'] ?? null;
            $email = $data['email'] ?? null;

            $query = "INSERT INTO " . $this->table . " (resident_id, household_id, first_name, middle_name, last_name, birth_date, gender, age, contact_no, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            
            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }

            $stmt->bind_param('ssssssisss', $resident_id, $household_id, $first_name, $middle_name, $last_name, $birth_date, $gender, $age, $contact_no, $email);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Resident created successfully!',
                    'resident_id' => $resident_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error creating resident: ' . $stmt->error,
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

    public function update($resident_id, $data) {
        try {
            // Validate resident exists
            $existing = $this->getById($resident_id);
            if (!$existing) {
                return [
                    'success' => false,
                    'message' => 'Resident not found',
                    'error_type' => 'not_found'
                ];
            }

            $household_id = $data['household_id'] ?? $existing['household_id'];
            $first_name = $data['first_name'] ?? $existing['first_name'];
            $middle_name = $data['middle_name'] ?? $existing['middle_name'];
            $last_name = $data['last_name'] ?? $existing['last_name'];
            $birth_date = $data['birth_date'] ?? $existing['birth_date'];
            $gender = $data['gender'] ?? $existing['gender'];
            $age = $data['age'] ?? $existing['age'];
            $contact_no = $data['contact_no'] ?? $existing['contact_no'];
            $email = $data['email'] ?? $existing['email'];

            $query = "UPDATE " . $this->table . " SET household_id = ?, first_name = ?, middle_name = ?, last_name = ?, birth_date = ?, gender = ?, age = ?, contact_no = ?, email = ? WHERE resident_id = ?";
            $stmt = $this->connection->prepare($query);
            
            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }

            $stmt->bind_param('ssssssisss', $household_id, $first_name, $middle_name, $last_name, $birth_date, $gender, $age, $contact_no, $email, $resident_id);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Resident updated successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error updating resident: ' . $stmt->error,
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

    public function delete($resident_id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE resident_id = ?";
            $stmt = $this->connection->prepare($query);
            
            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }

            $stmt->bind_param('s', $resident_id);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Resident deleted successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error deleting resident: ' . $stmt->error,
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
     * Create a resident with specific parameters (used by Household operations)
     */
    public function createResident($household_id, $first_name, $middle_name, $last_name, $birth_date, $gender, $contact_no = '', $email = '') {
        // Calculate age from birth date
        $age = 0;
        if ($birth_date) {
            $birthDate = new DateTime($birth_date);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
        }

        return $this->create([
            'household_id' => $household_id,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'birth_date' => $birth_date,
            'gender' => $gender,
            'age' => $age,
            'contact_no' => $contact_no,
            'email' => $email
        ]);
    }

    /**
     * Update a resident with specific parameters (used by Household operations)
     */
    public function updateResident($resident_id, $first_name, $middle_name, $last_name, $birth_date, $gender, $contact_no = '', $email = '') {
        // Calculate age from birth date
        $age = 0;
        if ($birth_date) {
            $birthDate = new DateTime($birth_date);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
        }

        return $this->update($resident_id, [
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'birth_date' => $birth_date,
            'gender' => $gender,
            'age' => $age,
            'contact_no' => $contact_no,
            'email' => $email
        ]);
    }

    /**
     * Delete a resident (used by Household operations)
     */
    public function deleteResident($resident_id) {
        return $this->delete($resident_id);
    }
}

?>
