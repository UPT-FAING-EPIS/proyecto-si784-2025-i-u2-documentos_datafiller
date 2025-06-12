<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Usuario;
use PDO;
use PDOStatement;

final class UsuarioTest extends TestCase
{
    private PDO $pdo;
    private PDOStatement $stmt;

    protected function setUp(): void
    {
        $this->pdo  = $this->createMock(PDO::class);
        $this->stmt = $this->createMock(PDOStatement::class);
    }

    public function testBuscarPorNombreReturnsFalseWhenNoUser(): void
    {
        $this->pdo
            ->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt
            ->method('rowCount')
            ->willReturn(0);

        $usuario = new Usuario($this->pdo);
        $result  = $usuario->buscarPorNombre('juan');
        $this->assertFalse($result);
    }

    public function testBuscarPorNombreReturnsArrayWhenUserFound(): void
    {
        $expected = ['id' => 1, 'nombre' => 'juan'];
        $this->pdo
            ->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt
            ->method('rowCount')
            ->willReturn(1);
        $this->stmt
            ->method('fetch')
            ->willReturn($expected);

        $usuario = new Usuario($this->pdo);
        $result  = $usuario->buscarPorNombre('juan');
        $this->assertSame($expected, $result);
    }

    public function testBuscarPorEmailReturnsFalseWhenNoUser(): void
    {
        $this->pdo
            ->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt
            ->method('rowCount')
            ->willReturn(0);

        $usuario = new Usuario($this->pdo);
        $this->assertFalse($usuario->buscarPorEmail('no@existe.com'));
    }

    public function testBuscarPorEmailReturnsArrayWhenUserFound(): void
    {
        $expected = ['id' => 2, 'nombre' => 'ana', 'apellido_paterno' => 'Pérez', 'email' => 'ana@example.com'];
        $this->pdo
            ->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt
            ->method('rowCount')
            ->willReturn(1);
        $this->stmt
            ->method('fetch')
            ->willReturn($expected);

        $usuario = new Usuario($this->pdo);
        $result  = $usuario->buscarPorEmail('ana@example.com');
        $this->assertSame($expected, $result);
    }

    public function testValidarLoginSuccess(): void
    {
        $hash = password_hash('secret', PASSWORD_DEFAULT);
        $dbResult = [
            'id'               => 3,
            'nombre'           => 'pepito',
            'apellido_paterno' => 'Gómez',
            'email'            => 'pepito@example.com',
            'password'         => $hash
        ];

        $stub = $this->getMockBuilder(Usuario::class)
                     ->setConstructorArgs([$this->pdo])
                     ->onlyMethods(['buscarPorNombre'])
                     ->getMock();
        $stub->method('buscarPorNombre')->willReturn($dbResult);

        $res = $stub->validarLogin('pepito', 'secret');
        $this->assertTrue($res['exito']);
        $this->assertArrayHasKey('usuario', $res);
        $this->assertSame(3, $res['usuario']['id']);
    }

    public function testValidarLoginFailure(): void
    {
        $stub = $this->getMockBuilder(Usuario::class)
                     ->setConstructorArgs([$this->pdo])
                     ->onlyMethods(['buscarPorNombre'])
                     ->getMock();
        $stub->method('buscarPorNombre')->willReturn(false);

        $res = $stub->validarLogin('desconocido', 'pass');
        $this->assertFalse($res['exito']);
    }

    public function testCrearReturnsFalseWhenUserExists(): void
    {
        $stub = $this->getMockBuilder(Usuario::class)
                     ->setConstructorArgs([$this->pdo])
                     ->onlyMethods(['buscarPorNombre'])
                     ->getMock();
        $stub->method('buscarPorNombre')->willReturn(['id' => 9]);

        $this->assertFalse($stub->crear());
    }

    public function testCrearReturnsTrueAndSetsIdWhenInsertSucceeds(): void
    {
        $this->pdo
            ->method('prepare')
            ->willReturn($this->stmt);
        $this->stmt
            ->method('execute')
            ->willReturn(true);
        $this->pdo
            ->method('lastInsertId')
            ->willReturn('42');

        $stub = $this->getMockBuilder(Usuario::class)
                     ->setConstructorArgs([$this->pdo])
                     ->onlyMethods(['buscarPorNombre'])
                     ->getMock();
        $stub->method('buscarPorNombre')->willReturn(false);

        $result = $stub->crear();
        $this->assertTrue($result);
        $this->assertSame('42', $stub->id);
    }

    public function testPuedeRealizarConsultaPremiumAlwaysTrue(): void
    {
        $row = ['tipo_plan' => 'premium'];
        $this->pdo->method('prepare')->willReturn($this->stmt);
        $this->stmt->method('rowCount')->willReturn(1);
        $this->stmt->method('fetch')->willReturn($row);

        $usuario = new Usuario($this->pdo);
        $this->assertTrue($usuario->puedeRealizarConsulta(7));
    }

    public function testPuedeRealizarConsultaLimitReached(): void
    {
        $today = date('Y-m-d');
        $row = ['tipo_plan' => 'gratuito', 'consultas_diarias' => 3, 'fecha_ultima_consulta' => $today];
        $this->pdo->method('prepare')->willReturn($this->stmt);
        $this->stmt->method('rowCount')->willReturn(1);
        $this->stmt->method('fetch')->willReturn($row);

        $usuario = new Usuario($this->pdo);
        $this->assertFalse($usuario->puedeRealizarConsulta(8));
    }
}