<?php 
include 'header.php';

// Verificar que hay estructura analizada
if(!isset($_SESSION['estructura_analizada']) || empty($_SESSION['estructura_analizada'])) {
    header('Location: generardata.php');
    exit();
}

$tablas = $_SESSION['estructura_analizada'];
$db_type = $_SESSION['db_type'] ?? 'sql';
$plan_usuario = $_SESSION['plan_usuario'] ?? 'gratuito'; // Asegura que el plan esté definido
?>

<div class="tab-container">
    <div class="tabs">
        <a href="generardata.php" class="tab">Input de Scripts</a>
        <a href="configuracion.php" class="tab active">Configuración</a>
        <a href="resultados.php" class="tab">Resultados</a>
    </div>
    
    <div class="tab-content">
        <?php if(isset($_SESSION['exito'])): ?>
            <div class="success-message">
                <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <h2 class="content-title">⚙️ Configuración de Generación de Datos</h2>
        
        <div class="config-summary">
            <h3>📊 Resumen de Estructura Detectada</h3>
            <div class="summary-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo count($tablas); ?></span>
                    <span class="stat-label">Tablas</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">
                        <?php 
                        $total_columnas = 0;
                        foreach($tablas as $tabla) {
                            $total_columnas += count($tabla['columnas']);
                        }
                        echo $total_columnas;
                        ?>
                    </span>
                    <span class="stat-label">Columnas</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo strtoupper($db_type); ?></span>
                    <span class="stat-label">Tipo BD</span>
                </div>
            </div>
        </div>
        
        <form action="../../controllers/DataGeneratorController.php" method="post" id="configForm">
            <!-- Configuración Global -->
            <div class="global-config">
                <h3>🌐 Configuración Global</h3>
                <div class="config-row">
                    <div class="config-item">
                        <label for="formato_salida">📁 Formato de Salida:</label>
                        <select name="formato_salida" id="formato_salida">
                            <option value="sql">SQL (INSERT statements)</option>
                            <option value="json">JSON</option>
                            <option value="csv">CSV</option>
                            <option value="xml">XML</option>
                        </select>
                    </div>
                    
                    <div class="config-item">
                        <label for="idioma_datos">🌍 Idioma de Datos:</label>
                        <select name="idioma_datos" id="idioma_datos">
                            <option value="es_ES">Español</option>
                            <option value="en_US">English</option>
                            <option value="pt_BR">Português</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Configuración por Tabla -->
            <div class="tables-config">
                <h3>🗃️ Configuración por Tabla</h3>
                
                <?php foreach($tablas as $index => $tabla): ?>
                    <div class="table-config-card">
                        <div class="table-header">
                            <h4>📋 Tabla: <?php echo htmlspecialchars($tabla['nombre']); ?></h4>
                            <div class="table-controls">
                                <label for="cantidad_<?php echo $index; ?>">Registros a generar:</label>
                                <input type="number" 
                                       name="cantidad[<?php echo $index; ?>]" 
                                       id="cantidad_<?php echo $index; ?>" 
                                       value="50" 
                                       min="1" 
                                       max="<?php echo $plan_usuario === 'premium' ? '10000' : '500'; ?>"
                                       class="cantidad-input">
                                
                                <?php if($plan_usuario === 'gratuito'): ?>
                                    <small class="limit-warning">Máximo 500 registros (plan gratuito)</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="columns-preview">
                            <div class="columns-grid">
                                <?php foreach($tabla['columnas'] as $col_index => $columna): ?>
                                    <div class="column-item">
                                        <div class="column-info">
                                            <span class="column-name"><?php echo htmlspecialchars($columna['nombre']); ?></span>
                                            <span class="column-type"><?php echo $columna['tipo_sql']; ?></span>
                                        </div>
                                        
                                        <div class="column-generation">
                                            <select name="tipo_generacion[<?php echo $index; ?>][<?php echo $col_index; ?>]" 
                                                    class="generation-type">
                                                <?php 
                                                $opciones_generacion = [
                                                    'auto_increment' => '🔢 Auto Incremento',
                                                    'enum_values' => '🎲 Valores ENUM',
                                                    'nombre_persona' => '👤 Nombre de Persona',
                                                    'email' => '📧 Email',
                                                    'telefono' => '📞 Teléfono',
                                                    'direccion' => '🏠 Dirección',
                                                    'fecha' => '📅 Fecha',
                                                    'fecha_hora' => '⏰ Fecha y Hora',
                                                    'numero_entero' => '🔢 Número Entero',
                                                    'numero_decimal' => '💰 Número Decimal',
                                                    'texto_aleatorio' => '📝 Texto Aleatorio',
                                                    'booleano' => '✅ Verdadero/Falso',
                                                    'foreign_key' => '🔗 Clave Foránea',
                                                    'personalizado' => '🎯 Valor Personalizado'
                                                ];
                                                
                                                foreach($opciones_generacion as $valor => $etiqueta): 
                                                    $selected = isset($columna['tipo_generacion']) && $columna['tipo_generacion'] === $valor ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo $valor; ?>" <?php echo $selected; ?>>
                                                        <?php echo $etiqueta; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            
                                            <!-- Inputs adicionales para configuración personalizada -->
                                            <div class="custom-config" style="display: none;">
                                                <input type="text" 
                                                       name="valor_personalizado[<?php echo $index; ?>][<?php echo $col_index; ?>]" 
                                                       placeholder="Valor fijo o patrón"
                                                       class="custom-value">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Datos ocultos de la tabla -->
                        <input type="hidden" name="tabla_nombre[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($tabla['nombre']); ?>">
                        <input type="hidden" name="tabla_script[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($tabla['script_original']); ?>">
                        
                        <?php foreach($tabla['columnas'] as $col_index => $columna): ?>
                            <input type="hidden" name="columna_info[<?php echo $index; ?>][<?php echo $col_index; ?>]" 
                                   value="<?php echo htmlspecialchars(json_encode($columna)); ?>">
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Configuración Avanzada -->
            <div class="advanced-config" style="display: none;">
                <h3>🔧 Configuración Avanzada</h3>
                <div class="config-row">
                    <div class="config-item">
                        <label>
                            <input type="checkbox" name="incluir_schema" value="1">
                            Incluir CREATE TABLE en la salida
                        </label>
                    </div>
                    
                    <div class="config-item">
                        <label>
                            <input type="checkbox" name="datos_relacionados" value="1">
                            Generar datos relacionados entre tablas
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn secondary-btn" onclick="toggleAdvanced()">
                    ⚙️ Configuración Avanzada
                </button>
                
                <button type="submit" class="btn primary-btn" id="generateBtn">
                    🚀 Rellenar Tablas
                </button>
                
                <a href="generardata.php" class="btn tertiary-btn">
                    ⬅️ Volver a Script
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Mostrar/ocultar configuración personalizada
document.querySelectorAll('.generation-type').forEach(select => {
    select.addEventListener('change', function() {
        const customConfig = this.parentNode.querySelector('.custom-config');
        if(this.value === 'personalizado') {
            customConfig.style.display = 'block';
        } else {
            customConfig.style.display = 'none';
        }
    });
});

// Toggle configuración avanzada
function toggleAdvanced() {
    const advanced = document.querySelector('.advanced-config');
    advanced.style.display = advanced.style.display === 'none' ? 'block' : 'none';
}

// Validar límites antes de enviar
document.getElementById('configForm').addEventListener('submit', function(e) {
    const plan = '<?php echo $plan_usuario; ?>';
    let totalRegistros = 0;
    
    document.querySelectorAll('.cantidad-input').forEach(input => {
        totalRegistros += parseInt(input.value) || 0;
    });
    
    if(plan === 'gratuito' && totalRegistros > 2000) {
        e.preventDefault();
        alert('Plan gratuito limitado a 2000 registros totales. Tienes: ' + totalRegistros);
        return false;
    }
    
    // Mostrar loading
    document.getElementById('generateBtn').disabled = true;
    document.getElementById('generateBtn').textContent = '⏳ Generando...';
});
</script>

<?php include 'footer.php'; ?>