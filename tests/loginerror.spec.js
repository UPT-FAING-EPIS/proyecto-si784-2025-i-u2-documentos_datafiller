const { test, expect } = require('@playwright/test');

test.use({ video: 'on' });

test('Prueba de login con credenciales incorrectas', async ({ page }) => {
  await page.goto('http://localhost/proyecto-si784-2025-i-u2-documentos_datafiller/DATAFILLER/views/Auth/login_view.php', { timeout: 10000 });

  // Intenta iniciar sesión con usuario y contraseña incorrectos
  await page.fill('input[name="nombre"]', 'usuario_inexistente');
  await page.fill('input[name="password"]', 'clave_incorrecta');
  await page.click('button[type="submit"]');

  // Espera que aparezca el mensaje de error
  await expect(page.locator('.error-message')).toBeVisible({ timeout: 5000 });
});