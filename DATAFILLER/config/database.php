<?php
// Si deseas usar namespaces para autoloading y facilitar pruebas unitarias:
namespace App\Config;

use PDO;
use PDOException;

class Database {
    // Parámetros de la base de datos
    private $host = 'localhost';
    private $db_name = 'datafiller';
    private $username = 'root';
    private $password = '';
    private $conn;

    // Permitir sobreescritura en pruebas unitarias
    public function __construct($host = null, $db_name = null, $username = null, $password = null) {
        if ($host) $this->host = $host;
        if ($db_name) $this->db_name = $db_name;
        if ($username) $this->username = $username;
        if ($password) $this->password = $password;
    }

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
            throw new \RuntimeException('Error de conexión: ' . $e->getMessage());
        }
        return $this->conn;
    }
}