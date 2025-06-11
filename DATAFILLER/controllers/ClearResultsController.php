<?php
session_start();
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if(isset($input['action']) && $input['action'] === 'clear_results') {
        // Limpiar variables de sesión relacionadas con resultados
        unset($_SESSION['datos_generados']);
        unset($_SESSION['estadisticas_generacion']);
        unset($_SESSION['estructura_analizada']);
        unset($_SESSION['db_type']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Resultados limpiados exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Acción no válida'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>