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

        $this->usuarioMock = $this->getMockBuilder(Usuario::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();

        $this->usuarioMock->method('obtenerInfoUsuario')->willReturn([
            'id' => 1,
            'plan' => 'premium'
        ]);

        $this->controller = new DataGeneratorController('es_ES', $this->usuarioMock);
    }

    public function testControllerSeCreaCorrectamente(): void
    {
        $this->assertInstanceOf(DataGeneratorController::class, $this->controller);
    }

    public function testGenerarDatosCuandoSeSuperaElLimite(): void
    {
        $controllerMock = $this->getMockBuilder(DataGeneratorController::class)
            ->setConstructorArgs(['es_ES', $this->usuarioMock])
            ->onlyMethods(['verificarLimitesUsuario'])
            ->getMock();

        $controllerMock->method('verificarLimitesUsuario')->willReturn(false);

        $configuracion = [
            'formato_salida' => 'json',
            'tablas' => []
        ];

        $respuesta = $controllerMock->generarDatos($configuracion, 1);

        $this->assertFalse($respuesta['exito']);
        $this->assertEquals('limite_superado', $respuesta['tipo']);
        $this->assertEquals(
            'Has superado los límites de tu plan. Actualiza a Premium para más registros.',
            $respuesta['mensaje']
        );
    }

    public function testGenerarDatosFallaPorLimiteExcedido(): void
    {
        $configuracion = [
            'formato_salida' => 'json',
            'tablas' => [
                [
                    'nombre' => 'usuarios',
                    'cantidad' => 1000000,
                    'columnas' => [],
                    'tipos_generacion' => [],
                    'valores_personalizados' => []
                ]
            ]
        ];

        $stub = $this->getMockBuilder(DataGeneratorController::class)
            ->setConstructorArgs(['es_ES', $this->usuarioMock])
            ->onlyMethods(['verificarLimitesUsuario'])
            ->getMock();

        $stub->method('verificarLimitesUsuario')->willReturn(false);

        $resultado = $stub->generarDatos($configuracion, 1);

        $this->assertFalse($resultado['exito']);
        $this->assertEquals('limite_superado', $resultado['tipo']);
    }

    public function testGenerarDatosLanzaError(): void
    {
        $configuracion = [
            'formato_salida' => 'json',
            'tablas' => []
        ];

        $stub = $this->getMockBuilder(DataGeneratorController::class)
            ->setConstructorArgs(['es_ES', $this->usuarioMock])
            ->onlyMethods(['verificarLimitesUsuario', 'registrarGeneracion'])
            ->getMock();

        $stub->method('verificarLimitesUsuario')->willReturn(true);
        $stub->method('registrarGeneracion')->will($this->throwException(new \Exception('Simulando error')));

        $resultado = $stub->generarDatos($configuracion, 1);

        $this->assertFalse($resultado['exito']);
        $this->assertEquals('error_interno', $resultado['tipo']);
        $this->assertEquals('Error interno generando los datos. Intente nuevamente.', $resultado['mensaje']);
    }

    public function testGenerarDatosExitoso(): void
    {
        $configuracion = [
            'formato_salida' => 'json',
            'tablas' => [
                [
                    'nombre' => 'clientes',
                    'cantidad' => 1,
                    'columnas' => [],
                    'tipos_generacion' => [],
                    'valores_personalizados' => []
                ]
            ]
        ];

        $stub = $this->getMockBuilder(DataGeneratorController::class)
            ->setConstructorArgs(['es_ES', $this->usuarioMock])
            ->onlyMethods([
                'verificarLimitesUsuario',
                'ordenarTablasPorDependencias',
                'generarDatosTabla',
                'convertirAFormato',
                'registrarGeneracion'
            ])
            ->getMock();

        $stub->method('verificarLimitesUsuario')->willReturn(true);
        $stub->method('ordenarTablasPorDependencias')->willReturn($configuracion['tablas']);
        $stub->method('generarDatosTabla')->willReturn([[ 'id' => 1, 'nombre' => 'Ejemplo' ]]);
        $stub->method('convertirAFormato')->willReturn('JSON_SIMULADO');

        $resultado = $stub->generarDatos($configuracion, 1);

        $this->assertTrue($resultado['exito']);
        $this->assertEquals('Datos generados exitosamente', $resultado['mensaje']);
        $this->assertEquals('JSON_SIMULADO', $resultado['contenido']);
        $this->assertEquals(1, $resultado['estadisticas']['total_tablas']);
        $this->assertEquals(1, $resultado['estadisticas']['total_registros']);
    }

    public function testSecuenciaDeLlamadasDuranteGeneracion(): void
    {
        $config = [
            'formato_salida' => 'json',
            'tablas' => []
        ];

        $mock = $this->getMockBuilder(DataGeneratorController::class)
            ->setConstructorArgs(['es_ES', $this->usuarioMock])
            ->onlyMethods([
                'verificarLimitesUsuario',
                'ordenarTablasPorDependencias',
                'generarDatosTabla',
                'convertirAFormato',
                'registrarGeneracion'
            ])
            ->getMock();

        $mock->expects($this->once())->method('verificarLimitesUsuario')->willReturn(true);
        $mock->expects($this->once())->method('ordenarTablasPorDependencias')->willReturn([]);
        $mock->expects($this->never())->method('generarDatosTabla');
        $mock->expects($this->once())->method('convertirAFormato')->willReturn('salida');
        $mock->expects($this->once())->method('registrarGeneracion');

        $resultado = $mock->generarDatos($config, 1);
        $this->assertTrue($resultado['exito']);
    }

    public function testGenerarDatosExitosamente(): void
    {
        $configuracion = [
            'formato_salida' => 'json',
            'tablas' => [
                [
                    'nombre' => 'usuarios',
                    'cantidad' => 2,
                    'columnas' => [],
                    'tipos_generacion' => [],
                    'valores_personalizados' => []
                ]
            ]
        ];

        $stub = $this->getMockBuilder(DataGeneratorController::class)
            ->setConstructorArgs(['es_ES', $this->usuarioMock])
            ->onlyMethods(['verificarLimitesUsuario', 'ordenarTablasPorDependencias', 'generarDatosTabla', 'convertirAFormato', 'registrarGeneracion'])
            ->getMock();

        $stub->method('verificarLimitesUsuario')->willReturn(true);
        $stub->method('ordenarTablasPorDependencias')->willReturn($configuracion['tablas']);
        $stub->method('generarDatosTabla')->willReturn([
            ['nombre' => 'Juan'], ['nombre' => 'Ana']
        ]);
        $stub->method('convertirAFormato')->willReturn('contenido_fake');
        $stub->method('registrarGeneracion')->willReturn(null);

        $resultado = $stub->generarDatos($configuracion, 1);

        $this->assertTrue($resultado['exito']);
        $this->assertEquals('Datos generados exitosamente', $resultado['mensaje']);
        $this->assertEquals('contenido_fake', $resultado['contenido']);
        $this->assertEquals(2, $resultado['estadisticas']['total_registros']);
        $this->assertEquals(1, $resultado['estadisticas']['total_tablas']);
    }

    public function testGenerarDatosTablaDevuelveCantidadCorrecta(): void
{
    $configuracionGlobal = ['formato_salida' => 'json'];

    $tablaConfig = [
        'nombre' => 'productos',
        'cantidad' => 5,
        'columnas' => [
            ['nombre' => 'id', 'tipo_sql' => 'INT', 'es_auto_increment' => true],
            ['nombre' => 'nombre', 'tipo_sql' => 'VARCHAR']
        ],
        'tipos_generacion' => ['auto_increment', 'nombre_persona'],
        'valores_personalizados' => []
    ];

    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
    $resultado = $controller->publicGenerarDatosTabla($tablaConfig, $configuracionGlobal);

    $this->assertCount(5, $resultado); // Deberían generarse 5 filas
    $this->assertArrayHasKey('id', $resultado[0]);
    $this->assertArrayHasKey('nombre', $resultado[0]);
}

