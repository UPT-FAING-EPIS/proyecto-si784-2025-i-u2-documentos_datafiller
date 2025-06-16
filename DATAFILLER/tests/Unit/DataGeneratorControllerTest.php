<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\DataGeneratorController;
use App\Models\Usuario;
use ReflectionClass;

final class DataGeneratorControllerTest extends TestCase
{
    protected $controller;
    protected $usuarioMock;
    protected $generador;
    private $helper;


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

        // Prepara un generador testable y un faker para los tests que lo necesiten
        $this->generador = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
        // Creamos el mock de Faker y stubeamos seed() para evitar el warning “Method name is not configured”
        $this->faker = $this->createMock(\Faker\Generator::class);
        $this->faker->method('seed')->willReturn(null);
        // Inyecta el faker mock en la instancia principal (para tests de generación de valores)
        $this->generador->setFaker($this->faker);

         // Prepara un helper separado para probar métodos protegidos
        $this->helper = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
        $this->helper->setFaker($this->faker);
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

        $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
        // Inyectamos el faker mock correctamente
        $controller->setFaker($this->faker);
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

    public function testValorPersonalizadoTienePrioridad()
    {
        $generador = $this->getMockBuilder(DataGeneratorControllerTestable::class)
            ->setConstructorArgs(['es_ES', $this->usuarioMock])
            ->onlyMethods(['procesarValorPersonalizado'])
            ->getMock();

        $contadores = [];
        $referencias = [];
        $columna = ['nombre' => 'test', 'tipo_sql' => 'VARCHAR'];

        $generador->expects($this->once())
            ->method('procesarValorPersonalizado')
            ->with('VALOR', 0)
            ->willReturn('RESULTADO');

        $res = $generador->publicGenerarValorColumna($columna, 'personalizado', 'VALOR', 0, $contadores, $referencias);
        $this->assertEquals('RESULTADO', $res);
    }


    public function testAutoIncrement()
    {
        $contadores = [];
        $referencias = [];
        $columna = ['nombre' => 'id', 'es_auto_increment' => true, 'tipo_sql' => 'INT'];
        $res1 = $this->generador->publicGenerarValorColumna($columna, '', '', 0, $contadores, $referencias);
        $res2 = $this->generador->publicGenerarValorColumna($columna, '', '', 0, $contadores, $referencias);
        $this->assertEquals(1, $res1);
        $this->assertEquals(2, $res2);
    }

    public function testPasswordGeneraHash()
    {
        $contadores = [];
        $referencias = [];
        $columna = ['nombre' => 'password', 'tipo_sql' => 'VARCHAR'];
        $res = $this->generador->publicGenerarValorColumna($columna, '', '', 0, $contadores, $referencias);
        $this->assertTrue(password_verify('123456', $res));
    }
public function testEnumGeneraValor()
    {
        $fakerMock = $this->getMockBuilder(\Faker\Generator::class)
            ->addMethods(['randomElement'])
            ->getMock();
        $fakerMock->expects($this->once())
            ->method('randomElement')
            ->with(['A', 'B'])
            ->willReturn('B');

        $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
        $controller->setFaker($fakerMock);
        $this->assertSame($fakerMock, $controller->getFaker());

        $columna = ['nombre' => 'estado', 'tipo_sql' => 'ENUM', 'enum_values' => ['A', 'B']];
        $contadores = [];
        $referencias = [];
        $res = $controller->publicGenerarValorColumna($columna, 'enum_values', '', 0, $contadores, $referencias);
        $this->assertEquals('B', $res);
    }

    public function testForeignKeyUsaReferencia()
    {
        $fakerMock = $this->getMockBuilder(\Faker\Generator::class)
            ->addMethods(['randomElement'])
            ->getMock();
        $fakerMock->expects($this->once())
            ->method('randomElement')
            ->with([10, 20, 30])
            ->willReturn(10);

        $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
        $controller->setFaker($fakerMock);
        $this->assertSame($fakerMock, $controller->getFaker());

        $columna = [
            'nombre' => 'otra_id',
            'es_foreign_key' => true,
            'references_table' => 'otra_tabla',
            'tipo_sql' => 'INT'
        ];
        $contadores = [];
        $referencias = ['otra_tabla' => [10, 20, 30]];
        $res = $controller->publicGenerarValorColumna($columna, 'foreign_key', '', 0, $contadores, $referencias);
        $this->assertEquals(10, $res);
    }

    public function testEmailGeneraFakerEmail()
    {
        $fakerMock = $this->getMockBuilder(\Faker\Generator::class)
            ->addMethods(['email'])
            ->getMock();
        $fakerMock->expects($this->once())
            ->method('email')
            ->willReturn('bbadillo@live.com');

        $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
        $controller->setFaker($fakerMock);
        $this->assertSame($fakerMock, $controller->getFaker());

        $columna = ['nombre' => 'email_usuario', 'tipo_sql' => 'VARCHAR'];
        $contadores = [];
        $referencias = [];
        $res = $controller->publicGenerarValorColumna($columna, '', '', 0, $contadores, $referencias);
        $this->assertEquals('bbadillo@live.com', $res);
    }

    

}

// Clase auxiliar para exponer y sobreescribir el faker privado del controlador
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

    // Inyectar el faker mock sobre la propiedad privada del padre
    public function setFaker($faker)
    {
        $ref = new ReflectionClass(\App\Controllers\DataGeneratorController::class);
        $prop = $ref->getProperty('faker');
        $prop->setAccessible(true);
        $prop->setValue($this, $faker);
    }

    // Obtener el faker activo (para verificar en tests)
    public function getFaker()
    {
        $ref = new ReflectionClass(\App\Controllers\DataGeneratorController::class);
        $prop = $ref->getProperty('faker');
        $prop->setAccessible(true);
        return $prop->getValue($this);
    }

    public function publicGenerarTelefono()
    {
        return $this->generarTelefono();
    }
    public function publicGenerarFecha($columna)
    {
        return $this->generarFecha($columna);
    }
    public function publicGenerarFechaHora($columna)
    {
        return $this->generarFechaHora($columna);
    }
    public function publicGenerarNumeroEntero($columna)
    {
        return $this->generarNumeroEntero($columna);
    }
    public function publicGenerarNumeroDecimal($columna)
    {
        return $this->generarNumeroDecimal($columna);
    }
    public function publicGenerarTextoAleatorio($columna)
    {
        return $this->generarTextoAleatorio($columna);
    }
    public function publicGenerarPorTipoSQL($columna)
    {
        return $this->generarPorTipoSQL($columna);
    }

    public function publicProcesarValorPersonalizado($valor, $indice)
    {
        return $this->procesarValorPersonalizado($valor, $indice);
    }
}

