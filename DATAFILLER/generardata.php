<?php include 'header.php'; ?>

<div class="tab-container">
    <div class="tabs">
        <a href="generardata.php" class="tab active">Input de Scripts</a>
        <a href="configuracion.php" class="tab">Configuración</a>
        <a href="resultados.php" class="tab">Resultados</a>
    </div>
    
    <div class="tab-content">
        <h2 class="content-title">Ingrese su script de definición de tablas:</h2>
        <form action="configuracion.php" method="post">
            <div class="form-group">
                <textarea name="script" id="script" class="script-input" required></textarea>
            </div>
            
            <div class="form-group">
                <h3>Tipo de base de datos:</h3>
                <div class="radio-option">
                    <input type="radio" id="sql" name="dbType" value="sql" checked>
                    <label for="sql">SQL (MySQL, PostgreSQL, SQLite, SQL Server)</label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="nosql" name="dbType" value="nosql">
                    <label for="nosql">NoSQL (MongoDB, ejemplo en formato JSON)</label>
                </div>
            </div>
            
            <div class="status-bar">
                <span>Listo para analizar su script</span>
                <span class="status-count">0 tablas detectadas</span>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn primary-btn" <?php echo (!isset($_SESSION['usuario'])) ? 'disabled' : ''; ?>>
                    Analizar Estructura
                </button>
                <?php if (!isset($_SESSION['usuario'])): ?>
                <p class="warning-text">Debe iniciar sesión para analizar estructuras</p>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>