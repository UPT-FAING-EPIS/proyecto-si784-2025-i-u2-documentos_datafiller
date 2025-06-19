Feature: Registro de usuarios

  Scenario: Registro con campos vacíos
    Given el usuario no completa ningún campo
    When intenta registrarse
    Then debe recibir el mensaje "Por favor complete todos los campos requeridos."

  Scenario: Registro con email inválido
    Given el usuario proporciona un email con formato incorrecto
    When intenta registrarse
    Then debe recibir el mensaje "Por favor ingrese un email válido."

  Scenario: Registro con contraseñas que no coinciden
    Given el usuario escribe dos contraseñas diferentes
    When intenta registrarse
    Then debe recibir el mensaje "Las contraseñas no coinciden."

  Scenario: Registro con contraseña muy corta
    Given el usuario escribe una contraseña de menos de 6 caracteres
    When intenta registrarse
    Then debe recibir el mensaje "La contraseña debe tener al menos 6 caracteres."

  Scenario: Registro con nombre de usuario ya existente
    Given el nombre de usuario ya está registrado
    When intenta registrarse
    Then debe recibir el mensaje "El nombre de usuario ya está registrado. Elija otro nombre."

  Scenario: Registro con email ya registrado
    Given el email ya fue usado por otro usuario
    When intenta registrarse
    Then debe recibir el mensaje "Este email ya está registrado. Solo se permite una cuenta por email."

  Scenario: Registro exitoso
    Given el usuario completa todos los campos correctamente
    And el nombre de usuario y el email son únicos
    When envía el formulario de registro
    Then el registro debe ser exitoso
    And el usuario debe iniciarse sesión automáticamente
    And debe ver el mensaje "Registro exitoso. Redirigiendo a promoción de planes..."