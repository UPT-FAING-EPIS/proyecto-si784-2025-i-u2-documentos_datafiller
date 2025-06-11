<?php
namespace App\Controllers;

require_once __DIR__ . '/../vendor/autoload.php'; // <- ¡Esto es necesario!

use App\Config\Database;
use App\Models\Usuario;
use App\Controllers\DebugHelper;
use Faker\Factory as FakerFactory;

class DataGeneratorController {
    private $faker;
    private $db;
    private $usuarioModel;
    private $idioma;
    
    public function __construct($idioma = 'es_ES') {
        $this->idioma = $idioma;
        $this->faker = FakerFactory::create($idioma);
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioModel = new Usuario($this->db);
    }

    public function generarDatos($configuracion, $usuario_id) {
        try {
            // Verificar límites del usuario
            if(!$this->verificarLimitesUsuario($usuario_id, $configuracion)) {
                return [
                    'exito' => false,
                    'mensaje' => 'Has superado los límites de tu plan. Actualiza a Premium para más registros.',
                    'tipo' => 'limite_superado'
                ];
            }
            
            $resultado = [
                'formato' => $configuracion['formato_salida'],
                'datos_generados' => [],
                'estadisticas' => [
                    'total_registros' => 0,
                    'total_tablas' => 0,
                    'tiempo_generacion' => 0
                ]
            ];
            
            $tiempo_inicio = microtime(true);
            
            // ✅ ORDENAR TABLAS POR DEPENDENCIAS (padres primero)
            $configuracion['tablas'] = $this->ordenarTablasPorDependencias($configuracion['tablas']);
            
            // Generar datos para cada tabla
            foreach($configuracion['tablas'] as $index => $tabla_config) {
                $datos_tabla = $this->generarDatosTabla($tabla_config, $configuracion);
                $resultado['datos_generados'][$tabla_config['nombre']] = $datos_tabla;
                $resultado['estadisticas']['total_registros'] += count($datos_tabla);
            }
            
            $resultado['estadisticas']['total_tablas'] = count($configuracion['tablas']);
            $resultado['estadisticas']['tiempo_generacion'] = round(microtime(true) - $tiempo_inicio, 2);
            
            // Convertir al formato de salida solicitado
            $contenido_final = $this->convertirAFormato($resultado, $configuracion);
            
            // Registrar en auditoría
            $this->registrarGeneracion($usuario_id, $resultado['estadisticas']);
            
            // Guardar en sesión para mostrar en resultados
            $_SESSION['datos_generados'] = $contenido_final;
            $_SESSION['estadisticas_generacion'] = $resultado['estadisticas'];
            $_SESSION['formato_salida'] = $configuracion['formato_salida'];
            
            return [
                'exito' => true,
                'mensaje' => 'Datos generados exitosamente',
                'estadisticas' => $resultado['estadisticas'],
                'contenido' => $contenido_final
            ];
            
        } catch(Exception $e) {
            error_log("Error generando datos: " . $e->getMessage());
            return [
                'exito' => false,
                'mensaje' => 'Error interno generando los datos. Intente nuevamente.',
                'tipo' => 'error_interno'
            ];
        }
    }
    
    private function generarDatosTabla($tabla_config, $configuracion_global) {
        $nombre_tabla = $tabla_config['nombre'];
        $cantidad = $tabla_config['cantidad'];
        $columnas = $tabla_config['columnas'];
        
        $datos = [];
        $contadores = []; // Para auto increment y IDs únicos
        
        // ✅ GENERAR IDS DE REFERENCIA PRIMERO
        $referencias = $this->obtenerReferenciasForeignKeys($tabla_config);
        
        for($i = 1; $i <= $cantidad; $i++) {
            $fila = [];
            
            foreach($columnas as $col_index => $columna) {
                $valor = $this->generarValorColumna(
                    $columna, 
                    $tabla_config['tipos_generacion'][$col_index] ?? $columna['tipo_generacion'],
                    $tabla_config['valores_personalizados'][$col_index] ?? null,
                    $i,
                    $contadores,
                    $referencias
                );
                
                $fila[$columna['nombre']] = $valor;
            }
            
            $datos[] = $fila;
        }
        
        return $datos;
    }
    
