<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\RegistroController;
use App\Models\Usuario;

class RegistroControllerTest extends TestCase
{
    private $registroController;
    private $mockDb;
    private $mockUsuarioModel;

    protected function setUp(): void
    {
        $this->mockDb = $this->createMock(PDO::class);
        $this->mockUsuarioModel = $this->createMock(Usuario::class);
        $this->registroController = new RegistroController($this->mockDb);

        // Inyecta el modelo de usuario simulado
        $this->registroController->setUsuarioModel($this->mockUsuarioModel);
    }

    public function testRegistrarDatosIncompletos()
    {
        $datos = [
            'nombre' => '',
            'apellido_paterno' => '',
            'apellido_materno' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => ''
        ];

        $resultado = $this->registroController->registrar($datos);

        $this->assertFalse($resultado['exito']);
        $this->assertEquals('Por favor complete todos los campos requeridos.', $resultado['mensaje']);
    }

    public function testRegistrarEmailInvalido()
    {
        $datos = [
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'email' => 'email_invalido',
            'password' => 'contraseña123',
            'confirm_password' => 'contraseña123'
        ];

        $resultado = $this->registroController->registrar($datos);

        $this->assertFalse($resultado['exito']);
        $this->assertEquals('Por favor ingrese un email válido.', $resultado['mensaje']);
    }

    public function testRegistrarContraseñasNoCoinciden()
    {
        $datos = [
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'email' => 'juan.perez@example.com',
            'password' => 'contraseña123',
            'confirm_password' => 'otraContraseña'
        ];

        $resultado = $this->registroController->registrar($datos);

        $this->assertFalse($resultado['exito']);
        $this->assertEquals('Las contraseñas no coinciden.', $resultado['mensaje']);
    }

    public function testPasswordTooShort()
    {
        $datos = [
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'email' => 'juan.perez@example.com',
            'password' => '12345',
            'confirm_password' => '12345'
        ];

        $resultado = $this->registroController->registrar($datos);

        $this->assertFalse($resultado['exito']);
        $this->assertEquals('La contraseña debe tener al menos 6 caracteres.', $resultado['mensaje']);
    }

    public function testUserAlreadyExists()
    {
        $datos = [
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'email' => 'juan.perez@example.com',
            'password' => 'contraseña123',
            'confirm_password' => 'contraseña123'
        ];

        $this->mockUsuarioModel
            ->method('buscarPorNombre')
            ->with(strtolower($datos['nombre']))
            ->willReturn(true);

        $resultado = $this->registroController->registrar($datos);

        $this->assertFalse($resultado['exito']);
        $this->assertEquals('El nombre de usuario ya está registrado. Elija otro nombre.', $resultado['mensaje']);
    }

    public function testEmailAlreadyExists()
    {
        $datos = [
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'email' => 'juan.perez@example.com',
            'password' => 'contraseña123',
            'confirm_password' => 'contraseña123'
        ];

        $this->mockUsuarioModel
            ->method('buscarPorEmail')
            ->with(strtolower($datos['email']))
            ->willReturn(true);

        $resultado = $this->registroController->registrar($datos);

        $this->assertFalse($resultado['exito']);
        $this->assertEquals('Este email ya está registrado. Solo se permite una cuenta por email.', $resultado['mensaje']);
    }

    public function testSuccessfulRegistration()
    {
        $datos = [
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'email' => 'juan.perez@example.com',
            'password' => 'contraseña123',
            'confirm_password' => 'contraseña123'
        ];

        $this->mockUsuarioModel
            ->method('buscarPorNombre')
            ->with(strtolower($datos['nombre']))
            ->willReturn(false);

        $this->mockUsuarioModel
            ->method('buscarPorEmail')
            ->with(strtolower($datos['email']))
            ->willReturn(false);

        $this->mockUsuarioModel
            ->method('crear')
            ->willReturn(true);

        $this->mockUsuarioModel->id = 1;

        $resultado = $this->registroController->registrar($datos);

        $this->assertTrue($resultado['exito']);
        $this->assertEquals('Registro exitoso. Redirigiendo a promoción de planes...', $resultado['mensaje']);
        $this->assertArrayHasKey('usuario', $_SESSION);
    }

    public function testRegistrationFailed()
    {
        $datos = [
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'email' => 'juan.perez@example.com',
            'password' => 'contraseña123',
            'confirm_password' => 'contraseña123'
        ];

        $this->mockUsuarioModel
            ->method('buscarPorNombre')
            ->with(strtolower($datos['nombre']))
            ->willReturn(false);

        $this->mockUsuarioModel
            ->method('buscarPorEmail')
            ->with(strtolower($datos['email']))
            ->willReturn(false);

        $this->mockUsuarioModel
            ->method('crear')
            ->willReturn(false);

        $resultado = $this->registroController->registrar($datos);

        $this->assertFalse($resultado['exito']);
        $this->assertEquals('No se pudo completar el registro. Por favor intente nuevamente.', $resultado['mensaje']);
    }
}