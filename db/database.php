<?php 
    class Database{
        
        private $host = "localhost";
        private $db_name = "Capstone_Defense_Scheduler";
        private $username = "root";
        private $password = "727727";
        private $conn;
        public $res;

        public function __construct(){
            $this->conn = new mysqli($this->host,$this->username,$this->password,$this->db_name);
            if($this->conn->connect_error)  die("Connection Failed");
        }
        public function __destruct(){
            $this->conn->close();
        }
    }

?>