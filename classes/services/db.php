<?php
/**
 * Připojení k DB, tato třída se volá všude, aby kód byl na jendom místě
 *
 *
 */

class DB {

    private $conn;
    private $errors;
    private static $instance = null;

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new DB();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "semestralka");
        if ($this->conn->connect_error) {
            die("DB connection failed with exception " . $this->conn->connect_error);
        }
    }

    function SELECT($query) {
        $result = $this->conn->query($query);
        $array = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($array, $row);
            }
        }
        return $array;
    }

    function INSERT($query): bool {
        if ($this->conn->query($query) === TRUE) {
            return true;
        } else {
            $this->errors .= $this->conn->error;
            return false;
        }
    }

    function UPDATE($query): bool {
        if ($this->conn->query($query) === TRUE) {
            return true;
        } else {
            $this->errors .= $this->conn->error;
            return false;
        }
    }

    function DELETE($query): bool {
        if ($this->conn->query($query) === TRUE) {
            return true;
        } else {
            $this->errors .= $this->conn->error;
            return false;
        }
    }

    function errorsPrint(){
        return $this->errors;
    }

    public function getConnection(){
        return $this->conn;
    }
}

?>
