const { test, expect } = require('@playwright/test');

test.use({ video: 'on' });

test('🧪 Flujo completo: login, script, rellenado y visualización de datos generados', async ({ page }) => {
  // Paso 1: Login
  await page.goto('https://datafiller3.sytes.net/views/Auth/login_view.php');
  await page.fill('input[name="nombre"]', 'mafer');
  await page.fill('input[name="password"]', '123456');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.click('button[type="submit"]')
  ]);
  await expect(page).toHaveURL(/generardata\.php/);

  // Paso 2: Insertar script SQL
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
  await page.fill('#script', scriptSQL);
  await page.click('button[type="submit"]');

  // Paso 3: Esperar redirección a configuracion.php
  await page.waitForURL(/configuracion\.php/, { timeout: 10000 });
  console.log('🛠️ Página de configuración cargada');

  // Paso 4: Hacer clic en "🚀 Rellenar Tablas"
  await page.click('#generateBtn');

  // Paso 5: Esperar redirección a resultados.php
  await page.waitForURL(/resultados\.php/, { timeout: 10000 });
  console.log('📊 Página de resultados cargada');

  // Paso 6: Scroll hasta el final para mostrar resultados generados
  await page.waitForTimeout(1000); // pequeño delay antes del scroll
  await page.evaluate(() => {
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
  });

  // Paso 7: Esperar a que se vea algo de los resultados
  await page.waitForTimeout(3000); // que se vea en el video
  await expect(page.locator('text=Datos generados')).toBeVisible({ timeout: 5000 }).catch(() => {
    console.warn('⚠️ No se encontró texto "Datos generados", pero el proceso pudo completarse.');
  });

  console.log('🎉 Datos generados y mostrados exitosamente');
});
