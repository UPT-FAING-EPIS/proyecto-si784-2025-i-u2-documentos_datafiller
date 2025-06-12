<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Usuario;
use PDO;
use PDOStatement;
use PDOException;

final class UsuarioTest extends TestCase
{
    private PDO $dbMock;
    private PDOStatement $stmtMock;

    protected function setUp(): void
    {
        // Prepara un stub de PDOStatement
        $this->stmtMock = $this->createMock(PDOStatement::class);
        // Prepara un stub de PDO que siempre devuelve nuestro PDOStatement
        $this->dbMock = $this->createMock(PDO::class);
        $this->dbMock
            ->method('prepare')
            ->willReturn($this->stmtMock);
    }

    public function testCrearDevuelveFalseCuandoYaExisteUsuario(): void
    {
        // Creamos un Usuario parcialmente mockeado para que buscarPorNombre devuelva array
        $usuario = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['buscarPorNombre'])
            ->getMock();
        $usuario->nombre = 'juan';
        $usuario->apellido_paterno = 'p';
        $usuario->apellido_materno = 'm';
        $usuario->email = 'j@e';
        $usuario->password = 'pw';

        $usuario
            ->expects($this->once())
            ->method('buscarPorNombre')
            ->with('juan')
            ->willReturn(['id'=>1,'nombre'=>'juan']);

