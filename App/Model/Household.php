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
        $query = "SELECT household_id, family_no, full_name, address, income FROM " . $this->table . " ORDER BY family_no ASC";
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
        $query = "SELECT household_id, family_no, full_name, address, income FROM " . $this->table . " WHERE household_id = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return null;
        $stmt->bind_param('s', $household_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    /**
     * Generate next household ID (e.g., HH001, HH002)
     */
    public function generateNextId() {
        $query = "SELECT household_id FROM " . $this->table . " ORDER BY household_id DESC LIMIT 1";
        $result = $this->connection->query($query);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = $row['household_id'];
            $number = intval(substr($lastId, 2)) + 1;
            return 'HH' . str_pad($number, 3, '0', STR_PAD_LEFT);
        }
        
        return 'HH001';
    }

    /**
     * Create household
     */
    public function create($family_no, $full_name, $address, $income = 0.00) {
        try {
            // Validate required fields
            if (empty($family_no) || empty($full_name) || empty($address)) {
                return [
                    'success' => false,
                    'message' => 'Family No, Full Name, and Address are required',
                    'error_type' => 'validation'
                ];
            }

            $household_id = $this->generateNextId();

            $query = "INSERT INTO " . $this->table . " (household_id, family_no, full_name, address, income) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            
            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }
            
            $stmt->bind_param('sissd', $household_id, $family_no, $full_name, $address, $income);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Household created successfully!',
                    'household_id' => $household_id
                ];
            } else {
                // Check if it's a duplicate entry error
                if ($this->connection->errno == 1062) {
                    return [
                        'success' => false,
                        'message' => 'Family No already exists',
                        'error_type' => 'validation'
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => 'Error creating household: ' . $stmt->error,
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
     * Update household
     */
    public function update($household_id, $family_no, $full_name, $address, $income = 0.00) {
        try {
            // Validate household exists
            $existing = $this->getById($household_id);
            if (!$existing) {
                return [
                    'success' => false,
                    'message' => 'Household not found',
                    'error_type' => 'not_found'
                ];
            }

            // Check if family_no already exists for other records
            $checkQuery = "SELECT household_id FROM " . $this->table . " WHERE family_no = ? AND household_id != ? LIMIT 1";
            $checkStmt = $this->connection->prepare($checkQuery);
            $checkStmt->bind_param('is', $family_no, $household_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Family No already exists',
                    'error_type' => 'validation'
                ];
            }

            $query = "UPDATE " . $this->table . " SET family_no = ?, full_name = ?, address = ?, income = ? WHERE household_id = ?";
            $stmt = $this->connection->prepare($query);
            
            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }
            
            $stmt->bind_param('issds', $family_no, $full_name, $address, $income, $household_id);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Household updated successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error updating household: ' . $stmt->error,
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
     * Delete household
     */
    public function delete($household_id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE household_id = ?";
            $stmt = $this->connection->prepare($query);
            
            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }
            
            $stmt->bind_param('s', $household_id);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Household deleted successfully!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error deleting household: ' . $stmt->error,
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
     * Create household with members in a single transaction
     */
    public function createWithMembers($family_no, $full_name, $address, $income = 0.00, $members = []) {
        try {
            // Start transaction
            $this->connection->begin_transaction();

            // Validate required fields
            if (empty($family_no) || empty($full_name) || empty($address)) {
                throw new Exception('Family No, Full Name, and Address are required');
            }

            // Check if family_no already exists
            $checkQuery = "SELECT household_id FROM " . $this->table . " WHERE family_no = ? LIMIT 1";
            $checkStmt = $this->connection->prepare($checkQuery);
            $checkStmt->bind_param('i', $family_no);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                throw new Exception('Family No already exists');
            }

            // Generate household ID
            $household_id = $this->generateNextId();

            // Insert household
            $query = "INSERT INTO " . $this->table . " (household_id, family_no, full_name, address, income) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Error preparing household statement: ' . $this->connection->error);
            }
            
            $stmt->bind_param('sissd', $household_id, $family_no, $full_name, $address, $income);
            
            if (!$stmt->execute()) {
                throw new Exception('Error creating household: ' . $stmt->error);
            }

            // Insert members if provided
            $membersCreated = 0;
            if (!empty($members) && is_array($members)) {
                require_once __DIR__ . '/Resident.php';
                // Pass the same connection to Resident model to use the same transaction
                $residentModel = new Resident($this->connection);

                foreach ($members as $index => $member) {
                    // Skip empty members
                    if (empty($member['first_name']) || empty($member['last_name'])) {
                        continue;
                    }

                    // Ensure age is set
                    if (empty($member['age'])) {
                        // Calculate age if birth_date is provided
                        if (!empty($member['birth_date'])) {
                            $birthDate = new DateTime($member['birth_date']);
                            $today = new DateTime();
                            $member['age'] = $today->diff($birthDate)->y;
                        } else {
                            $member['age'] = 0;
                        }
                    }

                    $memberData = [
                        'household_id' => $household_id,
                        'first_name' => $member['first_name'],
                        'middle_name' => $member['middle_name'] ?? '',
                        'last_name' => $member['last_name'],
                        'birth_date' => $member['birth_date'] ?? null,
                        'gender' => $member['gender'] ?? '',
                        'age' => $member['age'],
                        'contact_no' => $member['contact_no'] ?? '',
                        'email' => $member['email'] ?? ''
                    ];

                    $result = $residentModel->create($memberData);
                    if ($result['success']) {
                        $membersCreated++;
                    } else {
                        throw new Exception('Error creating member ' . ($index + 1) . ': ' . $result['message']);
                    }
                }
            }

            // Commit transaction
            $this->connection->commit();

            return [
                'success' => true,
                'message' => 'Household created successfully with ' . $membersCreated . ' member(s)!',
                'household_id' => $household_id,
                'members_created' => $membersCreated
            ];

        } catch (Exception $e) {
            // Rollback on error
            $this->connection->rollback();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_type' => 'exception'
            ];
        }
    }

    /**
     * Get all members of a household
     */
    public function getMembers($household_id) {
        $query = "SELECT r.resident_id, r.first_name, r.middle_name, r.last_name, r.birth_date, r.gender, r.age, r.contact_no, r.email 
                  FROM residents r 
                  WHERE r.household_id = ? 
                  ORDER BY r.age DESC";
        $stmt = $this->connection->prepare($query);
        
        if (!$stmt) {
            return [];
        }
        
        $stmt->bind_param('s', $household_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $members = [];
        while ($row = $result->fetch_assoc()) {
            $members[] = $row;
        }

        return $members;
    }

    /**
     * Update household with member management (add/update/delete members)
     */
    public function updateWithMembers($household_id, $family_no, $full_name, $address, $income = 0.00, $memberOperations = []) {
        require_once __DIR__ . '/Resident.php';
        
        try {
            // Start transaction
            $this->connection->begin_transaction();

            // Update household info
            $updateResult = $this->update($household_id, $family_no, $full_name, $address, $income);
            if (!$updateResult['success']) {
                throw new Exception($updateResult['message']);
            }

            // Pass the same connection to Resident model to use the same transaction
            $residentModel = new Resident($this->connection);
            $operationsSummary = [
                'updated' => 0,
                'added' => 0,
                'deleted' => 0
            ];

            // Process member operations
            if (isset($memberOperations['delete']) && !empty($memberOperations['delete'])) {
                foreach ($memberOperations['delete'] as $residentId) {
                    $deleteResult = $residentModel->deleteResident($residentId);
                    if ($deleteResult['success']) {
                        $operationsSummary['deleted']++;
                    } else {
                        throw new Exception('Error deleting member: ' . $deleteResult['message']);
                    }
                }
            }

            if (isset($memberOperations['update']) && !empty($memberOperations['update'])) {
                foreach ($memberOperations['update'] as $memberData) {
                    $updateResult = $residentModel->updateResident(
                        $memberData['resident_id'],
                        $memberData['first_name'],
                        $memberData['middle_name'],
                        $memberData['last_name'],
                        $memberData['birth_date'],
                        $memberData['gender'],
                        $memberData['contact_no'],
                        $memberData['email']
                    );
                    if ($updateResult['success']) {
                        $operationsSummary['updated']++;
                    } else {
                        throw new Exception('Error updating member: ' . $updateResult['message']);
                    }
                }
            }

            if (isset($memberOperations['add']) && !empty($memberOperations['add'])) {
                foreach ($memberOperations['add'] as $memberData) {
                    $createResult = $residentModel->createResident(
                        $household_id,
                        $memberData['first_name'],
                        $memberData['middle_name'],
                        $memberData['last_name'],
                        $memberData['birth_date'],
                        $memberData['gender'],
                        $memberData['contact_no'],
                        $memberData['email']
                    );
                    if ($createResult['success']) {
                        $operationsSummary['added']++;
                    } else {
                        throw new Exception('Error adding member: ' . $createResult['message']);
                    }
                }
            }

            // Commit transaction
            $this->connection->commit();

            $message = "Household updated successfully.";
            $details = [];
            if ($operationsSummary['added'] > 0) $details[] = "{$operationsSummary['added']} member(s) added";
            if ($operationsSummary['updated'] > 0) $details[] = "{$operationsSummary['updated']} member(s) updated";
            if ($operationsSummary['deleted'] > 0) $details[] = "{$operationsSummary['deleted']} member(s) removed";
            
            if (!empty($details)) {
                $message .= " " . implode(", ", $details) . ".";
            }

            return [
                'success' => true,
                'message' => $message,
                'operations' => $operationsSummary
            ];

        } catch (Exception $e) {
            $this->connection->rollback();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
?>