    private function generarValorColumna($columna, $tipo_generacion, $valor_personalizado, $indice, &$contadores, $referencias) {
        $nombre_columna = $columna['nombre'];
        $nombre_lower = strtolower($nombre_columna);
        
        // Valor personalizado tiene prioridad
        if($tipo_generacion === 'personalizado' && !empty($valor_personalizado)) {
            return $this->procesarValorPersonalizado($valor_personalizado, $indice);
        }
        
        // ✅ ENUM - USAR VALORES DETECTADOS
        if($tipo_generacion === 'enum_values' || 
           ($columna['tipo_sql'] === 'ENUM' && !empty($columna['enum_values']))) {
            error_log("GENERANDO ENUM para {$nombre_columna}: " . print_r($columna['enum_values'], true));
            return $this->faker->randomElement($columna['enum_values']);
        }
        
        // ✅ AUTO INCREMENT (DEBE IR PRIMERO)
        if($tipo_generacion === 'auto_increment' || 
           (isset($columna['es_auto_increment']) && $columna['es_auto_increment'])) {
            if(!isset($contadores[$nombre_columna])) {
                $contadores[$nombre_columna] = 1;
            }
            return $contadores[$nombre_columna]++;
        }
        
        // ✅ FOREIGN KEY - Usar ID existente
        if($tipo_generacion === 'foreign_key' || 
           (isset($columna['es_foreign_key']) && $columna['es_foreign_key'])) {
            $tabla_ref = $columna['references_table'] ?? null;
            if($tabla_ref && isset($referencias[$tabla_ref]) && !empty($referencias[$tabla_ref])) {
                return $this->faker->randomElement($referencias[$tabla_ref]);
            }
            return rand(1, 100); // ✅ FALLBACK GENÉRICO
        }
        
        // ✅ DETECCIÓN GENÉRICA POR NOMBRE (NO HARDCODE)
        if($nombre_lower === 'password' || $nombre_lower === 'contraseña') {
            return password_hash('123456', PASSWORD_DEFAULT);
        }
        
        if(strpos($nombre_lower, 'email') !== false || strpos($nombre_lower, 'correo') !== false) {
            return $this->faker->email();
        }
        
        if(strpos($nombre_lower, 'telefono') !== false || strpos($nombre_lower, 'phone') !== false) {
            return $this->generarTelefono();
        }
        
        if(strpos($nombre_lower, 'nombre') !== false && strpos($nombre_lower, 'usuario') === false) {
            return $this->faker->name();
        }
        
        if(strpos($nombre_lower, 'direccion') !== false || strpos($nombre_lower, 'address') !== false) {
            return $this->faker->address();
        }
        
        if(strpos($nombre_lower, 'fecha') !== false || strpos($nombre_lower, 'date') !== false) {
            return $this->generarFecha($columna);
        }
        
        if(strpos($nombre_lower, 'precio') !== false || strpos($nombre_lower, 'price') !== false) {
            return $this->faker->randomFloat(2, 10, 9999);
        }
        
        // Generar según el tipo (resto igual)
        switch($tipo_generacion) {
            case 'nombre_persona':
                return $this->faker->name();
                
            case 'email':
                return $this->faker->email();
                
            case 'telefono':
                return $this->generarTelefono();
                
            case 'direccion':
                return $this->faker->address();
                
            case 'fecha':
                return $this->generarFecha($columna);
                
            case 'fecha_hora':
                return $this->generarFechaHora($columna);
                
            case 'numero_entero':
                return $this->generarNumeroEntero($columna);
                
            case 'numero_decimal':
                return $this->generarNumeroDecimal($columna);
                
            case 'precio':
                return $this->faker->randomFloat(2, 10, 9999);
                
            case 'booleano':
                return $this->faker->boolean() ? 1 : 0;
                
            case 'texto_aleatorio':
                return $this->generarTextoAleatorio($columna);
                
            case 'primary_key_manual':
                if(!isset($contadores[$nombre_columna])) {
                    $contadores[$nombre_columna] = 1;
                }
                return $contadores[$nombre_columna]++;
                
            default:
                return $this->generarPorTipoSQL($columna);
        }
    }
    
