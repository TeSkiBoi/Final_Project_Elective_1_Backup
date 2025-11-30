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
        $query = "SELECT h.household_id, h.family_no, h.address, h.income, h.household_head_id,
                  CONCAT(r.first_name, ' ', IFNULL(CONCAT(r.middle_name, ' '), ''), r.last_name) as household_head_name
                  FROM " . $this->table . " h
                  LEFT JOIN residents r ON h.household_head_id = r.resident_id
                  ORDER BY h.family_no ASC";
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
        $query = "SELECT h.household_id, h.family_no, h.address, h.income, h.household_head_id,
                  CONCAT(r.first_name, ' ', IFNULL(CONCAT(r.middle_name, ' '), ''), r.last_name) as household_head_name
                  FROM " . $this->table . " h
                  LEFT JOIN residents r ON h.household_head_id = r.resident_id
                  WHERE h.household_id = ? LIMIT 1";
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
    public function create($family_no, $address, $income = 0.00) {
        try {
            // Validate required fields
            if (empty($family_no) || empty($address)) {
                return [
                    'success' => false,
                    'message' => 'Family No and Address are required',
                    'error_type' => 'validation'
                ];
            }

            $household_id = $this->generateNextId();

            $query = "INSERT INTO " . $this->table . " (household_id, family_no, address, income) VALUES (?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            
            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }
            
            $stmt->bind_param('sisd', $household_id, $family_no, $address, $income);
            
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
    public function update($household_id, $family_no, $address, $income = 0.00, $household_head_id = null) {
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

            $query = "UPDATE " . $this->table . " SET family_no = ?, address = ?, income = ?, household_head_id = ? WHERE household_id = ?";
            $stmt = $this->connection->prepare($query);
            
            if (!$stmt) {
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }
            
            $stmt->bind_param('isdss', $family_no, $address, $income, $household_head_id, $household_id);
            
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
            // Start transaction to ensure atomicity
            $this->connection->begin_transaction();
            
            // Step 1: Set household_head_id to NULL first to avoid circular foreign key constraint
            $nullHeadQuery = "UPDATE " . $this->table . " SET household_head_id = NULL WHERE household_id = ?";
            $nullStmt = $this->connection->prepare($nullHeadQuery);
            
            if (!$nullStmt) {
                $this->connection->rollback();
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }
            
            $nullStmt->bind_param('s', $household_id);
            if (!$nullStmt->execute()) {
                $this->connection->rollback();
                return [
                    'success' => false,
                    'message' => 'Error clearing household head: ' . $nullStmt->error,
                    'error_type' => 'database'
                ];
            }
            
            // Step 2: Now delete the household (CASCADE will delete all residents automatically)
            $query = "DELETE FROM " . $this->table . " WHERE household_id = ?";
            $stmt = $this->connection->prepare($query);
            
            if (!$stmt) {
                $this->connection->rollback();
                return [
                    'success' => false,
                    'message' => 'Error preparing statement: ' . $this->connection->error,
                    'error_type' => 'database'
                ];
            }
            
            $stmt->bind_param('s', $household_id);
            
            if ($stmt->execute()) {
                $this->connection->commit();
                return [
                    'success' => true,
                    'message' => 'Household deleted successfully!'
                ];
            } else {
                $this->connection->rollback();
                return [
                    'success' => false,
                    'message' => 'Error deleting household: ' . $stmt->error,
                    'error_type' => 'database'
                ];
            }

        } catch (Exception $e) {
            $this->connection->rollback();
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
    public function createWithMembers($family_no, $address, $income = 0.00, $members = []) {
        try {
            // Start transaction
            $this->connection->begin_transaction();

            // Validate required fields
            if (empty($family_no) || empty($address)) {
                throw new Exception('Family No and Address are required');
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
            $query = "INSERT INTO " . $this->table . " (household_id, family_no, address, income) VALUES (?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Error preparing household statement: ' . $this->connection->error);
            }
            
            $stmt->bind_param('sisd', $household_id, $family_no, $address, $income);
            
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
        $query = "SELECT r.resident_id, r.first_name, r.middle_name, r.last_name, r.birth_date, r.gender, r.age, r.contact_no, r.email, r.created_at
                  FROM residents r 
                  WHERE r.household_id = ? 
                  ORDER BY r.created_at ASC";
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
     * Auto-assign household head based on resident count
     * Logic: If exactly 1 resident exists, auto-assign as head
     *        If 0 residents, set head to NULL
     *        If multiple residents and no head set, assign first resident (oldest by created_at)
     */
    public function autoAssignHouseholdHead($household_id) {
        try {
            // Get all members for this household
            $members = $this->getMembers($household_id);
            $memberCount = count($members);
            
            error_log("Auto-assign head for household {$household_id}: {$memberCount} members found");
            
            if ($memberCount === 0) {
                // No residents - set head to NULL
                $updateQuery = "UPDATE " . $this->table . " SET household_head_id = NULL WHERE household_id = ?";
                $stmt = $this->connection->prepare($updateQuery);
                $stmt->bind_param('s', $household_id);
                $stmt->execute();
                
                error_log("No members - head set to NULL");
                return [
                    'success' => true,
                    'message' => 'No residents, head set to NULL',
                    'household_head_id' => null,
                    'member_count' => 0
                ];
            } elseif ($memberCount === 1) {
                // Exactly 1 resident - auto-assign as head
                $residentId = $members[0]['resident_id'];
                $updateQuery = "UPDATE " . $this->table . " SET household_head_id = ? WHERE household_id = ?";
                $stmt = $this->connection->prepare($updateQuery);
                $stmt->bind_param('ss', $residentId, $household_id);
                $stmt->execute();
                
                error_log("Single member - auto-assigned {$residentId} as head");
                return [
                    'success' => true,
                    'message' => 'Single resident auto-assigned as household head',
                    'household_head_id' => $residentId,
                    'member_count' => 1
                ];
            } else {
                // Multiple residents - check if head is already set
                $household = $this->getById($household_id);
                $currentHeadId = $household['household_head_id'] ?? null;
                
                // Check if current head is still a valid member
                $headStillExists = false;
                foreach ($members as $member) {
                    if ($member['resident_id'] === $currentHeadId) {
                        $headStillExists = true;
                        break;
                    }
                }
                
                if (!$currentHeadId || !$headStillExists) {
                    // No head set or head was removed - assign first resident (earliest created)
                    $newHeadId = $members[0]['resident_id'];
                    $updateQuery = "UPDATE " . $this->table . " SET household_head_id = ? WHERE household_id = ?";
                    $stmt = $this->connection->prepare($updateQuery);
                    $stmt->bind_param('ss', $newHeadId, $household_id);
                    $stmt->execute();
                    
                    error_log("Multiple members, head reassigned to {$newHeadId}");
                    return [
                        'success' => true,
                        'message' => 'Household head reassigned to first resident',
                        'household_head_id' => $newHeadId,
                        'member_count' => $memberCount
                    ];
                } else {
                    // Head is valid, no change needed
                    error_log("Multiple members, head {$currentHeadId} still valid");
                    return [
                        'success' => true,
                        'message' => 'Household head is valid',
                        'household_head_id' => $currentHeadId,
                        'member_count' => $memberCount
                    ];
                }
            }
        } catch (Exception $e) {
            error_log("Error in autoAssignHouseholdHead: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error auto-assigning household head: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update household with member management (add/update/delete members)
     */
    public function updateWithMembers($household_id, $family_no, $address, $income = 0.00, $household_head_id = null, $memberOperations = []) {
        require_once __DIR__ . '/Resident.php';
        
        try {
            // Start transaction
            $this->connection->begin_transaction();

            // IMPORTANT: Update household info WITHOUT household_head_id first to avoid foreign key constraint
            // We'll update the head AFTER adding new members
            $updateResult = $this->update($household_id, $family_no, $address, $income, null);
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

            // NOW update household head AFTER all members have been added/updated
            // This ensures the resident exists before setting as head
            if ($household_head_id !== null) {
                $headUpdateQuery = "UPDATE " . $this->table . " SET household_head_id = ? WHERE household_id = ?";
                $headStmt = $this->connection->prepare($headUpdateQuery);
                if ($headStmt) {
                    $headStmt->bind_param('ss', $household_head_id, $household_id);
                    if (!$headStmt->execute()) {
                        throw new Exception('Error updating household head: ' . $headStmt->error);
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
