Feature: Ingreso de script SQL y generación de datos

  Scenario: El usuario genera datos a partir de un script válido
    Given el usuario ha iniciado sesión correctamente
    When navega a la página de generación de datos
    And ingresa un script SQL válido en el textarea
    And hace clic en el botón de enviar
    Then es redirigido a la página de configuración
    When hace clic en el botón "Rellenar Tablas"
    Then es redirigido a la página de resultados
    And visualiza el mensaje "Datos generados"
