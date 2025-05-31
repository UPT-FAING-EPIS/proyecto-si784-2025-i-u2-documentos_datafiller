<?php
require_once 'models/Usuario.php';

class RegistroController {
    private $usuarioModel;
    
    public function __construct($db) {
        $this->usuarioModel = new Usuario($db);
    }
    
    public function registrar($datos) {
        // Validar datos
        if(empty($datos['nombre']) || empty($datos['apellido_paterno']) || 
           empty($datos['apellido_materno']) || empty($datos['password']) || 
           empty($datos['confirm_password'])) {
            return [
                'exito' => false,
                'mensaje' => 'Por favor complete todos los campos requeridos.'
            ];
        }
        
        // Verificar que las contraseñas coincidan
        if($datos['password'] !== $datos['confirm_password']) {
            return [
                'exito' => false,
                'mensaje' => 'Las contraseñas no coinciden.'
            ];
        }
        
        // Asignar valores al modelo
        $this->usuarioModel->nombre = $datos['nombre'];
        $this->usuarioModel->apellido_paterno = $datos['apellido_paterno'];
        $this->usuarioModel->apellido_materno = $datos['apellido_materno'];
        $this->usuarioModel->password = $datos['password'];
        
        // Crear usuario
        if($this->usuarioModel->crear()) {
            // Iniciar sesión para el usuario
            session_start();
            $_SESSION['usuario'] = [
                'nombre' => $datos['nombre'],
                'apellido_paterno' => $datos['apellido_paterno']
            ];
            
            // Redirigir a generardata.php
            header('Location: generardata.php');
            exit;
        } else {
            return [
                'exito' => false,
                'mensaje' => 'No se pudo completar el registro. Por favor intente de nuevo.'
            ];
        }
    }
}