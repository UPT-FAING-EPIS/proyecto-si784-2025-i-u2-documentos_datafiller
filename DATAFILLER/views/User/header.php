<?php
session_start();

// Obtener información del usuario usando SOLO el controlador
require_once '../../controllers/UsuarioController.php';

// Variables por defecto
$plan_usuario = 'gratuito';
$consultas_restantes = 0;

// Si hay usuario logueado, obtener datos del controlador
if(isset($_SESSION['usuario']) && isset($_SESSION['usuario']['id'])) {
    $usuarioController = new UsuarioController();
    $datosUsuario = $usuarioController->obtenerDatosParaHeader($_SESSION['usuario']['id']);
    
    $plan_usuario = $datosUsuario['plan_usuario'];
    $consultas_restantes = $datosUsuario['consultas_restantes'];
}

// Debug temporal - remover después
error_log("Plan usuario: " . $plan_usuario);
error_log("Consultas restantes: " . $consultas_restantes);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Data Filler</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="../../public/css/header.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <header class="main-header">
        <div class="logo-container">
            <img src="../../images/logo_datafiller.png" alt="Data Filler Logo">
            <h1>DATAFILLER</h1>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="generardata.php">GENERAR DATA</a></li>
                <li><a href="soporte.php">SOPORTE</a></li>
                <?php if(isset($_SESSION['usuario'])): ?>
                    <li><a href="../Auth/promocion_planes.php">PLANES</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="register-btn">
            <?php if(isset($_SESSION['usuario'])): ?>
                <!-- USUARIO LOGUEADO - Solo mostrar info y logout -->
                <div class="user-info">
                    <div class="user-details">
                        <span class="user-name">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                        <span class="user-plan plan-<?php echo $plan_usuario; ?>">
                            <?php echo $plan_usuario === 'premium' ? '👑 Premium' : '⭐ Gratuito'; ?>
                        </span>
                    </div>
                    <a href="../Auth/logout.php" class="btn logout-btn">Cerrar Sesión</a>
                </div>
            <?php else: ?>
                <!-- USUARIO NO LOGUEADO - Solo mostrar botones de acceso -->
                <a href="../Auth/login.php" class="btn">INICIAR SESIÓN</a>
                <a href="../Auth/registro.php" class="btn">REGÍSTRATE</a>
            <?php endif; ?>
        </div>
    </header>
    <main class="container"></main>