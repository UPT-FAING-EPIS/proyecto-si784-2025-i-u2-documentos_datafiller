Feature: Registro de nuevos usuarios

  Scenario: El usuario se registra exitosamente
    Given el usuario accede a la página de registro
    When completa el formulario con datos válidos
    And hace clic en el botón de registrarse
    Then se muestra un mensaje de éxito o redirección al login