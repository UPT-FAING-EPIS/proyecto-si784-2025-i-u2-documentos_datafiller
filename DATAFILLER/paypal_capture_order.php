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
    
    if (!isset($input['order_id'])) {
        throw new Exception('Order ID requerido');
    }
    
    $orderId = $input['order_id'];
    $userId = $_SESSION['usuario']['id'];
    
    // Crear controlador PayPal
    $paypalController = new PayPalController();
    
    // Capturar orden
    $capture = $paypalController->captureOrder($orderId);
    
    echo json_encode($capture);
    
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
