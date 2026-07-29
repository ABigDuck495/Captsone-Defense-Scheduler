<?php
class Database {
    private $host = "localhost";
    private $db_name = "Capstone_Defense_Scheduler";
    private $username = "root";
    private $password = "727727";
    private $conn;
    public $res;

    public function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function __destruct() {
        $this->conn = null; // closes PDO connection
    }

    public function select($table, $columns, $where = null) {
        $sql = "SELECT $columns FROM $table";
        if ($where != null) $sql .= " WHERE $where";

        $stmt = $this->conn->query($sql);
        $this->res = $stmt->fetchAll();
        return $this->res;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
