<?php
/**
 * Database Configuration Class
 * Handles database connection using MySQLi
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'student_information_system';
    private $username = 'root';
    private $password = '';
    private $connection;

    /**
     * Connect to Database
     */
    public function connect() {
        $this->connection = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->db_name
        );

        // Check connection
        if ($this->connection->connect_error) {
            die('Connection Failed: ' . $this->connection->connect_error);
        }

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
?>
