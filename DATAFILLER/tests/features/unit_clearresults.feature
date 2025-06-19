Feature: Limpieza de resultados generados

  Scenario: Se limpia una sesión con variables definidas
    Given que la sesión contiene resultados generados, estadísticas y estructura analizada
    When el usuario solicita limpiar resultados
    Then todas las variables de sesión deben eliminarse
    And se debe mostrar el mensaje "Resultados limpiados exitosamente"

  Scenario: Se limpia una sesión ya vacía
    Given una sesión sin variables definidas
    When el usuario solicita limpiar resultados
    Then no se genera error ni salida
    And se muestra el mensaje "Resultados limpiados exitosamente"

  Scenario: Se garantiza que el formato de retorno es válido
    When se llama al método de limpieza
    Then se debe retornar un array con las claves "success" y "message"
