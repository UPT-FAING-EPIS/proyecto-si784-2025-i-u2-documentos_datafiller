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

    public function testBuscarPorEmailException(): void
    {
        // PDO stub that throws on prepare() to hit the catch block
        $dbException = $this->createMock(\PDO::class);
        $dbException
            ->method('prepare')
            ->willThrowException(new \PDOException('DB fallo'));

        $usuario = new Usuario($dbException);
       // When prepare() fails, buscarPorEmail must catch and return false
        $this->assertFalse($usuario->buscarPorEmail('foo@bar'));
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

     public function testCrearDevuelveFalseCuandoInsercionFalla(): void
    {
        $usuario = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['buscarPorNombre'])
            ->getMock();

        // Prevenir existencia previa
        $usuario->method('buscarPorNombre')->willReturn(false);
        // Datos de usuario
        $usuario->nombre = 'maria';
        $usuario->apellido_paterno = 'p';
        $usuario->apellido_materno = 'm';
        $usuario->email = 'm@e';
        $usuario->password = 'pwd';


        // bindParam ok, pero execute falla
        $this->stmtMock->method('bindParam')->willReturn(true);
        $this->stmtMock->method('execute')->willReturn(false);

        // Ejecutar y esperar false en fallback
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

    public function testBuscarPorNombreException(): void
    {
        // Simula un error en prepare() para cubrir el catch
        $dbException = $this->createMock(\PDO::class);
        $dbException
            ->method('prepare')
            ->willThrowException(new \PDOException('DB fallo'));

        $usuario = new Usuario($dbException);
        // Al lanzar la excepción, debe devolver false en el catch
        $this->assertFalse($usuario->buscarPorNombre('cualquiera'));
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

    

    public function testPuedeRealizarConsultaCatchException(): void
    {
        $this->dbMock->method('prepare')->will($this->throwException(new \PDOException('DB error')));
        $usuario = new Usuario($this->dbMock);
        $this->assertFalse($usuario->puedeRealizarConsulta(6));
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
    
    public function testResetearConsultasDiariasCausaPDOExceptionRetornaFalse()
    {
        $usuario_id = 123;
        // Crear un mock de PDO que lanzará una excepción en prepare()
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')
            ->will($this->throwException(new \PDOException('Fallo en prepare')));

        $usuario = new \App\Models\Usuario($pdoMock);

        $resultado = $usuario->resetearConsultasDiarias($usuario_id);
        $this->assertFalse($resultado, "Debe retornar false si ocurre una excepción en prepare()");
    }

    public function testObtenerInfoCompletaCatchException()
    {
        $usuario_id = 99;
        // Mock de PDO que lanza excepción en prepare
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')
            ->will($this->throwException(new \PDOException('Simulated DB error')));

        $usuario = new \App\Models\Usuario($pdoMock);

        $this->assertFalse($usuario->obtenerInfoCompleta($usuario_id));
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

    public function testObtenerInfoUsuarioCatchException()
    {
        $usuario_id = 101;
        // Mock de PDO que lanza excepción en prepare()
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')
            ->will($this->throwException(new \PDOException('Simulated DB error')));

        $usuario = new \App\Models\Usuario($pdoMock);

        $this->assertFalse($usuario->obtenerInfoUsuario($usuario_id));
    }

    public function testObtenerConsultasHoyCatchException()
    {
        $usuario_id = 55;
        // Mock de PDO que lanzará una excepción en prepare()
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')
            ->will($this->throwException(new \PDOException('Simulated DB error')));

        $usuario = new \App\Models\Usuario($pdoMock);

        // Debe retornar 0 si ocurre una excepción
        $this->assertSame(0, $usuario->obtenerConsultasHoy($usuario_id));
    }

    public function testObtenerPlanUsuarioCatchException()
    {
        $usuario_id = 1234;
        // Mock de PDO que lanza excepción en prepare()
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')
            ->will($this->throwException(new \PDOException('Simulated DB error')));

        $usuario = new \App\Models\Usuario($pdoMock);

        // Debe retornar 'gratuito' si ocurre una excepción
        $this->assertSame('gratuito', $usuario->obtenerPlanUsuario($usuario_id));
    }

    public function testObtenerConsultasRestantesCatchException()
    {
        $usuario_id = 42;
        // Mock de PDO (no se usará pero se requiere por el constructor)
        $pdoMock = $this->createMock(PDO::class);

        // Creamos un mock parcial de Usuario para simular que obtenerInfoUsuario lanza una excepción PDO
        $usuarioMock = $this->getMockBuilder(\App\Models\Usuario::class)
            ->setConstructorArgs([$pdoMock])
            ->onlyMethods(['obtenerInfoUsuario'])
            ->getMock();

        $usuarioMock->method('obtenerInfoUsuario')
            ->will($this->throwException(new \PDOException('Simulated DB error')));

        // Debe retornar 0 si ocurre una excepción
        $this->assertSame(0, $usuarioMock->obtenerConsultasRestantes($usuario_id));
    }

    public function testActualizarPlanCatchException()
    {
        $usuario_id = 77;
        $nuevo_plan = 'premium';

        // Mock de PDO que lanza excepción en prepare()
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')
            ->will($this->throwException(new \PDOException('Simulated DB error')));

        $usuario = new \App\Models\Usuario($pdoMock);

        // Debe retornar false si ocurre una excepción
        $this->assertFalse($usuario->actualizarPlan($usuario_id, $nuevo_plan));
    }

    public function testObtenerEstadisticasUsuarioCatchException()
{
    $usuario_id = 888;
    // Mock de PDO que lanza excepción en prepare()
    $pdoMock = $this->createMock(PDO::class);
    $pdoMock->method('prepare')
        ->will($this->throwException(new \PDOException('Simulated DB error')));

    $usuario = new \App\Models\Usuario($pdoMock);

    $resultado = $usuario->obtenerEstadisticasUsuario($usuario_id);

    // El resultado debe tener todos los campos en 0 o null, como en el catch
    $this->assertEquals([
        'total_consultas' => 0,
        'total_registros_generados' => 0,
        'dias_activos' => 0,
        'ultima_actividad' => null
    ], $resultado);
}

public function testExisteUsuarioCatchException()
{
    $usuario_id = 555;
    // Mock de PDO que lanza excepción en prepare()
    $pdoMock = $this->createMock(PDO::class);
    $pdoMock->method('prepare')
        ->will($this->throwException(new \PDOException('Simulated DB error')));

    $usuario = new \App\Models\Usuario($pdoMock);

    // Debe retornar false si ocurre una excepción
    $this->assertFalse($usuario->existeUsuario($usuario_id));
}
public function testGuardarTokenRecuperacionCatchException()
{
    $usuario_id = 1;
    $token = 'fake-token';
    $expiracion = '2025-12-31 23:59:59';

    // Mock de PDO que lanza excepción en prepare()
    $pdoMock = $this->createMock(PDO::class);
    $pdoMock->method('prepare')
        ->will($this->throwException(new \PDOException('Simulated DB error')));

    $usuario = new \App\Models\Usuario($pdoMock);

    // Debe retornar false si ocurre una excepción
    $this->assertFalse($usuario->guardarTokenRecuperacion($usuario_id, $token, $expiracion));
}

public function testVerificarTokenRecuperacionCatchException()
{
    $token = 'token-ejemplo';

    // Mock de PDO que lanza excepción en prepare()
    $pdoMock = $this->createMock(PDO::class);
    $pdoMock->method('prepare')
        ->will($this->throwException(new \PDOException('Simulated DB error')));

    $usuario = new \App\Models\Usuario($pdoMock);

    // Debe retornar false si ocurre una excepción
    $this->assertFalse($usuario->verificarTokenRecuperacion($token));
}

public function testVerificarTokenRecuperacionRetornaFalseSiNoExiste()
{
    $token = 'token-inexistente';

    // Mock del statement que simula rowCount() === 0
    $stmtMock = $this->createMock(PDOStatement::class);
    $stmtMock->method('rowCount')
        ->willReturn(0);

    // El fetch nunca será llamado, pero si se llama, que retorne null
    $stmtMock->method('fetch')
        ->willReturn(null);

    // Mock del PDO principal, que retorna el statement anterior
    $pdoMock = $this->createMock(PDO::class);
    $pdoMock->method('prepare')
        ->willReturn($stmtMock);

    $usuario = new \App\Models\Usuario($pdoMock);

    // Verifica que retorne false cuando no hay coincidencias
    $this->assertFalse($usuario->verificarTokenRecuperacion($token));
}
public function testCambiarPasswordCatchException()
{
    $usuario_id = 1;
    $nueva_password = 'NuevaPasswordSegura123';

    // Mock de PDO que lanza excepción en prepare()
    $pdoMock = $this->createMock(PDO::class);
    $pdoMock->method('prepare')
        ->will($this->throwException(new \PDOException('Simulated DB error')));

    $usuario = new \App\Models\Usuario($pdoMock);

    // Debe retornar false si ocurre una excepción
    $this->assertFalse($usuario->cambiarPassword($usuario_id, $nueva_password));
}

public function testMarcarTokenUsadoCatchException()
{
    $token = 'token-prueba';

    // Mock de PDO que lanza excepción en prepare()
    $pdoMock = $this->createMock(PDO::class);
    $pdoMock->method('prepare')
        ->will($this->throwException(new \PDOException('Simulated DB error')));

    $usuario = new \App\Models\Usuario($pdoMock);

    // Debe retornar false si ocurre una excepción
    $this->assertFalse($usuario->marcarTokenUsado($token));
}

public function testCalcularConsultasRestantesCatchException()
{
    $usuario_id = 42;

    // Mock de PDO (no se usará pero se requiere para el constructor)
    $pdoMock = $this->createMock(PDO::class);

    // Mock parcial de Usuario para simular que obtenerInfoUsuario lanza una excepción PDO
    $usuarioMock = $this->getMockBuilder(\App\Models\Usuario::class)
        ->setConstructorArgs([$pdoMock])
        ->onlyMethods(['obtenerInfoUsuario'])
        ->getMock();

    $usuarioMock->method('obtenerInfoUsuario')
        ->will($this->throwException(new \PDOException('Simulated DB error')));

    // Debe retornar 0 si ocurre una excepción
    $this->assertSame(0, $usuarioMock->calcularConsultasRestantes($usuario_id));
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

    public function testLimpiarTokensExpiradosExecuteFails(): void
    {
        // Preparar un stmt que devuelva false en execute()
        $this->stmtMock
            ->method('execute')
            ->willReturn(false);

        $usuario = new Usuario($this->dbMock);
        // Si execute() falla, debe devolver false
        $this->assertFalse($usuario->limpiarTokensExpirados());
    }

    public function testLimpiarTokensExpiradosException(): void
    {
        // Simular excepción en prepare()
        $dbException = $this->createMock(\PDO::class);
        $dbException
            ->method('prepare')
            ->willThrowException(new \PDOException('DB error'));

        $usuario = new Usuario($dbException);
        // Al lanzarse PDOException, debe atraparse y devolver false
        $this->assertFalse($usuario->limpiarTokensExpirados());
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
    

    public function testPuedeRealizarConsultaUsuarioNoExiste(): void
    {
        // rowCount = 0 should hit the `return false;` path in try block
        $this->stmtMock
            ->method('rowCount')
            ->willReturn(0);

        $usuario = new Usuario($this->dbMock);
        $this->assertFalse($usuario->puedeRealizarConsulta(999));
    }

    public function testValidarLoginCatchBlock(): void
    {
        // Crear un stub de PDO que falle en prepare()
        $dbException = $this->createMock(\PDO::class);
        $dbException
            ->method('prepare')
            ->willThrowException(new \PDOException('simulated DB error'));

        // Instanciar Usuario con el PDO que lanza la excepción
        $usuario = new Usuario($dbException);

        // Al atrapar la excepción, validarLogin debe retornar ['exito' => false]
        $result = $usuario->validarLogin('any', 'any');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('exito', $result);
        $this->assertFalse($result['exito']);
    }

    public function testValidarLoginUsuarioNoExiste(): void
    {
        // buscarPorNombre returns false, so validarLogin should return exito=false
        $spy = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['buscarPorNombre'])
            ->getMock();

        $spy->method('buscarPorNombre')
            ->willReturn(false);

        $result = $spy->validarLogin('nonexistent', 'any');
        $this->assertFalse($result['exito']);
    }
    public function testIncrementarConsultasNuevoDia(): void
    {
        // Simula que no hubo consultas el mismo día (fecha anterior)
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // rowCount = 1 para entrar al if
        $this->stmtMock
            ->method('rowCount')
            ->willReturn(1);
        // fetch devuelve fecha_ultima_consulta distinta a hoy
        $this->stmtMock
            ->method('fetch')
            ->willReturn([
                'consultas_diarias' => 5,
                'fecha_ultima_consulta' => $yesterday
            ]);

        // execute() se debe llamar dos veces (SELECT + UPDATE)
        $this->stmtMock
            ->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);

        $usuario = new Usuario($this->dbMock);
        // Nuevo día: debe resetear a 1 y devolver true
        $this->assertTrue($usuario->incrementarConsultas(42));
    }

    public function testIncrementarConsultasCatchException(): void
    {
        $this->dbMock->method('prepare')->will($this->throwException(new \PDOException('DB error')));
        $usuario = new Usuario($this->dbMock);
        $this->assertFalse($usuario->incrementarConsultas(99));
    }

    public function testIncrementarConsultasSinRegistros(): void
    {
        // Simula que no existe registro previo: rowCount = 0
        $this->stmtMock
            ->method('rowCount')
            ->willReturn(0);

        $usuario = new Usuario($this->dbMock);
        // Sin filas: debe retornar false
        $this->assertFalse($usuario->incrementarConsultas(99));
    }
    
    public function testValidarLoginDevuelveFalsoSiNoExisteUsuario(): void
    {
        $spy = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['buscarPorNombre'])
            ->getMock();

        $spy->method('buscarPorNombre')
            ->with('noexiste')
            ->willReturn(null);

        $fail = $spy->validarLogin('noexiste', 'cualquier');
        $this->assertFalse($fail['exito']);
    }

    public function testValidarLoginCachaExcepcionYDevuelveFalso(): void
    {
        $spy = $this->getMockBuilder(Usuario::class)
            ->setConstructorArgs([$this->dbMock])
            ->onlyMethods(['buscarPorNombre'])
            ->getMock();

        $spy->method('buscarPorNombre')
            ->will($this->throwException(new \PDOException('DB error')));

        $fail = $spy->validarLogin('error', 'clave');
        $this->assertFalse($fail['exito']);
    }
}