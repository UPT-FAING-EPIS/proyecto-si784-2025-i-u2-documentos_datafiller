<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\SqlAnalyzerController;
use App\Models\Usuario;
use ReflectionClass;
use PDO;
use PDOStatement;

final class SqlAnalyzerControllerTest extends TestCase
{
    private int $usuarioId = 42;
    private string $dbType = 'sql';
    private SqlAnalyzerController $controller;

    protected function setUp(): void
    {
        // Evitar bloque POST de tu controller
        $_SERVER['REQUEST_METHOD'] = 'GET';
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = [];

        // Creamos instancia SIN invocar __construct (no carga Database)
        $this->controller = $this->getMockBuilder(SqlAnalyzerController::class)
                                 ->disableOriginalConstructor()
                                 ->getMock();
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
        // Usuario que ya no tiene consultas
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('obtenerConsultasRestantes')
                ->with($this->usuarioId)
                ->willReturn(0);

        $db = $this->createMock(PDO::class);

        $this->injectDependencies($usuario, $db);

        $result = $this->controller->analizarEstructura('cualquier', $this->dbType, $this->usuarioId);

        $this->assertFalse($result['exito']);
        $this->assertSame('limite_consultas', $result['tipo']);
        $this->assertStringContainsString('agotado tus consultas', $result['mensaje']);
    }

    public function testSinTablasEnScriptDevuelveSinTablas(): void
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('obtenerConsultasRestantes')->willReturn(1);

        $db = $this->createMock(PDO::class);

        $this->injectDependencies($usuario, $db);

        $result = $this->controller->analizarEstructura('NO HAY TABLAS', $this->dbType, $this->usuarioId);

        $this->assertFalse($result['exito']);
        $this->assertSame('sin_tablas', $result['tipo']);
        $this->assertStringContainsString('No se encontraron declaraciones CREATE TABLE', $result['mensaje']);
    }

    public function testEstructuraValidaDevuelveTablas(): void
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('obtenerConsultasRestantes')->willReturn(1);
        $usuario->expects($this->once())
                ->method('incrementarConsultas')
                ->with($this->usuarioId);

        $db = $this->createMock(PDO::class);
        $stmt = $this->createMock(PDOStatement::class);
        $db->method('prepare')->willReturn($stmt);
        $stmt->method('bindParam')->willReturn(true);
        $stmt->method('execute')->willReturn(true);

        $this->injectDependencies($usuario, $db);

        $script = 'CREATE TABLE users (id INT);';
        $result = $this->controller->analizarEstructura($script, $this->dbType, $this->usuarioId);

        $this->assertTrue($result['exito']);
        $this->assertSame(1, $result['total_tablas']);
        $this->assertCount(1, $result['tablas']);
        $this->assertSame('users', $result['tablas'][0]['nombre']);
    }
}