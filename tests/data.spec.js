const { test, expect } = require('@playwright/test');

test.use({ video: 'on' });

test('🔐 Flujo completo: acceso al dashboard solo para usuarios autenticados', async ({ page }) => {
  console.log('🔐 Accediendo a login directamente...');
  await page.goto('https://datafiller3.sytes.net/views/Auth/login_view.php');

  console.log('✍️ Llenando formulario...');
  await page.fill('input[name="nombre"]', 'fer');
  await page.fill('input[name="password"]', '123456');

  console.log('📨 Enviando login...');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 10000 }),
    page.click('button[type="submit"]')
  ]);

  const currentURL = page.url();
  console.log('🌐 URL actual:', currentURL);

  if (currentURL.includes('login')) {
    const html = await page.content();
    console.log('HTML tras login fallido:', html);
    const mensajeError = await page.locator('.error-message').textContent().catch(() => '');
    console.error('❌ No se redirigió. Mensaje de error detectado:', mensajeError || 'No visible');
    throw new Error('Login fallido, revisar credenciales o lógica en el backend.');
  }

  await expect(page).toHaveURL(/generardata\.php/);
  await expect(page.locator('text=Input de Scripts')).toBeVisible();
  console.log('🎉 Login confirmado y dashboard accesible');
});
