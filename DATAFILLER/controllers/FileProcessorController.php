<?php
header('Content-Type: application/json');

class FileProcessorController {
    private $allowedTypes = ['sql', 'bak', 'json'];
    private $maxFileSize = 10 * 1024 * 1024; // 10MB

    public function processFile() {
        try {
            // Verificar que se subió un archivo - CORREGIDO EL NOMBRE
            if (!isset($_FILES['database_file']) || $_FILES['database_file']['error'] !== UPLOAD_ERR_OK) {
                return [
                    'success' => false,
                    'message' => 'No se pudo subir el archivo.'
                ];
            }

            $file = $_FILES['database_file']; // CAMBIADO DE 'file' A 'database_file'
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            
            // Obtener extensión
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            // Validar tipo de archivo
            if (!in_array($fileExtension, $this->allowedTypes)) {
                return [
                    'success' => false,
                    'message' => 'Tipo de archivo no soportado. Solo se permiten archivos .sql, .bak y .json'
                ];
            }

            // Validar tamaño
            if ($fileSize > $this->maxFileSize) {
                return [
                    'success' => false,
                    'message' => 'El archivo es demasiado grande. Máximo 10MB permitido.'
                ];
            }

            // Leer contenido del archivo
            $content = file_get_contents($fileTmpName);
            if ($content === false) {
                return [
                    'success' => false,
                    'message' => 'No se pudo leer el contenido del archivo.'
                ];
            }

            // Procesar según el tipo de archivo
            switch ($fileExtension) {
                case 'sql':
                    return $this->processSqlFile($content, $fileName);
                case 'bak':
                    return $this->processBakFile($content, $fileName);
                case 'json':
                    return $this->processJsonFile($content, $fileName);
                default:
                    return [
                        'success' => false,
                        'message' => 'Tipo de archivo no reconocido.'
                    ];
            }

        } catch (Exception $e) {
            error_log("Error procesando archivo: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno procesando el archivo.'
            ];
        }
    }

    private function processSqlFile($content, $fileName) {
        // Limpiar el contenido SQL
        $cleanContent = $this->cleanSqlContent($content);
        
        // Contar tablas
        $tableCount = substr_count(strtoupper($cleanContent), 'CREATE TABLE');
        
        if ($tableCount === 0) {
            return [
                'success' => false,
                'message' => 'No se encontraron declaraciones CREATE TABLE en el archivo SQL.'
            ];
        }

        return [
            'success' => true,
            'message' => "Archivo SQL procesado exitosamente. Se encontraron {$tableCount} tablas.",
            'content' => $cleanContent,
            'tables_count' => $tableCount
        ];
    }

    private function processBakFile($content, $fileName) {
        // Los archivos .bak pueden contener SQL como texto
        $sqlContent = $this->extractSqlFromBak($content);
        
        if (empty($sqlContent)) {
            return [
                'success' => false,
                'message' => 'No se pudo extraer contenido SQL del archivo .bak. Asegúrate de que sea un backup en formato de texto.'
            ];
        }

        $cleanContent = $this->cleanSqlContent($sqlContent);
        $tableCount = substr_count(strtoupper($cleanContent), 'CREATE TABLE');

        return [
            'success' => true,
            'message' => "Archivo BAK procesado exitosamente. Se encontraron {$tableCount} tablas.",
            'content' => $cleanContent,
            'tables_count' => $tableCount
        ];
    }

