Feature: Flujo completo de login y generación de datos

  Scenario: Usuario realiza login, envía script y genera datos correctamente
    Given el usuario accede a la página de login
    When ingresa credenciales válidas y accede a la herramienta
    And llena el textarea con un script SQL válido
    And hace clic en analizar y luego en generar
    Then el sistema muestra los datos generados en la vista final