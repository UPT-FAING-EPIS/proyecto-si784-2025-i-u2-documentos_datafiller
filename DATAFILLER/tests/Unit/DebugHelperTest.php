<?php
namespace App\Tests\Unit;


use PHPUnit\Framework\TestCase;
use App\Controllers\DebugHelper;

class DebugHelperTest extends TestCase
{
    private $logDir;
    private $logFile;

    protected function setUp(): void
    {
        $this->logDir = __DIR__ . '/../../logs';
        $this->logFile = $this->logDir . '/debug.log';

        // Limpia antes de cada test
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
        if (file_exists($this->logDir)) {
            rmdir($this->logDir);
        }
    }

    protected function tearDown(): void
    {
        // Limpia después de cada test
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
        if (file_exists($this->logDir)) {
            rmdir($this->logDir);
        }
    }

    public function testLogCreatesDirectoryAndFileAndWritesMessage()
    {
        $message = "Mensaje de prueba";

        DebugHelper::log($message);

        $this->assertDirectoryExists($this->logDir);
        $this->assertFileExists($this->logFile);

        $contenido = file_get_contents($this->logFile);
        $this->assertStringContainsString($message, $contenido);
        // Verifica formato de fecha
        $this->assertMatchesRegularExpression('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] ' . preg_quote($message, '/') . '/m', $contenido);
    }

    public function testLogAppendsMessages()
    {
        $msg1 = "Primer mensaje";
        $msg2 = "Segundo mensaje";

        DebugHelper::log($msg1);
        DebugHelper::log($msg2);

        $contenido = file_get_contents($this->logFile);
        $this->assertStringContainsString($msg1, $contenido);
        $this->assertStringContainsString($msg2, $contenido);

        $lines = explode("\n", trim($contenido));
        $this->assertStringContainsString($msg1, $lines[0]);
        $this->assertStringContainsString($msg2, $lines[1]);
    }
}