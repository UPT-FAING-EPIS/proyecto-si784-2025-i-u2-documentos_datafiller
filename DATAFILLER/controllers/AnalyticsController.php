<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;
use Exception;

class AnalyticsController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function registrarDescarga($formato, $tamaño) {
        try {
            if(!isset($_SESSION['usuario'])) {
                return ['success' => false, 'message' => 'Usuario no autenticado'];
            }

            $usuario_id = $_SESSION['usuario']['id'];

            $query = "INSERT INTO tbauditoria_consultas (usuario_id, tipo_consulta, cantidad_registros, formato_exportacion, fecha_consulta, ip_usuario) 
                      VALUES (:usuario_id, :tipo, :cantidad, :formato, NOW(), :ip)";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindValue(':tipo', 'descarga_datos');
            $stmt->bindParam(':cantidad', $tamaño);
            $stmt->bindParam(':formato', $formato);
            $stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);

            $stmt->execute();

            return ['success' => true, 'message' => 'Descarga registrada'];

        } catch(Exception $e) {
            error_log("Error registrando descarga: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error interno'];
        }
    }
}