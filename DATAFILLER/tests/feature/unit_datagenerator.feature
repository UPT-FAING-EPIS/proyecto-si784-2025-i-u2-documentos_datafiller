Feature: Generación de datos simulados con reglas personalizadas

  Scenario: El controlador se instancia correctamente
    Given el sistema carga el controlador de generación
    Then debe ser una instancia válida de DataGeneratorController

  Scenario: No se permite generar datos si se exceden los límites del plan
    Given un usuario con plan limitado
    When intenta generar datos con una configuración excedida
    Then el sistema debe devolver un mensaje de "limite_superado"

  Scenario: Se genera un error interno durante la generación
    Given un usuario con acceso válido
    When ocurre una excepción al registrar la generación
    Then el sistema devuelve un error tipo "error_interno"

  Scenario: Se genera exitosamente un conjunto de datos
    Given un usuario premium
    When solicita generar una tabla con 1 fila
    Then el sistema responde con éxito y contenido generado

  Scenario: El sistema respeta la secuencia lógica de generación
    Given un flujo normal de generación
    When se simulan los pasos sin datos
    Then se llaman las funciones en orden esperado y no hay errores

  Scenario: Se generan exactamente 5 registros de una tabla
    Given una configuración para generar 5 filas
    When se ejecuta la función de generación
    Then se obtienen 5 filas con columnas "id" y "nombre"

  Scenario: Valor personalizado tiene prioridad sobre cualquier tipo
    Given un valor fijo personalizado
    When se procesa esa columna
    Then debe prevalecer sobre cualquier otro tipo de generación

  Scenario: Se generan valores auto-incrementales correctamente
    Given una columna tipo auto_increment
    When se solicitan dos valores consecutivos
    Then deben ser 1 y 2 respectivamente

  Scenario: El campo password genera un hash válido
    Given una columna "password"
    When se genera su valor
    Then debe ser un hash que valida contra "123456"

  Scenario: El tipo ENUM selecciona un valor válido
    Given una columna ENUM con valores "A" y "B"
    When se solicita un valor
    Then debe retornar uno de los valores esperados

  Scenario: Una foreign key se resuelve usando la tabla referenciada
    Given una columna con clave foránea
    When existen valores referenciados
    Then se retorna un valor desde las referencias

  Scenario: Se genera un email válido
    Given una columna de tipo "email_usuario"
    When se solicita el valor
    Then se devuelve un correo electrónico válido