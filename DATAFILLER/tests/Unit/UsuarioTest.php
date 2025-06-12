<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Usuario;
use PDO;
use PDOException;

final class UsuarioTest extends TestCase
{
    private PDO $dbException;

    protected function setUp(): void
    {
        // Stub PDO so that prepare() always throws
        $this->dbException = $this->createMock(PDO::class);
        $this->dbException
            ->method('prepare')
            ->willThrowException(new PDOException('DB error'));
    }

    public function testBuscarPorEmailException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->buscarPorEmail('foo@bar'));
    }

    public function testObtenerInfoCompletaException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->obtenerInfoCompleta(1));
    }

    public function testObtenerInfoUsuarioException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->obtenerInfoUsuario(1));
    }

    public function testObtenerConsultasHoyException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertSame(0, $usuario->obtenerConsultasHoy(1));
    }

    public function testObtenerPlanUsuarioException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertSame('gratuito', $usuario->obtenerPlanUsuario(1));
    }

    public function testPuedeRealizarConsultaException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->puedeRealizarConsulta(1));
    }

    public function testIncrementarConsultasException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->incrementarConsultas(1));
    }

    public function testActualizarPlanException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->actualizarPlan(1, 'premium'));
    }

    public function testObtenerEstadisticasUsuarioException(): void
    {
        $usuario = new Usuario($this->dbException);
        $expected = [
            'total_consultas' => 0,
            'total_registros_generados' => 0,
            'dias_activos' => 0,
            'ultima_actividad' => null,
        ];
        $this->assertSame($expected, $usuario->obtenerEstadisticasUsuario(1));
    }

    public function testExisteUsuarioException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->existeUsuario(1));
    }

    public function testLimpiarTokensExpiradosException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->limpiarTokensExpirados());
    }

    public function testGuardarTokenRecuperacionException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->guardarTokenRecuperacion(1, 'tok', '2025-06-12'));
    }

    public function testVerificarTokenRecuperacionException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->verificarTokenRecuperacion('tok'));
    }

    public function testCambiarPasswordException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->cambiarPassword(1, 'new'));
    }

    public function testMarcarTokenUsadoException(): void
    {
        $usuario = new Usuario($this->dbException);
        $this->assertFalse($usuario->marcarTokenUsado('tok'));
    }

    public function testCalcularConsultasRestantesException(): void
    {
        $spy = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbException])
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();
        $spy->method('obtenerInfoUsuario')
            ->willThrowException(new PDOException('err'));
        $this->assertSame(0, $spy->calcularConsultasRestantes(1));
    }

    public function testResetearConsultasDiariasException(): void
    {
        // Cover private resetearConsultasDiarias catch block
        $usuario = new Usuario($this->dbException);
        $ref   = new \ReflectionClass($usuario);
        $m     = $ref->getMethod('resetearConsultasDiarias');
        $m->setAccessible(true);
        $this->assertFalse($m->invoke($usuario, 1));
    }
}