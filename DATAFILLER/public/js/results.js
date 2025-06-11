document.addEventListener('DOMContentLoaded', function() { // ✅ CORREGIDO: agregada la 'D'
    const generatedData = document.getElementById('generatedData');
    const wrapText = document.getElementById('wrapText');
    const showLineNumbers = document.getElementById('showLineNumbers');
    const fontSizeSelect = document.getElementById('fontSizeSelect');
    
    // Opciones de visualización
    if (wrapText) {
        wrapText.addEventListener('change', function() {
            generatedData.style.whiteSpace = this.checked ? 'pre-wrap' : 'pre';
        });
    }
    
    if (showLineNumbers) {
        showLineNumbers.addEventListener('change', function() {
            // Implementar números de línea si es necesario
            console.log('Toggle line numbers:', this.checked);
        });
    }
    
    if (fontSizeSelect) {
        fontSizeSelect.addEventListener('change', function() {
            generatedData.style.fontSize = this.value + 'px';
        });
    }
});

// Mostrar overlay
function showOverlay(message) {
    const overlay = document.getElementById('contentOverlay');
    const overlayMessage = document.getElementById('overlayMessage');
    if (overlay && overlayMessage) {
        overlayMessage.textContent = message;
        overlay.style.display = 'flex';
    }
}

// Ocultar overlay
function hideOverlay() {
    const overlay = document.getElementById('contentOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

// Mostrar notificación
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Copiar datos al portapapeles
function copiarDatos() {
    showOverlay('Copiando al portapapeles...');
    const textarea = document.getElementById('generatedData');
    
    try {
        textarea.select();
        textarea.setSelectionRange(0, 99999); // Para móviles
        
        // Intentar usar la API moderna primero
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textarea.value)
                .then(() => {
                    hideOverlay();
                    showNotification('✅ Datos copiados al portapapeles exitosamente', 'success');
                })
                .catch(() => {
                    // Fallback al método clásico
                    document.execCommand('copy');
                    hideOverlay();
                    showNotification('✅ Datos copiados al portapapeles', 'success');
                });
        } else {
            // Método clásico
            document.execCommand('copy');
            hideOverlay();
            showNotification('✅ Datos copiados al portapapeles', 'success');
        }
    } catch (err) {
        hideOverlay();
        showNotification('❌ Error copiando al portapapeles', 'error');
    }
    
    // Deseleccionar texto
    textarea.setSelectionRange(0, 0);
    textarea.blur();
}

// Obtener formato de salida
function getFormatoSalida() {
    // Intentar obtener del DOM o usar valor por defecto
    const formatElement = document.querySelector('[data-formato]');
    return formatElement ? formatElement.dataset.formato : 'sql';
}

// Descargar archivo
function descargarArchivo() {
    showOverlay('Preparando descarga...');
    
    const datos = document.getElementById('generatedData').value;
    const formato = getFormatoSalida();
    const timestamp = new Date().toISOString().slice(0, 19).replace(/[:-]/g, '');
    
    // Determinar tipo MIME y extensión
    let mimeType, extension, filename;
    
    switch(formato) {
        case 'sql':
            mimeType = 'application/sql';
            extension = 'sql';
            filename = `datafiller_${timestamp}.sql`;
            break;
        case 'json':
            mimeType = 'application/json';
            extension = 'json';
            filename = `datafiller_${timestamp}.json`;
            break;
        case 'csv':
            mimeType = 'text/csv';
            extension = 'csv';
            filename = `datafiller_${timestamp}.csv`;
            break;
        case 'xml':
            mimeType = 'application/xml';
            extension = 'xml';
            filename = `datafiller_${timestamp}.xml`;
            break;
        default:
            mimeType = 'text/plain';
            extension = 'txt';
            filename = `datafiller_${timestamp}.txt`;
    }
    
    try {
        // Crear blob y descargar
        const blob = new Blob([datos], { type: mimeType });
        const url = window.URL.createObjectURL(blob);
        
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Limpiar URL
        window.URL.revokeObjectURL(url);
        
        hideOverlay();
        showNotification(`✅ Archivo ${filename} descargado exitosamente`, 'success');
        
        // Registrar descarga en analytics
        registrarDescarga(formato, datos.length);
        
    } catch (error) {
        hideOverlay();
        showNotification('❌ Error al descargar el archivo', 'error');
        console.error('Error descargando archivo:', error);
    }
}

// Registrar descarga en analytics
function registrarDescarga(formato, tamaño) {
    fetch('../../controllers/AnalyticsController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'registrar_descarga',
            formato: formato,
            tamaño: tamaño
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.warn('Error registrando descarga:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en analytics:', error);
    });
}

// Limpiar datos
function limpiarDatos() {
    const modal = document.getElementById('confirmModal');
    const confirmMessage = document.getElementById('confirmMessage');
    const confirmAction = document.getElementById('confirmAction');
    
    confirmMessage.textContent = '¿Estás seguro de que quieres limpiar todos los resultados generados? Esta acción no se puede deshacer.';
    
    confirmAction.onclick = function() {
        showOverlay('Limpiando resultados...');
        
        fetch('../../controllers/ClearResultsController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'clear_results'
            })
        })
        .then(response => response.json())
        .then(data => {
            hideOverlay();
            cerrarModal();
            
            if (data.success) {
                showNotification('✅ Resultados limpiados exitosamente', 'success');
                setTimeout(() => {
                    window.location.href = 'generardata.php';
                }, 1500);
            } else {
                showNotification('❌ Error limpiando resultados: ' + data.message, 'error');
            }
        })
        .catch(error => {
            hideOverlay();
            cerrarModal();
            showNotification('❌ Error de conexión', 'error');
            console.error('Error:', error);
        });
    };
    
    modal.style.display = 'block';
}

// Cerrar modal
function cerrarModal() {
    const modal = document.getElementById('confirmModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Cerrar modal al hacer clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('confirmModal');
    if (event.target === modal) {
        cerrarModal();
    }
}