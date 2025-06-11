<?php
session_start();

// ✅ CREAR LOG EN EL PROYECTO
if(!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0777, true);
}

// ✅ FUNCIÓN HELPER PARA DEBUG
function debug_log($message) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents(__DIR__ . '/../logs/debug.log', "[$timestamp] $message\n", FILE_APPEND);
}

require_once '../config/database.php';
require_once '../models/Usuario.php';
require_once 'FileProcessorController.php';
require_once '../vendor/autoload.php';

use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Components\CreateDefinition;
use PhpMyAdmin\SqlParser\Statements\CreateStatement;

class SqlAnalyzerController {
    private $db;
    private $usuarioModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioModel = new Usuario($this->db);
    }
    
    public function analizarEstructura($script, $dbType, $usuario_id) {
        try {
            // Verificar límites de consultas
            if(!$this->verificarLimitesUsuario($usuario_id)) {
                return [
                    'exito' => false,
                    'mensaje' => 'Has agotado tus consultas diarias. Actualiza a Premium para consultas ilimitadas.',
                    'tipo' => 'limite_consultas'
                ];
            }
            
            // ✅ USAR LIBRERÍA SQL PARSER
            $tablas = $this->extraerTablasConParser($script);
            
            if(empty($tablas)) {
                return [
                    'exito' => false,
                    'mensaje' => 'No se encontraron declaraciones CREATE TABLE válidas en el script.',
                    'tipo' => 'sin_tablas'
                ];
            }
            
            // Analizar cada tabla
            $estructura_analizada = [];
            foreach($tablas as $tabla) {
                $estructura_analizada[] = $tabla; // Ya viene analizada del parser
            }
            
            // Registrar consulta en auditoría
            $this->registrarConsulta($usuario_id, 'analisis_estructura', count($tablas));
            
            // Guardar en sesión para siguiente paso
            $_SESSION['estructura_analizada'] = $estructura_analizada;
            $_SESSION['db_type'] = $dbType;
            
            return [
                'exito' => true,
                'mensaje' => 'Estructura analizada correctamente',
                'tablas' => $estructura_analizada,
                'total_tablas' => count($tablas)
            ];
            
        } catch(Exception $e) {
            error_log("Error analizando estructura: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error interno analizando la estructura. Verifique el formato del script.',
                'tipo' => 'error_interno'
            ];
        }
    }
    
    // ✅ NUEVO MÉTODO CON LIBRERÍA SQL PARSER
    private function extraerTablasConParser($script) {
        // ✅ FORZAR USO DEL FALLBACK (más confiable)
        error_log("Usando método fallback para parsing SQL");
        return $this->extraerTablasFallback($script);
    }
    
    // ✅ ANALIZAR TABLA CON PARSER
    private function analizarTablaConParser($statement) {
        try {
            $nombre_tabla = $statement->name->table;
            $columnas = [];
            $foreign_keys = [];
            
            // ✅ CORREGIDO: Acceso directo a fields
            if(isset($statement->fields) && is_array($statement->fields)) {
                foreach($statement->fields as $field) {
                    if($field instanceof \PhpMyAdmin\SqlParser\Components\CreateDefinition) {
                        
                        // ✅ DETECTAR FOREIGN KEY
                        if(isset($field->key) && $field->key && 
                           isset($field->key->type) && 
                           strtoupper($field->key->type) === 'FOREIGN KEY') {
                            
                            $foreign_keys[] = [
                                'column' => $field->name,
                                'references_table' => isset($field->references->table) ? $field->references->table : null,
                                'references_column' => isset($field->references->columns[0]) ? $field->references->columns[0] : null
                            ];
                            continue;
                        }
                        
                        // ✅ PROCESAR COLUMNA NORMAL
                        $columna = $this->procesarColumnaParser($field);
                        if($columna) {
                            $columnas[] = $columna;
                        }
                    }
                }
            }
            
            // ✅ APLICAR FOREIGN KEYS
            foreach($foreign_keys as $fk) {
                foreach($columnas as &$columna) {
                    if($columna['nombre'] === $fk['column']) {
                        $columna['es_foreign_key'] = true;
                        $columna['references_table'] = $fk['references_table'];
                        $columna['references_column'] = $fk['references_column'];
                        $columna['tipo_generacion'] = 'foreign_key';
                        break;
                    }
                }
            }
            
            return [
                'nombre' => $nombre_tabla,
                'columnas' => $columnas,
                'script_original' => $statement->build()
            ];
            
        } catch(Exception $e) {
            error_log("Error analizando tabla: " . $e->getMessage());
            return null;
        }
    }
    
    // ✅ PROCESAR COLUMNA CON PARSER
    private function procesarColumnaParser($field) {
        try {
            $nombre = $field->name;
            $tipo_info = $field->type;
            
            if(!$tipo_info) return null;
            
            // ✅ DEBUG TEMPORAL
            error_log("PROCESANDO COLUMNA: $nombre");
            error_log("TIPO INFO: " . print_r($tipo_info, true));
            error_log("TIPO NAME: " . print_r($tipo_info->name, true));
            
            // ✅ CORREGIR: Manejar que name puede ser array
            $tipo_base = '';
            if(is_array($tipo_info->name)) {
                $tipo_base = strtoupper($tipo_info->name[0] ?? 'VARCHAR');
                error_log("TIPO ES ARRAY: " . $tipo_base);
            } else {
                $tipo_base = strtoupper($tipo_info->name);
                error_log("TIPO ES STRING: " . $tipo_base);
            }
            
            $parametros = isset($tipo_info->parameters) ? $tipo_info->parameters : [];
            $longitud = null;
            $decimales = null;
            $enum_values = [];
            $default_value = null;
            
            // ✅ PROCESAR PARÁMETROS SEGÚN TIPO
            if($tipo_base === 'ENUM') {
                // Para ENUM, extraer valores de los parámetros
                foreach($parametros as $param) {
                    $valor = trim($param, "'\"");
                    if($valor) {
                        $enum_values[] = $valor;
                    }
                }
                
                error_log("ENUM ENCONTRADO en $nombre: " . print_r($enum_values, true));
                
                // Si no se encontraron valores, usar defaults
                if(empty($enum_values)) {
                    $enum_values = ['default1', 'default2'];
                    error_log("NO SE ENCONTRÓ ENUM en $nombre, usando defaults");
                }
            } else {
                // Para otros tipos, longitud y decimales
                if(count($parametros) >= 1) {
                    $longitud = intval($parametros[0]);
                }
                if(count($parametros) >= 2) {
                    $decimales = intval($parametros[1]);
                }
            }
            
            // ✅ EXTRAER MODIFICADORES DE FORMA MÁS SEGURA
            $es_primary_key = false;
            $es_auto_increment = false;
            $es_not_null = false;
            
            // Revisar si hay key definida
            if(isset($field->key) && $field->key) {
                $key_type = strtoupper($field->key->type ?? '');
                if($key_type === 'PRIMARY KEY' || $key_type === 'PRIMARY') {
                    $es_primary_key = true;
                }
            }
            
            // Revisar opciones
            if(isset($field->options) && $field->options) {
                // La librería v5 maneja options diferente
                if(is_object($field->options) && isset($field->options->options)) {
                    foreach($field->options->options as $option_key => $option_value) {
                        // ✅ CORREGIR LÍNEA 236: Manejar que option_key puede ser array
                        if(is_array($option_key)) {
                            $option_name = strtoupper($option_key[0] ?? '');
                        } elseif(is_string($option_key)) {
                            $option_name = strtoupper($option_key);
                        } elseif(is_string($option_value)) {
                            $option_name = strtoupper($option_value);
                        } else {
                            continue; // Skip si no podemos procesar
                        }
                        
                        if($option_name === 'AUTO_INCREMENT') {
                            $es_auto_increment = true;
                        }
                        if($option_name === 'NOT NULL') {
                            $es_not_null = true;
                        }
                        if($option_name === 'DEFAULT') {
                            $default_value = is_string($option_value) ? trim($option_value, "'\"") : '';
                        }
                    }
                }
            }
            
            return [
                'nombre' => $nombre,
                'tipo_sql' => $tipo_base,
                'longitud' => $longitud,
                'decimales' => $decimales,
                'enum_values' => $enum_values,
                'default_value' => $default_value,
                'es_primary_key' => $es_primary_key,
                'es_auto_increment' => $es_auto_increment,
                'es_not_null' => $es_not_null,
                'es_foreign_key' => false, // Se establecerá después
                'references_table' => null,
                'references_column' => null,
                'tipo_generacion' => $this->determinarTipoGeneracion($nombre, $tipo_base, '', $enum_values, $es_auto_increment),
                'modificadores' => $this->construirModificadores($es_primary_key, $es_auto_increment, $es_not_null, $default_value)
            ];
            
        } catch(Exception $e) {
            error_log("Error procesando columna: " . $e->getMessage());
            return null;
        }
    }
    
    // ✅ CONSTRUIR MODIFICADORES PARA COMPATIBILIDAD
    private function construirModificadores($es_primary_key, $es_auto_increment, $es_not_null, $default_value) {
        $modificadores = [];
        
        if($es_not_null) $modificadores[] = 'NOT NULL';
        if($es_auto_increment) $modificadores[] = 'AUTO_INCREMENT';
        if($es_primary_key) $modificadores[] = 'PRIMARY KEY';
        if($default_value) $modificadores[] = "DEFAULT '$default_value'";
        
        return implode(' ', $modificadores);
    }
    
    // ✅ LIMPIAR SCRIPT
    private function limpiarScript($script) {
        // Remover comentarios
        $script = preg_replace('/--.*$/m', '', $script);
        $script = preg_replace('/\/\*.*?\*\//s', '', $script);
        
        // Remover comandos no necesarios
        $script = preg_replace('/^\s*SET\s+.*?;\s*$/m', '', $script);
        $script = preg_replace('/^\s*USE\s+.*?;\s*$/m', '', $script);
        
        return trim($script);
    }
    
    // ✅ FALLBACK AL MÉTODO ANTERIOR
    private function extraerTablasFallback($script) {
        $tablas = [];
        
        // ✅ PATRÓN MEJORADO PARA DETECTAR CREATE TABLE
        $patron = '/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:`?)(\w+)(?:`?)\s*\(\s*(.*?)\s*\)\s*(?:ENGINE.*?)?(?:;|$)/is';
        
        if(preg_match_all($patron, $script, $matches, PREG_SET_ORDER)) {
            foreach($matches as $match) {
                $nombre_tabla = trim($match[1], '`');
                $definicion_columnas = $match[2];
                
                $tabla = [
                    'nombre' => $nombre_tabla,
                    'columnas' => $this->extraerColumnasFallbackMejorado($definicion_columnas),
                    'script_original' => $match[0]
                ];
                
                $tablas[] = $tabla;
            }
        }
        
        return $tablas;
    }
    
    // ✅ MÉTODO FALLBACK MEJORADO PARA EXTRAER COLUMNAS
    private function extraerColumnasFallbackMejorado($definicion) {
        $columnas = [];
        $foreign_keys = [];
        
        // ✅ DETECTAR FOREIGN KEYS PRIMERO
        if(preg_match_all('/CONSTRAINT\s+`?\w+`?\s+FOREIGN\s+KEY\s+\(`?(\w+)`?\)\s+REFERENCES\s+`?(\w+)`?\s+\(`?(\w+)`?\)/i', $definicion, $fk_matches, PREG_SET_ORDER)) {
            foreach($fk_matches as $match) {
                $foreign_keys[] = [
                    'column' => $match[1],
                    'references_table' => $match[2],
                    'references_column' => $match[3]
                ];
            }
        }
        
        // ✅ PATRÓN GENÉRICO PARA FOREIGN KEY SIN CONSTRAINT
        if(preg_match_all('/FOREIGN\s+KEY\s+\(`?(\w+)`?\)\s+REFERENCES\s+`?(\w+)`?\s+\(`?(\w+)`?\)/i', $definicion, $fk2_matches, PREG_SET_ORDER)) {
            foreach($fk2_matches as $match) {
                $foreign_keys[] = [
                    'column' => $match[1],
                    'references_table' => $match[2],
                    'references_column' => $match[3]
                ];
            }
        }
        
        error_log("FOREIGN KEYS DETECTADAS: " . print_r($foreign_keys, true));
        
        // ✅ DIVIDIR POR COMAS INTELIGENTEMENTE
        $lineas = $this->dividirPorComasSeguras($definicion);
        
        // ✅ LIMPIAR Y PROCESAR CADA LÍNEA
        $columnas_procesadas = [];
        foreach($lineas as $linea) {
            $linea = trim($linea);
            
            // Ignorar líneas vacías
            if(empty($linea)) continue;
            
            // Ignorar PRIMARY KEY, FOREIGN KEY, CONSTRAINT, etc.
            if(preg_match('/^(PRIMARY\s+KEY|FOREIGN\s+KEY|CONSTRAINT|KEY\s+`|INDEX|ENGINE|CHARSET|COLLATE)/i', $linea)) {
                continue;
            }
            
            // Buscar patrón de columna: `nombre` tipo(params) modificadores
            if(preg_match('/^`?(\w+)`?\s+(\w+)(?:\(([^)]*)\))?\s*(.*)?$/i', $linea, $matches)) {
                $nombre = $matches[1];
                $tipo_base = strtoupper($matches[2]);
                $parametros = $matches[3] ?? '';
                $modificadores = strtoupper($matches[4] ?? '');
                
                error_log("COLUMNA DETECTADA: $nombre | TIPO: $tipo_base | PARAMS: $parametros | MODS: $modificadores");
                
                // Procesar ENUM
                $enum_values = [];
                if($tipo_base === 'ENUM') {
                    if(preg_match_all("/'([^']+)'/", $parametros, $enum_matches)) {
                        $enum_values = $enum_matches[1];
                    }
                }
                
                // Extraer longitud
                $longitud = null;
                $decimales = null;
                if($parametros && $tipo_base !== 'ENUM') {
                    $params = explode(',', $parametros);
                    $longitud = isset($params[0]) ? intval(trim($params[0])) : null;
                    $decimales = isset($params[1]) ? intval(trim($params[1])) : null;
                }
                
                // Extraer default
                $default_value = null;
                if(preg_match("/DEFAULT\s+'([^']+)'/i", $modificadores, $default_match)) {
                    $default_value = $default_match[1];
                } elseif(preg_match('/DEFAULT\s+(\w+)/i', $modificadores, $default_match)) {
                    $default_value = $default_match[1];
                }
                
                $columna = [
                    'nombre' => $nombre,
                    'tipo_sql' => $tipo_base,
                    'longitud' => $longitud,
                    'decimales' => $decimales,
                    'enum_values' => $enum_values,
                    'default_value' => $default_value,
                    'es_primary_key' => strpos($modificadores, 'PRIMARY KEY') !== false,
                    'es_auto_increment' => strpos($modificadores, 'AUTO_INCREMENT') !== false,
                    'es_not_null' => strpos($modificadores, 'NOT NULL') !== false,
                    'es_foreign_key' => false,
                    'references_table' => null,
                    'references_column' => null,
                    'tipo_generacion' => $this->determinarTipoGeneracion($nombre, $tipo_base, $modificadores, $enum_values, strpos($modificadores, 'AUTO_INCREMENT') !== false),
                    'modificadores' => $modificadores
                ];
                
                $columnas_procesadas[] = $columna;
            }
        }
        
        // ✅ APLICAR FOREIGN KEYS
        foreach($foreign_keys as $fk) {
            foreach($columnas_procesadas as &$columna) {
                if($columna['nombre'] === $fk['column']) {
                    $columna['es_foreign_key'] = true;
                    $columna['references_table'] = $fk['references_table'];
                    $columna['references_column'] = $fk['references_column'];
                    $columna['tipo_generacion'] = 'foreign_key';
                    break;
                }
            }
        }
        
        error_log("TOTAL COLUMNAS DETECTADAS: " . count($columnas_procesadas));
        return $columnas_procesadas;
    }
    
    // ✅ MÉTODO PARA DIVIDIR POR COMAS INTELIGENTEMENTE
    private function dividirPorComasSeguras($texto) {
        // ✅ MÉTODO SIMPLE Y DIRECTO
        $resultado = [];
        $actual = '';
        $nivel_parentesis = 0;
        $en_comillas = false;
        $caracter_comilla = '';
        
        for($i = 0; $i < strlen($texto); $i++) {
            $char = $texto[$i];
            
            // Detectar inicio/fin de comillas
            if(($char === "'" || $char === '"') && !$en_comillas) {
                $en_comillas = true;
                $caracter_comilla = $char;
            } elseif($char === $caracter_comilla && $en_comillas) {
                $en_comillas = false;
                $caracter_comilla = '';
            }
            
            // Solo procesar separadores si no estamos en comillas
            if(!$en_comillas) {
                if($char === '(') {
                    $nivel_parentesis++;
                } elseif($char === ')') {
                    $nivel_parentesis--;
                } elseif($char === ',' && $nivel_parentesis === 0) {
                    // Encontramos separador válido
                    $linea_limpia = trim($actual);
                    if(!empty($linea_limpia)) {
                        $resultado[] = $linea_limpia;
                    }
                    $actual = '';
                    continue;
                }
            }
            
            $actual .= $char;
        }
        
        // Agregar último fragmento
        $linea_limpia = trim($actual);
        if(!empty($linea_limpia)) {
            $resultado[] = $linea_limpia;
        }
        
        // ✅ DEBUG: Ver todas las líneas detectadas  
        error_log("=== DIVISIÓN POR COMAS ===");
        error_log("TEXTO COMPLETO: " . $texto); // ✅ VER TODO EL TEXTO
        error_log("LÍNEAS ENCONTRADAS: " . count($resultado));
        foreach($resultado as $i => $linea) {
            error_log("LÍNEA $i: [$linea]"); // ✅ CON CORCHETES PARA VER ESPACIOS
        }
        
        // ✅ TAMBIÉN MOSTRAR CARACTERES ESPECIALES
        error_log("TEXTO EN HEX: " . bin2hex(substr($texto, 0, 200)));
        error_log("=== FIN DIVISIÓN ===");
        
        return $resultado;
    }
    
    private function determinarTipoGeneracion($nombre, $tipo, $modificadores, $enum_values = [], $es_auto_increment = false) {
        $nombre_lower = strtolower($nombre);
        
        // ✅ AUTO INCREMENT PRIMERO
        if($es_auto_increment) {
            return 'auto_increment';
        }
        
        // ✅ ENUM DEBE IR ANTES QUE CUALQUIER OTRA COSA
        if($tipo === 'ENUM') {
            error_log("DETECTANDO ENUM PARA $nombre - Valores: " . print_r($enum_values, true));
            return 'enum_values'; // ✅ RETORNAR ENUM_VALUES
        }
        
        // Por nombre de campo (resto igual)
        if(in_array($nombre_lower, ['id', 'user_id', 'usuario_id', 'client_id'])) {
            return 'numero_entero';
        }
        
        if(strpos($nombre_lower, 'nombre') !== false || strpos($nombre_lower, 'name') !== false) {
            return 'nombre_persona';
        }
        
        if(strpos($nombre_lower, 'email') !== false || strpos($nombre_lower, 'correo') !== false) {
            return 'email';
        }
        
        if(strpos($nombre_lower, 'telefono') !== false || strpos($nombre_lower, 'phone') !== false) {
            return 'telefono';
        }
        
        if(strpos($nombre_lower, 'direccion') !== false || strpos($nombre_lower, 'address') !== false) {
            return 'direccion';
        }
        
        if(strpos($nombre_lower, 'fecha') !== false || strpos($nombre_lower, 'date') !== false) {
            return 'fecha';
        }
        
        if(strpos($nombre_lower, 'precio') !== false || strpos($nombre_lower, 'price') !== false) {
            return 'numero_decimal';
        }
        
        // Por tipo SQL
        switch($tipo) {
            case 'VARCHAR':
            case 'CHAR':
            case 'TEXT':
                return 'texto_aleatorio';
            case 'INT':
            case 'INTEGER':
            case 'BIGINT':
                return 'numero_entero';
            case 'DECIMAL':
            case 'FLOAT':
            case 'DOUBLE':
                return 'numero_decimal';
            case 'DATE':
                return 'fecha';
            case 'DATETIME':
            case 'TIMESTAMP':
                return 'fecha_hora';
            case 'BOOLEAN':
            case 'BOOL':
                return 'booleano';
            case 'ENUM':
                return 'enum_values'; // ✅ REDUNDANTE PERO SEGURO
            default:
                return 'texto_aleatorio';
        }
    }
    
    private function verificarLimitesUsuario($usuario_id) {
        $consultas_restantes = $this->usuarioModel->obtenerConsultasRestantes($usuario_id);
        return $consultas_restantes > 0;
    }
    
    private function registrarConsulta($usuario_id, $tipo, $cantidad_tablas) {
        try {
            $query = "INSERT INTO tbauditoria_consultas (usuario_id, tipo_consulta, cantidad_registros, fecha_consulta, ip_usuario) 
                      VALUES (:usuario_id, :tipo, :cantidad, NOW(), :ip)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':cantidad', $cantidad_tablas);
            $stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
            $stmt->execute();
            
            $this->usuarioModel->incrementarConsultas($usuario_id);
            
        } catch(Exception $e) {
            error_log("Error registrando consulta: " . $e->getMessage());
        }
    }
}

