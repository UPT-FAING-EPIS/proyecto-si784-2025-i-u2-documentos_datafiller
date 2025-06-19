<?php


namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\RegistroController;
use App\Models\Usuario;
use \PDO; 

class RegistroControllerTest extends TestCase
{
    private $registroController;
    private $mockDb;

    protected function setUp(): void
    {
        $this->mockDb = $this->createMock(PDO::class);
        $this->registroController = new RegistroController($this->mockDb);
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

    // Agrega más pruebas para cada caso del método.
    public function testRegistrarContrasenaMuyCorta()
{
    $datos = [
        'nombre' => 'Juan',
        'apellido_paterno' => 'Pérez',
        'apellido_materno' => 'Gómez',
        'email' => 'juan@example.com',
        'password' => '123',
        'confirm_password' => '123'
    ];

    $resultado = $this->registroController->registrar($datos);

    $this->assertFalse($resultado['exito']);
    $this->assertEquals('La contraseña debe tener al menos 6 caracteres.', $resultado['mensaje']);
}
public function testRegistrarUsuarioYaExiste()
{
    $mockUsuario = $this->getMockBuilder(Usuario::class)
        ->disableOriginalConstructor()
        ->onlyMethods(['buscarPorNombre'])
        ->getMock();

    $mockUsuario->expects($this->once())
        ->method('buscarPorNombre')
        ->with('juan')
        ->willReturn(['id' => 1]);

    // Inyectamos el mock usando reflection
    $reflection = new \ReflectionClass($this->registroController);
    $prop = $reflection->getProperty('usuarioModel');
    $prop->setAccessible(true);
    $prop->setValue($this->registroController, $mockUsuario);

    $datos = [
        'nombre' => 'Juan',
        'apellido_paterno' => 'Pérez',
        'apellido_materno' => 'Gómez',
        'email' => 'juan@example.com',
        'password' => 'contraseña123',
        'confirm_password' => 'contraseña123'
    ];

    $resultado = $this->registroController->registrar($datos);

    $this->assertFalse($resultado['exito']);
    $this->assertEquals('El nombre de usuario ya está registrado. Elija otro nombre.', $resultado['mensaje']);
}
public function testRegistrarEmailYaExiste()
{
    $mockUsuario = $this->getMockBuilder(Usuario::class)
        ->disableOriginalConstructor()
        ->onlyMethods(['buscarPorNombre', 'buscarPorEmail'])
        ->getMock();

    $mockUsuario->method('buscarPorNombre')->willReturn(null);
    $mockUsuario->method('buscarPorEmail')->willReturn(['id' => 1]);

    $reflection = new \ReflectionClass($this->registroController);
    $prop = $reflection->getProperty('usuarioModel');
    $prop->setAccessible(true);
    $prop->setValue($this->registroController, $mockUsuario);

    $datos = [
        'nombre' => 'Juan',
        'apellido_paterno' => 'Pérez',
        'apellido_materno' => 'Gómez',
        'email' => 'juan@example.com',
        'password' => 'contraseña123',
        'confirm_password' => 'contraseña123'
    ];

    $resultado = $this->registroController->registrar($datos);

    $this->assertFalse($resultado['exito']);
    $this->assertEquals('Este email ya está registrado. Solo se permite una cuenta por email.', $resultado['mensaje']);
}
public function testRegistrarExitoso()
{
    $mockUsuario = $this->getMockBuilder(Usuario::class)
        ->disableOriginalConstructor()
        ->onlyMethods(['buscarPorNombre', 'buscarPorEmail', 'crear'])
        ->getMock();

    $mockUsuario->method('buscarPorNombre')->willReturn(null);
    $mockUsuario->method('buscarPorEmail')->willReturn(null);
    $mockUsuario->method('crear')->willReturn(true);

    // Simular que se asigna un ID después de crear
    $mockUsuario->id = 123;

    $reflection = new \ReflectionClass($this->registroController);
    $prop = $reflection->getProperty('usuarioModel');
    $prop->setAccessible(true);
    $prop->setValue($this->registroController, $mockUsuario);

    // Datos válidos
    $datos = [
        'nombre' => 'Juan',
        'apellido_paterno' => 'Pérez',
        'apellido_materno' => 'Gómez',
        'email' => 'juan@example.com',
        'password' => 'contraseña123',
        'confirm_password' => 'contraseña123'
    ];

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }

    $resultado = $this->registroController->registrar($datos);

    $this->assertTrue($resultado['exito']);
    $this->assertEquals('Registro exitoso. Redirigiendo a promoción de planes...', $resultado['mensaje']);
    $this->assertEquals(123, $_SESSION['usuario']['id']);
}

public function testRegistrarFallaAlCrearUsuario()
{
    $mockUsuario = $this->getMockBuilder(\App\Models\Usuario::class)
        ->disableOriginalConstructor()
        ->onlyMethods(['buscarPorNombre', 'buscarPorEmail', 'crear'])
        ->getMock();

    $mockUsuario->method('buscarPorNombre')->willReturn(null);
    $mockUsuario->method('buscarPorEmail')->willReturn(null);
    $mockUsuario->method('crear')->willReturn(false);

    // Inyectar el mock en el controller
    $reflection = new \ReflectionClass($this->registroController);
    $prop = $reflection->getProperty('usuarioModel');
    $prop->setAccessible(true);
    $prop->setValue($this->registroController, $mockUsuario);

    $datos = [
        'nombre' => 'Juan',
        'apellido_paterno' => 'Pérez',
        'apellido_materno' => 'Gómez',
        'email' => 'juan@example.com',
        'password' => 'contraseña123',
        'confirm_password' => 'contraseña123'
    ];

    $resultado = $this->registroController->registrar($datos);

    $this->assertFalse($resultado['exito']);
    $this->assertEquals('No se pudo completar el registro. Por favor intente nuevamente.', $resultado['mensaje']);
}

}