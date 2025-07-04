<?php
namespace App\Controllers;

use App\Models\Usuario; // <-- Agrega el use statement correcto
use App\Helpers\TelemetryHelper; // ✅ AGREGAR TELEMETRÍA

class RegistroController {
    private $usuarioModel;

    public function __construct($db) {
        $this->usuarioModel = new Usuario($db); // <-- Usa el nombre calificado por el namespace
    }

    public function registrar($datos) {
        // Validar datos
        if(empty($datos['nombre']) || empty($datos['apellido_paterno']) ||
           empty($datos['apellido_materno']) || empty($datos['email']) ||
           empty($datos['password']) || empty($datos['confirm_password'])) {
            return [
                'exito' => false,
                'mensaje' => 'Por favor complete todos los campos requeridos.'
            ];
        }

        // Verificar formato de email
        if(!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'exito' => false,
                'mensaje' => 'Por favor ingrese un email válido.'
            ];
        }

        // Verificar que las contraseñas coincidan
        if($datos['password'] !== $datos['confirm_password']) {
            return [
                'exito' => false,
                'mensaje' => 'Las contraseñas no coinciden.'
            ];
        }

        // Verificar longitud de contraseña
        if(strlen($datos['password']) < 6) {
            return [
                'exito' => false,
                'mensaje' => 'La contraseña debe tener al menos 6 caracteres.'
            ];
        }

        // Verificar si el usuario ya existe
        $usuarioExistente = $this->usuarioModel->buscarPorNombre(strtolower($datos['nombre']));
        if($usuarioExistente) {
            return [
                'exito' => false,
                'mensaje' => 'El nombre de usuario ya está registrado. Elija otro nombre.'
            ];
        }

        // Verificar si el email ya existe
        $emailExistente = $this->usuarioModel->buscarPorEmail(strtolower($datos['email']));
        if($emailExistente) {
            return [
                'exito' => false,
                'mensaje' => 'Este email ya está registrado. Solo se permite una cuenta por email.'
            ];
        }

        // Asignar valores al modelo
        $this->usuarioModel->nombre = $datos['nombre'];
        $this->usuarioModel->apellido_paterno = $datos['apellido_paterno'];
        $this->usuarioModel->apellido_materno = $datos['apellido_materno'];
        $this->usuarioModel->email = $datos['email'];
        $this->usuarioModel->password = $datos['password'];

        // Crear usuario
        if($this->usuarioModel->crear()) {
            // ✅ TRACKEAR REGISTRO DE USUARIO EXITOSO
            TelemetryHelper::trackUserRegistration(
                $this->usuarioModel->id,
                $datos['nombre'],
                $datos['email']
            );
            
            // Iniciar sesión para el usuario
            if(session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION['usuario'] = [
                'id' => $this->usuarioModel->id,
                'nombre' => $datos['nombre'],
                'apellido_paterno' => $datos['apellido_paterno'],
                'email' => $datos['email']
            ];

            return [
                'exito' => true,
                'mensaje' => 'Registro exitoso. Redirigiendo a promoción de planes...'
            ];
        } else {
            // ✅ TRACKEAR ERROR DE REGISTRO
            TelemetryHelper::trackError(
                'Error al crear usuario en base de datos',
                [
                    'error_type' => 'registration_failed',
                    'email' => $datos['email'],
                    'nombre' => $datos['nombre']
                ]
            );
            
            return [
                'exito' => false,
                'mensaje' => 'No se pudo completar el registro. Por favor intente nuevamente.'
            ];
        }
    }
}