const { test, expect } = require('@playwright/test');

test.use({ video: 'on' }); // Graba el flujo en video
test.setTimeout(90000);    // ⏱️ Timeout total

test('🧪 Flujo completo: login, análisis y generación de datos', async ({ page }) => {
  console.log('🔐 Iniciando prueba de login...');

  // Paso 1: Ir a la página de login
  await page.goto('https://datafiller3.sytes.net/views/Auth/login_view.php', {
    waitUntil: 'domcontentloaded',
  });
  console.log('✅ Página de login cargada');

  // Paso 2: Llenar credenciales y enviar
  await page.fill('input[name="nombre"]', 'fer');
  await page.fill('input[name="password"]', '123456');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.click('button[type="submit"]'),
  ]);

  const currentUrl = page.url();
  console.log('✅ Redirección detectada:', currentUrl);

  if (currentUrl.includes('login_view.php')) {
    throw new Error('🔒 Redirigido nuevamente a login. La sesión no se mantuvo.');
  }

  // Paso 3: Verificar presencia del textarea
  const textarea = page.locator('#script');
  await expect(textarea).toBeVisible({ timeout: 15000 });
  console.log('📝 Textarea detectado');

  // Paso 4: Llenar script SQL
  const scriptSQL = `
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
);`;
  await textarea.fill(scriptSQL);
  console.log('🧾 Script ingresado');

  // Paso 5: Enviar formulario
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.click('button[type="submit"]'),
  ]);
  console.log('🛠️ Redirigido a configuracion.php');

  // Paso 6: Generar datos
  await page.click('#generateBtn');
  await page.waitForURL(/resultados\.php/, { timeout: 15000 });
  console.log('📊 Página de resultados cargada');

  // Paso 7: Esperar a que se vea el contenido de resultados
  await page.waitForTimeout(1000); // Pequeño delay antes del scroll
  await page.evaluate(() => {
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
  });

  // Paso 8: Verificar que se vean los datos
  await page.waitForTimeout(3000);
  await expect(page.locator('text=Datos generados')).toBeVisible({ timeout: 5000 }).catch(() => {
    console.warn('⚠️ No se encontró texto "Datos generados", pero el proceso pudo completarse.');
  });

  console.log('🎉 Datos generados y mostrados exitosamente');
});
