Feature: Procesamiento de archivos de base de datos

  Scenario: Procesar archivo SQL válido
    Given un archivo SQL con 2 sentencias CREATE TABLE
    When se procesa el archivo
    Then el resultado debe ser exitoso
    And debe mostrar "2 tablas"
    And debe contener el texto "CREATE TABLE users"

  Scenario: Procesar archivo SQL sin sentencias válidas
    Given un archivo SQL sin CREATE TABLE
    When se procesa el archivo
    Then el resultado debe ser fallido
    And debe mostrar el mensaje "No se encontraron declaraciones CREATE TABLE en el archivo SQL."

  Scenario: Procesar archivo .bak con sentencias SQL
    Given un archivo .bak con 2 CREATE TABLE embebidos
    When se procesa el archivo
    Then debe ser exitoso y contener ambas tablas

  Scenario: Procesar archivo .bak sin contenido útil
    Given un archivo .bak sin sentencias SQL
    When se procesa el archivo
    Then debe retornar éxito con 0 tablas detectadas

  Scenario: Procesar archivo JSON con clave "tables"
    Given un archivo JSON que contiene definiciones de tablas
    When se procesa el archivo
    Then debe retornar éxito con 1 tabla
    And debe contener "CREATE TABLE `users`"

  Scenario: Procesar archivo JSON como arreglo
    Given un archivo JSON como array de objetos
    When se procesa el archivo
    Then debe crear una tabla "data_table"
    And debe detectar tipos INT y DECIMAL

  Scenario: Procesar archivo JSON malformado
    Given un archivo JSON inválido
    When se procesa el archivo
    Then debe fallar con el mensaje "El archivo JSON no tiene un formato válido."

  Scenario: Subir archivo con tipo no soportado
    Given un archivo con extensión .txt
    When se procesa el archivo
    Then debe fallar con mensaje de tipo no soportado

  Scenario: Subir archivo demasiado grande
    Given un archivo mayor a 10MB
    When se procesa el archivo
    Then debe fallar con mensaje "El archivo es demasiado grande"

  Scenario: No se sube ningún archivo
    Given no se ha enviado ningún archivo
    When se llama al proceso
    Then debe retornar "No se pudo subir el archivo."
