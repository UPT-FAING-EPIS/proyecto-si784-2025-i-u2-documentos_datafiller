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

    public function testGenerarTelefonoDevuelvePrefijoValidoYLongitudCorrecta()
{
    // Preparamos el mock de Faker para randomElement y numerify
    $fakerMock = $this->getMockBuilder(\Faker\Generator::class)
        ->addMethods(['randomElement', 'numerify'])
        ->getMock();

    // Configuramos el mock para devolver un prefijo específico y una secuencia de números
    $fakerMock->expects($this->once())
        ->method('randomElement')
        ->with(['987', '986', '985', '984', '983', '982', '981', '980'])
        ->willReturn('981');
    $fakerMock->expects($this->once())
        ->method('numerify')
        ->with('######')
        ->willReturn('112233');

    // Instancia del controlador testable con el faker mock inyectado
    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
    $controller->setFaker($fakerMock);

    $telefono = $controller->publicGenerarTelefono();

    $this->assertStringStartsWith('981', $telefono);
    $this->assertEquals(9, strlen($telefono));
    $this->assertEquals('981112233', $telefono);
}
    
public function testGenerarFechaDevuelveFormatoCorrecto()
{
    $fakerMock = $this->getMockBuilder(\Faker\Generator::class)
        ->addMethods(['date'])
        ->getMock();
    $fakerMock->expects($this->once())
        ->method('date')
        ->with('Y-m-d')
        ->willReturn('2025-06-19');

    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
    $controller->setFaker($fakerMock);

    $columna = ['nombre' => 'fecha_nacimiento', 'tipo_sql' => 'DATE'];
    $fecha = $controller->publicGenerarFecha($columna);

    $this->assertEquals('2025-06-19', $fecha);
}
public function testGenerarFechaHoraDevuelveFormatoCorrecto()
{
    $dateTimeMock = $this->getMockBuilder(\DateTime::class)
        ->onlyMethods(['format'])
        ->getMock();
    $dateTimeMock->expects($this->once())
        ->method('format')
        ->with('Y-m-d H:i:s')
        ->willReturn('2025-06-19 16:45:00');

    $fakerMock = $this->getMockBuilder(\Faker\Generator::class)
        ->addMethods(['dateTime'])
        ->getMock();
    $fakerMock->expects($this->once())
        ->method('dateTime')
        ->willReturn($dateTimeMock);

    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
    $controller->setFaker($fakerMock);

    $columna = ['nombre' => 'fecha_evento', 'tipo_sql' => 'DATETIME'];
    $fechaHora = $controller->publicGenerarFechaHora($columna);

    $this->assertEquals('2025-06-19 16:45:00', $fechaHora);
}
/**/
public function testGenerarNumeroEnteroPorDefecto()
{
    $fakerMock = $this->getMockBuilder(\Faker\Generator::class)
        ->onlyMethods(['numberBetween'])
        ->getMock();
    $fakerMock->expects($this->once())
        ->method('numberBetween')
        ->with(1, 999999)
        ->willReturn(12345);

    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
    $controller->setFaker($fakerMock);

    $columna = ['nombre' => 'cantidad', 'tipo_sql' => 'INT', 'longitud' => null];
    $numero = $controller->publicGenerarNumeroEntero($columna);

    $this->assertEquals(12345, $numero);
}

public function testGenerarNumeroEnteroConLongitud()
{
    $fakerMock = $this->getMockBuilder(\Faker\Generator::class)
        ->onlyMethods(['numberBetween'])
        ->getMock();
    // Si longitud = 3, max debería ser 999
    $fakerMock->expects($this->once())
        ->method('numberBetween')
        ->with(1, 999)
        ->willReturn(321);

    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
    $controller->setFaker($fakerMock);

    $columna = ['nombre' => 'codigo', 'tipo_sql' => 'INT', 'longitud' => 3];
    $numero = $controller->publicGenerarNumeroEntero($columna);

    $this->assertEquals(321, $numero);
}
/**/
public function testGenerarNumeroDecimalPorDefecto()
{
    $fakerMock = $this->getMockBuilder(\Faker\Generator::class)
        ->onlyMethods(['randomFloat'])
        ->getMock();
    $fakerMock->expects($this->once())
        ->method('randomFloat')
        ->with(2, 1, 9999)
        ->willReturn(55.33);

    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
    $controller->setFaker($fakerMock);

    $columna = ['nombre' => 'precio', 'tipo_sql' => 'DECIMAL'];
    $decimal = $controller->publicGenerarNumeroDecimal($columna);

    $this->assertEquals(55.33, $decimal);
}

