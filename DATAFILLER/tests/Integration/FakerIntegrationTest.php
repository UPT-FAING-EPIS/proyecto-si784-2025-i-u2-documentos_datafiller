<?php
// filepath: c:\xampp\htdocs\proyecto-si784-2025-i-u2-documentos_datafiller\DATAFILLER\tests\Integration\FakerIntegrationTest.php

require_once __DIR__ . '/../BaseTestCase.php';

class FakerIntegrationTest extends BaseTestCase
{
    private $faker;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create('es_ES'); // Español de España
    }
    
    public function testFakerGeneraNombres()
    {
        $nombre = $this->faker->name;
        $this->assertIsString($nombre);
        $this->assertNotEmpty($nombre);
    }
    
    public function testFakerGeneraEmails()
    {
        $email = $this->faker->email;
        $this->assertIsString($email);
        $this->assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }
    
    public function testFakerGeneraFechas()
    {
        $fecha = $this->faker->date('Y-m-d');
        $this->assertIsString($fecha);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $fecha);
    }
    
    public function testFakerGeneraNumerosEnRango()
    {
        $numero = $this->faker->numberBetween(1, 100);
        $this->assertIsInt($numero);
        $this->assertGreaterThanOrEqual(1, $numero);
        $this->assertLessThanOrEqual(100, $numero);
    }
}