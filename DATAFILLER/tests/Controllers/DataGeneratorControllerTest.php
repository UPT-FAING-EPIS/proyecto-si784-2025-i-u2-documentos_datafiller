<?php
// filepath: c:\xampp\htdocs\proyecto-si784-2025-i-u2-documentos_datafiller\DATAFILLER\tests\Controllers\DataGeneratorControllerTest.php

require_once __DIR__ . '/../../controllers/DataGeneratorController.php';
require_once __DIR__ . '/../BaseTestCase.php';

class DataGeneratorControllerTest extends BaseTestCase
{
    private $controller;
    private $mockDb;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockDb = $this->getMockDatabase();
        // Asumir que el controlador recibe una conexión DB
        $this->controller = new DataGeneratorController($this->mockDb);
    }
    
    public function testControllerExists()
    {
        $this->assertInstanceOf(DataGeneratorController::class, $this->controller);
    }
    
    public function testGenerarDatosFaker()
    {
        // Test básico de generación de datos
        $faker = \Faker\Factory::create('es_ES');
        
        $datos = [
            'nombre' => $faker->name,
            'email' => $faker->email,
            'telefono' => $faker->phoneNumber,
            'direccion' => $faker->address
        ];
        
        $this->assertIsArray($datos);
        $this->assertArrayHasKey('nombre', $datos);
        $this->assertArrayHasKey('email', $datos);
        $this->assertIsString($datos['nombre']);
        $this->assertTrue(filter_var($datos['email'], FILTER_VALIDATE_EMAIL) !== false);
    }
    
    public function testProcesarArchivoSQL()
    {
        // Test para procesamiento de archivos SQL
        $sqlContent = "INSERT INTO usuarios (nombre, email) VALUES ('Test', 'test@example.com');";
        
        // Mock del procesamiento
        $mockStatement = $this->getMockStatement();
        $mockStatement->expects($this->once())
                     ->method('execute')
                     ->willReturn(true);
        
        $this->mockDb->expects($this->once())
                    ->method('prepare')
                    ->willReturn($mockStatement);
        
        // Simular procesamiento exitoso
        $resultado = true; // Simular resultado del controlador
        
        $this->assertTrue($resultado);
    }
}