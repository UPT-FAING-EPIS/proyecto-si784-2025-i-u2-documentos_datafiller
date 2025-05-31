<?php
require_once 'models/Usuario.php';

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
            // Iniciar sesión
            $_SESSION['usuario'] = $resultado['usuario'];
            
            return [
                'exito' => true,
                'mensaje' => 'Inicio de sesión exitoso.',
                'usuario' => $resultado['usuario']
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'Nombre de usuario o contraseña incorrectos.'
            ];
        }
    }
}