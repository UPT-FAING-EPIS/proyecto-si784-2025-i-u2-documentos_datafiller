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
    private function invoke(string $method, array $args = [])
    {
        $m = $this->ref->getMethod($method);
        $m->setAccessible(true);
        return $m->invokeArgs($this->controller, $args);
    }

    public function testReturnsNullWhenTypeIsNull(): void
    {
        $field = new \stdClass();
        $field->name = 'col1';
        $field->type = null;

        $result = $this->invoke('procesarColumnaParser', [$field]);
        $this->assertNull($result);
    }

    public function testParsesBasicTypeWithParameters(): void
    {
        $type = new \stdClass();
        $type->name = 'int';
        $type->parameters = ['10', '2'];

        $field = new \stdClass();
        $field->name = 'amount';
        $field->type = $type;
        // no key, no options

        $out = $this->invoke('procesarColumnaParser', [$field]);
        $this->assertIsArray($out);
        $this->assertSame('AMOUNT', strtoupper($out['nombre'])); // same name, case-insensitive
        $this->assertSame('INT', $out['tipo_sql']);
        $this->assertSame(10, $out['longitud']);
        $this->assertSame(2, $out['decimales']);
        $this->assertEmpty($out['enum_values']);
        $this->assertNull($out['default_value']);
        $this->assertFalse($out['es_primary_key']);
        $this->assertFalse($out['es_auto_increment']);
        $this->assertFalse($out['es_not_null']);
    }

    public function testParsesEnumWithValues(): void
    {
        $type = new \stdClass();
        $type->name = 'ENUM';
        $type->parameters = ["'a'", "'b'"];

        $field = new \stdClass();
        $field->name = 'status';
        $field->type = $type;

        $out = $this->invoke('procesarColumnaParser', [$field]);
        $this->assertSame('ENUM', $out['tipo_sql']);
        $this->assertSame(['a','b'], $out['enum_values']);
        $this->assertSame('enum_values', $out['tipo_generacion']);
    }

    public function testParsesEnumWithNoValuesUsesDefaults(): void
    {
        $type = new \stdClass();
        $type->name = 'ENUM';
        $type->parameters = []; // no values

        $field = new \stdClass();
        $field->name = 'choice';
        $field->type = $type;

        $out = $this->invoke('procesarColumnaParser', [$field]);
        $this->assertSame(['default1','default2'], $out['enum_values']);
    }

    public function testDetectsPrimaryKey(): void
    {
        $type = new \stdClass();
        $type->name = 'int';
        $type->parameters = [];

        $field = new \stdClass();
        $field->name = 'id';
        $field->type = $type;
        $field->key = (object)['type' => 'PRIMARY']; // also catches PRIMARY KEY

        $out = $this->invoke('procesarColumnaParser', [$field]);
        $this->assertTrue($out['es_primary_key']);
    }

    public function testProcessesOptionsForAutoIncrementNotNullAndDefault(): void
    {
        $type = new \stdClass();
        $type->name = 'int';
        $type->parameters = [];

        $opts = new \stdClass();
        $opts->options = [
            'AUTO_INCREMENT' => null,
            0                => 'NOT NULL',
            'DEFAULT'        => "'Z'",
        ];

        $field = new \stdClass();
        $field->name = 'code';
        $field->type = $type;
        $field->options = $opts;

        $out = $this->invoke('procesarColumnaParser', [$field]);
        $this->assertTrue($out['es_auto_increment'], 'AUTO_INCREMENT should be detected');
        $this->assertTrue($out['es_not_null'], 'NOT NULL should be detected');
        $this->assertSame('Z', $out['default_value'], 'DEFAULT value should be stripped of quotes');
    }
    
}