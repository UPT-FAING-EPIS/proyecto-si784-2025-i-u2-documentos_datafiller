<?php include 'header.php'; ?>

<div class="tab-container">
    <div class="tabs">
        <a href="generardata.php" class="tab">Input de Scripts</a>
        <a href="configuracion.php" class="tab">Configuración</a>
        <a href="resultados.php" class="tab active">Resultados</a>
    </div>
    
    <div class="tab-content">
        <div class="status-bar">
            <span>Generación completada</span>
            <span class="status-count">0 registros generados</span>
        </div>
        
        <div class="result-actions">
            <button type="button" class="btn action-btn copy-btn">Copiar Todo</button>
            <div class="secondary-actions">
                <button type="button" class="btn success-btn">Descargar</button>
                <button type="button" class="btn danger-btn">Limpiar</button>
            </div>
        </div>
        
        <div class="form-group">
            <h3>Datos Generados:</h3>
            <textarea id="generatedData" class="result-output" readonly></textarea>
        </div>
    </div>
</div>

<script>
document.querySelector('.copy-btn').addEventListener('click', function() {
    var textarea = document.getElementById('generatedData');
    textarea.select();
    document.execCommand('copy');
});

document.querySelector('.danger-btn').addEventListener('click', function() {
    document.getElementById('generatedData').value = '';
});
</script>

<?php include 'footer.php'; ?>