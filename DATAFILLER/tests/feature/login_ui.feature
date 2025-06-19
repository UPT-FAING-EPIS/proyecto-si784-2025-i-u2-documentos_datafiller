Feature: Inicio de sesión en el sistema

  Scenario: El usuario inicia sesión con credenciales válidas
    Given el usuario está en la página de login
    When ingresa "usuario" y "contraseña" válidos
    And presiona el botón de ingresar
    Then es redirigido al área de usuario o panel principal