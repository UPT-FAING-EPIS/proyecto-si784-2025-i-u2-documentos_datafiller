<?php include 'header.php'; ?>

<div class="tab-container">
    <div class="tabs">
        <a href="generardata.php" class="tab active">Input de Scripts</a>
        <a href="configuracion.php" class="tab">Configuración</a>
        <a href="resultados.php" class="tab">Resultados</a>
    </div>
    
    <div class="tab-content">
        <!-- MENSAJES DE ÉXITO/ERROR -->
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
        
        <?php if(isset($_SESSION['usuario'])): ?>
            <div class="user-welcome">
                <h2 class="content-title">
                    ¡Hola <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>! 
                    Ingrese su script de definición de tablas:
                </h2>
                <div class="plan-status">
                    <span class="plan-badge plan-<?php echo $plan_usuario; ?>">
                        Plan <?php echo ucfirst($plan_usuario); ?>
                    </span>
                    <?php if($plan_usuario === 'gratuito'): ?>
                        <span class="consultas-restantes">
                            Consultas restantes hoy: <?php echo max(0, $consultas_restantes); ?>/3
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <h2 class="content-title">Ingrese su script de definición de tablas:</h2>
        <?php endif; ?>
        
        <form action="../../controllers/SqlAnalyzerController.php" method="post" enctype="multipart/form-data">
            <!-- SECCIÓN: Selector de archivos -->
            <div class="file-upload-section">
                <h3>📁 Opción 1: Subir archivo de base de datos</h3>
                <div class="file-upload-container">
                    <div class="file-upload-area" id="fileUploadArea">
                        <div class="file-upload-content">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17,8 12,3 7,8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <p class="file-upload-text">
                                <strong>Arrastra tu archivo aquí</strong> o 
                                <label for="fileInput" class="file-upload-link">haz clic para seleccionar</label>
                            </p>
                            <p class="file-upload-hint">
                                Archivos soportados: .sql, .bak, .json (máximo 10MB)
                            </p>
                        </div>
                        <input type="file" id="fileInput" name="database_file" accept=".sql,.bak,.json" style="display: none;">
                    </div>
                    
                    <!-- Información del archivo seleccionado -->
                    <div class="file-info" id="fileInfo" style="display: none;">
                        <div class="file-details">
                            <span class="file-icon">📄</span>
                            <div class="file-text">
                                <span class="file-name" id="fileName"></span>
                                <span class="file-size" id="fileSize"></span>
                            </div>
                            <button type="button" class="remove-file" id="removeFile">✕</button>
                        </div>
                        <button type="button" class="btn secondary-btn" id="processFile">
                            🔄 Procesar Archivo
                        </button>
                    </div>
                </div>
            </div>

            <!-- SEPARADOR -->
            <div class="separator">
                <div class="separator-line"></div>
                <span class="separator-text">O</span>
                <div class="separator-line"></div>
            </div>

            <!-- SECCIÓN: GitHub -->
            <div class="github-input-section">
                <h3>🐙 Opción 2: Cargar desde repositorio de GitHub</h3>
                <div class="github-container">
                    <div class="github-step">
                        <label>👤 Usuario/Organización de GitHub:</label>
                        <input type="text" id="githubUser" placeholder="ejemplo: tu-usuario" class="github-input">
                    </div>
                    
                    <div class="github-step">
                        <label>📁 Repositorio:</label>
                        <select id="githubRepo" class="github-select" disabled>
                            <option value="">Primero selecciona un usuario</option>
                        </select>
                        <button type="button" id="loadRepos" class="btn small-btn" disabled>🔄 Cargar Repositorios</button>
                    </div>
                    
                    <div class="github-step">
                        <label>📂 Ruta del archivo (opcional):</label>
                        <input type="text" id="githubPath" placeholder="ejemplo: database/schema.sql o deja vacío para buscar en raíz" class="github-input" disabled>
                    </div>
                    
                    <div class="github-step">
                        <label>📄 Archivos disponibles:</label>
                        <select id="githubFiles" class="github-select" disabled>
                            <option value="">Selecciona repositorio y ruta primero</option>
                        </select>
                        <button type="button" id="loadFiles" class="btn small-btn" disabled>🔍 Buscar Archivos .sql</button>
                    </div>
                    
                    <div class="github-actions">
                        <button type="button" id="previewGithubFile" class="btn secondary-btn" disabled>
                            👁️ Vista Previa
                        </button>
                        <button type="button" id="loadGithubFile" class="btn primary-btn" disabled>
                            ⬇️ Cargar Archivo
                        </button>
                    </div>
                    
                    <!-- Preview del archivo -->
                    <div class="github-preview" id="githubPreview" style="display: none;">
                        <div class="preview-header">
                            <h4>Vista previa del archivo:</h4>
                            <span class="preview-info" id="previewInfo"></span>
                        </div>
                        <pre class="preview-content" id="previewContent"></pre>
                    </div>
                </div>
            </div>

            <!-- SEPARADOR -->
            <div class="separator">
                <div class="separator-line"></div>
                <span class="separator-text">O</span>
                <div class="separator-line"></div>
            </div>

            <!-- SECCIÓN: Textarea manual -->
            <div class="manual-input-section">
                <h3>✍️ Opción 3: Escribir/Pegar script manualmente</h3>
                <div class="form-group">
                    <div class="textarea-header">
                        <span class="textarea-label">Script de definición de tablas:</span>
                        <button type="button" class="btn small-btn" id="clearScript">🗑️ Limpiar</button>
                    </div>
                    <textarea name="script" id="script" class="script-input" placeholder="Ejemplo:
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    email VARCHAR(100),
    fecha_registro DATE
);

CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(200),
    precio DECIMAL(10,2),
    categoria VARCHAR(100)
);"></textarea>
                </div>
            </div>
            
            <div class="form-group">
                <h3>🗄️ Tipo de base de datos:</h3>
                <div class="radio-options">
                    <div class="radio-option">
                        <input type="radio" id="sql" name="dbType" value="sql" checked>
                        <label for="sql">
                            <span class="radio-icon">🔹</span>
                            SQL (MySQL, PostgreSQL, SQLite, SQL Server)
                        </label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="nosql" name="dbType" value="nosql">
                        <label for="nosql">
                            <span class="radio-icon">🔸</span>
                            NoSQL (MongoDB, formato JSON)
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="status-bar">
                <span id="statusText">Listo para analizar su script</span>
                <span class="status-count" id="statusCount">0 tablas detectadas</span>
            </div>
            
            <div class="form-actions">
                <?php if(isset($_SESSION['usuario'])): ?>
                    <?php if($plan_usuario === 'gratuito' && $consultas_restantes <= 0): ?>
                        <button type="button" class="btn primary-btn" disabled>
                            Sin consultas disponibles hoy
                        </button>
                        <p class="warning-text">
                            Has agotado tus consultas diarias. 
                            <a href="../Auth/promocion_planes.php">¡Actualiza a Premium!</a>
                        </p>
                    <?php else: ?>
                        <button type="submit" class="btn primary-btn">
                            🚀 Analizar Estructura
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <button type="button" class="btn primary-btn" disabled>
                        Analizar Estructura
                    </button>
                    <p class="warning-text">
                        <a href="../Auth/login.php">Debe iniciar sesión</a> para analizar estructuras
                    </p>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Loading overlay -->
<div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <p>Procesando archivo...</p>
    </div>
</div>

<script src="../../public/js/file-upload.js"></script>
<script src="../../public/js/github-integration.js"></script>

<?php include 'footer.php'; ?>