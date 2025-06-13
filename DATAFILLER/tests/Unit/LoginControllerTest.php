<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\LoginController;
use App\Models\Usuario;

class LoginControllerTest extends TestCase
{
    private $mockUsuario;
    private $loginController;

    protected function setUp(): void
    {
        // Mock del modelo Usuario
        $this->mockUsuario = $this->createMock(Usuario::class);

        // El constructor de LoginController requiere el modelo Usuario vía $db,
        // así que hacemos un stub para Usuario y lo forzamos en la propiedad privada.
        $this->loginController = $this->getMockBuilder(LoginController::class)
            ->setConstructorArgs(['fake_db']) // el valor real no importa, lo sobrescribimos abajo
            ->onlyMethods([])
            ->getMock();

        // Reemplazar la propiedad privada usuarioModel por nuestro mock
        $reflection = new \ReflectionClass($this->loginController);
        $prop = $reflection->getProperty('usuarioModel');
        $prop->setAccessible(true);
        $prop->setValue($this->loginController, $this->mockUsuario);

        // Limpiar sesiones
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
        } else {
            @session_start();
        }
    }

    public function testCamposVaciosRetornaError()
    {
        $resultado = $this->loginController->procesarLogin(['nombre' => '', 'password' => '']);
        $this->assertFalse($resultado['exito']);
        $this->assertEquals('Por favor complete todos los campos.', $resultado['mensaje']);

        $resultado2 = $this->loginController->procesarLogin(['nombre' => 'admin']);
        $this->assertFalse($resultado2['exito']);
    }

    public function testLoginExitoso()
    {
        $datosUsuario = [
            'id' => 1,
            'nombre' => 'admin',
            'apellido_paterno' => 'García',
            'email' => 'admin@ejemplo.com'
        ];

        $this->mockUsuario->method('validarLogin')->willReturn([
            'exito' => true,
            'usuario' => $datosUsuario
        ]);

        $resultado = $this->loginController->procesarLogin([
            'nombre' => 'Admin ',
            'password' => '1234'
        ]);

        $this->assertTrue($resultado['exito']);
        $this->assertEquals('Inicio de sesión exitoso.', $resultado['mensaje']);
        $this->assertEquals($datosUsuario, $resultado['usuario']);

        $this->assertArrayHasKey('usuario', $_SESSION);
        $this->assertEquals($datosUsuario['id'], $_SESSION['usuario']['id']);
        $this->assertEquals($datosUsuario['nombre'], $_SESSION['usuario']['nombre']);
    }

    public function testLoginFallido()
    {
        $this->mockUsuario->method('validarLogin')->willReturn([
            'exito' => false
        ]);

        $resultado = $this->loginController->procesarLogin([
            'nombre' => 'admin',
            'password' => 'malapass'
        ]);
        $this->assertFalse($resultado['exito']);
        $this->assertEquals('Nombre de usuario o contraseña incorrectos.', $resultado['mensaje']);
        $this->assertArrayNotHasKey('usuario', $_SESSION);
    }
}