// Procesar la solicitud
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_SESSION['usuario'])) {
        $_SESSION['error'] = 'Debe iniciar sesión para usar esta funcionalidad.';
        header('Location: ../views/Auth/login.php');
        exit();
    }
    
    $script = $_POST['script'] ?? '';
    $dbType = $_POST['dbType'] ?? 'sql';
    $usuario_id = $_SESSION['usuario']['id'];
    
    // Si se subió un archivo, procesar primero
    if(isset($_FILES['database_file']) && $_FILES['database_file']['error'] === UPLOAD_ERR_OK) {
        $fileProcessor = new FileProcessorController();
        $fileResult = $fileProcessor->processFile();
        
        if($fileResult['success']) {
            $script = $fileResult['content'];
        } else {
            $_SESSION['error'] = 'Error procesando archivo: ' . $fileResult['message'];
            header('Location: ../views/User/generardata.php');
            exit();
        }
    }
    
    if(empty(trim($script))) {
        $_SESSION['error'] = 'Debe proporcionar un script SQL o subir un archivo válido.';
        header('Location: ../views/User/generardata.php');
        exit();
    }
    
    $analyzer = new SqlAnalyzerController();
    $resultado = $analyzer->analizarEstructura($script, $dbType, $usuario_id);
    
    if($resultado['exito']) {
        $_SESSION['exito'] = $resultado['mensaje'] . ". Se detectaron {$resultado['total_tablas']} tablas.";
        header('Location: ../views/User/configuracion.php');
    } else {
        $_SESSION['error'] = $resultado['mensaje'];
        header('Location: ../views/User/generardata.php');
    }
    exit();
}
?>