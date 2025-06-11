<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Data Filler</title>
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
                <h2>Iniciar Sesión</h2>

                <?php if (!empty($mensaje_error)): ?>
                    <div class="error-message"><?php echo $mensaje_error; ?></div>
                <?php endif; ?>

                <?php if (!empty($mensaje_exito)): ?>
                    <div class="success-message"><?php echo $mensaje_exito; ?></div>
                <?php endif; ?>

                <!-- AQUÍ ESTÁ EL CAMBIO: action="login.php" -->
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre de Usuario</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit" class="submit-btn">Iniciar Sesión</button>
                </form>

                <div class="form-links">
                    <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
                    <p><a href="recuperar_password.php">¿Olvidaste tu contraseña?</a></p>
                    <p><a href="../../index.php">Volver al inicio</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>