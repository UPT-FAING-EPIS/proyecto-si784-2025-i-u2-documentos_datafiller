<?php
header('Content-Type: application/json');

// Usa el autoload de Composer y namespaces modernos
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Database;
use App\Models\Usuario;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

// Obtener JSON del body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['email']) || empty($data['email'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'Email requerido']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $usuarioModel = new Usuario($db);
    
    // Verificar si el email existe
    $usuario = $usuarioModel->buscarPorEmail($data['email']);
    
    if (!$usuario) {
        echo json_encode([
            'exito' => false, 
            'mensaje' => 'No existe una cuenta asociada a este email.'
        ]);
        exit();
    }
    
    // Generar token único
    $token = bin2hex(random_bytes(32));
    $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Guardar token en base de datos
    if ($usuarioModel->guardarTokenRecuperacion($usuario['id'], $token, $expiracion)) {
        echo json_encode([
            'exito' => true,
            'nombre' => $usuario['nombre'],
            'token' => $token,
            'mensaje' => 'Token generado correctamente'
        ]);
    } else {
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Error interno. Intente nuevamente.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error en recuperación: " . $e->getMessage());
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error interno del servidor.'
    ]);
}