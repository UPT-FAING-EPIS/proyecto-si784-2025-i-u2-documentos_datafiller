<?php 
include 'header.php';

// Verificar que hay datos generados
if(!isset($_SESSION['datos_generados']) || empty($_SESSION['datos_generados'])) {
    header('Location: generardata.php');
    exit();
}

$datos_generados = $_SESSION['datos_generados'];
$estadisticas = $_SESSION['estadisticas_generacion'] ?? [];
$formato_salida = $_SESSION['formato_salida'] ?? 'sql';

// Función helper para formatear tamaño de archivo
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>

<div class="tab-container">
    <div class="tabs">
        <a href="generardata.php" class="tab">Input de Scripts</a>
        <a href="configuracion.php" class="tab">Configuración</a>
        <a href="resultados.php" class="tab active">Resultados</a>
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
        
        <div class="results-header">
            <h2 class="content-title">🎉 ¡Datos Generados Exitosamente!</h2>
            
            <!-- Estadísticas -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo number_format($estadisticas['total_registros'] ?? 0); ?></span>
                        <span class="stat-label">Registros Generados</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">🗃️</div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo $estadisticas['total_tablas'] ?? 0; ?></span>
                        <span class="stat-label">Tablas Procesadas</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">⚡</div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo number_format($estadisticas['tiempo_generacion'] ?? 0, 2); ?>s</span>
                        <span class="stat-label">Tiempo de Generación</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📄</div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo strtoupper($formato_salida); ?></span>
                        <span class="stat-label">Formato de Salida</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Controles de Resultado -->
        <div class="result-controls">
            <div class="control-section">
                <h3>📁 Acciones del Archivo</h3>
                <div class="control-buttons">
                    <button onclick="copiarDatos()" class="btn secondary-btn">
                        📋 Copiar al Portapapeles
                    </button>
                    
                    <button onclick="descargarArchivo()" class="btn primary-btn">
                        💾 Descargar Archivo
                    </button>
                    
                    <button onclick="limpiarDatos()" class="btn danger-btn">
                        🗑️ Limpiar Resultados
                    </button>
                </div>
            </div>
            
            <div class="control-section">
                <h3>🔧 Opciones de Visualización</h3>
                <div class="view-options">
                    <label class="option-label">
                        <input type="checkbox" id="wrapText" checked>
                        <span>Ajustar texto</span>
                    </label>
                    
                    <label class="option-label">
                        <input type="checkbox" id="showLineNumbers">
                        <span>Mostrar números de línea</span>
                    </label>
                    
                    <select id="fontSizeSelect" class="font-size-select">
                        <option value="12">Tamaño: 12px</option>
                        <option value="14" selected>Tamaño: 14px</option>
                        <option value="16">Tamaño: 16px</option>
                        <option value="18">Tamaño: 18px</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Vista Previa del Contenido -->
        <div class="content-preview">
            <div class="preview-header">
                <h3>👁️ Vista Previa del Contenido Generado</h3>
                <div class="preview-info">
                    <span class="file-size">Tamaño: <span id="fileSize"><?php echo formatFileSize(strlen($datos_generados)); ?></span></span>
                    <span class="file-lines">Líneas: <span id="lineCount"><?php echo number_format(substr_count($datos_generados, "\n") + 1); ?></span></span>
                </div>
            </div>
            
            <div class="content-container">
                <textarea id="generatedData" class="generated-content" readonly><?php echo htmlspecialchars($datos_generados); ?></textarea>
                
                <!-- Overlay de carga para operaciones -->
                <div class="content-overlay" id="contentOverlay" style="display: none;">
                    <div class="overlay-content">
                        <div class="loading-spinner"></div>
                        <p id="overlayMessage">Procesando...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Información Adicional -->
        <div class="additional-info">
            <div class="info-section">
                <h4>💡 Información del Archivo</h4>
                <ul class="info-list">
                    <li><strong>Formato:</strong> <?php echo strtoupper($formato_salida); ?></li>
                    <li><strong>Codificación:</strong> UTF-8</li>
                    <li><strong>Generado:</strong> <?php echo date('d/m/Y H:i:s'); ?></li>
                    <li><strong>Tamaño:</strong> <?php echo formatFileSize(strlen($datos_generados)); ?></li>
                    <li><strong>Registros totales:</strong> <?php echo number_format($estadisticas['total_registros'] ?? 0); ?></li>
                </ul>
            </div>
            
            <div class="info-section">
                <h4>🚀 Siguientes Pasos</h4>
                <ul class="steps-list">
                    <li>✅ Descargar el archivo generado</li>
                    <li>📊 Importar a tu base de datos</li>
                    <li>🔍 Verificar la integridad de los datos</li>
                    <li>🎯 ¡Usar en tu proyecto!</li>
                </ul>
            </div>
        </div>
        
        <!-- Acciones Rápidas -->
        <div class="quick-actions">
            <a href="generardata.php" class="btn tertiary-btn">
                🔄 Generar Nuevos Datos
            </a>
            
            <a href="configuracion.php" class="btn secondary-btn">
                ⚙️ Ajustar Configuración
            </a>
            
            <?php if(isset($plan_usuario) && $plan_usuario === 'gratuito'): ?>
                <a href="../Auth/promocion_planes.php" class="btn premium-btn">
                    ⭐ Actualizar a Premium
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div id="confirmModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>⚠️ Confirmar Acción</h3>
            <button class="close-modal" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="confirmMessage">¿Estás seguro de que quieres realizar esta acción?</p>
        </div>
        <div class="modal-footer">
            <button class="btn secondary-btn" onclick="cerrarModal()">Cancelar</button>
            <button class="btn danger-btn" id="confirmAction">Confirmar</button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="../../public/js/results.js"></script>

<?php include 'footer.php'; ?>