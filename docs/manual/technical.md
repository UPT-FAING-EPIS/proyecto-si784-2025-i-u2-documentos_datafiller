# Documentación Técnica - DataFiller

## 1. Arquitectura del Sistema

### 1.1 Stack Tecnológico
- **Backend**: PHP 8.0.30
- **Base de Datos**: MySQL 8.0
- **Servidor**: Apache 2.4 (XAMPP)
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap
- **Control de Versiones**: Git/GitHub
- **Documentación**: DocFX 2.78.3
- **CI/CD**: GitHub Actions

### 1.2 Arquitectura MVC
DataFiller implementa una arquitectura Modelo-Vista-Controlador (MVC) para mantener una clara separación de responsabilidades:

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │    Backend      │    │   Base de       │
│   HTML/CSS/JS   │◄──►│    PHP 8.0      │◄──►│   Datos MySQL   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### 1.3 Estructura de Archivos
```
DATAFILLER/
├── index.php                    # Punto de entrada principal
├── config/
│   └── Database.php             # Configuración de conexión a BD
├── controllers/
│   ├── AnalyticsController.php  # Análisis de datos
│   ├── ClearResultsController.php # Limpieza de resultados
│   ├── DataGeneratorController.php # Generación de datos
│   ├── FileProcessorController.php # Procesamiento de archivos
│   ├── LoginController.php      # Control de autenticación
│   ├── RegistroController.php   # Registro de usuarios
│   ├── SqlAnalyzerController.php # Análisis de SQL
│   └── UsuarioController.php    # Gestión de usuarios
├── models/
│   └── Usuario.php              # Modelo de usuario
├── views/
│   ├── Auth/                    # Vistas de autenticación
│   └── User/                    # Vistas de usuario
├── public/
│   ├── css/                     # Estilos CSS
│   └── js/                      # Scripts JavaScript
├── images/
│   └── videos/                  # Videos del sistema
├── logs/                        # Registro de eventos 
├── logs_BORRABLE/               # Logs temporales
├── resultados_BORRABLE/         # Resultados generados 
├── script_sql_BORRABLE/         # Scripts SQL temporales
└── tests/
    └── Unit/                    # Tests unitarios
        └── Stubs/               # Stubs para testing
```

## 2. Componentes Principales

### 2.1 Analizador SQL
El componente `SqlAnalyzerController` es responsable de interpretar los scripts SQL proporcionados por el usuario:

```php
// Implementación simplificada del SqlAnalyzerController
class SqlAnalyzerController {
    private $sqlContent;
    
    public function __construct(string $sql = null) {
        $this->sqlContent = $sql;
    }
    
    public function parseSql(): array {
        // Algoritmo para extraer definiciones de tablas
        // ...
        return $detectedTables;
    }
    
    public function detectRelationships(): array {
        // Algoritmo para extraer relaciones entre tablas
        // ...
        return $relationships;
    }
}
```

### 2.2 Motor de Generación de Datos
El sistema usa la librería Faker para generar datos realistas:

```php
class DataGeneratorController {
    private $schema;
    private $faker;
    
    public function __construct($schema = null) {
        $this->schema = $schema;
        $this->faker = \Faker\Factory::create('es_ES'); // Instancia localizada de Faker
    }
    
    public function generate($amount = 10): array {
        // Generación de datos respetando relaciones e integridad referencial
        // ...
        return $generatedData;
    }
    
    // Generación de tipos específicos
    private function generateForColumn($columnName, $type) {
        // Mapeo de tipos de columna a generadores de Faker
        // ...
    }
}
```

## 3. Flujo de Trabajo Principal

### 3.1 Proceso de Generación de Datos
1. Usuario sube/ingresa script SQL
2. `FileProcessorController` procesa el archivo
3. `SqlAnalyzerController` analiza la estructura
4. `DataGeneratorController` genera los datos sintéticos
5. Se almacenan resultados en `resultados_BORRABLE/`
6. Usuario descarga los resultados generados

### 3.2 Autenticación y Seguridad
- Manejo de sesiones mediante `LoginController`
- Registro de usuarios con `RegistroController`
- Gestión de usuarios existentes con `UsuarioController`

### 3.3 Análisis y Reportes
- `AnalyticsController` proporciona métricas sobre los datos generados
- Logs detallados de actividad en la carpeta `logs/`

## 4. Base de Datos

