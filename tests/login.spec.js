const { test, expect } = require('@playwright/test');

test.use({ video: 'on' });

test('Prueba de login en el sistema - caso exitoso', async ({ page }) => {
  console.log('🔐 Iniciando prueba de login...');

  await page.goto('http://localhost/proyecto-si784-2025-i-u2-documentos_datafiller/DATAFILLER/views/Auth/login_view.php', {
    timeout: 10000
  });
  console.log('✅ Página de login cargada');

  await page.fill('input[name="nombre"]', 'fer');
  await page.fill('input[name="password"]', '123456');
  console.log('✍️ Credenciales ingresadas');

  await page.click('button[type="submit"]');
  console.log('📨 Formulario enviado');

  // Verificamos que se redirige a otra URL (salió del login)
  await expect(page).not.toHaveURL(/login_view\.php/);
  console.log('✅ Redirección exitosa después del login');

  // Verificamos si hay algún texto que confirme el login
  const mensaje = page.locator('text=Bienvenido');
  if (await mensaje.count()) {
    console.log('🎉 Login confirmado con mensaje de bienvenida');
  } else {
    console.warn('⚠️ No se encontró el texto "Bienvenido", revisa si el login muestra otra cosa');
  }
});
