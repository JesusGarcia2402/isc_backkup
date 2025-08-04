<?php
require_once 'config.php';

class Database {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }
        
        $this->conn->set_charset("utf8");
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        $this->conn->close();
    }

    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $types = '';
            $values = [];
            
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                
                $values[] = $param;
            }
            
            array_unshift($values, $types);
            call_user_func_array([$stmt, 'bind_param'], $this->refValues($values));
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return $stmt->affected_rows;
    }

    private function refValues($arr) {
        $refs = [];
        
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        
        return $refs;
    }
}
?>