const { test, expect } = require('@playwright/test');

test.use({ video: 'on' });

test('Prueba de registro de nuevo usuario', async ({ page }) => {
  console.log('🚀 Navegando al formulario de registro');
  await page.goto('http://localhost/proyecto-si784-2025-i-u2-documentos_datafiller/DATAFILLER/views/Auth/registro_view.php');

  const random = Math.floor(Math.random() * 10000);
  const nombre = `usuario${random}`;
  const email = `usuario${random}@test.com`;

  await page.fill('input[name="nombre"]', nombre);
  await page.fill('input[name="apellido_paterno"]', 'Perez');
  await page.fill('input[name="apellido_materno"]', 'Lopez');
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', '123456');
  await page.fill('input[name="confirm_password"]', '123456');

  console.log('📨 Enviando formulario');
  await page.click('button[type="submit"]');

  // Verificar redirección
  await expect(page).toHaveURL(/promocion_planes\.php/);
});
