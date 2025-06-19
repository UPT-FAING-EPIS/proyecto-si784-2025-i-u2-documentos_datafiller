Feature: Acceso restringido al dashboard

  Scenario: El usuario autenticado puede acceder al dashboard
    Given el usuario se encuentra en la página de login
    When ingresa credenciales válidas y envía el formulario
    Then es redirigido a la página de generación de datos
    And se muestra el mensaje "Input de Scripts"