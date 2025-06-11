<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Data Filler</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="../../public/css/auth.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="registro-container">
        <div class="logo-section">
            <img src="../../images/logo_datafiller.jpg" alt="Data Filler Logo">
        </div>
        <div class="form-section">
            <div class="registro-form">
                <h2>Registro de Usuario</h2>
                
                <?php if(isset($mensaje_exito)): ?>
                    <div class="success-message">
                        <?php echo $mensaje_exito; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($mensaje_error)): ?>
                    <div class="error-message">
                        <?php echo $mensaje_error; ?>
                    </div>
                <?php endif; ?>
                
                <form action="registro.php" method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre de Usuario</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="apellido_paterno">Apellido Paterno</label>
                        <input type="text" id="apellido_paterno" name="apellido_paterno" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="apellido_materno">Apellido Materno</label>
                        <input type="text" id="apellido_materno" name="apellido_materno" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" required placeholder="ejemplo@correo.com">
                        <small class="input-help">Solo se permite una cuenta por email</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required minlength="6">
                        <small class="input-help">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="submit-btn">Registrarse</button>
                </form>
                
                <div class="form-links">
                    <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                    <p><a href="../../index.php">Volver al inicio</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>