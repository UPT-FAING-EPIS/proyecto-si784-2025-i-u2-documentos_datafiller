<?php
session_start();

// Redirigir si ya hay una sesión iniciada
if(isset($_SESSION['usuario'])) {
    header('Location: ../User/generardata.php');
    exit();
}

require_once '../../config/database.php';
require_once '../../controllers/LoginController.php';

// Inicializar la conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Inicializar el controlador de login
$loginController = new LoginController($db);

$mensaje_error = '';
$mensaje_exito = '';

// Procesar el formulario si se ha enviado
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $loginController->procesarLogin($_POST);
    
    if($resultado['exito']) {
        header('Location: ../User/generardata.php');
        exit();
    } else {
        $mensaje_error = $resultado['mensaje'];
    }
}

// Cargar la vista
include_once 'login_view.php';