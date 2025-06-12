<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\RegistroController;
use App\Models\Usuario;

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
}