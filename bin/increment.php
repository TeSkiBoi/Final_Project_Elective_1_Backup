 /**
     * Generate unique department ID
     */
    // private function generateDepartmentId() {
    //     $query = "SELECT department_id FROM " . $this->table . " ORDER BY department_id DESC LIMIT 1";
    //     $result = $this->connection->query($query);

    //     if ($result && $result->num_rows > 0) {
    //         $row = $result->fetch_assoc();
    //         $lastId = $row['department_id'];
    //         $number = (int)substr($lastId, 1) + 1;
    //     } else {
    //         $number = 1;
    //     }

    //     return 'D' . str_pad($number, 3, '0', STR_PAD_LEFT);
    // }