<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Usuario;
use PDO;
use PDOStatement;

final class UsuarioTest extends TestCase
{
    private PDO $dbMock;
    private PDOStatement $stmtMock;

    protected function setUp(): void
    {
        // Stub de PDOStatement
        $this->stmtMock = $this->createMock(PDOStatement::class);
        // PDO que siempre devuelve el mismo PDOStatement
        $this->dbMock = $this->createMock(PDO::class);
        $this->dbMock
            ->method('prepare')
            ->willReturn($this->stmtMock);
    }

    public function testCrearDevuelveFalseCuandoYaExisteUsuario(): void
    {
        $usuario = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['buscarPorNombre'])
            ->getMock();

        $usuario->nombre = 'juan';
        $usuario->apellido_paterno = 'p';
        $usuario->apellido_materno = 'm';
        $usuario->email = 'j@e';
        $usuario->password = 'pw';

        $usuario->expects($this->once())
                ->method('buscarPorNombre')
                ->with('juan')
                ->willReturn(['id'=>1,'nombre'=>'juan']);

        $this->assertFalse($usuario->crear());
    }

    public function testCrearInsertaYDevuelveTrueCuandoNoExiste(): void
    {
        $usuario = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['buscarPorNombre'])
            ->getMock();

        $usuario->nombre = 'ana';
        $usuario->apellido_paterno = 'x';
        $usuario->apellido_materno = 'y';
        $usuario->email = 'a@e';
        $usuario->password = 'pw';

        $usuario->method('buscarPorNombre')->willReturn(false);

        $this->stmtMock->method('bindParam')->willReturn(true);
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $this->dbMock->method('lastInsertId')->willReturn('123');

        $this->assertTrue($usuario->crear());
        $this->assertSame('123', (string)$usuario->id);
    }

    public function testBuscarPorNombre(): void
    {
        $data = [
            'id'=>5,
            'nombre'=>'pep',
            'apellido_paterno'=>'p',
            'apellido_materno'=>'m',
            'email'=>'p@e'
        ];
        $this->stmtMock->expects($this->once())->method('execute');
        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')
                       ->with(PDO::FETCH_ASSOC)
                       ->willReturn($data);

        $usuario = new Usuario($this->dbMock);
        $this->assertSame($data, $usuario->buscarPorNombre('pep'));

        // Cuando no existe
        $this->stmtMock->method('rowCount')->willReturn(0);
        $this->assertFalse($usuario->buscarPorNombre('nadie'));
    }

    public function testValidarLoginExitoYFallo(): void
    {
        $hash = password_hash('secret', PASSWORD_DEFAULT);
        $spy = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['buscarPorNombre'])
            ->getMock();

        $spy->method('buscarPorNombre')
            ->with('u')
            ->willReturn([
                'id'=>9,
                'nombre'=>'u',
                'apellido_paterno'=>'p',
                'email'=>'u@e',
                'password'=>$hash
            ]);

        $ok = $spy->validarLogin('u', 'secret');
        $this->assertTrue($ok['exito']);
        $this->assertSame(9, $ok['usuario']['id']);

        $fail = $spy->validarLogin('u', 'wrong');
        $this->assertFalse($fail['exito']);
    }

    public function testPuedeRealizarConsultaPremium(): void
    {
        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')->willReturn([
            'tipo_plan'=>'premium',
            'consultas_diarias'=>10,
            'fecha_ultima_consulta'=>'2000-01-01'
        ]);

        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->puedeRealizarConsulta(1));
    }

    public function testPuedeRealizarConsultaNuevoDia(): void
    {
        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')->willReturn([
            'tipo_plan'=>'gratuito',
            'consultas_diarias'=>3,
            'fecha_ultima_consulta'=>'2000-01-01'
        ]);

        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->puedeRealizarConsulta(2));
    }

    public function testPuedeRealizarConsultaMismoDiaMenosLimite(): void
    {
        $today = date('Y-m-d');
        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')->willReturn([
            'tipo_plan'=>'gratuito',
            'consultas_diarias'=>1,
            'fecha_ultima_consulta'=>$today
        ]);

        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->puedeRealizarConsulta(3));
    }

    public function testPuedeRealizarConsultaMismoDiaMaximo(): void
    {
        $today = date('Y-m-d');
        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')->willReturn([
            'tipo_plan'=>'gratuito',
            'consultas_diarias'=>3,
            'fecha_ultima_consulta'=>$today
        ]);

        $usuario = new Usuario($this->dbMock);
        $this->assertFalse($usuario->puedeRealizarConsulta(4));
    }

    public function testIncrementarConsultas(): void
    {
        $today = date('Y-m-d');

        // Primera consulta: SELECT
        $this->stmtMock->expects($this->exactly(1))
                       ->method('rowCount')
                       ->willReturn(1);
        $this->stmtMock->expects($this->exactly(1))
                       ->method('fetch')
                       ->willReturn(['consultas_diarias'=>2,'fecha_ultima_consulta'=>$today]);

        // La llamada a execute: una para SELECT (no revisada) y otra para UPDATE
        $this->stmtMock->expects($this->exactly(2))
                       ->method('execute')
                       ->willReturn(true);

        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->incrementarConsultas(5));
    }

    public function testObtenerConsultasHoy(): void
    {
        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')->willReturn(['consultas_diarias'=>2]);

        $usuario = new Usuario($this->dbMock);
        $this->assertSame(2, $usuario->obtenerConsultasHoy(7));

        // Sin registro
        $this->stmtMock->method('rowCount')->willReturn(0);
        $this->assertSame(0, $usuario->obtenerConsultasHoy(8));
    }

    public function testObtenerConsultasRestantes(): void
    {
        $today = date('Y-m-d');
        $spy = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();

        // Premium
        $spy->method('obtenerInfoUsuario')->willReturn([
            'plan'=>'premium',
            'consultas_diarias'=>0,
            'fecha_ultima_consulta'=>$today
        ]);
        $this->assertGreaterThan(100, $spy->obtenerConsultasRestantes(9));

        // Gratuito mismo día
        $spy->method('obtenerInfoUsuario')->willReturn([
            'plan'=>'gratuito',
            'consultas_diarias'=>1,
            'fecha_ultima_consulta'=>$today
        ]);
        $this->assertSame(2, $spy->obtenerConsultasRestantes(9));

        // Gratuito nuevo día
        $spy->method('obtenerInfoUsuario')->willReturn([
            'plan'=>'gratuito',
            'consultas_diarias'=>3,
            'fecha_ultima_consulta'=>'2000-01-01'
        ]);
        $this->assertSame(3, $spy->obtenerConsultasRestantes(9));
    }

    public function testActualizarPlan(): void
    {
        $this->stmtMock->method('execute')->willReturn(true);
        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->actualizarPlan(10, 'premium'));

        $this->stmtMock->method('execute')->willReturn(false);
        $this->assertFalse($usuario->actualizarPlan(10, 'gratuito'));
    }

    public function testExisteUsuario(): void
    {
        $this->stmtMock->method('rowCount')->willReturn(1);
        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->existeUsuario(11));

        $this->stmtMock->method('rowCount')->willReturn(0);
        $this->assertFalse($usuario->existeUsuario(12));
    }

    public function testCrudTokensYPassword(): void
    {
        $this->stmtMock->method('execute')->willReturn(true);
        $usuario = new Usuario($this->dbMock);

        $this->assertTrue($usuario->limpiarTokensExpirados());
        $this->assertTrue($usuario->guardarTokenRecuperacion(13, 'tok', '2025-01-01'));

        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')->willReturn(['token'=>'tok','email'=>'e','nombre'=>'n']);
        $this->assertIsArray($usuario->verificarTokenRecuperacion('tok'));

        $this->stmtMock->method('execute')->willReturn(true);
        $this->assertTrue($usuario->cambiarPassword(14, 'newpass'));
        $this->assertTrue($usuario->marcarTokenUsado('tok'));
    }
}