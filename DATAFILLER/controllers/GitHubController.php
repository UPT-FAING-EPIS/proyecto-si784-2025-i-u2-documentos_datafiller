<?php
namespace App\Controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use Exception;

class GitHubController {
    private $baseUrl = 'https://api.github.com';
    private $userAgent = 'DataFiller-App/1.0';
    private $headers;
    
    public function __construct() {
        // Configurar headers para GitHub API
        $this->headers = [
            'User-Agent: ' . $this->userAgent,
            'Accept: application/vnd.github.v3+json',
            'Content-Type: application/json'
        ];
    }
    
    /**
     * Obtener repositorios de un usuario
     */
    public function getUserRepositories($username, $page = 1, $perPage = 100) {
        try {
            $url = "{$this->baseUrl}/users/{$username}/repos?sort=updated&page={$page}&per_page={$perPage}";
            
            $response = $this->makeRequest($url);
            
            if ($response['status'] === 200) {
                return [
                    'success' => true,
                    'data' => json_decode($response['body'], true),
                    'rate_limit' => $response['rate_limit']
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $this->getErrorMessage($response['status']),
                    'status' => $response['status']
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error de conexión: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener contenidos de un directorio
     */
    public function getRepositoryContents($username, $repository, $path = '') {
        try {
            $url = "{$this->baseUrl}/repos/{$username}/{$repository}/contents";
            if (!empty($path)) {
                $url .= '/' . ltrim($path, '/');
            }
            
            $response = $this->makeRequest($url);
            
            if ($response['status'] === 200) {
                $contents = json_decode($response['body'], true);
                
                // Filtrar archivos SQL, BAK y JSON
                $sqlFiles = [];
                $directories = [];
                
                if (is_array($contents)) {
                    foreach ($contents as $item) {
                        if ($item['type'] === 'file') {
                            $fileName = strtolower($item['name']);
                            if (preg_match('/\.(sql|bak|json)$/', $fileName)) {
                                $sqlFiles[] = $item;
                            }
                        } elseif ($item['type'] === 'dir') {
                            $directories[] = $item;
                        }
                    }
                } else {
                    // Es un archivo individual
                    $fileName = strtolower($contents['name']);
                    if (preg_match('/\.(sql|bak|json)$/', $fileName)) {
                        $sqlFiles[] = $contents;
                    }
                }
                
                return [
                    'success' => true,
                    'sql_files' => $sqlFiles,
                    'directories' => $directories,
                    'rate_limit' => $response['rate_limit']
                ];
                
            } else {
                return [
                    'success' => false,
                    'error' => $this->getErrorMessage($response['status']),
                    'status' => $response['status']
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error de conexión: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Descargar contenido de un archivo
     */
    public function downloadFile($downloadUrl) {
        try {
            $response = $this->makeRequest($downloadUrl, false); // No usar API headers para download
            
            if ($response['status'] === 200) {
                return [
                    'success' => true,
                    'content' => $response['body'],
                    'size' => strlen($response['body'])
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'No se pudo descargar el archivo',
                    'status' => $response['status']
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error descargando archivo: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Realizar petición HTTP
     */
    private function makeRequest($url, $useApiHeaders = true) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => $this->userAgent
        ]);
        
        if ($useApiHeaders) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        if (curl_errno($ch)) {
            throw new Exception('cURL Error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        
        // Extraer rate limit info
        $rateLimit = [
            'remaining' => $this->extractHeader($headers, 'X-RateLimit-Remaining'),
            'reset' => $this->extractHeader($headers, 'X-RateLimit-Reset'),
            'limit' => $this->extractHeader($headers, 'X-RateLimit-Limit')
        ];
        
        return [
            'status' => $httpCode,
            'headers' => $headers,
            'body' => $body,
            'rate_limit' => $rateLimit
        ];
    }
    
    /**
     * Extraer header específico
     */
    private function extractHeader($headers, $headerName) {
        if (preg_match("/{$headerName}:\s*(\d+)/i", $headers, $matches)) {
            return intval($matches[1]);
        }
        return null;
    }
    
    /**
     * Obtener mensaje de error según código HTTP
     */
    private function getErrorMessage($statusCode) {
        switch ($statusCode) {
            case 404:
                return 'Usuario, repositorio o archivo no encontrado';
            case 403:
                return 'Límite de API de GitHub alcanzado o repositorio privado';
            case 401:
                return 'No autorizado para acceder a este repositorio';
            case 422:
                return 'Parámetros inválidos';
            case 500:
                return 'Error interno del servidor de GitHub';
            default:
                return "Error HTTP {$statusCode}";
        }
    }
    
    /**
     * Verificar límite de rate limit
     */
    public function checkRateLimit() {
        try {
            $url = "{$this->baseUrl}/rate_limit";
            $response = $this->makeRequest($url);
            
            if ($response['status'] === 200) {
                $data = json_decode($response['body'], true);
                return [
                    'success' => true,
                    'rate_limit' => $data['rate']
                ];
            }
            
            return [
                'success' => false,
                'error' => 'No se pudo verificar el rate limit'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

// ===== ENDPOINT API =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    $github = new GitHubController();
    $response = ['success' => false];
    
    switch ($action) {
        case 'get_repos':
            $username = $input['username'] ?? '';
            if (empty($username)) {
                $response['error'] = 'Username requerido';
                break;
            }
            $response = $github->getUserRepositories($username);
            break;
            
        case 'get_files':
            $username = $input['username'] ?? '';
            $repository = $input['repository'] ?? '';
            $path = $input['path'] ?? '';
            
            if (empty($username) || empty($repository)) {
                $response['error'] = 'Username y repository requeridos';
                break;
            }
            
            $response = $github->getRepositoryContents($username, $repository, $path);
            break;
            
        case 'download_file':
            $downloadUrl = $input['download_url'] ?? '';
            if (empty($downloadUrl)) {
                $response['error'] = 'Download URL requerida';
                break;
            }
            $response = $github->downloadFile($downloadUrl);
            break;
            
        case 'rate_limit':
            $response = $github->checkRateLimit();
            break;
            
        default:
            $response['error'] = 'Acción no válida';
    }
    
    echo json_encode($response);
    exit;
}
?>
