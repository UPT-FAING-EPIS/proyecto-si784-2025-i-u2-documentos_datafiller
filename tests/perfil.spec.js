const { test, expect } = require('@playwright/test');

test.use({ video: 'on' });

test('Prueba de acceso a perfil solo después de login', async ({ page }) => {
  // Intenta acceder al perfil sin iniciar sesión
  await page.goto('http://localhost/proyecto-si784-2025-i-u2-documentos_datafiller/DATAFILLER/views/User/perfil.php', { timeout: 10000 });

  // Debe redirigir a la página de login
  await expect(page).toHaveURL(/login_view\.php/);

  // Ahora inicia sesión
  await page.fill('input[name="nombre"]', 'fer');
  await page.fill('input[name="password"]', '123456');
  await page.click('button[type="submit"]');

  // Accede al perfil después de login
  await page.goto('http://localhost/proyecto-si784-2025-i-u2-documentos_datafiller/DATAFILLER/views/User/perfil.php');

  // Verifica que el perfil es accesible (ajusta el texto según tu vista)
  await expect(page.locator('text=Perfil')).toBeVisible();
});