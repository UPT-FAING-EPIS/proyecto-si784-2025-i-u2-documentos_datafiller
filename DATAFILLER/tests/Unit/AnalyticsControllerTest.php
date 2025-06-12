<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\AnalyticsController;
use PDO;

// Intercepta App\Config\Database para usar el stub solo en este test
spl_autoload_register(function (string $class) {
    if ($class === 'App\Config\Database') {
        require __DIR__ . '/Stubs/DatabaseNowStub.php';
        return true;
    }
    return false;
}, true, true);

final class AnalyticsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION = [];
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }

    public function testRegistrarDescargaUsuarioNoAutenticado(): void
    {
        $controller = new AnalyticsController();
        $result = $controller->registrarDescarga('csv', 10);

        $this->assertFalse($result['success']);
        $this->assertEquals('Usuario no autenticado', $result['message']);
    }

    public function testRegistrarDescargaUsuarioAutenticado(): void
    {
        $_SESSION['usuario'] = ['id' => 99];

        $controller = new AnalyticsController();

        // Crea la tabla simulada en memoria
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

        $result = $controller->registrarDescarga('xlsx', 5);

        $this->assertTrue($result['success']);
        $this->assertEquals('Descarga registrada', $result['message']);

        // Verifica que el registro fue insertado correctamente
        $stmt = $pdo->query("SELECT * FROM tbauditoria_consultas");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotFalse($row);
        $this->assertEquals(99, $row['usuario_id']);
        $this->assertEquals('descarga_datos', $row['tipo_consulta']);
        $this->assertEquals(5, $row['cantidad_registros']);
        $this->assertEquals('xlsx', $row['formato_exportacion']);
        $this->assertEquals('127.0.0.1', $row['ip_usuario']);
    }

    /**
     * Helper para obtener el PDO real desde el controlador
     */
    private function getPrivatePdo($controller): PDO
    {
        $ref = new \ReflectionClass($controller);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        return $prop->getValue($controller);
    }
}