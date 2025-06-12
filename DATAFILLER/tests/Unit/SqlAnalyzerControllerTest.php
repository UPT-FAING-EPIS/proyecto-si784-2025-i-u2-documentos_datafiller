<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\SqlAnalyzerController;
use App\Models\Usuario;
use ReflectionClass;
use PDO;
use PDOStatement;

// Intercept App\Config\Database para cargar el stub en lugar de la clase real
spl_autoload_register(function (string $class) {
    if ($class === 'App\Config\Database') {
        require __DIR__ . '/Stubs/DatabaseStub.php';
    }
}, /* prepend */ true, /* throw */ true);

final class SqlAnalyzerControllerTest extends TestCase
{
    private int $usuarioId = 42;
    private string $dbType = 'sql';
    private SqlAnalyzerController $controller;
    private ReflectionClass $ref; // <- Added ReflectionClass property


    protected function setUp(): void
    {
        // Evitar que el file-scope de tu controlador procese POST
        $_SERVER['REQUEST_METHOD'] = 'GET';
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = [];

        // Creamos una instancia limpia; al instanciar, el controlador
        // hará new Database() pero cargará nuestro stub SQLite in‐memory.
        $this->controller = new SqlAnalyzerController();
        $this->ref = new ReflectionClass(SqlAnalyzerController::class); // <- Initialize ReflectionClass

    }

    /** 
     * Inyecta por reflexión el mock de Usuario y el mock de PDO
     */
    private function injectDependencies(Usuario $usuarioMock, PDO $dbMock): void
    {
        $ref = new ReflectionClass($this->controller);

        $p1 = $ref->getProperty('usuarioModel');
        $p1->setAccessible(true);
        $p1->setValue($this->controller, $usuarioMock);

        $p2 = $ref->getProperty('db');
        $p2->setAccessible(true);
        $p2->setValue($this->controller, $dbMock);
    }

