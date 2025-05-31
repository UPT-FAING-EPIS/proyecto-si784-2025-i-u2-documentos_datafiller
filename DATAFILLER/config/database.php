<?php
class Database {
    // Parámetros de la base de datos
    private $host = 'localhost';
    private $db_name = 'datafiller';
    private $username = 'root';
    private $password = '';
    private $conn;
    
    // Conexión a la base de datos
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
        }
        
        return $this->conn;
    }
}