    private function processJsonFile($content, $fileName) {
        // Decodificar JSON
        $jsonData = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'message' => 'El archivo JSON no tiene un formato válido.'
            ];
        }

        // Convertir JSON a SQL CREATE TABLE
        $sqlContent = $this->convertJsonToSql($jsonData);
        
        if (empty($sqlContent)) {
            return [
                'success' => false,
                'message' => 'No se pudo generar SQL a partir de la estructura JSON.'
            ];
        }

        $tableCount = substr_count(strtoupper($sqlContent), 'CREATE TABLE');

        return [
            'success' => true,
            'message' => "Archivo JSON procesado exitosamente. Se encontraron {$tableCount} tablas.",
            'content' => $sqlContent,
            'tables_count' => $tableCount
        ];
    }

    private function cleanSqlContent($content) {
        // Remover comentarios de estilo MySQL
        $content = preg_replace('/^\s*\/\*!.*?\*\/;\s*$/m', '', $content);
        
        // Remover comandos SET
        $content = preg_replace('/^\s*SET\s+.*?;\s*$/m', '', $content);
        
        // Remover USE database
        $content = preg_replace('/^\s*USE\s+.*?;\s*$/m', '', $content);
        
        // Remover CREATE DATABASE
        $content = preg_replace('/^\s*CREATE\s+DATABASE.*?;\s*$/m', '', $content);
        
        // Remover comentarios de línea
        $content = preg_replace('/--.*$/m', '', $content);
        
        // Remover comentarios de bloque
        $content = preg_replace('/\/\*.*?\*\//s', '', $content);
        
        // Limpiar líneas vacías excesivas
        $content = preg_replace('/\n\s*\n+/', "\n\n", $content);
        
        // Limpiar espacios
        $content = trim($content);
        
        return $content;
    }

    private function extractSqlFromBak($content) {
        // Intentar encontrar declaraciones CREATE TABLE en contenido
        if (preg_match_all('/CREATE\s+TABLE.*?;/is', $content, $matches)) {
            return implode("\n\n", $matches[0]);
        }
        
        // Si no es exitoso, devolver el contenido original limpio
        return $this->cleanSqlContent($content);
    }

    private function convertJsonToSql($jsonData) {
        $sql = '';
        
        // Si es un array de objetos, intentar generar CREATE TABLE
        if (isset($jsonData['tables']) && is_array($jsonData['tables'])) {
            foreach ($jsonData['tables'] as $tableName => $tableData) {
                $sql .= $this->generateCreateTableFromJson($tableName, $tableData);
            }
        } else if (is_array($jsonData)) {
            // Si es un array simple, generar una tabla genérica
            $tableName = 'data_table';
            $sql .= $this->generateCreateTableFromArray($tableName, $jsonData);
        }
        
        return $sql;
    }

    private function generateCreateTableFromJson($tableName, $tableData) {
        $sql = "CREATE TABLE `{$tableName}` (\n";
        $columns = [];
        
        if (isset($tableData['columns']) && is_array($tableData['columns'])) {
            foreach ($tableData['columns'] as $columnName => $columnData) {
                $columnDef = "  `{$columnName}` ";
                
                // Determinar tipo de datos
                $type = $columnData['type'] ?? 'VARCHAR(255)';
                $columnDef .= strtoupper($type);
                
                // Agregar modificadores
                if (isset($columnData['null']) && $columnData['null'] === false) {
                    $columnDef .= ' NOT NULL';
                }
                
                if (isset($columnData['primary']) && $columnData['primary'] === true) {
                    $columnDef .= ' PRIMARY KEY';
                }
                
                if (isset($columnData['auto_increment']) && $columnData['auto_increment'] === true) {
                    $columnDef .= ' AUTO_INCREMENT';
                }
                
                $columns[] = $columnDef;
            }
        }
        
        $sql .= implode(",\n", $columns);
        $sql .= "\n);\n\n";
        
        return $sql;
    }

    private function generateCreateTableFromArray($tableName, $data) {
        if (empty($data)) return '';
        
        $sql = "CREATE TABLE `{$tableName}` (\n";
        $columns = [];
        
        // Analizar el primer elemento para determinar estructura
        $firstRow = $data[0];
        if (is_array($firstRow)) {
            foreach ($firstRow as $key => $value) {
                $type = $this->inferDataType($value);
                $columns[] = "  `{$key}` {$type}";
            }
        }
        
        $sql .= implode(",\n", $columns);
        $sql .= "\n);\n\n";
        
        return $sql;
    }

    private function inferDataType($value) {
        if (is_int($value)) {
            return 'INT';
        } elseif (is_float($value)) {
            return 'DECIMAL(10,2)';
        } elseif (is_bool($value)) {
            return 'BOOLEAN';
        } elseif (is_string($value)) {
            $length = strlen($value);
            if ($length <= 50) return 'VARCHAR(100)';
            if ($length <= 255) return 'VARCHAR(255)';
            return 'TEXT';
        } else {
            return 'VARCHAR(255)';
        }
    }

    // MÉTODO SEPARADO PARA RESPUESTA DIRECTA (cuando se llama vía AJAX)
    public function handleDirectRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $result = $this->processFile();
            echo json_encode($result);
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }
    }
}

// Solo procesar si se llama directamente (no desde otro controlador)
if (basename($_SERVER['PHP_SELF']) === 'FileProcessorController.php') {
    $processor = new FileProcessorController();
    $processor->handleDirectRequest();
}
?>