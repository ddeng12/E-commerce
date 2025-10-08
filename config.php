<?php
class DatabaseConnection {
    private $host = 'localhost';
    private $db_name = 'ecommerce_2025A_david_deng';
    private $username = 'david.deng';
    private $password = 'dengdit3';
    private $conn;git 

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
