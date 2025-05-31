<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Data Filler</title>
    <link rel="stylesheet" href="css/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .registro-container {
            display: flex;
            height: 100vh;
        }
        
        .logo-section {
            flex: 1;
            background-color: #2196F3;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        
        .logo-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem;
            background-color: #f5f5f5;
        }
        
        .registro-form {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
        }
        
        .registro-form h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #2196F3;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .submit-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .submit-btn:hover {
            background-color: #1e88e5;
        }
        
        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .error-message {
            background-color: #f44336;
            color: white;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="registro-container">
        <div class="logo-section">
            <img src="images/logo_datafiller.jpg" alt="Data Filler Logo">
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
                        <label for="nombre">Nombre</label>
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
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="submit-btn">Registrarse</button>
                </form>
                
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="index.php">Volver al inicio</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>