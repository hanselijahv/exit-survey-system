<?php
/**
 * This class is responsible for handling the database connection.
 * @author Fabe
 */
class Database
{

    private $servername = "db-container";
    private $username = "root";
    private $password = "password";
    private $dbname = "exit_survey";

    public $conn;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function closeConnection()
    {
        $this->conn->close();
    }
}

