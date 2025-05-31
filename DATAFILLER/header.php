<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Data Filler</title>
    <link rel="stylesheet" href="css/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <header class="main-header">
        <div class="logo-container">
            <img src="images/logo_datafiller.png" alt="Data Filler Logo">
            <h1>DATAFILLER</h1>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="generardata.php">GENERAR DATA</a></li>
                <li><a href="soporte.php">SOPORTE</a></li>
            </ul>
        </nav>
        <div class="register-btn">
        <?php if(isset($_SESSION['usuario'])): ?>
        <div class="user-info">
            <span>Bienvenido, <?php echo $_SESSION['usuario']['nombre']; ?></span>
            <a href="logout.php" class="btn logout-btn">Cerrar Sesión</a>
        </div>
        <?php else: ?>
            <a href="login.php" class="btn">INICIAR SESIÓN</a>
            <a href="registro.php" class="btn">REGÍSTRATE</a>
        <?php endif; ?>
        </div>
    </header>
    <main class="container">