public function testGenerarNumeroDecimalConLongitudYDecimales()
{
    $fakerMock = $this->getMockBuilder(\Faker\Generator::class)
        ->onlyMethods(['randomFloat'])
        ->getMock();
    // longitud=5, decimales=2 => max=(10^(5-2))-1 = 999
    $fakerMock->expects($this->once())
        ->method('randomFloat')
        ->with(2, 1, 999)
        ->willReturn(123.45);

    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
    $controller->setFaker($fakerMock);

    $columna = ['nombre' => 'precio', 'tipo_sql' => 'DECIMAL', 'longitud' => 5, 'decimales' => 2];
    $decimal = $controller->publicGenerarNumeroDecimal($columna);

    $this->assertEquals(123.45, $decimal);
}



public function testProcesarValorPersonalizadoReemplazaPatrones()
{
    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);

    $valor = 'abc_{i}_{random}_{date}';
    $resultado = $controller->publicProcesarValorPersonalizado($valor, 5);

    $this->assertStringContainsString('abc_5_', $resultado);
    $this->assertStringContainsString(date('Y-m-d'), $resultado);
    // No se puede predecir el random, pero debe tener longitud 4
    $partes = explode('_', $resultado);
    $this->assertEquals(4, strlen($partes[2]));
}
/** */
public function testConvertirAFormatoSQLReal()
{
    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);

    $resultado = [
        'datos_generados' => [
            'usuarios' => [
                ['id'=>1, 'nombre'=>'Ana', 'edad'=>30]
            ]
        ]
    ];
    $config = ['formato_salida'=>'sql'];

    $sql = $controller->publicConvertirAFormato($resultado, $config);
    $this->assertStringContainsString("INSERT INTO `usuarios`", $sql);
    $this->assertStringContainsString("'Ana'", $sql);
}

public function testConvertirAFormatoJSONReal()
{
    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);

    $resultado = ['datos_generados' => ['usuarios' => [['id'=>1, 'nombre'=>'Ana']]]];
    $config = ['formato_salida'=>'json'];

    $json = $controller->publicConvertirAFormato($resultado, $config);
    $this->assertJson($json);
    $this->assertStringContainsString('Ana', $json);
}

public function testConvertirAFormatoCSVReal()
{
    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);

    // Suponiendo que tu método convertirACSV lo hace bien
    // Simulamos una tabla simple
    $resultado = [
        'datos_generados' => [
            'clientes' => [
                ['id'=>1, 'nombre'=>'Juan'],
                ['id'=>2, 'nombre'=>'Ana']
            ]
        ]
    ];
    $config = ['formato_salida'=>'csv'];

    $csv = $controller->publicConvertirAFormato($resultado, $config);
    $this->assertStringContainsString("id,nombre", $csv);
    $this->assertStringContainsString("Juan", $csv);
    $this->assertStringContainsString("Ana", $csv);
}

public function testConvertirAFormatoXMLReal()
{
    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);

    $resultado = [
        'datos_generados' => [
            'clientes' => [
                ['id'=>1, 'nombre'=>'Juan']
            ]
        ]
    ];
    $config = ['formato_salida'=>'xml'];

    $xml = $controller->publicConvertirAFormato($resultado, $config);
    $this->assertStringContainsString('<?xml', $xml);
    $this->assertStringContainsString('<table name="clientes">', $xml);
    $this->assertStringContainsString('<nombre>Juan</nombre>', $xml);
}

