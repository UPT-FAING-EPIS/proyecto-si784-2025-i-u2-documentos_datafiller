Feature: Registro de mensajes de depuración

  Scenario: Se crea el archivo y carpeta de logs al registrar un mensaje
    Given que no existe la carpeta ni archivo de logs
    When se registra el mensaje "Mensaje de prueba"
    Then se crea la carpeta de logs
    And se crea el archivo "debug.log"
    And el archivo contiene el mensaje "Mensaje de prueba" con una fecha válida

  Scenario: Se agregan múltiples mensajes al archivo de logs
    Given que el archivo de logs está vacío
    When se registran los mensajes "Primer mensaje" y "Segundo mensaje"
    Then ambos mensajes deben aparecer en el archivo
    And deben estar en líneas separadas en el orden registrado