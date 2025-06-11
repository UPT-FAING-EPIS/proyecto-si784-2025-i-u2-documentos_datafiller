<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - Data Filler</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="../../public/css/auth.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- EmailJS CDN -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
</head>
<body>
    <div class="registro-container">
        <div class="logo-section">
            <img src="../../images/welcome_datafiller.jpg" alt="Data Filler Logo">
        </div>
        <div class="form-section">
            <div class="recovery-form">
                <h2>Recuperar Contraseña</h2>
                <p>Ingresa tu email para recibir un enlace de recuperación</p>

                <form id="recovery-form" onsubmit="procesarRecuperacion(event)">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="ejemplo@correo.com">
                        <small class="input-help">Ingresa el email con el que te registraste</small>
                    </div>

                    <button type="submit" class="submit-btn" id="submit-btn">
                        Enviar Enlace de Recuperación
                    </button>
                </form>

                <div class="form-links">
                    <p><a href="login.php">Volver al inicio de sesión</a></p>
                    <p><a href="../../index.php">Volver al inicio</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="../../public/js/email-recovery.js"></script>
    <script>
        async function procesarRecuperacion(event) {
            event.preventDefault();
            
            const email = document.getElementById('email').value;
            const submitBtn = document.getElementById('submit-btn');
            
            // Deshabilitar botón
            submitBtn.disabled = true;
            submitBtn.textContent = 'Procesando...';
            
            try {
                // Verificar email y generar token
                const result = await verificarEmailYGenerarToken(email);
                
                if(result.exito) {
                    // Enviar email usando EmailJS
                    enviarEmailRecuperacion(email, result.nombre, result.token);
                } else {
                    mostrarMensaje('❌ ' + result.mensaje, 'error');
                }
                
            } catch(error) {
                console.error('Error:', error);
                mostrarMensaje('❌ Error procesando la solicitud.', 'error');
            } finally {
                // Rehabilitar botón
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar Enlace de Recuperación';
            }
        }
    </script>
</body>
</html>