<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\DataGeneratorController;
use App\Models\Usuario;

final class DataGeneratorControllerTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION = [];
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['REQUEST_METHOD'] = 'CLI';
    }

    public function testGenerarDatosBasico(): void
    {
        $config = [
            'formato_salida' => 'sql',
            'idioma_datos' => 'es_ES',
            'incluir_schema' => false,
            'tablas' => [
                [
                    'nombre' => 'usuarios',
                    'cantidad' => 2,
                    'columnas' => [
                        [
                            'nombre' => 'nombre',
                            'tipo_sql' => 'VARCHAR',
                            'tipo_generacion' => 'nombre_persona',
                            'longitud' => 50
                        ]
                    ],
                    'tipos_generacion' => [0 => 'nombre_persona'],
                    'valores_personalizados' => [0 => null],
                    'script_original' => 'CREATE TABLE usuarios (nombre VARCHAR(50));'
                ]
            ]
        ];

        $_SESSION['usuario'] = ['id' => 1];
        
        // Mock de Usuario
        $usuarioMock = $this->getMockBuilder(Usuario::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();
        $usuarioMock->method('obtenerInfoUsuario')->willReturn([
            'id' => 1,
            'plan' => 'premium'
        ]);

        // Crea una instancia del controlador pasando el mock como dependencia
        $controller = new DataGeneratorController('es_ES', $usuarioMock);

        $result = $controller->generarDatos($config, 1);

        $this->assertTrue($result['exito']);
        $this->assertEquals('Datos generados exitosamente', $result['mensaje']);
    }
}