    private function invoke(string $methodName, array $args = [])
    {
        $method = $this->ref->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->controller, $args);
    }

    public function testLimiteConsultasAgotadoDevuelveError(): void
    {
        // Usuario que ya no tiene consultas restantes
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('obtenerConsultasRestantes')
                ->with($this->usuarioId)
                ->willReturn(0);

        // PDO stub (no importa qué haga porque no llegamos a registrarConsulta)
        $db = $this->createMock(PDO::class);

        $this->injectDependencies($usuario, $db);

        $result = $this->controller->analizarEstructura('cualquier', $this->dbType, $this->usuarioId);

        $this->assertFalse($result['exito']);
        $this->assertSame('limite_consultas', $result['tipo']);
        $this->assertStringContainsString('agotado tus consultas', $result['mensaje']);
    }

    public function testSinTablasEnScriptDevuelveSinTablas(): void
    {
        // Usuario con consultas OK
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('obtenerConsultasRestantes')
                ->willReturn(1);

        $db = $this->createMock(PDO::class);

        $this->injectDependencies($usuario, $db);

        // Script sin CREATE TABLE
        $result = $this->controller->analizarEstructura('NO HAY TABLAS', $this->dbType, $this->usuarioId);

        $this->assertFalse($result['exito']);
        $this->assertSame('sin_tablas', $result['tipo']);
        $this->assertStringContainsString('No se encontraron declaraciones CREATE TABLE', $result['mensaje']);
    }

    public function testEstructuraValidaDevuelveTablas(): void
    {
        // Usuario con consultas OK y spy en incrementarConsultas()
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('obtenerConsultasRestantes')->willReturn(1);
        $usuario->expects($this->once())
                ->method('incrementarConsultas')
                ->with($this->usuarioId);

        // Stub de PDO / PDOStatement para que registrarConsulta no falle
        $db = $this->createMock(PDO::class);
        $stmt = $this->createMock(PDOStatement::class);
        $db->method('prepare')->willReturn($stmt);
        $stmt->method('bindParam')->willReturn(true);
        $stmt->method('execute')->willReturn(true);

        $this->injectDependencies($usuario, $db);

        // Script que sí contiene CREATE TABLE => fallback lo detecta
        $script = 'CREATE TABLE users (id INT);';

        $result = $this->controller->analizarEstructura($script, $this->dbType, $this->usuarioId);

        $this->assertTrue($result['exito']);
        $this->assertSame(1, $result['total_tablas']);
        $this->assertCount(1, $result['tablas']);
        $this->assertSame('users', $result['tablas'][0]['nombre']);
    }
    

    public function testProcesarColumnaParserReturnsNullIfNoType(): void
    {
        $field = new \stdClass();
        $field->name = 'col';
        $field->type = null;

        $result = $this->invoke('procesarColumnaParser', [$field]);
        $this->assertNull($result);
    }
    
    public function testProcesarColumnaParserEnumSinValores() {
    $field = new \stdClass();
    $field->name = 'col_enum';
    $field->type = (object)[
        'name' => 'ENUM',
        'parameters' => [],
    ];
    $field->options = (object)['options' => []];
    $result = $this->invoke('procesarColumnaParser', [$field]);
    $this->assertIsArray($result);
    $this->assertEquals('ENUM', $result['tipo_sql']);
    $this->assertEquals(['default1', 'default2'], $result['enum_values']);
    }

    public function testProcesarColumnaParserConOpcionesVariadas() {
        $field = new \stdClass();
        $field->name = 'col_test';
        $field->type = (object)[
            'name' => 'VARCHAR',
            'parameters' => [10],
        ];
        $field->key = (object)['type' => 'PRIMARY KEY'];
        // Simula opciones de not null y default
        $field->options = (object)['options' => [
            'not null' => 'NOT NULL',
            'auto_increment' => 'AUTO_INCREMENT',
            'default' => 'valor'
        ]];
        $result = $this->invoke('procesarColumnaParser', [$field]);
        $this->assertTrue($result['es_primary_key']);
        $this->assertTrue($result['es_auto_increment']);
        $this->assertTrue($result['es_not_null']);
        $this->assertEquals('valor', $result['default_value']);
    }

    public function testConstruirModificadores() {
        $result = $this->invoke('construirModificadores', [true, true, true, 'valor']);
        $this->assertStringContainsString('NOT NULL', $result);
        $this->assertStringContainsString('AUTO_INCREMENT', $result);
        $this->assertStringContainsString('PRIMARY KEY', $result);
        $this->assertStringContainsString("DEFAULT 'valor'", $result);
    }

    public function testLimpiarScript() {
        $script = "-- comentario\nCREATE TABLE test (id INT);\n/* otro */\nSET NAMES utf8;\nUSE mi_db;";
        $result = $this->invoke('limpiarScript', [$script]);
        $this->assertStringNotContainsString('--', $result);
        $this->assertStringNotContainsString('/*', $result);
        $this->assertStringNotContainsString('SET NAMES', $result);
        $this->assertStringNotContainsString('USE mi_db', $result);
        $this->assertStringContainsString('CREATE TABLE', $result);
    }

    public function testDeterminarTipoGeneracionPorNombre() {
        $this->assertEquals('numero_entero', $this->invoke('determinarTipoGeneracion', ['id', 'INT', '', [], false]));
        $this->assertEquals('nombre_persona', $this->invoke('determinarTipoGeneracion', ['nombre_completo', 'VARCHAR', '', [], false]));
        $this->assertEquals('email', $this->invoke('determinarTipoGeneracion', ['email_usuario', 'VARCHAR', '', [], false]));
        $this->assertEquals('telefono', $this->invoke('determinarTipoGeneracion', ['telefono_principal', 'VARCHAR', '', [], false]));
        $this->assertEquals('direccion', $this->invoke('determinarTipoGeneracion', ['direccion_envio', 'VARCHAR', '', [], false]));
        $this->assertEquals('fecha', $this->invoke('determinarTipoGeneracion', ['fecha_nacimiento', 'DATE', '', [], false]));
        $this->assertEquals('numero_decimal', $this->invoke('determinarTipoGeneracion', ['precio_unitario', 'DECIMAL', '', [], false]));
        $this->assertEquals('auto_increment', $this->invoke('determinarTipoGeneracion', ['id', 'INT', '', [], true]));
        $this->assertEquals('enum_values', $this->invoke('determinarTipoGeneracion', ['estado', 'ENUM', '', ['A', 'B'], false]));
        $this->assertEquals('texto_aleatorio', $this->invoke('determinarTipoGeneracion', ['campoX', 'VARCHAR', '', [], false]));
    }
}