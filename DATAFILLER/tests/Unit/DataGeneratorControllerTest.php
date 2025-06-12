<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\DataGeneratorController;

// Stub temporal dentro de tests para Usuario
namespace App\Models;
class Usuario {
    public function __construct($db) {}
    public function obtenerInfoUsuario($usuario_id) {
        return ['id' => 1, 'plan' => 'premium'];
    }
}

namespace App\Tests\Unit; // Regresa al namespace correcto

spl_autoload_register(function ($class) {
    if ($class === 'App\Config\Database') {
        require __DIR__ . '/Stubs/DatabaseStub.php';
        return true;
    }
    return false;
}, true, true);

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
        $_POST['formato_salida'] = 'sql';
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
        $controller = new DataGeneratorController('es_ES');

        // Mock de Usuario SOLO para este test
        $usuarioMock = $this->getMockBuilder(\App\Models\Usuario::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();
        $usuarioMock->method('obtenerInfoUsuario')->willReturn([
            'id' => 1,
            'plan' => 'premium'
        ]);
        // Inyecta el mock en el controlador antes de llamar a generarDatos
        $refCtrl = new \ReflectionClass($controller);
        if ($refCtrl->hasProperty('usuario')) {
            $propUsuario = $refCtrl->getProperty('usuario');
            $propUsuario->setAccessible(true);
            $propUsuario->setValue($controller, $usuarioMock);
        } else {
            // Si tu controlador usa otro nombre para la propiedad del modelo Usuario, cámbialo aquí
            $this->fail('No se encontró property usuario en DataGeneratorController');
        }

        $pdo = $this->getPrivatePdo($controller);
        $pdo->exec("
            CREATE TABLE tbauditoria_consultas (
                usuario_id INTEGER,
                tipo_consulta TEXT,
                cantidad_registros INTEGER,
                formato_exportacion TEXT,
                fecha_consulta TEXT DEFAULT (CURRENT_TIMESTAMP),
                ip_usuario TEXT
            )
        ");

        $result = $controller->generarDatos($config, 1);

        $this->assertTrue($result['exito']);
        $this->assertEquals('Datos generados exitosamente', $result['mensaje']);
        $this->assertIsArray($result['estadisticas']);
        $this->assertArrayHasKey('contenido', $result);
        $this->assertStringContainsString('INSERT INTO `usuarios`', $result['contenido']);
    }

    private function getPrivatePdo($controller): \PDO
    {
        $ref = new \ReflectionClass($controller);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        return $prop->getValue($controller);
    }
}