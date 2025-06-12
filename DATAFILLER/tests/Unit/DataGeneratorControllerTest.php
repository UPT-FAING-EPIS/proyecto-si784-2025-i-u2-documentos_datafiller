<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\DataGeneratorController;

// Puedes dejar SOLO el autoload para DatabaseStub si lo necesitas
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
        $_SERVER['REQUEST_METHOD'] = 'CLI'; // Previene ejecución automática del controlador
        $_POST['formato_salida'] = 'sql';
    }

    public function testGenerarDatosBasico(): void
    {
        // Configuración simple: 1 tabla, 1 columna
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

        // Crear tabla auditoría en el PDO stub antes de llamar a generarDatos
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