public function testGenerarValorColumnaAutoIncrement(): void
{
    $columna = ['nombre' => 'id'];
    $tipoGeneracion = 'auto_increment';
    $valorPersonalizado = null;
    $i = 1;
    $contadores = [];
    $referencias = [];

    $controller = new DataGeneratorControllerTestable();
    $valor = $controller->publicGenerarValorColumna(
        $columna,
        $tipoGeneracion,
        $valorPersonalizado,
        $i,
        $contadores,
        $referencias
    );

    $this->assertEquals(1, $valor);
}


}

// Clase auxiliar para exponer métodos protegidos del controlador
class DataGeneratorControllerTestable extends \App\Controllers\DataGeneratorController
{
    public function __construct($locale = 'es_ES', $usuario = null)
    {
        parent::__construct($locale, $usuario);
    }

    // Permitir acceso público a generarDatosTabla
    public function publicGenerarDatosTabla($tabla_config, $configuracion_global)
    {
        return $this->generarDatosTabla($tabla_config, $configuracion_global);
    }

    // Permitir acceso público a generarValorColumna
    public function publicGenerarValorColumna($columna, $tipo_generacion, $valor_personalizado, $i, &$contadores, $referencias)
    {
        return $this->generarValorColumna($columna, $tipo_generacion, $valor_personalizado, $i, $contadores, $referencias);
    }
}

