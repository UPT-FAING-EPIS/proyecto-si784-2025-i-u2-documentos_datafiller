<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error - Data Filler</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="../../public/css/auth.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="registro-container">
        <div class="logo-section">
            <img src="../../images/welcome_datafiller.jpg" alt="Data Filler Logo">
        </div>
        <div class="form-section">
            <div class="registro-form">
                <h2>Enlace No Válido</h2>
                
                <div class="error-message">
                    <?php echo isset($error_message) ? $error_message : 'El enlace de recuperación no es válido o ha expirado.'; ?>
                </div>
                
                <p>Por favor, solicita un nuevo enlace de recuperación.</p>
                
                <div class="form-links">
                    <p><a href="recuperar_password.php" class="btn">Solicitar Nuevo Enlace</a></p>
                    <p><a href="login_view.php">Volver al inicio de sesión</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>