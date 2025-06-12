<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\ClearResultsController;

final class ClearResultsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        // Asegura que la sesión siempre está activa y limpia antes de cada test
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION = [];
    }

    public function testClearResultsLimpiaVariablesDeSesionYRetornaSuccess(): void
    {
        // Prepara datos de sesión simulados
        $_SESSION['datos_generados'] = [1, 2, 3];
        $_SESSION['estadisticas_generacion'] = ['ok' => true];
        $_SESSION['estructura_analizada'] = ['table' => 'test'];
        $_SESSION['db_type'] = 'sqlite';
        $_SESSION['otro'] = 'debería quedar';

        $controller = new ClearResultsController();
        $result = $controller->clearResults();

        $this->assertTrue($result['success']);
        $this->assertEquals('Resultados limpiados exitosamente', $result['message']);

        // Las variables específicas deben haber sido eliminadas
        $this->assertArrayNotHasKey('datos_generados', $_SESSION);
        $this->assertArrayNotHasKey('estadisticas_generacion', $_SESSION);
        $this->assertArrayNotHasKey('estructura_analizada', $_SESSION);
        $this->assertArrayNotHasKey('db_type', $_SESSION);

        // Cualquier otra variable debe quedar intacta
        $this->assertArrayHasKey('otro', $_SESSION);
        $this->assertEquals('debería quedar', $_SESSION['otro']);
    }
}