Feature: Validación de solicitudes directas de archivos

  Scenario: Se hace una solicitud GET directa
    Given que el método HTTP es GET
    When se realiza la solicitud al controlador
    Then el sistema responde en formato JSON
    And el mensaje es "Método no permitido"
    And la propiedad "success" es falsa

  Scenario: Se hace una solicitud POST sin archivo
    Given que el método HTTP es POST
    And no se ha subido ningún archivo
    When se realiza la solicitud al controlador
    Then el sistema responde en formato JSON
    And el mensaje es "No se pudo subir el archivo."
    And la propiedad "success" es falsa

  Scenario: Se infiere correctamente el tipo de dato SQL
    Given distintos tipos de valores
    Then el sistema debe devolver los siguientes tipos SQL:
      | Valor                | Tipo esperado     |
      | 42                  | INT               |
      | 3.1415              | DECIMAL(10,2)     |
      | true                | BOOLEAN           |
      | false               | BOOLEAN           |
      | cadena 50 caracteres| VARCHAR(100)      |
      | cadena 200 caract.  | VARCHAR(255)      |
      | cadena 300 caract.  | TEXT              |
      | null                | VARCHAR(255)      |
      | arreglo             | VARCHAR(255)      |
      | objeto              | VARCHAR(255)      |