        $this->assertFalse($usuario->crear());
    }

    public function testCrearInsertaYDevuelveTrueCuandoNoExiste(): void
    {
        // stub buscarPorNombre en false
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

        // preparar execute y lastInsertId
        $this->stmtMock->method('bindParam')->willReturn(true);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->dbMock->method('lastInsertId')->willReturn(123);

        $this->assertTrue($usuario->crear());
        $this->assertSame(123, $usuario->id);
    }

    public function testBuscarPorNombreDevuelveDatosSiExiste(): void
    {
        $data = ['id'=>5,'nombre'=>'pep','apellido_paterno'=>'p','apellido_materno'=>'m','email'=>'p@e'];
        $this->stmtMock->expects($this->once())->method('execute');
        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')->with(PDO::FETCH_ASSOC)->willReturn($data);

        $usuario = new Usuario($this->dbMock);
        $result = $usuario->buscarPorNombre('pep');

        $this->assertSame($data, $result);
    }

    public function testBuscarPorNombreDevuelveFalseSiNoExiste(): void
    {
        $this->stmtMock->method('execute');
        $this->stmtMock->method('rowCount')->willReturn(0);

        $usuario = new Usuario($this->dbMock);
        $this->assertFalse($usuario->buscarPorNombre('nadie'));
    }

    public function testValidarLoginExitoYFallo(): void
    {
        $hash = password_hash('secret', PASSWORD_DEFAULT);
        // simulamos buscarPorNombre interno
        $usuarioSpy = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['buscarPorNombre'])
            ->getMock();
        $usuarioSpy->method('buscarPorNombre')
                   ->willReturn(['id'=>9,'nombre'=>'u','apellido_paterno'=>'p','email'=>'u@e','password'=>$hash]);

        $ok = $usuarioSpy->validarLogin('u', 'secret');
        $this->assertTrue($ok['exito']);
        $this->assertSame(9, $ok['usuario']['id']);

        $fail = $usuarioSpy->validarLogin('u', 'wrong');
        $this->assertFalse($fail['exito']);
    }

    public function testPuedeRealizarConsultaPremiumYGratuito(): void
    {
        $today = date('Y-m-d');
        // 1) Usuario premium
        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')->willReturn([
            'tipo_plan'=>'premium','consultas_diarias'=>10,'fecha_ultima_consulta'=>'2000-01-01'
        ]);
        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->puedeRealizarConsulta(1));

        // 2) Usuario gratuito, nuevo día => reset y true
        $this->stmtMock->method('fetch')->willReturn([
            'tipo_plan'=>'gratuito','consultas_diarias'=>3,'fecha_ultima_consulta'=>$today
        ]);
        $usuario = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['resetearConsultasDiarias'])
            ->getMock();
        $usuario->method('resetearConsultasDiarias')->with(2)->willReturn(true);
        $this->assertTrue($usuario->puedeRealizarConsulta(2));

        // 3) Usuario gratuito mismo día con menos de 3 consultas
        $this->stmtMock->method('fetch')->willReturn([
            'tipo_plan'=>'gratuito','consultas_diarias'=>1,'fecha_ultima_consulta'=>$today
        ]);
        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->puedeRealizarConsulta(3));

        // 4) Usuario gratuito alcanzó límite
        $this->stmtMock->method('fetch')->willReturn([
            'tipo_plan'=>'gratuito','consultas_diarias'=>3,'fecha_ultima_consulta'=>$today
        ]);
        $this->assertFalse($usuario->puedeRealizarConsulta(4));
    }

    public function testIncrementarConsultas(): void
    {
        $today = date('Y-m-d');
        // preparamos primer fetch
        $seq = $this->stmtMock->expects($this->exactly(2))->method('execute');
        // rowCount y fetch para leer
        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')->willReturn(['consultas_diarias'=>2,'fecha_ultima_consulta'=>$today]);
        // el UPDATE devuelve true
        $this->stmtMock->method('execute')->willReturnOnConsecutiveCalls(true, true);

        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->incrementarConsultas(5));
    }

    public function testObtenerConsultasHoyYRestantes(): void
    {
        $today = date('Y-m-d');
        // obtenerConsultasHoy cuando hay registro
        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')->willReturn(['consultas_diarias' => 2]);
        $usuario = new Usuario($this->dbMock);
        $this->assertSame(2, $usuario->obtenerConsultasHoy(7));

        // sin registros hoy
        $this->stmtMock->method('rowCount')->willReturn(0);
        $this->assertSame(0, $usuario->obtenerConsultasHoy(8));

        // obtenerConsultasRestantes premium
        $spy = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();
        $spy->method('obtenerInfoUsuario')->willReturn(['plan'=>'premium','consultas_diarias'=>0,'fecha_ultima_consulta'=>$today]);
        $this->assertGreaterThan(100, $spy->obtenerConsultasRestantes(9));

        // rest restantes gratuito mismo día
        $spy->method('obtenerInfoUsuario')->willReturn(['plan'=>'gratuito','consultas_diarias'=>1,'fecha_ultima_consulta'=>$today]);
        $this->assertSame(2, $spy->obtenerConsultasRestantes(9));

        // nuevo día
        $spy->method('obtenerInfoUsuario')->willReturn(['plan'=>'gratuito','consultas_diarias'=>3,'fecha_ultima_consulta'=>'2000-01-01']);
        $this->assertSame(3, $spy->obtenerConsultasRestantes(9));
    }

    public function testActualizarPlanExisteYNo(): void
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

    public function testRecuperacionTokenCrud(): void
    {
        // limpiarTokensExpirados
        $this->stmtMock->method('execute')->willReturn(true);
        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->limpiarTokensExpirados());

        // guardarTokenRecuperacion
        $this->stmtMock->method('execute')->willReturn(true);
        $this->assertTrue($usuario->guardarTokenRecuperacion(13, 'tok', '2025-01-01'));

        // verificarTokenRecuperacion
        $this->stmtMock->method('rowCount')->willReturn(1);
        $this->stmtMock->method('fetch')->willReturn(['token'=>'tok','email'=>'e','nombre'=>'n']);
        $this->assertIsArray($usuario->verificarTokenRecuperacion('tok'));

        // cambiarPassword
        $this->stmtMock->method('execute')->willReturn(true);
        $this->assertTrue($usuario->cambiarPassword(14, 'newpass'));

        // marcarTokenUsado
        $this->stmtMock->method('execute')->willReturn(true);
        $this->assertTrue($usuario->marcarTokenUsado('tok'));
    }
}