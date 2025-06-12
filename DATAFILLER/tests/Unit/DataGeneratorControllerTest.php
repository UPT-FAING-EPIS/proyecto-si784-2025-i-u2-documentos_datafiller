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
}