<?php
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// ✅ PROTECCIÓN: Verificar autenticación en páginas de usuario
$pagina_actual = basename($_SERVER['PHP_SELF']);
$carpeta_actual = basename(dirname($_SERVER['PHP_SELF']));

// Si estamos en carpeta User, verificar que esté logueado
if($carpeta_actual === 'User') {
    if(!isset($_SESSION['usuario'])) {
        header('Location: ../Auth/login.php');
        exit();
    }
}

require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload de Composer

use App\Controllers\UsuarioController;

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
    <main class="container">