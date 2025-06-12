<?php
// Si deseas usar namespaces para autoloading y facilitar pruebas unitarias:
namespace App\Config;

use PDO;
use PDOException;


class Database {
    // Parámetros de la base de datos
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $conn;

    // Permitir sobreescritura en pruebas unitarias y usar variables de entorno
    public function __construct($host = null, $db_name = null, $username = null, $password = null, $port = null) {
        // Prioridad: Parámetros del constructor → Variables de entorno → Defaults locales
        $this->host = $host ?: $this->getEnvValue('MYSQLHOST', 'localhost');
        $this->db_name = $db_name ?: $this->getEnvValue('MYSQLDATABASE', 'datafiller');
        $this->username = $username ?: $this->getEnvValue('MYSQLUSER', 'root');
        $this->password = $password ?: $this->getEnvValue('MYSQLPASSWORD', '');
        $this->port = $port ?: $this->getEnvValue('MYSQLPORT', '3306');
    }

    // Función helper para obtener variables de entorno
    private function getEnvValue($key, $default = '') {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }

    // Conexión a la base de datos
    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db_name . ';charset=utf8mb4';
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            // SSL para conexiones remotas (Railway lo requiere)
            if ($this->host !== 'localhost' && $this->host !== '127.0.0.1') {
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
            }

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $e) {
            throw new \RuntimeException('Error de conexión: ' . $e->getMessage());
        }
        return $this->conn;
    }
}