### 4.1 Diagrama ER
```
┌─────────────┐       ┌──────────────────┐
│   usuarios  │       │   generaciones   │
├─────────────┤       ├──────────────────┤
│ id          │       │ id               │
│ email       │───────│ usuario_id       │
│ password    │       │ fecha            │
│ tipo_plan   │       │ ruta_archivo     │
│ created_at  │       │ formato          │
└─────────────┘       └──────────────────┘
```

### 4.2 Conexión a Base de Datos
La clase `App\Config\Database` gestiona la conexión:

```php
namespace App\Config;

class Database {
    private $host = 'localhost';
    private $db_name = 'datafiller';
    private $username = 'root';
    private $password = '';
    private $conn;
    
    public function connect() {
        $this->conn = null;
        
        try {
            $this->conn = new \PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }
        
        return $this->conn;
    }
}
```

## 5. Integración con Faker

DataFiller utiliza la biblioteca FakerPHP para generar datos sintéticos realistas. Esta librería ofrece múltiples proveedores que se utilizan según el contexto:

```php
// Ejemplo de uso de Faker en el proyecto
$faker = \Faker\Factory::create('es_ES'); // Localizado para español

// Generación de datos según el tipo de columna
switch ($columnType) {
    case 'email':
        return $faker->email;
    case 'nombre':
        return $faker->name;
    case 'direccion':
        return $faker->address;
    case 'telefono':
        return $faker->phoneNumber;
    case 'fecha':
        return $faker->date('Y-m-d');
    // más tipos...
}
```

### 5.1 Localizaciones Soportadas
La implementación soporta múltiples localizaciones, incluyendo:
- Español (es_ES, es_AR, es_PE)
- Inglés (en_US, en_GB)
- Francés (fr_FR)
- Alemán (de_DE)
- Y muchos otros idiomas

## 6. Test Unitarios

### 6.1 Suite de Pruebas
DataFiller implementa tests unitarios con PHPUnit:

```php
// Ejemplo de test unitario para SqlAnalyzerController
class SqlAnalyzerControllerTest extends \PHPUnit\Framework\TestCase {
    
    public function testParseSql() {
        $sql = "CREATE TABLE usuarios (id INT, nombre VARCHAR(255))";
        $analyzer = new SqlAnalyzerController($sql);
        $result = $analyzer->parseSql();
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('usuarios', $result[0]['name']);
    }
    
    public function testDetectRelationships() {
        // Test para detección de relaciones entre tablas
        // ...
    }
}
```

## 7. Seguridad

### 7.1 Autenticación y Autorización
- Hasheo seguro de contraseñas con `password_hash()`
- Verificación de sesiones activas en cada controlador
- Sanitización de input de usuarios

### 7.2 Protección de Datos
- Los datos generados son temporales y se limpian periódicamente
- No se almacenan datos sensibles de los usuarios
- Sistema de logs para auditorías de seguridad

## 8. Deployment

### 8.1 Entornos
- **Desarrollo**: Local (XAMPP)
- **Pruebas**: Azure App Service (desarrollo)
- **Producción**: Azure App Service (producción)

### 8.2 CI/CD
GitHub Actions workflow para despliegue continuo:
```yaml
name: Deploy

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.0'
    
    - name: Deploy to Azure Web App
      uses: azure/webapps-deploy@v2
      with:
        app-name: 'datafiller3'
        publish-profile: ${{ secrets.AZURE_WEBAPP_PUBLISH_PROFILE }}
        package: .
```

## 9. Video Explicativo

El siguiente video muestra la arquitectura y funcionamiento técnico del sistema:

[![DataFiller Video Técnico](https://img.youtube.com/vi/SzGoWlZsskU/0.jpg)](https://youtu.be/SzGoWlZsskU)

[Ver video técnico completo](https://youtu.be/SzGoWlZsskU)

## 10. Monitoreo y Depuración

### 10.1 Sistema de Logs
El `DebugHelper` implementa las funcionalidades de registro:

```php
// Implementación simplificada del sistema de logs
class DebugHelper {
    public static function log($message, $level = 'INFO') {
        $logEntry = date('Y-m-d H:i:s') . " [$level] " . $message . PHP_EOL;
        file_put_contents('logs/app_' . date('Y-m-d') . '.log', $logEntry, FILE_APPEND);
    }
    
    public static function logError($message) {
        self::log($message, 'ERROR');
    }
}
```

### 10.2 Métricas de Rendimiento
- Tiempo promedio de análisis SQL: 0.5s
- Tiempo de generación por 100 registros: 1.2s
- Tamaño medio de resultados: 45KB por cada 100 registros

---

*Documentación actualizada el 12 de junio de 2025*