public function testConvertirAFormatoDefaultUsaSQLReal()
{
    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);

    $resultado = [
        'datos_generados' => [
            'x' => [
                ['id' => 1]
            ]
        ]
    ];
    $config = ['formato_salida'=>'otro'];

    $sql = $controller->publicConvertirAFormato($resultado, $config);
    $this->assertStringContainsString("INSERT INTO `x`", $sql);
}
/** */
public function testRegistrarGeneracionInsertaCorrectamente()
{
    $usuario_id = 7;
    $estadisticas = [
        'total_registros' => 42
    ];
    $_POST['formato_salida'] = 'csv';
    $_SERVER['REMOTE_ADDR'] = '123.123.123.123';

    // Usamos un contador para validar el orden y los valores de bindParam
    $paramsEsperados = [
        [':usuario_id', $usuario_id],
        [':cantidad', $estadisticas['total_registros']],
        [':formato', $_POST['formato_salida']],
        [':ip', $_SERVER['REMOTE_ADDR']],
    ];
    $bindCall = 0;

    $statementMock = $this->getMockBuilder(\PDOStatement::class)
        ->disableOriginalConstructor()
        ->onlyMethods(['bindParam', 'execute'])
        ->getMock();

    $statementMock->expects($this->exactly(4))
        ->method('bindParam')
        ->with($this->callback(function($param) use (&$bindCall, $paramsEsperados) {
            // Validar solo el primer argumento de cada llamada
            return $param === $paramsEsperados[$bindCall][0];
        }), $this->callback(function($value) use (&$bindCall, $paramsEsperados) {
            // Validar solo el segundo argumento de cada llamada
            $ok = $value === $paramsEsperados[$bindCall][1];
            $bindCall++;
            return $ok;
        }));

    $statementMock->expects($this->once())
        ->method('execute');

    $pdoMock = $this->getMockBuilder(\PDO::class)
        ->disableOriginalConstructor()
        ->onlyMethods(['prepare'])
        ->getMock();

    $pdoMock->expects($this->once())
        ->method('prepare')
        ->with($this->stringContains('INSERT INTO tbauditoria_consultas'))
        ->willReturn($statementMock);

    $controller = $this->getMockBuilder(DataGeneratorControllerTestable::class)
        ->setConstructorArgs(['es_ES', $this->usuarioMock])
        ->onlyMethods([])
        ->getMock();

    /// Inyectar el mock de PDO en la propiedad protegida $db de la clase padre
    $ref = new \ReflectionClass(\App\Controllers\DataGeneratorController::class);
    $prop = $ref->getProperty('db');
    $prop->setAccessible(true);
    $prop->setValue($controller, $pdoMock);

    $controller->publicRegistrarGeneracion($usuario_id, $estadisticas);
}
/** */


public function testVerificarLimitesUsuarioGratisDentroLimite()
{
    $usuarioModelMock = $this->getMockBuilder(\App\Models\Usuario::class)
        ->disableOriginalConstructor()
        ->onlyMethods(['obtenerInfoUsuario'])
        ->getMock();
    $usuarioModelMock->method('obtenerInfoUsuario')
        ->with(2)
        ->willReturn(['id' => 2, 'plan' => 'gratis']);

    $controller = $this->getMockBuilder(\App\Controllers\DataGeneratorController::class)
        ->setConstructorArgs(['es_ES', $usuarioModelMock])
        ->onlyMethods([])
        ->getMock();

    $config = [
        'tablas' => [
            ['cantidad' => 1500],
            ['cantidad' => 200],
        ]
    ];

    $ref = new \ReflectionClass($controller);
    $method = $ref->getMethod('verificarLimitesUsuario');
    $method->setAccessible(true);
    $result = $method->invoke($controller, 2, $config);

    $this->assertTrue($result); // Total: 1700, está dentro del límite
}

public function testVerificarLimitesUsuarioPremium()
{
    $usuarioModelMock = $this->getMockBuilder(\App\Models\Usuario::class)
        ->disableOriginalConstructor()
        ->onlyMethods(['obtenerInfoUsuario'])
        ->getMock();
    $usuarioModelMock->method('obtenerInfoUsuario')
        ->willReturn(['id' => 1, 'plan' => 'premium']);

    // Instancia el controlador real (no un mock)
    $controller = new \App\Controllers\DataGeneratorController('es_ES');

    // Usa reflection para inyectar el mock en la propiedad privada
    $ref = new \ReflectionClass($controller);
    $prop = $ref->getProperty('usuarioModel');
    $prop->setAccessible(true);
    $prop->setValue($controller, $usuarioModelMock);

    $config = [
        'tablas' => [
            ['cantidad' => 10000],
            ['cantidad' => 5000],
        ]
    ];

    $method = $ref->getMethod('verificarLimitesUsuario');
    $method->setAccessible(true);
    $result = $method->invoke($controller, 1, $config);

    $this->assertTrue($result); // Premium nunca tiene límite
}

