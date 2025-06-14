<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\LoginController;
use App\Models\Usuario;

class LoginControllerTest extends TestCase
{
    private $loginController;
    private $mockUsuario;

    protected function setUp(): void
{
    // Mock del modelo Usuario
    $this->mockUsuario = $this->createMock(\App\Models\Usuario::class);

    // Mock de PDO para evitar el error 'prepare() on string'
    $mockDb = $this->createMock(\PDO::class);

    // Crear instancia del controlador con los mocks
    $this->loginController = new \App\Controllers\LoginController($mockDb, $this->mockUsuario);

    // Limpiar sesión
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }
    $_SESSION = [];
}


    public function testCamposVaciosRetornaError(): void
    {
        $resultado = $this->loginController->procesarLogin(['nombre' => '', 'password' => '']);
        $this->assertFalse($resultado['exito']);
        $this->assertEquals('Por favor complete todos los campos.', $resultado['mensaje']);

        $resultado2 = $this->loginController->procesarLogin(['nombre' => 'admin']);
        $this->assertFalse($resultado2['exito']);
    }
        
    public function testLoginExitoso(): void
{
    $datosUsuario = [
        'id' => 1,
        'nombre' => 'admin',
        'apellido_paterno' => 'García',
        'email' => 'admin@ejemplo.com'
    ];

    // Creamos mocks
    $mockUsuario = $this->createMock(Usuario::class);
    $mockUsuario->method('validarLogin')->willReturn([
        'exito' => true,
        'usuario' => $datosUsuario
    ]);

    $mockDb = $this->createMock(\PDO::class);

    // Crear controlador real
    $controller = new LoginController($mockDb);

    // Usar reflexión para reemplazar usuarioModel
    $refObject = new \ReflectionObject($controller);
    $refProperty = $refObject->getProperty('usuarioModel');
    $refProperty->setAccessible(true);
    $refProperty->setValue($controller, $mockUsuario);

    $resultado = $controller->procesarLogin([
        'nombre' => 'Admin ',
        'password' => '1234'
    ]);

    $this->assertTrue($resultado['exito']);
    $this->assertEquals('Inicio de sesión exitoso.', $resultado['mensaje']);
    $this->assertEquals($datosUsuario, $resultado['usuario']);
    $this->assertArrayHasKey('usuario', $_SESSION);
    $this->assertEquals($datosUsuario['id'], $_SESSION['usuario']['id']);
}

public function testLoginFallido(): void
{
    $mockUsuario = $this->createMock(Usuario::class);
    $mockUsuario->method('validarLogin')->willReturn([
        'exito' => false
    ]);

    $mockDb = $this->createMock(\PDO::class);

    $controller = new LoginController($mockDb);

    // Usar reflexión para reemplazar usuarioModel
    $refObject = new \ReflectionObject($controller);
    $refProperty = $refObject->getProperty('usuarioModel');
    $refProperty->setAccessible(true);
    $refProperty->setValue($controller, $mockUsuario);

    $resultado = $controller->procesarLogin([
        'nombre' => 'admin',
        'password' => 'malapass'
    ]);

    $this->assertFalse($resultado['exito']);
    $this->assertEquals('Nombre de usuario o contraseña incorrectos.', $resultado['mensaje']);
    $this->assertArrayNotHasKey('usuario', $_SESSION);
}


}