    private function generarTelefono() {
        $prefijos = ['987', '986', '985', '984', '983', '982', '981', '980'];
        return $this->faker->randomElement($prefijos) . $this->faker->numerify('######');
    }
    
    private function generarFecha($columna) {
        return $this->faker->date('Y-m-d');
    }
    
    private function generarFechaHora($columna) {
        return $this->faker->dateTime()->format('Y-m-d H:i:s');
    }
    
    private function generarNumeroEntero($columna) {
        $min = 1;
        $max = 999999;
        
        // Ajustar según longitud si está especificada
        if($columna['longitud']) {
            $max = pow(10, $columna['longitud']) - 1;
        }
        
        return $this->faker->numberBetween($min, $max);
    }
    
    private function generarNumeroDecimal($columna) {
        $decimales = $columna['decimales'] ?? 2;
        $max = $columna['longitud'] ? pow(10, $columna['longitud'] - $decimales) - 1 : 9999;
        
        return $this->faker->randomFloat($decimales, 1, $max);
    }
    
    private function generarTextoAleatorio($columna) {
        $longitud = $columna['longitud'] ?? 100;
        
        if($longitud <= 10) {
            return $this->faker->word();
        } elseif($longitud <= 50) {
            return $this->faker->words(3, true);
        } elseif($longitud <= 255) {
            return $this->faker->sentence();
        } else {
            return $this->faker->paragraph();
        }
    }
    
    private function generarPorTipoSQL($columna) {
        switch(strtoupper($columna['tipo_sql'])) {
            case 'ENUM':
                if(!empty($columna['enum_values'])) {
                    $enum_values = $columna['enum_values'];
                    $default_value = $columna['default_value'] ?? null;
                    
                    if(!empty($default_value) && $this->faker->boolean(70)) {
                        return strtolower($default_value); // ✅ DEFAULT EN MINÚSCULAS
                    }
                    return $this->faker->randomElement($enum_values);
                }
                return 'valor_default';
                
            case 'VARCHAR':
            case 'CHAR':
                return $this->generarTextoAleatorio($columna);
            case 'TEXT':
                return $this->faker->paragraph();
            case 'INT':
            case 'INTEGER':
            case 'BIGINT':
                return $this->generarNumeroEntero($columna);
            case 'DECIMAL':
            case 'FLOAT':
            case 'DOUBLE':
                return $this->generarNumeroDecimal($columna);
            case 'DATE':
                return $this->generarFecha($columna);
            case 'DATETIME':
            case 'TIMESTAMP':
                return $this->generarFechaHora($columna);
            case 'BOOLEAN':
            case 'BOOL':
                return $this->faker->boolean() ? 1 : 0;
            default:
                return $this->faker->word();
        }
    }
    
    private function procesarValorPersonalizado($valor, $indice) {
        // Procesar patrones especiales
        $valor = str_replace('{i}', $indice, $valor);
        $valor = str_replace('{random}', rand(1000, 9999), $valor);
        $valor = str_replace('{date}', date('Y-m-d'), $valor);
        
        return $valor;
    }
    
    private function convertirAFormato($resultado, $configuracion) {
        switch($configuracion['formato_salida']) {
            case 'sql':
                return $this->convertirASQL($resultado, $configuracion);
            case 'json':
                return $this->convertirAJSON($resultado);
            case 'csv':
                return $this->convertirACSV($resultado);
            case 'xml':
                return $this->convertirAXML($resultado);
            default:
                return $this->convertirASQL($resultado, $configuracion);
        }
    }
    