/** */

public function testOrdenarTablasPorDependenciasSoloPadres()
{
    $tablas = [
        [
            'nombre' => 'usuarios',
            'columnas' => [
                ['nombre' => 'id', 'es_foreign_key' => false],
                ['nombre' => 'nombre']
            ]
        ],
        [
            'nombre' => 'productos',
            'columnas' => [
                ['nombre' => 'id'],
                ['nombre' => 'descripcion']
            ]
        ]
    ];

    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
    $ordenadas = $controller->publicOrdenarTablasPorDependencias($tablas);

    $this->assertEquals('usuarios', $ordenadas[0]['nombre']);
    $this->assertEquals('productos', $ordenadas[1]['nombre']);
}

public function testOrdenarTablasPorDependenciasPadreYHija()
{
    $tablas = [
        [
            'nombre' => 'usuarios',
            'columnas' => [
                ['nombre' => 'id', 'es_foreign_key' => false],
                ['nombre' => 'nombre']
            ]
        ],
        [
            'nombre' => 'pedidos',
            'columnas' => [
                ['nombre' => 'id'],
                ['nombre' => 'usuario_id', 'es_foreign_key' => true]
            ]
        ]
    ];

    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
    $ordenadas = $controller->publicOrdenarTablasPorDependencias($tablas);

    // Debe ir primero usuarios (padre), luego pedidos (hija)
    $this->assertEquals('usuarios', $ordenadas[0]['nombre']);
    $this->assertEquals('pedidos', $ordenadas[1]['nombre']);
}

public function testOrdenarTablasPorDependenciasTodasHijas()
{
    $tablas = [
        [
            'nombre' => 'detalle',
            'columnas' => [
                ['nombre' => 'id'],
                ['nombre' => 'pedido_id', 'es_foreign_key' => true]
            ]
        ],
        [
            'nombre' => 'pago',
            'columnas' => [
                ['nombre' => 'id'],
                ['nombre' => 'pedido_id', 'es_foreign_key' => true]
            ]
        ]
    ];

    $controller = new DataGeneratorControllerTestable('es_ES', $this->usuarioMock);
    $ordenadas = $controller->publicOrdenarTablasPorDependencias($tablas);

    // Como todas son hijas, el orden se mantiene igual
    $this->assertEquals('detalle', $ordenadas[0]['nombre']);
    $this->assertEquals('pago', $ordenadas[1]['nombre']);
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
        public function publicConvertirAFormato($resultado, $configuracion)
    {
        return $this->convertirAFormato($resultado, $configuracion);
    }
    public function publicConvertirASQL($resultado, $configuracion)
    {
        return $this->convertirASQL($resultado, $configuracion);
    }
    public function publicConvertirAJSON($resultado)
    {
        return json_encode($resultado); // o llama al método real si lo tienes
    }
    public function publicConvertirACSV($resultado)
    {
        return 'csv'; // solo para fines de mock
    }
    public function publicConvertirAXML($resultado)
    {
        return 'xml'; // solo para fines de mock
    }

    public function publicRegistrarGeneracion($usuario_id, $estadisticas)
    {
        return $this->registrarGeneracion($usuario_id, $estadisticas);
    }

    public function setUsuarioModel($usuarioModel)
    {
        $ref = new \ReflectionClass(DataGeneratorController::class);
        $prop = $ref->getProperty('usuarioModel');
        $prop->setAccessible(true);
        $prop->setValue($this, $usuarioModel);
    }
    // Puedes añadir métodos para exponer protegidos si lo necesitas
    public function callVerificarLimitesUsuario($usuario_id, $config)
    {
        return $this->verificarLimitesUsuario($usuario_id, $config);
    }
    public function publicOrdenarTablasPorDependencias($tablas)
    {
        return $this->ordenarTablasPorDependencias($tablas);
    }
}

