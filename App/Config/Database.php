<?php
/**
 * Database Configuration Class
 * Handles database connection using MySQLi
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'barangay_biga_db';
    private $username = 'root';
    private $password = '';
    private $connection;

    /**
     * Connect to Database
     */
    public function connect() {
        // Set connection timeout and error reporting
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        $this->connection = new mysqli();
        $this->connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
        $this->connection->real_connect(
            $this->host,
            $this->username,
            $this->password,
            $this->db_name
        );

        // Check connection
        if ($this->connection->connect_error) {
            // Return JSON error instead of HTML
            if (headers_sent()) {
                throw new Exception('Database connection failed: ' . $this->connection->connect_error);
            }
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database connection failed']);
            exit;
        }

        // Set charset
        $this->connection->set_charset("utf8mb4");
        
        return $this->connection;
    }

    /**
     * Get Connection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Close Connection
     */
    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}