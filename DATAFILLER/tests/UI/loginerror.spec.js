// tests/loginerror.spec.js
const { test, expect } = require('@playwright/test');

test.use({ video: 'on' });

test('Prueba de login con credenciales incorrectas', async ({ page }) => {
  console.log('🔐 Iniciando prueba de login...');
  await page.goto('https://datafiller3.sytes.net/views/Auth/login_view.php', { timeout: 10000 });

  console.log('✅ Página de login cargada');
  await page.fill('input[name="nombre"]', 'usuario_inexistente');
  await page.fill('input[name="password"]', 'clave_incorrecta');
  console.log('✍️ Credenciales ingresadas');

  await page.click('button[type="submit"]');
  console.log('📨 Formulario enviado');

  // Solo verifica el mensaje de error
  await expect(page.locator('.error-message')).toBeVisible({ timeout: 5000 });
});
