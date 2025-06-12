<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controllers\ClearResultsController;

final class ClearResultsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = [];
    }

    public function testClearResultsLimpiaVariablesDeSesionYRetornaSuccess(): void
    {
        // Simula datos en la sesión
        $_SESSION['datos_generados'] = ['algo'];
        $_SESSION['estadisticas_generacion'] = ['otro'];
        $_SESSION['estructura_analizada'] = ['mas'];
        $_SESSION['db_type'] = 'sql';

        $controller = new ClearResultsController();
        $result = $controller->clearResults();

        // Verifica que las variables de sesión han sido eliminadas
        $this->assertArrayNotHasKey('datos_generados', $_SESSION);
        $this->assertArrayNotHasKey('estadisticas_generacion', $_SESSION);
        $this->assertArrayNotHasKey('estructura_analizada', $_SESSION);
        $this->assertArrayNotHasKey('db_type', $_SESSION);

        // Verifica la respuesta del método
        $this->assertTrue($result['success']);
        $this->assertEquals('Resultados limpiados exitosamente', $result['message']);
    }
}