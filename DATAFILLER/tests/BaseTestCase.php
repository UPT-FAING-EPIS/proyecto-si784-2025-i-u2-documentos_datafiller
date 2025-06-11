<?php
// filepath: c:\xampp\htdocs\proyecto-si784-2025-i-u2-documentos_datafiller\DATAFILLER\tests\BaseTestCase.php

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Configuración común para todos los tests
    }
    
    protected function tearDown(): void
    {
        // Limpieza después de cada test
        parent::tearDown();
    }
    
    /**
     * Crear un mock de PDO para testing
     */
    protected function getMockDatabase()
    {
        return $this->createMock(PDO::class);
    }
    
    /**
     * Crear un mock de PDOStatement
     */
    protected function getMockStatement()
    {
        return $this->createMock(PDOStatement::class);
    }
    
    /**
     * Configurar una conexión de base de datos real para tests de integración
     */
    protected function getRealTestDatabase()
    {
        try {
            $pdo = new PDO(
                'mysql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . ';dbname=' . ($_ENV['DB_NAME'] ?? 'test_datafiller'),
                $_ENV['DB_USER'] ?? 'root',
                $_ENV['DB_PASS'] ?? ''
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            $this->markTestSkipped('No se pudo conectar a la base de datos de prueba: ' . $e->getMessage());
        }
    }
}