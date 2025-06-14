const { test, expect } = require('@playwright/test');

test.use({ video: 'on' });

test('Prueba de registro de nuevo usuario', async ({ page }) => {
  await page.goto('http://localhost/proyecto-si784-2025-i-u2-documentos_datafiller/DATAFILLER/views/Auth/registro_view.php', { timeout: 10000 });

  const timestamp = Date.now();
  await page.fill('input[name="nombre"]', `usuario_test_${timestamp}`);
  await page.fill('input[name="apellido_paterno"]', 'Prueba');
  await page.fill('input[name="apellido_materno"]', 'Automatizada');
  await page.fill('input[name="email"]', `usuario_test_${timestamp}@example.com`);
  await page.fill('input[name="password"]', 'test1234');
  await page.fill('input[name="confirm_password"]', 'test1234');

  await page.click('button[type="submit"]');

  // Espera que la URL cambie (ya no sea la de registro)
  await expect(page).not.toHaveURL(/registro_view\.php/, { timeout: 5000 });
});
