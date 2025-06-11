<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Data Filler</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <main class="video-background-container">
        <video autoplay muted loop class="video-fondo">
            <source src="images/videos/fondo_index.mp4" type="video/mp4">
            Tu navegador no soporta videos HTML5.
        </video>

        <div class="contenido-centro">
            <h1 class="titulo-principal">PRESIONAME</h1>
            <a href="views/Auth/login_view.php" class="boton-iniciar">😎 DATA FILLER 😎</a>
        </div>
    </main>

</body>
</html>