<?php
use PHPUnit\Framework\TestCase;
use App\Controllers\GitHubController;

class GitHubControllerTest extends TestCase
{
    /** 
     * Crea un mock de GitHubController con makeRequest mockeado.
     */
    private function getControllerWithMockedMakeRequest($makeRequestReturn)
    {
        $mock = $this->getMockBuilder(GitHubController::class)
            ->onlyMethods(['makeRequest'])
            ->getMock();

        $mock->method('makeRequest')->willReturn($makeRequestReturn);

        return $mock;
    }

    public function testGetUserRepositoriesSuccess()
    {
        $fakeBody = json_encode([['id'=>1,'name'=>'repo']]);
        $mockReturn = [
            'status' => 200,
            'headers' => '',
            'body' => $fakeBody,
            'rate_limit' => ['remaining'=>89, 'reset'=>1234567890, 'limit'=>90]
        ];
        $controller = $this->getControllerWithMockedMakeRequest($mockReturn);

        $result = $controller->getUserRepositories('octocat');
        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertArrayHasKey('rate_limit', $result);
    }

    public function testGetUserRepositoriesFail()
    {
        $mockReturn = [
            'status' => 404,
            'headers' => '',
            'body' => '',
            'rate_limit' => []
        ];
        $controller = $this->getControllerWithMockedMakeRequest($mockReturn);

        $result = $controller->getUserRepositories('nonexistentuser');
        $this->assertFalse($result['success']);
        $this->assertEquals('Usuario, repositorio o archivo no encontrado', $result['error']);
        $this->assertEquals(404, $result['status']);
    }

    public function testGetRepositoryContentsWithFilesAndDirs()
    {
        $contents = [
            ['type'=>'file','name'=>'backup.sql'],
            ['type'=>'file','name'=>'readme.md'],
            ['type'=>'file','name'=>'dump.bak'],
            ['type'=>'dir','name'=>'src'],
            ['type'=>'file','name'=>'config.json'],
        ];
        $mockReturn = [
            'status' => 200,
            'headers' => '',
            'body' => json_encode($contents),
            'rate_limit' => []
        ];
        $controller = $this->getControllerWithMockedMakeRequest($mockReturn);

        $result = $controller->getRepositoryContents('octocat', 'repo');
        $this->assertTrue($result['success']);
        $this->assertCount(3, $result['sql_files']); // backup.sql, dump.bak, config.json
        $this->assertCount(1, $result['directories']); // src
    }

    public function testGetRepositoryContentsWithSingleFile()
    {
        $fileContent = ['type'=>'file','name'=>'data.sql'];
        $mockReturn = [
            'status' => 200,
            'headers' => '',
            'body' => json_encode($fileContent),
            'rate_limit' => []
        ];
        $controller = $this->getControllerWithMockedMakeRequest($mockReturn);

        $result = $controller->getRepositoryContents('octocat', 'repo', 'data.sql');
        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['sql_files']);
        $this->assertCount(0, $result['directories']);
    }

    public function testGetRepositoryContentsApiError()
    {
        $mockReturn = [
            'status' => 403,
            'headers' => '',
            'body' => '',
            'rate_limit' => []
        ];
        $controller = $this->getControllerWithMockedMakeRequest($mockReturn);

        $result = $controller->getRepositoryContents('octocat', 'repo');
        $this->assertFalse($result['success']);
        $this->assertEquals('Límite de API de GitHub alcanzado o repositorio privado', $result['error']);
        $this->assertEquals(403, $result['status']);
    }

    public function testDownloadFileSuccess()
    {
        $mockReturn = [
            'status' => 200,
            'headers' => '',
            'body' => 'filecontent',
            'rate_limit' => []
        ];
        $controller = $this->getControllerWithMockedMakeRequest($mockReturn);

        $result = $controller->downloadFile('http://example.com/file.sql');
        $this->assertTrue($result['success']);
        $this->assertEquals('filecontent', $result['content']);
        $this->assertEquals(strlen('filecontent'), $result['size']);
    }

    public function testDownloadFileFail()
    {
        $mockReturn = [
            'status' => 404,
            'headers' => '',
            'body' => '',
            'rate_limit' => []
        ];
        $controller = $this->getControllerWithMockedMakeRequest($mockReturn);

        $result = $controller->downloadFile('http://example.com/file.sql');
        $this->assertFalse($result['success']);
        $this->assertEquals('No se pudo descargar el archivo', $result['error']);
        $this->assertEquals(404, $result['status']);
    }

    public function testCheckRateLimitSuccess()
    {
        $body = json_encode(['rate'=>['limit'=>60,'remaining'=>50,'reset'=>1234567]]);
        $mockReturn = [
            'status' => 200,
            'headers' => '',
            'body' => $body,
            'rate_limit' => []
        ];
        $controller = $this->getControllerWithMockedMakeRequest($mockReturn);

        $result = $controller->checkRateLimit();
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('rate_limit', $result);
        $this->assertEquals(60, $result['rate_limit']['limit']);
    }

    public function testCheckRateLimitFail()
    {
        $mockReturn = [
            'status' => 403,
            'headers' => '',
            'body' => '',
            'rate_limit' => []
        ];
        $controller = $this->getControllerWithMockedMakeRequest($mockReturn);

        $result = $controller->checkRateLimit();
        $this->assertFalse($result['success']);
        $this->assertEquals('No se pudo verificar el rate limit', $result['error']);
    }

    public function testGetErrorMessage()
    {
        $controller = new GitHubController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('getErrorMessage');
        $method->setAccessible(true);
        
        $this->assertEquals('Usuario, repositorio o archivo no encontrado', $method->invoke($controller, 404));
        $this->assertEquals('Límite de API de GitHub alcanzado o repositorio privado', $method->invoke($controller, 403));
        $this->assertEquals('No autorizado para acceder a este repositorio', $method->invoke($controller, 401));
        $this->assertEquals('Parámetros inválidos', $method->invoke($controller, 422));
        $this->assertEquals('Error interno del servidor de GitHub', $method->invoke($controller, 500));
        $this->assertEquals('Error HTTP 418', $method->invoke($controller, 418));
    }

    public function testExtractHeader()
    {
        $controller = new GitHubController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('extractHeader');
        $method->setAccessible(true);

        $headers = "X-RateLimit-Remaining: 60\r\nX-RateLimit-Reset: 1234567\r\n";
        $this->assertEquals(60, $method->invoke($controller, $headers, 'X-RateLimit-Remaining'));
        $this->assertEquals(1234567, $method->invoke($controller, $headers, 'X-RateLimit-Reset'));
        $this->assertNull($method->invoke($controller, $headers, 'X-RateLimit-Limit'));
    }
}