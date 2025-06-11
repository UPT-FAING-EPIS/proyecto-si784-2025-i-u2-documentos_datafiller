document.addEventListener('DOMContentLoaded', function() {
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFile = document.getElementById('removeFile');
    const processFile = document.getElementById('processFile');
    const scriptTextarea = document.getElementById('script');
    const clearScript = document.getElementById('clearScript');
    const statusText = document.getElementById('statusText');
    const statusCount = document.getElementById('statusCount');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Drag and drop functionality
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        fileUploadArea.classList.add('dragover');
    });

    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
    });

    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    // Click to select file
    fileUploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            handleFile(this.files[0]);
        }
    });

    // Handle file selection
    function handleFile(file) {
        // Validate file type
        const allowedTypes = ['.sql', '.bak', '.json'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!allowedTypes.includes(fileExtension)) {
            alert('❌ Tipo de archivo no soportado. Solo se permiten archivos .sql, .bak y .json');
            return;
        }

        // Validate file size (10MB max)
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            alert('❌ El archivo es demasiado grande. Máximo 10MB permitido.');
            return;
        }

        // Show file info
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileUploadArea.style.display = 'none';
        fileInfo.style.display = 'block';

        // Store file reference
        fileInput.files = createFileList(file);
    }

    // Process file
    processFile.addEventListener('click', function() {
        const file = fileInput.files[0];
        if (!file) return;

        loadingOverlay.style.display = 'flex';
        
        const formData = new FormData();
        formData.append('database_file', file); // ✅ CORREGIDO: era 'file'

        fetch('../../controllers/FileProcessorController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            loadingOverlay.style.display = 'none';
            
            if (data.success) {
                // Insert processed content into textarea
                scriptTextarea.value = data.content;
                
                // Update status
                statusText.textContent = `✅ Archivo procesado correctamente`;
                statusCount.textContent = `${data.tables_count} tablas detectadas`;
                
                // Show success message
                showNotification('✅ Archivo procesado exitosamente', 'success');
                
                // Scroll to textarea
                scriptTextarea.scrollIntoView({ behavior: 'smooth' });
                
            } else {
                showNotification('❌ Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            loadingOverlay.style.display = 'none';
            console.error('Error:', error);
            showNotification('❌ Error procesando el archivo', 'error');
        });
    });

    // Remove file
    removeFile.addEventListener('click', function() {
        fileInput.value = '';
        fileInfo.style.display = 'none';
        fileUploadArea.style.display = 'block';
        statusText.textContent = 'Listo para analizar su script';
        statusCount.textContent = '0 tablas detectadas';
    });

    // Clear script
    clearScript.addEventListener('click', function() {
        if (scriptTextarea.value.trim() !== '') {
            if (confirm('¿Estás seguro de que quieres limpiar el contenido?')) {
                scriptTextarea.value = '';
                statusText.textContent = 'Listo para analizar su script';
                statusCount.textContent = '0 tablas detectadas';
            }
        }
    });

    // Helper functions
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function createFileList(file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        return dt.files;
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            ${type === 'success' ? 'background: linear-gradient(135deg, #4CAF50, #45a049);' : 'background: linear-gradient(135deg, #f44336, #da190b);'}
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    // Real-time script analysis
    scriptTextarea.addEventListener('input', function() {
        const content = this.value.trim();
        if (content) {
            const tableCount = (content.match(/CREATE\s+TABLE/gi) || []).length;
            statusCount.textContent = `${tableCount} tabla${tableCount !== 1 ? 's' : ''} detectada${tableCount !== 1 ? 's' : ''}`;
            statusText.textContent = tableCount > 0 ? '✅ Script válido detectado' : '⚠️ Verificando sintaxis...';
        } else {
            statusCount.textContent = '0 tablas detectadas';
            statusText.textContent = 'Listo para analizar su script';
        }
    });
});