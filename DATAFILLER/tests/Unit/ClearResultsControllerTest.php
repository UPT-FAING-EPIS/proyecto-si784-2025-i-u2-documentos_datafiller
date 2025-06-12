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
        ob_start(); // <-- previene errores de headers

        $_SESSION['datos_generados'] = ['algo'];
        $_SESSION['estadisticas_generacion'] = ['otro'];
        $_SESSION['estructura_analizada'] = ['mas'];
        $_SESSION['db_type'] = 'sql';

        $controller = new ClearResultsController();
        $result = $controller->clearResults();

        ob_end_clean(); // <-- limpia el buffer

        $this->assertArrayNotHasKey('datos_generados', $_SESSION);
        $this->assertArrayNotHasKey('estadisticas_generacion', $_SESSION);
        $this->assertArrayNotHasKey('estructura_analizada', $_SESSION);
        $this->assertArrayNotHasKey('db_type', $_SESSION);

        $this->assertTrue($result['success']);
        $this->assertEquals('Resultados limpiados exitosamente', $result['message']);
    }
}