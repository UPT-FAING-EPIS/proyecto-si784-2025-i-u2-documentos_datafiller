// https://www.emailjs.com/

(function() {
    // NUEVA SINTAXIS DE INICIALIZACIÓN
    emailjs.init({
        publicKey: "Y5F72MyWAkxwD3oXU"
    });
})();

function enviarEmailRecuperacion(email, nombre, token) {
    const templateParams = {
        to_email: email,  // Cambiar a to_email
        user_name: nombre, // Agregar nombre si lo necesitas
        recovery_link: `https://datafiller3.sytes.net/views/Auth/nueva_password.php?token=${token}` // Cambiar a recovery_link
    };
    
    console.log('🔍 Enviando email con parámetros:', templateParams);
    
    // USAR LA NUEVA API
    emailjs.send('service_n0eq7qa', 'template_o9hdtmh', templateParams)
        .then(function(response) {
            console.log('✅ Email enviado exitosamente!', response.status, response.text);
            mostrarMensaje('✅ Se ha enviado un enlace de recuperación a tu email. Revisa tu bandeja de entrada.', 'success');
        })
        .catch(function(error) {
            console.error('❌ Error completo:', error);
            console.log('Status:', error.status);
            console.log('Text:', error.text);
            
            if(error.status === 404) {
                mostrarMensaje('❌ Servicio de email no disponible. Verifica tu configuración.', 'error');
            } else if(error.status === 422) {
                mostrarMensaje('❌ Error en los parámetros del email. Contacta al administrador.', 'error');
            } else {
                mostrarMensaje('❌ Error enviando el email. Intente nuevamente.', 'error');
            }
        });
}

function mostrarMensaje(mensaje, tipo) {
    // Remover mensajes anteriores
    const mensajesAnteriores = document.querySelectorAll('.success-message, .error-message');
    mensajesAnteriores.forEach(msg => msg.remove());
    
    const div = document.createElement('div');
    div.className = tipo === 'success' ? 'success-message' : 'error-message';
    div.innerHTML = mensaje;
    
    const form = document.querySelector('.recovery-form');
    form.insertBefore(div, form.firstChild);
    
    setTimeout(() => div.remove(), 8000);
}

// Función para verificar email y generar token
async function verificarEmailYGenerarToken(email) {
    try {
        const response = await fetch('recuperar_password_process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email: email })
        });
        
        const result = await response.json();
        return result;
        
    } catch(error) {
        console.error('Error:', error);
        return {
            exito: false,
            mensaje: 'Error de conexión. Intente nuevamente.'
        };
    }
}