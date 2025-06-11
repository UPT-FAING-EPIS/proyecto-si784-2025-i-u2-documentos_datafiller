<?php
// filepath: c:\xampp\htdocs\proyecto-si784-2025-i-u2-documentos_datafiller\DATAFILLER\tests\Models\UsuarioTest.php

require_once __DIR__ . '/../../models/Usuario.php';
require_once __DIR__ . '/../BaseTestCase.php';

class UsuarioTest extends BaseTestCase
{
    private $usuario;
    private $mockDb;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un mock de PDO para simular la base de datos
        $this->mockDb = $this->createMock(PDO::class);
        
        // Crear instancia de Usuario con el mock de la base de datos
        $this->usuario = new Usuario($this->mockDb);
    }
    
    public function testCrearUsuario()
    {
        // Test básico de creación de usuario
        $this->assertInstanceOf(Usuario::class, $this->usuario);
    }
    
    public function testValidarEmail()
    {
        // Test de validación de email (funciones nativas de PHP)
        $emailValido = "test@example.com";
        $emailInvalido = "email-invalido";
        
        $this->assertTrue(filter_var($emailValido, FILTER_VALIDATE_EMAIL) !== false);
        $this->assertFalse(filter_var($emailInvalido, FILTER_VALIDATE_EMAIL) !== false);
    }
    
    public function testPropiedadesUsuario()
    {
        // Test para verificar que las propiedades se pueden asignar
        $this->usuario->nombre = "Juan";
        $this->usuario->email = "juan@test.com";
        $this->usuario->tipo_plan = "gratuito";
        
        $this->assertEquals("Juan", $this->usuario->nombre);
        $this->assertEquals("juan@test.com", $this->usuario->email);
        $this->assertEquals("gratuito", $this->usuario->tipo_plan);
    }
    
    public function testBuscarPorNombreConMock()
    {
        // Configurar el mock para simular una consulta exitosa
        $mockStatement = $this->createMock(PDOStatement::class);
        
        // Configurar el comportamiento esperado
        $mockStatement->expects($this->once())
                     ->method('execute')
                     ->willReturn(true);
                     
        $mockStatement->expects($this->once())
                     ->method('rowCount')
                     ->willReturn(1);
                     
        $mockStatement->expects($this->once())
                     ->method('fetch')
                     ->with(PDO::FETCH_ASSOC)
                     ->willReturn([
                         'id' => 1,
                         'nombre' => 'testuser',
                         'email' => 'test@example.com'
                     ]);
        
        $this->mockDb->expects($this->once())
                    ->method('prepare')
                    ->willReturn($mockStatement);
        
        // Ejecutar el método
        $resultado = $this->usuario->buscarPorNombre('testuser');
        
        // Verificar resultado
        $this->assertIsArray($resultado);
        $this->assertEquals('testuser', $resultado['nombre']);
    }
    
    public function testBuscarPorEmailConMock()
    {
        // Configurar mock para búsqueda por email
        $mockStatement = $this->createMock(PDOStatement::class);
        
        $mockStatement->expects($this->once())
                     ->method('execute')
                     ->willReturn(true);
                     
        $mockStatement->expects($this->once())
                     ->method('rowCount')
                     ->willReturn(1);
                     
        $mockStatement->expects($this->once())
                     ->method('fetch')
                     ->with(PDO::FETCH_ASSOC)
                     ->willReturn([
                         'id' => 1,
                         'nombre' => 'testuser',
                         'email' => 'test@example.com'
                     ]);
        
        $this->mockDb->expects($this->once())
                    ->method('prepare')
                    ->willReturn($mockStatement);
        
        $resultado = $this->usuario->buscarPorEmail('test@example.com');
        
        $this->assertIsArray($resultado);
        $this->assertEquals('test@example.com', $resultado['email']);
    }
    
    public function testObtenerPlanUsuario()
    {
        // Test para el método obtenerPlanUsuario
        $mockStatement = $this->createMock(PDOStatement::class);
        
        $mockStatement->expects($this->once())
                     ->method('execute')
                     ->willReturn(true);
                     
        $mockStatement->expects($this->once())
                     ->method('rowCount')
                     ->willReturn(1);
                     
        $mockStatement->expects($this->once())
                     ->method('fetch')
                     ->with(PDO::FETCH_ASSOC)
                     ->willReturn(['tipo_plan' => 'premium']);
        
        $this->mockDb->expects($this->once())
                    ->method('prepare')
                    ->willReturn($mockStatement);
        
        $plan = $this->usuario->obtenerPlanUsuario(1);
        
        $this->assertEquals('premium', $plan);
    }
    
    public function testExisteUsuario()
    {
        // Test para verificar si un usuario existe
        $mockStatement = $this->createMock(PDOStatement::class);
        
        $mockStatement->expects($this->once())
                     ->method('execute')
                     ->willReturn(true);
                     
        $mockStatement->expects($this->once())
                     ->method('rowCount')
                     ->willReturn(1);
        
        $this->mockDb->expects($this->once())
                    ->method('prepare')
                    ->willReturn($mockStatement);
        
        $existe = $this->usuario->existeUsuario(1);
        
        $this->assertTrue($existe);
    }
    
    protected function tearDown(): void
    {
        $this->usuario = null;
        $this->mockDb = null;
        parent::tearDown();
    }
}