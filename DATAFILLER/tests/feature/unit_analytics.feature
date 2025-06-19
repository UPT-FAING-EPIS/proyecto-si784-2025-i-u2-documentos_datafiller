Feature: Registro de descargas en el sistema de auditoría

  Scenario: Usuario no autenticado intenta registrar una descarga
    Given un usuario no autenticado
    When intenta registrar una descarga en formato "csv" con 10 registros
    Then el sistema debe rechazar la operación con un mensaje de "Usuario no autenticado"

  Scenario: Usuario autenticado registra una descarga correctamente
    Given un usuario autenticado con ID 99
    When registra una descarga en formato "xlsx" con 5 registros
    Then el sistema guarda la acción en la tabla de auditoría
    And devuelve un mensaje de "Descarga registrada"