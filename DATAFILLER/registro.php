<?php
session_start();

require_once 'config/database.php';
require_once 'controllers/RegistroController.php';

// Inicializar base de datos
$database = new Database();
$db = $database->getConnection();

// Inicializar controlador
$controller = new RegistroController($db);

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $controller->registrar($_POST);
    
    if ($resultado['exito']) {
        $mensaje_exito = $resultado['mensaje'];
    } else {
        $mensaje_error = $resultado['mensaje'];
    }
}

// Cargar la vista
include_once 'views/registro_view.php';