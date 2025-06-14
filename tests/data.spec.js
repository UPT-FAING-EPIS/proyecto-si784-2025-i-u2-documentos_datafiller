const { test, expect } = require('@playwright/test');

test.use({ video: 'on' });

test('Prueba de acceso a dashboard solo para usuarios autenticados', async ({ page }) => {
  console.log('🔐 Intentando acceder al dashboard sin sesión...');
  await page.goto('http://localhost/proyecto-si784-2025-i-u2-documentos_datafiller/DATAFILLER/views/User/generardata.php', {
    waitUntil: 'domcontentloaded',
    timeout: 20000,
  });

  console.log('🔍 Esperando campo de usuario...');
  await page.waitForSelector('input[name="nombre"]', { timeout: 15000 });

  console.log('📝 Rellenando formulario de login...');
  await page.fill('input[name="nombre"]', 'fer');
  await page.fill('input[name="password"]', '123456');

  console.log('🚀 Enviando formulario...');
  await page.click('button[type="submit"]');

  console.log('📂 Accediendo nuevamente al dashboard...');
  await page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 15000 });

  console.log('👀 Verificando texto "Generar Data"...');
  await expect(page.locator('text=Generar Data')).toBeVisible({ timeout: 10000 });
});
