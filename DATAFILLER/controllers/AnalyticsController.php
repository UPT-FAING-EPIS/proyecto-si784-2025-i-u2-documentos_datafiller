<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../models/Usuario.php';

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

// Procesar solicitud
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if(isset($input['action'])) {
        $analytics = new AnalyticsController();
        
        switch($input['action']) {
            case 'registrar_descarga':
                $formato = $input['formato'] ?? 'unknown';
                $tamaño = $input['tamaño'] ?? 0;
                $result = $analytics->registrarDescarga($formato, $tamaño);
                echo json_encode($result);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no especificada']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método not permitido']);
}
?>