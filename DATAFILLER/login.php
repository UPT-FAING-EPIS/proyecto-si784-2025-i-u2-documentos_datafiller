<?php
session_start();

// Redirigir si ya hay una sesión iniciada
if(isset($_SESSION['usuario'])) {
    header('Location: generardata.php');
    exit();
}

require_once 'config/database.php';
require_once 'controllers/LoginController.php';

// Inicializar la conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Inicializar el controlador de login
$loginController = new LoginController($db);

// Procesar el formulario si se ha enviado
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $loginController->procesarLogin($_POST);
    
    if($resultado['exito']) {
        // Si el login fue exitoso, redirigir a la página principal
        header('Location: generardata.php');
        exit();
    } else {
        // Si hubo un error, mostrar mensaje
        $mensaje_error = $resultado['mensaje'];
    }
}

// Cargar la vista
include_once 'views/login_view.php';