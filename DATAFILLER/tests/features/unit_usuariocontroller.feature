Feature: Datos del usuario para el header

  Scenario: Usuario no encontrado al obtener datos del header
    Given que no existe información del usuario con ID 1
    When se solicita obtenerDatosParaHeader
    Then debe retornar plan "gratuito" y 0 consultas restantes

  Scenario: Usuario con plan premium
    Given que existe el usuario con ID 2 con plan "premium" y 5 consultas restantes
    When se solicita obtenerDatosParaHeader
    Then debe retornar plan "premium" y 5 consultas restantes

  Scenario: Obtener información del usuario por ID
    Given que el usuario con ID 3 tiene información completa
    When se solicita obtenerInfoUsuario
    Then debe retornar la información del usuario

  Scenario: Obtener número de consultas restantes
    Given que el usuario con ID 4 tiene 7 consultas restantes
    When se solicita obtenerConsultasRestantes
    Then debe retornar 7
