<?php
class DatabaseConnection {
    private $host = 'localhost';
    private $db_name = 'u801837948_shop';
    private $username = 'u801837948_ecommerc2025';
    private $password = 'Dreamsandnightmares3!';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>
