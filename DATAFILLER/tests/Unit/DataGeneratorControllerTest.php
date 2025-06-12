<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\DataGeneratorController;

// Stub mínimo del modelo Usuario para testear límites
class UsuarioStub {
    public function __construct($db) {}
    public function obtenerInfoUsuario($usuario_id) {
        // Siempre premium para evitar límite
        return ['id' => $usuario_id, 'plan' => 'premium'];
    }
}

// Autoloader para inyectar el stub de Usuario y el stub de Database (PDO in-memory)
spl_autoload_register(function ($class) {
    if ($class === 'App\Models\Usuario') {
        eval('namespace App\Models; class Usuario extends \App\Tests\Unit\UsuarioStub {}');
        return true;
    }
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
        $_SERVER['REQUEST_METHOD'] = 'CLI'; // Esto evita el error del controlador fuera de la clase
        $_POST['formato_salida'] = 'sql';
    }

    public function testGenerarDatosBasico(): void
    {
        // Configuración mínima: 1 tabla, 1 columna
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
        $result = $controller->generarDatos($config, 1);

        $this->assertTrue($result['exito']);
        $this->assertEquals('Datos generados exitosamente', $result['mensaje']);
        $this->assertIsArray($result['estadisticas']);
        $this->assertArrayHasKey('contenido', $result);
        $this->assertStringContainsString('INSERT INTO `usuarios`', $result['contenido']);
    }
}