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

    public function testBuscarPorEmail(): void
    {
        $row = ['id'=>7,'nombre'=>'alex','apellido_paterno'=>'p','email'=>'a@e'];
        // primer escenario: encuentra
        $this->stmtMock->method('rowCount')->willReturnOnConsecutiveCalls(1, 0);
        $this->stmtMock->method('fetch')->with(PDO::FETCH_ASSOC)->willReturn($row);

        $usuario = new Usuario($this->dbMock);
        $this->assertSame($row, $usuario->buscarPorEmail('a@e'));
        // segundo escenario: no encuentra
        $this->assertFalse($usuario->buscarPorEmail('b@e'));
    }

    public function testObtenerInfoCompleta(): void
    {
        $data = [
            'id'=>8,'nombre'=>'ana','apellido_paterno'=>'x','email'=>'x@e',
            'tipo_plan'=>'gratuito','consultas_diarias'=>1,'fecha_ultima_consulta'=>'2025-06-12'
        ];
        $this->stmtMock->method('rowCount')->willReturnOnConsecutiveCalls(1, 0);
        $this->stmtMock->method('fetch')->with(PDO::FETCH_ASSOC)->willReturn($data);

        $usuario = new Usuario($this->dbMock);
        // existe registro
        $this->assertSame($data, $usuario->obtenerInfoCompleta(8));
        // no existe
        $this->assertFalse($usuario->obtenerInfoCompleta(9));
    }

    public function testObtenerInfoUsuario(): void
    {
        $info = [
            'id'=>9,'nombre'=>'pepe','apellido_paterno'=>'p','email'=>'p@e',
            'plan'=>'premium','consultas_diarias'=>5,'fecha_ultima_consulta'=>'2025-06-11'
        ];
        $this->stmtMock->method('rowCount')->willReturnOnConsecutiveCalls(1, 0);
        $this->stmtMock->method('fetch')->with(PDO::FETCH_ASSOC)->willReturn($info);

        $usuario = new Usuario($this->dbMock);
        $this->assertSame($info, $usuario->obtenerInfoUsuario(9));
        $this->assertFalse($usuario->obtenerInfoUsuario(10));
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
                ->willReturn(['id' => 1, 'nombre' => 'juan']);

        $this->assertFalse($usuario->crear());
    }

    public function testCrearInsertaYDevuelveTrueCuandoNoExiste(): void
    {
        $usuario = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['buscarPorNombre'])
            ->getMock();

        // Datos de entrada
        $usuario->nombre = 'ana';
        $usuario->apellido_paterno = 'x';
        $usuario->apellido_materno = 'y';
        $usuario->email = 'a@e';
        $usuario->password = 'pw';

        // No existe usuario previo
        $usuario->method('buscarPorNombre')->willReturn(false);

        // Stub de bindParam y execute para simular inserción
        $this->stmtMock->method('bindParam')->willReturn(true);
        $this->stmtMock->method('execute')->willReturn(true);

        // Simular lastInsertId como string
        $this->dbMock->method('lastInsertId')->willReturn('123');

        $this->assertTrue($usuario->crear());
        $this->assertSame('123', (string)$usuario->id);
    }

    public function testBuscarPorNombre(): void
    {
        $data = [
            'id' => 5,
            'nombre' => 'pep',
            'apellido_paterno' => 'p',
            'apellido_materno' => 'm',
            'email' => 'p@e'
        ];

        // rowCount y fetch para llamadas consecutivas
        $this->stmtMock
            ->method('rowCount')
            ->willReturnOnConsecutiveCalls(1, 0);

        $this->stmtMock
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($data);

        $usuario = new Usuario($this->dbMock);

        // Primer llamada: existe
        $result1 = $usuario->buscarPorNombre('pep');
        $this->assertSame($data, $result1);

        // Segunda llamada: no existe
        $result2 = $usuario->buscarPorNombre('nadie');
        $this->assertFalse($result2);
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
                'id' => 9,
                'nombre' => 'u',
                'apellido_paterno' => 'p',
                'email' => 'u@e',
                'password' => $hash
            ]);

        $ok = $spy->validarLogin('u', 'secret');
        $this->assertTrue($ok['exito']);
        $this->assertSame(9, $ok['usuario']['id']);

        $fail = $spy->validarLogin('u', 'wrong');
        $this->assertFalse($fail['exito']);
    }

    public function testPuedeRealizarConsultaPremium(): void
    {
        $this->stmtMock
            ->method('rowCount')
            ->willReturn(1);
        $this->stmtMock
            ->method('fetch')
            ->willReturn([
                'tipo_plan' => 'premium',
                'consultas_diarias' => 10,
                'fecha_ultima_consulta' => '2000-01-01'
            ]);

        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->puedeRealizarConsulta(1));
    }

    public function testPuedeRealizarConsultaNuevoDia(): void
    {
        $this->stmtMock
            ->method('rowCount')
            ->willReturn(1);
        $this->stmtMock
            ->method('fetch')
            ->willReturn([
                'tipo_plan' => 'gratuito',
                'consultas_diarias' => 3,
                'fecha_ultima_consulta' => '2000-01-01'
            ]);

        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->puedeRealizarConsulta(2));
    }

    public function testPuedeRealizarConsultaMismoDiaMenosLimite(): void
    {
        $today = date('Y-m-d');
        $this->stmtMock
            ->method('rowCount')
            ->willReturn(1);
        $this->stmtMock
            ->method('fetch')
            ->willReturn([
                'tipo_plan' => 'gratuito',
                'consultas_diarias' => 1,
                'fecha_ultima_consulta' => $today
            ]);

        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->puedeRealizarConsulta(3));
    }

    public function testPuedeRealizarConsultaMismoDiaMaximo(): void
    {
        $today = date('Y-m-d');
        $this->stmtMock
            ->method('rowCount')
            ->willReturn(1);
        $this->stmtMock
            ->method('fetch')
            ->willReturn([
                'tipo_plan' => 'gratuito',
                'consultas_diarias' => 3,
                'fecha_ultima_consulta' => $today
            ]);

        $usuario = new Usuario($this->dbMock);
        $this->assertFalse($usuario->puedeRealizarConsulta(4));
    }

    public function testIncrementarConsultas(): void
    {
        $today = date('Y-m-d');

        // rowCount y fetch para la primera lectura
        $this->stmtMock
            ->method('rowCount')
            ->willReturn(1);
        $this->stmtMock
            ->method('fetch')
            ->willReturn(['consultas_diarias' => 2, 'fecha_ultima_consulta' => $today]);

        // execute() se llamará dos veces (SELECT + UPDATE)
        $this->stmtMock
            ->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);

        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->incrementarConsultas(5));
    }

    public function testObtenerConsultasHoy(): void
    {
        // Caso con registro
        $this->stmtMock
            ->method('rowCount')
            ->willReturnOnConsecutiveCalls(1, 0);
        $this->stmtMock
            ->method('fetch')
            ->willReturn(['consultas_diarias' => 2]);

        $usuario = new Usuario($this->dbMock);

        $this->assertSame(2, $usuario->obtenerConsultasHoy(7));
        // Sin registro hoy
        $this->assertSame(0, $usuario->obtenerConsultasHoy(8));
    }

    public function testObtenerConsultasRestantes(): void
    {
        $today = date('Y-m-d');

        // Premium
        $spy1 = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();
        $spy1->method('obtenerInfoUsuario')
             ->willReturn([
                 'plan' => 'premium',
                 'consultas_diarias' => 0,
                 'fecha_ultima_consulta' => $today
             ]);
        $this->assertGreaterThan(100, $spy1->obtenerConsultasRestantes(9));

        // Gratuito mismo día
        $spy2 = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();
        $spy2->method('obtenerInfoUsuario')
             ->willReturn([
                 'plan' => 'gratuito',
                 'consultas_diarias' => 1,
                 'fecha_ultima_consulta' => $today
             ]);
        $this->assertSame(2, $spy2->obtenerConsultasRestantes(9));

        // Nuevo día
        $spy3 = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();
        $spy3->method('obtenerInfoUsuario')
             ->willReturn([
                 'plan' => 'gratuito',
                 'consultas_diarias' => 3,
                 'fecha_ultima_consulta' => '2000-01-01'
             ]);
        $this->assertSame(3, $spy3->obtenerConsultasRestantes(9));
    }

    public function testActualizarPlan(): void
    {
        // Simulamos dos ejecuciones: la primera true, la segunda false
        $this->stmtMock
            ->method('execute')
            ->willReturnOnConsecutiveCalls(true, false);

        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->actualizarPlan(10, 'premium'));
        $this->assertFalse($usuario->actualizarPlan(10, 'gratuito'));
    }
    public function testExisteUsuario(): void
    {
        $this->stmtMock
            ->expects($this->exactly(2))
            ->method('rowCount')
            ->willReturnOnConsecutiveCalls(1, 0);

        $usuario = new Usuario($this->dbMock);
        $this->assertTrue($usuario->existeUsuario(11));
        $this->assertFalse($usuario->existeUsuario(12));
    }

     public function testCrudTokensYPassword(): void
    {
        // Hacemos que execute() devuelva true en todas las llamadas
        $this->stmtMock
            ->method('execute')
            ->willReturn(true);

        $usuario = new Usuario($this->dbMock);

        // limpiarTokensExpirados y guardarTokenRecuperacion
        $this->assertTrue($usuario->limpiarTokensExpirados());
        $this->assertTrue($usuario->guardarTokenRecuperacion(13, 'tok', '2025-01-01'));

        // verificarTokenRecuperacion
        $this->stmtMock
            ->method('rowCount')
            ->willReturn(1);
        $this->stmtMock
            ->method('fetch')
            ->willReturn(['token' => 'tok', 'email' => 'e', 'nombre' => 'n']);
        $this->assertIsArray($usuario->verificarTokenRecuperacion('tok'));

        // cambiarPassword y marcarTokenUsado
        $this->assertTrue($usuario->cambiarPassword(14, 'newpass'));
        $this->assertTrue($usuario->marcarTokenUsado('tok'));
    }
    public function testObtenerPlanUsuario(): void
    {
        // caso encuentra plan
        $this->stmtMock->method('rowCount')->willReturnOnConsecutiveCalls(1, 0);
        $this->stmtMock->method('fetch')->with(PDO::FETCH_ASSOC)->willReturn(['tipo_plan'=>'premium']);

        $usuario = new Usuario($this->dbMock);
        $this->assertSame('premium', $usuario->obtenerPlanUsuario(11));
        // caso default gratuito
        $this->assertSame('gratuito', $usuario->obtenerPlanUsuario(12));
    }

    public function testObtenerEstadisticasUsuario(): void
    {
        $stats = [
            'total_consultas'=>4,
            'total_registros_generados'=>100,
            'dias_activos'=>2,
            'ultima_actividad'=>'2025-06-12'
        ];
        // rowCount true y luego false
        $this->stmtMock->method('rowCount')->willReturnOnConsecutiveCalls(1, 0);
        $this->stmtMock->method('fetch')->with(PDO::FETCH_ASSOC)->willReturn($stats);

        $usuario = new Usuario($this->dbMock);
        // hay registros
        $this->assertSame($stats, $usuario->obtenerEstadisticasUsuario(13));
        // no hay, retorna estructura por defecto
        $expected = [
            'total_consultas'=>0,
            'total_registros_generados'=>0,
            'dias_activos'=>0,
            'ultima_actividad'=>null
        ];
        $this->assertSame($expected, $usuario->obtenerEstadisticasUsuario(14));
    }

    public function testCalcularConsultasRestantes(): void
    {
        $today = date('Y-m-d');
        // premium => 999
        $spy1 = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();
        $spy1->method('obtenerInfoUsuario')->willReturn([
            'plan'=>'premium','consultas_diarias'=>0,'fecha_ultima_consulta'=>$today
        ]);
        $this->assertGreaterThan(100, $spy1->calcularConsultasRestantes(15));

        // mismo día => restan 2
        $spy2 = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();
        $spy2->method('obtenerInfoUsuario')->willReturn([
            'plan'=>'gratuito','consultas_diarias'=>1,'fecha_ultima_consulta'=>$today
        ]);
        $this->assertSame(2, $spy2->calcularConsultasRestantes(16));

        // nuevo día => restan 3
        $spy3 = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();
        $spy3->method('obtenerInfoUsuario')->willReturn([
            'plan'=>'gratuito','consultas_diarias'=>3,'fecha_ultima_consulta'=>'2000-01-01'
        ]);
        $this->assertSame(3, $spy3->calcularConsultasRestantes(17));
    }
}
