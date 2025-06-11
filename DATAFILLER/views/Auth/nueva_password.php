<?php
session_start();

// Verificar que se recibió un token
if(!isset($_GET['token']) || empty($_GET['token'])) {
    header('Location: login.php?error=token_invalido');
    exit();
}

$token = $_GET['token'];

// Usa autoload de Composer (¡muy importante!)
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Database;
use App\Models\Usuario;

$database = new Database();
$db = $database->getConnection();
$usuarioModel = new Usuario($db);

// Verificar token
$tokenData = $usuarioModel->verificarTokenRecuperacion($token);

if(!$tokenData) {
    $error_message = "El enlace de recuperación ha expirado o no es válido.";
    include 'token_error_view.php';
    exit();
}

$mensaje_error = '';
$mensaje_exito = '';

// Procesar cambio de contraseña
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(empty($_POST['password']) || empty($_POST['confirm_password'])) {
        $mensaje_error = 'Todos los campos son obligatorios.';
    } elseif($_POST['password'] !== $_POST['confirm_password']) {
        $mensaje_error = 'Las contraseñas no coinciden.';
    } elseif(strlen($_POST['password']) < 6) {
        $mensaje_error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        // Cambiar contraseña
        if($usuarioModel->cambiarPassword($tokenData['usuario_id'], $_POST['password'])) {
            // Marcar token como usado
            $usuarioModel->marcarTokenUsado($token);
            $mensaje_exito = 'Contraseña cambiada exitosamente. Puedes iniciar sesión con tu nueva contraseña.';
        } else {
            $mensaje_error = 'Error cambiando la contraseña. Intente nuevamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña - Data Filler</title>
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
                <h2>Crear Nueva Contraseña</h2>
                
                <?php if(!empty($mensaje_error)): ?>
                    <div class="error-message"><?php echo $mensaje_error; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($mensaje_exito)): ?>
                    <div class="success-message"><?php echo $mensaje_exito; ?></div>
                    <div class="form-links">
                        <p><a href="login_view.php" class="btn">Ir a Iniciar Sesión</a></p>
                    </div>
                <?php else: ?>
                    <p>Ingresa tu nueva contraseña para la cuenta: <strong><?php echo htmlspecialchars($tokenData['email']); ?></strong></p>
                    
                    <form method="post">
                        <div class="form-group">
                            <label for="password">Nueva Contraseña</label>
                            <input type="password" id="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Nueva Contraseña</label>
                            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repite la contraseña">
                        </div>
                        
                        <button type="submit" class="submit-btn">Cambiar Contraseña</button>
                    </form>
                <?php endif; ?>
                
                <div class="form-links">
                    <p><a href="login_view.php">Volver al inicio de sesión</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>