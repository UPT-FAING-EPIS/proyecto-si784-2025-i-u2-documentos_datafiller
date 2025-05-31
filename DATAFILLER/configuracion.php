<?php include 'header.php'; ?>

<div class="tab-container">
    <div class="tabs">
        <a href="generardata.php" class="tab">Input de Scripts</a>
        <a href="configuracion.php" class="tab active">Configuración</a>
        <a href="resultados.php" class="tab">Resultados</a>
    </div>
    
    <div class="tab-content">
        <h2 class="content-title">Configuración General</h2>
        
        <form action="resultados.php" method="post">
            <div class="form-group">
                <label for="numRegistros">Número de registros a generar por tabla:</label>
                <input type="number" id="numRegistros" name="numRegistros" value="50" min="1" max="1000" class="number-input">
            </div>
            
            <div class="form-group">
                <h3>Formato de Salida</h3>
                <div class="radio-option">
                    <input type="radio" id="sqlInsert" name="outputFormat" value="sqlInsert" checked>
                    <label for="sqlInsert">Sentencias SQL INSERT</label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="json" name="outputFormat" value="json">
                    <label for="json">JSON</label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="csv" name="outputFormat" value="csv">
                    <label for="csv">CSV (un archivo por tabla)</label>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn primary-btn">Rellenar Tablas</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>