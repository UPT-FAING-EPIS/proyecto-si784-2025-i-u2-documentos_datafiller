<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login_view.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planes y Precios - Data Filler</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="../../public/css/planes.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="promocion-container">
        <!-- Header -->
        <header class="promo-header">
            <div class="header-content">
                <img src="../../images/logo_datafiller.png" alt="Data Filler Logo" class="logo">
                <h1>¡Bienvenido a Data Filler, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>!</h1>
                <p>Elige el plan perfecto para tus necesidades</p>
            </div>
        </header>

        <!-- Planes Section -->
        <section class="planes-section">
            <div class="planes-container">
                
                <!-- Plan Gratuito -->
                <div class="plan-card gratuito">
                    <div class="plan-badge">ACTUAL</div>
                    <div class="plan-header">
                        <h2>Plan Gratuito</h2>
                        <div class="precio">
                            <span class="precio-numero">$0</span>
                            <span class="precio-periodo">/mes</span>
                        </div>
                    </div>
                    
                    <div class="plan-features">
                        <ul>
                            <li>✅ 3 consultas diarias</li>
                            <li>✅ Hasta 10 registros por tabla</li>
                            <li>✅ Formato SQL únicamente</li>
                            <li>✅ Datos básicos estándar</li>
                            <li>❌ Sin datos personalizados</li>
                            <li>❌ Exportación limitada</li>
                        </ul>
                    </div>
                    
                    <div class="plan-footer">
                        <a href="../User/generardata.php" class="btn-plan btn-current">
                            Comenzar Ahora
                        </a>
                        <p class="plan-note">Ya tienes este plan activado</p>
                    </div>
                </div>

                <!-- Plan Premium -->
                <div class="plan-card premium">
                    <div class="plan-badge popular">MÁS POPULAR</div>
                    <div class="plan-header">
                        <h2>Plan Premium</h2>
                        <div class="precio">
                            <span class="precio-numero">$9.99</span>
                            <span class="precio-periodo">/mes</span>
                        </div>
                    </div>
                    
                    <div class="plan-features">
                        <ul>
                            <li>✅ Consultas ilimitadas</li>
                            <li>✅ Hasta 1,000 registros por tabla</li>
                            <li>✅ Todos los formatos (SQL, CSV, JSON, XML)</li>
                            <li>✅ Datos personalizados por industria</li>
                            <li>✅ Generación de datos específicos</li>
                            <li>✅ Soporte prioritario</li>
                            <li>✅ Sin límites diarios</li>
                        </ul>
                    </div>
                    
                    <div class="plan-footer">
                        <a href="#" class="btn-plan btn-premium" onclick="alert('Próximamente disponible')">
                            Actualizar a Premium
                        </a>
                        <p class="plan-note">7 días de prueba gratis</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Comparación Section -->
        <section class="comparacion-section">
            <h2>Comparación de Planes</h2>
            <div class="tabla-comparacion">
                <table>
                    <thead>
                        <tr>
                            <th>Característica</th>
                            <th>Gratuito</th>
                            <th>Premium</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Consultas diarias</td>
                            <td>3</td>
                            <td>Ilimitadas</td>
                        </tr>
                        <tr>
                            <td>Registros por tabla</td>
                            <td>10</td>
                            <td>1,000</td>
                        </tr>
                        <tr>
                            <td>Formatos de exportación</td>
                            <td>SQL</td>
                            <td>SQL, CSV, JSON, XML</td>
                        </tr>
                        <tr>
                            <td>Datos personalizados</td>
                            <td>❌</td>
                            <td>✅</td>
                        </tr>
                        <tr>
                            <td>Soporte</td>
                            <td>Básico</td>
                            <td>Prioritario</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- CTA Final -->
        <section class="cta-section">
            <div class="cta-content">
                <h2>¿Listo para generar datos increíbles?</h2>
                <p>Comienza con tu plan gratuito y actualiza cuando lo necesites</p>
                <div class="cta-buttons">
                    <a href="../User/generardata.php" class="btn-cta primary">
                        Comenzar a Generar Datos
                    </a>
                    <a href="#" class="btn-cta secondary" onclick="alert('Próximamente disponible')">
                        Obtener Premium
                    </a>
                </div>
            </div>
        </section>
    </div>
</body>
</html>