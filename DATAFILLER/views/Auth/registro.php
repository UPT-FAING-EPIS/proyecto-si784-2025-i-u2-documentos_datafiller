<?php
// filepath: c:\xampp\htdocs\proyecto-si784-2025-i-u2-documentos_datafiller\DATAFILLER\views\Auth\registro.php
session_start();

// Redirigir si ya hay una sesión iniciada
if (isset($_SESSION['usuario'])) {
    header('Location: ../User/generardata.php');
    exit();
}

// Verificar que el autoloader existe
$autoloader_path = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoloader_path)) {
    die('Error: Autoloader not found at: ' . $autoloader_path);
}

// Cargar el autoloader de Composer
require_once $autoloader_path;

// Debug: Mostrar información de debugging
echo "<!-- Debug Info:\n";
echo "Autoloader path: " . $autoloader_path . "\n";
echo "Current directory: " . __DIR__ . "\n";
echo "Config directory exists: " . (is_dir(__DIR__ . '/../../config') ? 'YES' : 'NO') . "\n";
echo "Database.php exists: " . (file_exists(__DIR__ . '/../../config/Database.php') ? 'YES' : 'NO') . "\n";
echo "-->\n";

// Verificar clases con mejor debug
try {
    if (!class_exists('App\Config\Database')) {
        die('Error: Database class not found. File exists: ' . 
            (file_exists(__DIR__ . '/../../config/Database.php') ? 'YES' : 'NO'));
    }

    if (!class_exists('App\Controllers\RegistroController')) {
        die('Error: RegistroController class not found. File exists: ' . 
            (file_exists(__DIR__ . '/../../controllers/RegistroController.php') ? 'YES' : 'NO'));
    }
} catch (Exception $e) {
    die('Error loading classes: ' . $e->getMessage());
}

// Importar las clases necesarias
use App\Config\Database;
use App\Controllers\RegistroController;

$mensaje_error = '';
$mensaje_exito = '';

try {
    // Inicializar la conexión a la base de datos
    $database = new Database();
    $db = $database->getConnection();

    // Inicializar el controlador de registro
    $registroController = new RegistroController($db);

    // Procesar el formulario si se ha enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $resultado = $registroController->registrar($_POST);
        
        if ($resultado['exito']) {
            $mensaje_exito = $resultado['mensaje'];
            header('Location: promocion_planes.php');
            exit();
        } else {
            $mensaje_error = $resultado['mensaje'];
        }
    }
} catch (Exception $e) {
    error_log("Error en registro: " . $e->getMessage());
    $mensaje_error = 'Error interno: ' . $e->getMessage();
}

// Cargar la vista
include_once 'registro_view.php';