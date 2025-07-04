<?php
header('Content-Type: application/json');
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\PayPalController;
use App\Helpers\TelemetryHelper;

try {
    // Obtener datos del POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['plan_id'])) {
        throw new Exception('Plan ID requerido');
    }
    
    $planId = $input['plan_id'];
    $userId = $_SESSION['usuario']['id'];
    
    // Debug: Log de valores recibidos
    error_log("DEBUG PayPal - Plan ID: $planId, User ID: $userId");
    
    // Crear controlador PayPal
    $paypalController = new PayPalController();
    
    // Crear orden
    $order = $paypalController->createOrder($planId, $userId);
    
    echo json_encode($order);
    
} catch (Exception $e) {
    http_response_code(500);
    
    // Trackear error de pago
    TelemetryHelper::trackPaymentError(
        $_SESSION['usuario']['id'] ?? null,
        $e->getMessage()
    );
    
    echo json_encode(['error' => $e->getMessage()]);
}
?>
