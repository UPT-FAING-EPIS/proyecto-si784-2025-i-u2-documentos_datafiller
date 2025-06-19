Feature: Gestión de usuarios y consultas

  Scenario: Buscar usuario por email
    Given un email registrado "a@e"
    When se ejecuta buscarPorEmail
    Then debe retornar los datos del usuario

  Scenario: Buscar email inexistente
    Given un email no registrado "b@e"
    When se ejecuta buscarPorEmail
    Then debe retornar false

  Scenario: Buscar por email lanza excepción
    Given una excepción al preparar la consulta
    When se ejecuta buscarPorEmail
    Then debe retornar false

  Scenario: Obtener info completa de usuario
    Given el usuario con ID 8 existe
    When se ejecuta obtenerInfoCompleta
    Then debe retornar sus datos
    And si no existe debe retornar false

  Scenario: Crear usuario ya existente
    Given un nombre de usuario ya registrado
    When se ejecuta crear
    Then debe retornar false

  Scenario: Crear usuario falla en inserción
    Given un usuario válido pero falla al insertar
    When se ejecuta crear
    Then debe retornar false

  Scenario: Crear usuario exitosamente
    Given un usuario nuevo con datos válidos
    When se ejecuta crear
    Then debe retornar true y asignar ID

  Scenario: Validar login correcto
    Given un nombre de usuario y contraseña correctos
    When se ejecuta validarLogin
    Then debe retornar éxito

  Scenario: Validar login con contraseña incorrecta
    Given una contraseña errónea
    When se ejecuta validarLogin
    Then debe retornar exito=false

  Scenario: Validar login sin usuario
    Given un nombre de usuario que no existe
    When se ejecuta validarLogin
    Then debe retornar exito=false

  Scenario: Validar login con excepción
    Given una excepción al buscar el usuario
    When se ejecuta validarLogin
    Then debe retornar exito=false

  Scenario: Puede realizar consulta - plan premium
    Given un usuario con plan premium
    When se consulta si puede realizar
    Then debe retornar true

  Scenario: Puede realizar consulta - nuevo día
    Given un usuario gratuito con fecha anterior
    When se consulta si puede realizar
    Then debe retornar true

  Scenario: Puede realizar consulta - mismo día y disponible
    Given un usuario con 1 consulta hoy
    When se consulta si puede realizar
    Then debe retornar true

  Scenario: Puede realizar consulta - límite alcanzado
    Given un usuario con máximo de consultas hoy
    When se consulta si puede realizar
    Then debe retornar false

  Scenario: Incrementar consultas en mismo día
    Given un usuario con consultas anteriores hoy
    When se incrementan las consultas
    Then debe retornar true

  Scenario: Incrementar consultas en nuevo día
    Given un usuario con fecha anterior
    When se incrementan las consultas
    Then debe resetear y retornar true

  Scenario: Incrementar consultas sin registros previos
    Given un usuario sin registros
    When se incrementan las consultas
    Then debe retornar false

  Scenario: Incrementar consultas con excepción
    Given una excepción al preparar la consulta
    When se ejecuta incrementarConsultas
    Then debe retornar false

  Scenario: Obtener estadísticas de usuario
    Given un usuario con actividad
    When se consulta estadísticas
    Then debe retornar los datos
    And si no hay registros debe retornar valores por defecto

  Scenario: Obtener plan del usuario
    Given un usuario con plan premium
    When se consulta el plan
    Then debe retornar "premium"
    And si no existe debe retornar "gratuito"

  Scenario: Operaciones con tokens
    Given un usuario con token válido
    When se guarda, verifica, cambia password y marca como usado
    Then todas las operaciones deben retornar true

  Scenario: Limpiar tokens expirados exitoso
    Given hay tokens expirados
    When se limpia
    Then debe retornar true

  Scenario: Limpiar tokens falla en execute
    Given el execute() devuelve false
    When se limpia
    Then debe retornar false

  Scenario: Limpiar tokens lanza excepción
    Given una excepción en prepare
    When se limpia
    Then debe retornar false

  Scenario: Calcular consultas restantes
    Given un usuario premium o gratuito
    When se consulta
    Then debe retornar cantidad adecuada

  Scenario: Usuario no existe en puedeRealizarConsulta
    Given el usuario no existe
    When se ejecuta puedeRealizarConsulta
    Then debe retornar false