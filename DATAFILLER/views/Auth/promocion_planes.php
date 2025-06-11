<?php
session_start();

// Verificar que el usuario esté logueado
if(!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

require_once '../../config/database.php';

// Obtener información de los planes
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM tbplanes WHERE activo = 1 ORDER BY precio_mensual ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$planes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once 'promocion_planes_view.php';