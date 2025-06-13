<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\UsuarioController;
use App\Models\Usuario;

class UsuarioControllerTest extends TestCase
{
    private $usuarioModelMock;
    private $usuarioController;

    protected function setUp(): void
    {
        // Mock del modelo Usuario
        $this->usuarioModelMock = $this->createMock(Usuario::class);

        // Mock de la base de datos
        $dbMock = $this->createStub(stdClass::class);

        // Instancia real del controlador usando Reflection para inyectar el mock
        $this->usuarioController = $this->getMockBuilder(UsuarioController::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Inyectar el mock del modelo y la DB en el controlador real
        $refClass = new ReflectionClass(UsuarioController::class);
        $controller = $refClass->newInstanceWithoutConstructor();
        $refPropUsuario = $refClass->getProperty('usuarioModel');
        $refPropUsuario->setAccessible(true);
        $refPropUsuario->setValue($controller, $this->usuarioModelMock);

        $refPropDb = $refClass->getProperty('db');
        $refPropDb->setAccessible(true);
        $refPropDb->setValue($controller, $dbMock);

        $this->usuarioController = $controller;
    }

    public function testObtenerDatosParaHeaderSinUsuario()
    {
        $usuarioId = 1;

        // Configura el mock para que obtenerInfoCompleta devuelva false
        $this->usuarioModelMock->method('obtenerInfoCompleta')
            ->with($usuarioId)
            ->willReturn(false);

        $resultado = $this->usuarioController->obtenerDatosParaHeader($usuarioId);

        $this->assertEquals([
            'plan_usuario' => 'gratuito',
            'consultas_restantes' => 0
        ], $resultado);
    }

    public function testObtenerDatosParaHeaderConUsuario()
    {
        $usuarioId = 2;
        $infoUsuario = ['tipo_plan' => 'premium'];

        $this->usuarioModelMock->method('obtenerInfoCompleta')
            ->with($usuarioId)
            ->willReturn($infoUsuario);

        $this->usuarioModelMock->method('obtenerConsultasRestantes')
            ->with($usuarioId)
            ->willReturn(5);

        $resultado = $this->usuarioController->obtenerDatosParaHeader($usuarioId);

        $this->assertEquals([
            'plan_usuario' => 'premium',
            'consultas_restantes' => 5
        ], $resultado);
    }

    public function testObtenerInfoUsuario()
    {
        $usuarioId = 3;
        $infoUsuario = ['id' => 3, 'tipo_plan' => 'gratuito'];
        $this->usuarioModelMock->method('obtenerInfoCompleta')
            ->with($usuarioId)
            ->willReturn($infoUsuario);

        $resultado = $this->usuarioController->obtenerInfoUsuario($usuarioId);

        $this->assertEquals($infoUsuario, $resultado);
    }

    public function testObtenerConsultasRestantes()
    {
        $usuarioId = 4;
        $this->usuarioModelMock->method('obtenerConsultasRestantes')
            ->with($usuarioId)
            ->willReturn(7);

        $resultado = $this->usuarioController->obtenerConsultasRestantes($usuarioId);

        $this->assertEquals(7, $resultado);
    }
}