<?php
session_start();

// Verificar que el usuario esté logueado
if(!isset($_SESSION['usuario'])) {
    header('Location: login_view.php');
    exit();
}

require_once __DIR__ . '/../../vendor/autoload.php'; // Usa autoload de Composer

use App\Config\Database;

// Obtener información de los planes
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM tbplanes WHERE activo = 1 ORDER BY precio_mensual ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$planes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once 'promocion_planes_view.php';