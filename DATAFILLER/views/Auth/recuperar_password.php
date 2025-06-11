<?php
session_start();

// Redirigir si ya hay una sesión iniciada
if(isset($_SESSION['usuario'])) {
    header('Location: ../User/generardata.php');
    exit();
}

require_once '../../config/database.php';

$mensaje_error = '';
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje_error = 'Por favor ingrese un email válido.';
    } else {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Verificar si el email existe
        $query = "SELECT id, nombre FROM tbusuario WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() === 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Generar token único
            $token = bin2hex(random_bytes(32));
            $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Guardar token en la base de datos
            $query = "INSERT INTO tbrecuperacion_password (usuario_id, token, email, fecha_expiracion) 
                      VALUES (:usuario_id, :token, :email, :fecha_expiracion)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario['id']);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':fecha_expiracion', $fecha_expiracion);
            
            if ($stmt->execute()) {
                $mensaje_exito = 'Se ha enviado un enlace de recuperación a tu email. (Funcionalidad de email pendiente de implementar)';
                // Aquí implementarías el envío de email en el futuro
            } else {
                $mensaje_error = 'Error al procesar la solicitud. Intenta nuevamente.';
            }
        } else {
            $mensaje_error = 'No se encontró una cuenta asociada a este email.';
        }
    }
}

include_once 'recuperar_password_view.php';