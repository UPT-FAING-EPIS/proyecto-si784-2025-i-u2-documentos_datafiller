Feature: Análisis de estructura SQL

  Scenario: Usuario sin consultas disponibles
    Given el usuario ha agotado sus consultas
    When intenta analizar un script
    Then el sistema debe mostrar "Has agotado tus consultas"
    And el tipo de error debe ser "limite_consultas"

  Scenario: Script sin tablas
    Given el script no contiene ninguna sentencia CREATE TABLE
    And el usuario aún tiene consultas disponibles
    When realiza el análisis
    Then debe recibir un mensaje indicando que no se encontraron tablas
    And el tipo debe ser "sin_tablas"

  Scenario: Script válido con tablas
    Given el script contiene una tabla válida
    And el usuario tiene consultas disponibles
    When realiza el análisis
    Then debe recibir un resultado exitoso
    And debe verse reflejada la tabla analizada
    And se debe incrementar el contador de consultas

  Scenario: Parser devuelve columna nula si no hay tipo
    Given una columna sin tipo definido
    When se procesa
    Then debe retornar null

  Scenario: Parser procesa ENUM sin valores
    Given una columna de tipo ENUM sin opciones
    When se procesa
    Then debe retornar valores por defecto "default1", "default2"

  Scenario: Parser maneja múltiples modificadores
    Given una columna con clave primaria, not null, auto_increment y valor por defecto
    When se construye el SQL
    Then debe contener todos los modificadores correctamente

  Scenario: Limpieza de script SQL
    Given un script SQL con comentarios, USE y SET
    When se limpia
    Then debe eliminar esas líneas y conservar solo el SQL útil

  Scenario: Detección de tipo de datos para generación
    Given una columna con distintos nombres y tipos
    When se determina el tipo de generación
    Then debe retornar correctamente el tipo sugerido

  Scenario: División de columnas por comas seguras
    Given una lista compleja de columnas con ENUM y VARCHAR
    When se divide por comas
    Then debe separar correctamente sin romper valores internos