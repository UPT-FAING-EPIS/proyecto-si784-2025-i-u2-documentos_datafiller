// https://www.emailjs.com/

(function() {
    emailjs.init("Y5F72MyWAkxwD3oXU"); // Tu clave pública
})();

function enviarEmailRecuperacion(email, nombre, token) {
    const templateParams = {
        email: email,  // Para {{email}} en el template
        link: `http://localhost/DATAFILLER/views/Auth/nueva_password.php?token=${token}` // Para {{link}} en el template
    };
    
    // CORREGIDO: Usar los IDs reales que me mostraste
    emailjs.send('service_n0eq7qa', 'template_o9hdtmh', templateParams)
        .then(function(response) {
            console.log('Email enviado!', response.status, response.text);
            mostrarMensaje('✅ Se ha enviado un enlace de recuperación a tu email. Revisa tu bandeja de entrada.', 'success');
        }, function(error) {
            console.log('Error:', error);
            mostrarMensaje('❌ Error enviando el email. Intente nuevamente.', 'error');
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