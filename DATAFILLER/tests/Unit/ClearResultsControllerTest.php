<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\ClearResultsController;

final class ClearResultsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION = [];
    }

    public function testClearResultsLimpiaVariablesDeSesionYRetornaSuccess(): void
    {
        if (headers_sent()) {
            $this->markTestSkipped('No se puede probar sesiones porque los headers ya han sido enviados por PHPUnit.');
        }

        $_SESSION['datos_generados']         = ['dato'];
        $_SESSION['estadisticas_generacion'] = ['est'];
        $_SESSION['estructura_analizada']    = ['estr'];
        $_SESSION['db_type']                 = 'sql';

        // Capturamos y descartamos cualquier output inesperado
        ob_start();
        $controller = new ClearResultsController();
        $result     = $controller->clearResults();
        ob_end_clean();

        $this->assertArrayNotHasKey('datos_generados', $_SESSION);
        $this->assertArrayNotHasKey('estadisticas_generacion', $_SESSION);
        $this->assertArrayNotHasKey('estructura_analizada', $_SESSION);
        $this->assertArrayNotHasKey('db_type', $_SESSION);

        $this->assertTrue($result['success']);
        $this->assertEquals('Resultados limpiados exitosamente', $result['message']);
    }

    public function testClearResultsConSesionVaciaRetornaSuccessSinOutput(): void
    {
        if (headers_sent()) {
            $this->markTestSkipped('No se puede probar sesiones porque los headers ya han sido enviados por PHPUnit.');
        }

        // $_SESSION ya está vacío
        ob_start();
        $controller = new ClearResultsController();
        $result     = $controller->clearResults();
        $output     = ob_get_clean();

        // No debe imprimir nada
        $this->assertEmpty($output);

        $this->assertEmpty($_SESSION);
        $this->assertTrue($result['success']);
        $this->assertEquals('Resultados limpiados exitosamente', $result['message']);
    }

    public function testClearResultsSiempreDevuelveArrayConClavesEsperadas(): void
    {
        ob_start();
        $controller = new ClearResultsController();
        $result     = $controller->clearResults();
        ob_end_clean();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertTrue(is_bool($result['success']));
        $this->assertIsString($result['message']);
    }
}