// playwright.config.js
const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  timeout: 30000, // Tiempo máximo de cada test
  testDir: './DATAFILLER/tests', // Carpeta raíz donde están tus pruebas
  retries: 0,
  reporter: [
    ['list'],
    ['allure-playwright'] // Generador de reportes tipo BDD
  ],
  use: {
    video: 'on',
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
    baseURL: 'https://datafiller3.sytes.net'
  }
});
