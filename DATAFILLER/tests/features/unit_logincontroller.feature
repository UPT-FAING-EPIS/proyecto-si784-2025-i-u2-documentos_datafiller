Feature: Validación del inicio de sesión

  Scenario: Envío de campos vacíos
    Given que el usuario no llena los campos "nombre" y "password"
    When se intenta iniciar sesión
    Then el resultado debe ser fallido
    And el mensaje debe ser "Por favor complete todos los campos."

  Scenario: Login exitoso con credenciales válidas
    Given credenciales correctas con nombre y contraseña válidos
    When se ejecuta el login
    Then debe retornar éxito con el mensaje "Inicio de sesión exitoso."
    And los datos del usuario deben guardarse en la sesión

  Scenario: Login fallido con credenciales incorrectas
    Given nombre correcto pero contraseña incorrecta
    When se ejecuta el login
    Then debe retornar error con mensaje "Nombre de usuario o contraseña incorrectos."
    And no debe haber sesión activa del usuario