    private function convertirASQL($resultado, $configuracion) {
        $sql = '';
        
        // Incluir CREATE TABLE si está marcado
        if(isset($configuracion['incluir_schema']) && $configuracion['incluir_schema']) {
            $sql .= "-- Estructura de tablas\n\n";
            foreach($configuracion['tablas'] as $tabla_config) {
                $sql .= $tabla_config['script_original'] . "\n\n";
            }
            $sql .= "-- Datos generados\n\n";
        }
        
        foreach($resultado['datos_generados'] as $nombre_tabla => $datos) {
            if(empty($datos)) continue;
            
            $sql .= "-- Datos para la tabla `$nombre_tabla`\n";
            
            // Obtener nombres de columnas
            $columnas = array_keys($datos[0]);
            $nombres_columnas = '`' . implode('`, `', $columnas) . '`';
            
            $sql .= "INSERT INTO `$nombre_tabla` ($nombres_columnas) VALUES\n";
            
            $valores_filas = [];
            foreach($datos as $fila) {
                $valores = [];
                foreach($fila as $valor) {
                    if(is_null($valor)) {
                        $valores[] = 'NULL';
                    } elseif(is_string($valor)) {
                        $valores[] = "'" . addslashes($valor) . "'";
                    } else {
                        $valores[] = $valor;
                    }
                }
                $valores_filas[] = '(' . implode(', ', $valores) . ')';
            }
            
            $sql .= implode(",\n", $valores_filas) . ";\n\n";
        }
        
        return $sql;
    }
    
