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
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->dbMock = $this->createMock(PDO::class);
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);
    }

    public function testBuscarPorEmail(): void
    {
        $row = ['id'=>7,'nombre'=>'alex','apellido_paterno'=>'p','email'=>'a@e'];
        $this->stmtMock->method('rowCount')->willReturnOnConsecutiveCalls(1, 0);
        $this->stmtMock->method('fetch')->with(PDO::FETCH_ASSOC)->willReturn($row);

        $usuario = new Usuario($this->dbMock);
        $this->assertSame($row, $usuario->buscarPorEmail('a@e'));
        $this->assertFalse($usuario->buscarPorEmail('b@e'));
    }

    // ... (el resto de tus tests igual que ya tienes)
}