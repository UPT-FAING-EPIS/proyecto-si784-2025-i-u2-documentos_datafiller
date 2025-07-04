<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Helpers\TelemetryHelper; // ✅ AGREGAR TELEMETRÍA

class LoginController {
    private $usuarioModel;
    
    // Constructor
    public function __construct($db) {
        $this->usuarioModel = new Usuario($db);
    }
    
    // Método para procesar el inicio de sesión
    public function procesarLogin($datos) {
        // Validar que se enviaron los datos necesarios
        if(empty($datos['nombre']) || empty($datos['password'])) {
            return [
                'exito' => false,
                'mensaje' => 'Por favor complete todos los campos.'
            ];
        }
        
        // Sanitizar y preparar datos
        $nombre = strtolower(trim($datos['nombre']));
        $password = trim($datos['password']);
        
        // Validar login usando el modelo de usuario
        $resultado = $this->usuarioModel->validarLogin($nombre, $password);
        
        if($resultado['exito']) {
            // Solo asignar a la sesión (ya está iniciada en login.php)
            $_SESSION['usuario'] = [
                'id' => $resultado['usuario']['id'],
                'nombre' => $resultado['usuario']['nombre'],
                'apellido_paterno' => $resultado['usuario']['apellido_paterno'],
                'email' => $resultado['usuario']['email']
            ];
            
            // ✅ TRACKEAR LOGIN EXITOSO
            TelemetryHelper::trackLogin(
                $resultado['usuario']['id'],
                $resultado['usuario']['nombre'],
                true
            );
            
            return [
                'exito' => true,
                'mensaje' => 'Inicio de sesión exitoso.',
                'usuario' => $resultado['usuario']
            ];
        } else {
            // ✅ TRACKEAR LOGIN FALLIDO
            TelemetryHelper::trackLogin(
                null,
                $nombre,
                false,
                'Credenciales incorrectas'
            );
            
            return [
                'exito' => false,
                'mensaje' => 'Nombre de usuario o contraseña incorrectos.'
            ];
        }
    }
}