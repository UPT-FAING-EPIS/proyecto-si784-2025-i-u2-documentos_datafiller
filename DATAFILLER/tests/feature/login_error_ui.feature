Feature: Inicio de sesión fallido

  Scenario: El usuario intenta iniciar sesión con credenciales inválidas
    Given el usuario está en la página de login
    When ingresa un nombre de usuario incorrecto o una contraseña inválida
    And hace clic en el botón de login
    Then se muestra un mensaje de error indicando "Credenciales inválidas"