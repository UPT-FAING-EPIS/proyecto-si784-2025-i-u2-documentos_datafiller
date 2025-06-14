<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\DataGeneratorController;
use App\Models\Usuario;

final class DataGeneratorControllerTest extends TestCase
{
    protected $controller;
    protected $usuarioMock;

    protected function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION = [];
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['REQUEST_METHOD'] = 'CLI';

        // Mock de Usuario
        $this->usuarioMock = $this->getMockBuilder(Usuario::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();

        // Configurar el mock para devolver un array válido
        $this->usuarioMock->method('obtenerInfoUsuario')->willReturn([
            'id' => 1,
            'plan' => 'premium'
        ]);

        // Crear una instancia del controlador pasándole el mock
        $this->controller = new DataGeneratorController('es_ES', $this->usuarioMock);
    }

    public function testControllerSeCreaCorrectamente(): void
    {
        $this->assertInstanceOf(DataGeneratorController::class, $this->controller);
    }

    public function testGenerarDatosCuandoSeSuperaElLimite(): void
{
    // Creamos un mock parcial del controlador
    $controllerMock = $this->getMockBuilder(DataGeneratorController::class)
        ->setConstructorArgs(['es_ES', $this->usuarioMock])
        ->onlyMethods(['verificarLimitesUsuario'])
        ->getMock();

    // Simulamos que el usuario ha superado los límites
    $controllerMock->method('verificarLimitesUsuario')->willReturn(false);

    $configuracionFalsa = [
        'formato_salida' => 'json',
        'tablas' => []
    ];

    $respuesta = $controllerMock->generarDatos($configuracionFalsa, 1);

    $this->assertFalse($respuesta['exito']);
    $this->assertEquals('limite_superado', $respuesta['tipo']);
    $this->assertEquals('Has superado los límites de tu plan. Actualiza a Premium para más registros.', $respuesta['mensaje']);
}

}
