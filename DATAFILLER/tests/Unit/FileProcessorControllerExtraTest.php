<?php
use PHPUnit\Framework\TestCase;
use App\Controllers\FileProcessorController;

/**
 * Extend the real controller to override the exit hook for tests.
 */
class TestableFileProcessorController extends FileProcessorController
{
    /**
     * Override to prevent killing the PHPUnit process.
     */
    protected function terminate(): void
    {
        // No-op in tests
    }
}

class FileProcessorControllerExtraTest extends TestCase
{
    protected function setUp(): void
    {
        // Ensure globals are reset
        $_FILES = [];
        $_SERVER = [];
    }

    public function testHandleDirectRequestWithGetMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_FILES = [];

        ob_start();
        $controller = new TestableFileProcessorController();
        $controller->handleDirectRequest();
        $output = ob_get_clean();

        $this->assertJson($output, 'Response should be valid JSON');
        $data = json_decode($output, true);

        $this->assertFalse($data['success']);
        $this->assertSame('Método no permitido', $data['message']);
    }

    public function testHandleDirectRequestWithPostMethodNoFile(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_FILES = [];

        ob_start();
        $controller = new TestableFileProcessorController();
        $controller->handleDirectRequest();
        $output = ob_get_clean();

        $this->assertJson($output, 'Response should be valid JSON');
        $data = json_decode($output, true);

        $this->assertFalse($data['success']);
        $this->assertSame('No se pudo subir el archivo.', $data['message']);
    }

    public function testInferDataTypeReturnsCorrectSqlType(): void
    {
        $ref    = new \ReflectionClass(FileProcessorController::class);
        $method = $ref->getMethod('inferDataType');
        $method->setAccessible(true);
        $inst   = new FileProcessorController();

        // integer
        $this->assertSame('INT',             $method->invoke($inst, 42));
        // float
        $this->assertSame('DECIMAL(10,2)',   $method->invoke($inst, 3.1415));
        // boolean true/false
        $this->assertSame('BOOLEAN',         $method->invoke($inst, true));
        $this->assertSame('BOOLEAN',         $method->invoke($inst, false));
        // short string (length 50)
        $short  = str_repeat('a', 50);
        $this->assertSame('VARCHAR(100)',    $method->invoke($inst, $short));
        // medium string (>50, <=255)
        $medium = str_repeat('b', 200);
        $this->assertSame('VARCHAR(255)',    $method->invoke($inst, $medium));
        // long string (>255)
        $long   = str_repeat('c', 300);
        $this->assertSame('TEXT',            $method->invoke($inst, $long));
        // other types (null, array, object)
        $this->assertSame('VARCHAR(255)',    $method->invoke($inst, null));
        $this->assertSame('VARCHAR(255)',    $method->invoke($inst, ['x', 'y']));
        $this->assertSame('VARCHAR(255)',    $method->invoke($inst, (object)[]));
    }
}