    private function convertirAJSON($resultado) {
        return json_encode($resultado['datos_generados'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    private function convertirACSV($resultado) {
        $csv = '';
        
        foreach($resultado['datos_generados'] as $nombre_tabla => $datos) {
            if(empty($datos)) continue;
            
            $csv .= "# Tabla: $nombre_tabla\n";
            
            // Encabezados
            $encabezados = array_keys($datos[0]);
            $csv .= implode(',', $encabezados) . "\n";
            
            // Datos
            foreach($datos as $fila) {
                $valores = [];
                foreach($fila as $valor) {
                    // Escapar comillas y envolver en comillas si contiene comas
                    if(is_string($valor) && (strpos($valor, ',') !== false || strpos($valor, '"') !== false)) {
                        $valor = '"' . str_replace('"', '""', $valor) . '"';
                    }
                    $valores[] = $valor;
                }
                $csv .= implode(',', $valores) . "\n";
            }
            
            $csv .= "\n";
        }
        
        return $csv;
    }
    
    private function convertirAXML($resultado) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<database>' . "\n";
        
        foreach($resultado['datos_generados'] as $nombre_tabla => $datos) {
            $xml .= "  <table name=\"$nombre_tabla\">\n";
            
            foreach($datos as $fila) {
                $xml .= "    <row>\n";
                foreach($fila as $columna => $valor) {
                    $valor_escapado = htmlspecialchars($valor);
                    $xml .= "      <$columna>$valor_escapado</$columna>\n";
                }
                $xml .= "    </row>\n";
            }
            
            $xml .= "  </table>\n";
        }
        
        $xml .= '</database>';
        
        return $xml;
    }
    
    private function verificarLimitesUsuario($usuario_id, $configuracion) {
        $usuario_info = $this->usuarioModel->obtenerInfoUsuario($usuario_id);
        
        if($usuario_info['plan'] === 'premium') {
            return true; // Sin límites
        }
        
        // Calcular total de registros
        $total_registros = 0;
        foreach($configuracion['tablas'] as $tabla) {
            $total_registros += $tabla['cantidad'];
        }
        
        // Plan gratuito: máximo 2000 registros totales
        return $total_registros <= 2000;
    }
    
    private function registrarGeneracion($usuario_id, $estadisticas) {
        try {
            $query = "INSERT INTO tbauditoria_consultas (usuario_id, tipo_consulta, cantidad_registros, formato_exportacion, fecha_consulta, ip_usuario) 
                      VALUES (:usuario_id, 'generacion_datos', :cantidad, :formato, NOW(), :ip)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':cantidad', $estadisticas['total_registros']);
            $stmt->bindParam(':formato', $_POST['formato_salida']);
            $stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
            $stmt->execute();
        } catch(Exception $e) {
            error_log("Error registrando generación: " . $e->getMessage());
        }
    }
    
     private function obtenerReferenciasForeignKeys($tabla_config) {
        $referencias = [];
        
        if(!isset($tabla_config['columnas'])) {
            return $referencias;
        }
        
        // ✅ MAPEAR TABLA -> CANTIDAD DE REGISTROS
        $cantidades_por_tabla = [];
        if(isset($_SESSION['estructura_analizada'])) {
            foreach($_SESSION['estructura_analizada'] as $index => $tabla_info) {
                $cantidad = $_POST['cantidad'][$index] ?? 50;
                $cantidades_por_tabla[$tabla_info['nombre']] = $cantidad;
            }
        }
        
        foreach($tabla_config['columnas'] as $columna) {
            if(isset($columna['es_foreign_key']) && $columna['es_foreign_key']) {
                $tabla_ref = $columna['references_table'];
                
                // ✅ USAR CANTIDAD REAL DE LA TABLA REFERENCIADA
                $cantidad_ref = $cantidades_por_tabla[$tabla_ref] ?? 50;
                $referencias[$tabla_ref] = range(1, $cantidad_ref);
                
                DebugHelper::log("FK REAL: {$columna['nombre']} -> $tabla_ref (IDs: 1-$cantidad_ref)");
            }
        }
        
        return $referencias;
    }
    
    private function ordenarTablasPorDependencias($tablas) {
        $tablas_ordenadas = [];
        $tablas_con_fks = [];
        
        // ✅ SEPARAR tablas sin FK (padres) y con FK (hijas)
        foreach($tablas as $tabla) {
            $tiene_fk = false;
            foreach($tabla['columnas'] as $columna) {
                if(isset($columna['es_foreign_key']) && $columna['es_foreign_key']) {
                    $tiene_fk = true;
                    break;
                }
            }
            
            if($tiene_fk) {
                $tablas_con_fks[] = $tabla;
            } else {
                $tablas_ordenadas[] = $tabla;
            }
        }
        
        // ✅ AGREGAR tablas con FK al final
        foreach($tablas_con_fks as $tabla) {
            $tablas_ordenadas[] = $tabla;
        }
        
        DebugHelper::log("ORDEN DE GENERACIÓN:");
        foreach($tablas_ordenadas as $i => $tabla) {
            DebugHelper::log("$i. {$tabla['nombre']}");
        }
        
        return $tablas_ordenadas;
    }
}

// Procesar la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    if (!isset($_SESSION['usuario'])) {
        header('Location: ../views/Auth/login.php');
        exit();
    }
    
    // Procesar configuración del formulario
    $configuracion = [
        'formato_salida' => $_POST['formato_salida'] ?? 'sql',
        'idioma_datos' => $_POST['idioma_datos'] ?? 'es_ES',
        'incluir_schema' => isset($_POST['incluir_schema']),
        'datos_relacionados' => isset($_POST['datos_relacionados']),
        'tablas' => []
    ];
    
    // Procesar cada tabla
    foreach ($_POST['tabla_nombre'] as $index => $nombre_tabla) {
        $cantidad = intval($_POST['cantidad'][$index] ?? 50);
        $columnas = [];
        $tipos_generacion = [];
        $valores_personalizados = [];
        foreach ($_POST['columna_info'][$index] as $col_index => $columna_json) {
            $columna = json_decode($columna_json, true);
            $columnas[] = $columna;
            $tipos_generacion[$col_index] = $_POST['tipo_generacion'][$index][$col_index] ?? $columna['tipo_generacion'];
            $valores_personalizados[$col_index] = $_POST['valor_personalizado'][$index][$col_index] ?? null;
        }
        $configuracion['tablas'][] = [
            'nombre' => $nombre_tabla,
            'cantidad' => $cantidad,
            'columnas' => $columnas,
            'tipos_generacion' => $tipos_generacion,
            'valores_personalizados' => $valores_personalizados,
            'script_original' => $_POST['tabla_script'][$index] ?? ''
        ];
    }
    
    // Generar datos
    $generator = new DataGeneratorController($configuracion['idioma_datos']);
    $resultado = $generator->generarDatos($configuracion, $_SESSION['usuario']['id']);
    
    if ($resultado['exito']) {
        $_SESSION['exito'] = "¡Datos generados exitosamente! " . 
                         "{$resultado['estadisticas']['total_registros']} registros en " .
                         "{$resultado['estadisticas']['total_tablas']} tablas. " .
                         "Tiempo: {$resultado['estadisticas']['tiempo_generacion']}s";
        header('Location: ../views/User/resultados.php');
    } else {
        $_SESSION['error'] = $resultado['mensaje'];
        header('Location: ../views/User/configuracion.php');
    }
    exit();
}