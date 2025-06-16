<?php
use PHPUnit\Framework\TestCase;
use App\Controllers\FileProcessorController;

class FileProcessorControllerTest extends TestCase
{
    private $controller;
    private $tmpFiles = [];

    protected function setUp(): void
    {
        $this->controller = new FileProcessorController();
    }

    protected function tearDown(): void
    {
        // Remove any temporary files created
        foreach ($this->tmpFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->tmpFiles = [];
        $_FILES = [];
        $_SERVER = [];
    }

    private function createUploadFile(string $content, string $extension): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'upl');
        file_put_contents($tmp, $content);
        $this->tmpFiles[] = $tmp;
        return [
            'name' => "test.$extension",
            'type' => 'application/octet-stream',
            'tmp_name' => $tmp,
            'error' => UPLOAD_ERR_OK,
            'size' => strlen($content),
        ];
    }

    public function testProcessSqlFileSuccessAndCount()
    {
        $sql = "
            CREATE TABLE users (id INT);
            -- a comment
            CREATE TABLE posts (id INT);
        ";
        $_FILES['database_file'] = $this->createUploadFile($sql, 'sql');

        $result = $this->controller->processFile();

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('2 tablas', $result['message']);
        $this->assertStringContainsString('CREATE TABLE users', $result['content']);
        $this->assertEquals(2, $result['tables_count']);
    }

    public function testProcessSqlFileNoCreateTable()
    {
        $sql = "-- only comments\nSELECT * FROM users;";
        $_FILES['database_file'] = $this->createUploadFile($sql, 'sql');

        $result = $this->controller->processFile();

        $this->assertFalse($result['success']);
        $this->assertEquals('No se encontraron declaraciones CREATE TABLE en el archivo SQL.', $result['message']);
    }

    public function testProcessBakFileExtractsSql()
    {
        $bak = "
            random header
            CREATE TABLE foo (a INT);
            CREATE TABLE bar (b INT);
            random footer
        ";
        $_FILES['database_file'] = $this->createUploadFile($bak, 'bak');

        $result = $this->controller->processFile();

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['tables_count']);
        $this->assertStringContainsString('CREATE TABLE foo', $result['content']);
    }

    public function testProcessBakFileEmptyExtraction()
    {
        $bak = "no sql here";
        $_FILES['database_file'] = $this->createUploadFile($bak, 'bak');

        $result = $this->controller->processFile();
        // In this case extractSqlFromBak returns cleaned content (empty or same), so still returns success with 0 tables
        $this->assertTrue($result['success']);
        $this->assertEquals(0, $result['tables_count']);
    }

    public function testProcessJsonFileWithTablesKey()
    {
        $json = json_encode([
            'tables' => [
                'users' => [
                    'columns' => [
                        'id' => ['type' => 'int', 'primary' => true],
                        'name' => ['type' => 'varchar(50)', 'null' => false]
                    ]
                ]
            ]
        ]);
        $_FILES['database_file'] = $this->createUploadFile($json, 'json');

        $result = $this->controller->processFile();

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('1 tablas', $result['message']);
        $this->assertMatchesRegularExpression('/CREATE TABLE `users`/', $result['content']);
    }

    public function testProcessJsonFileAsArray()
    {
        $json = json_encode([
            ['a' => 1, 'b' => 3.14],
            ['a' => 2, 'b' => 2.71],
        ]);
        $_FILES['database_file'] = $this->createUploadFile($json, 'json');

        $result = $this->controller->processFile();

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['tables_count']);
        $this->assertStringContainsString('data_table', $result['content']);
        $this->assertStringContainsString('INT', $result['content']);
        $this->assertStringContainsString('DECIMAL', $result['content']);
    }

    public function testProcessJsonFileInvalidFormat()
    {
        $_FILES['database_file'] = $this->createUploadFile('{ invalid json', 'json');

        $result = $this->controller->processFile();

        $this->assertFalse($result['success']);
        $this->assertEquals('El archivo JSON no tiene un formato válido.', $result['message']);
    }

    public function testUnsupportedFileType()
    {
        $_FILES['database_file'] = $this->createUploadFile('data', 'txt');

        $result = $this->controller->processFile();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Tipo de archivo no soportado', $result['message']);
    }

    public function testFileTooLarge()
    {
        $large = str_repeat('x', 11 * 1024 * 1024);
        $_FILES['database_file'] = [
            'name' => 'big.sql',
            'tmp_name' => tempnam(sys_get_temp_dir(), 'upl'),
            'error' => UPLOAD_ERR_OK,
            'size' => strlen($large),
            'type' => 'application/octet-stream',
        ];
        file_put_contents($_FILES['database_file']['tmp_name'], $large);
        $this->tmpFiles[] = $_FILES['database_file']['tmp_name'];

        $result = $this->controller->processFile();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('El archivo es demasiado grande', $result['message']);
    }

    public function testNoFileUploaded()
    {
        // $_FILES is empty
        $result = $this->controller->processFile();
        $this->assertFalse($result['success']);
        $this->assertEquals('No se pudo subir el archivo.', $result['message']);
    }

    
}