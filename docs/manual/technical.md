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
├── config/
│   ├── database.php       # Configuración de conexión a BD
│   ├── constants.php      # Constantes del sistema
│   └── app.php            # Configuración general
├── controllers/
│   ├── AuthController.php       # Autenticación y seguridad
│   ├── ProjectController.php    # Gestión de proyectos
│   ├── GeneratorController.php  # Generación de datos
│   └── UserController.php       # Gestión de usuarios
├── models/
│   ├── User.php                 # Modelo de usuario
│   ├── Project.php              # Modelo de proyecto
│   ├── Subscription.php         # Modelo de suscripción
│   ├── DatabaseSchema.php       # Modelo de esquema de BD
│   └── generators/
│       ├── SqlGenerator.php     # Generador de SQL
│       ├── CsvGenerator.php     # Generador de CSV
│       └── JsonGenerator.php    # Generador de JSON
├── views/
│   ├── auth/
│   ├── dashboard/
│   ├── generator/
│   ├── projects/
│   └── user/
├── public/
│   ├── css/
│   ├── js/
│   ├── uploads/
│   └── index.php          # Punto de entrada
├── tests/
│   ├── Unit/
│   └── Integration/
└── docs/                  # Documentación técnica
```

## 2. Componentes Principales

### 2.1 Analizador SQL
El componente `SqlParser` es responsable de interpretar los scripts SQL proporcionados por el usuario:

```php
// Clase simplificada para análisis de SQL
class SqlParser {
    private $rawSql;
    
    public function __construct(string $sql) {
        $this->rawSql = $sql;
    }
    
    public function detectTables(): array {
        // Algoritmo para extraer definiciones de tablas
        // ...
        return $detectedTables;
    }
    
    public function detectRelationships(): array {
        // Algoritmo para extraer relaciones entre tablas
        // ...
        return $relationships;
    }
    
    // Otros métodos de análisis
}
```

### 2.2 Motor de Generación de Datos
El sistema usa algoritmos avanzados para generar datos realistas respetando las restricciones:

```php
class DataGenerator {
    private $schema;
    private $relationships;
    private $options;
    
    public function __construct(DatabaseSchema $schema, array $relationships, array $options = []) {
        $this->schema = $schema;
        $this->relationships = $relationships;
        $this->options = $options;
    }
    
    public function generate(): array {
        // Generación de datos respetando relaciones e integridad referencial
        // ...
        return $generatedData;
    }
    
    // Métodos auxiliares para tipos específicos de datos
}
```

## 3. API Endpoints

### 3.1 Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/register` - Registrar usuario
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/verify` - Verificar token JWT

### 3.2 Proyectos
- `GET /api/projects` - Listar proyectos del usuario
- `POST /api/projects` - Crear proyecto
- `GET /api/projects/{id}` - Obtener proyecto
- `PUT /api/projects/{id}` - Actualizar proyecto
- `DELETE /api/projects/{id}` - Eliminar proyecto

### 3.3 Generador
- `POST /api/generator/parse` - Analizar script SQL
- `POST /api/generator/generate` - Generar datos
- `GET /api/generator/download/{id}` - Descargar datos generados

### 3.4 Usuario
- `GET /api/user/profile` - Obtener perfil
- `PUT /api/user/profile` - Actualizar perfil
- `POST /api/user/subscription` - Gestionar suscripción

## 4. Base de Datos

### 4.1 Diagrama ER
```
┌─────────────┐       ┌──────────────┐       ┌──────────────────┐
│   users     │       │   projects   │       │   generations    │
├─────────────┤       ├──────────────┤       ├──────────────────┤
│ id          │       │ id           │       │ id               │
│ email       │       │ user_id      │<──────│ project_id       │
│ password    │       │ name         │       │ created_at       │
│ plan_type   │────┐  │ description  │       │ data_path        │
│ created_at  │    └─>│ schema       │       │ format           │
└─────────────┘       │ created_at   │       └──────────────────┘
                      └──────────────┘
```

### 4.2 Definición de Tablas

#### 4.2.1 users
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    plan_type ENUM('free', 'premium') DEFAULT 'free',
    generations_count INT DEFAULT 0,
    last_generation_date DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 4.2.2 projects
```sql
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    schema LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### 4.2.3 generations
```sql
CREATE TABLE generations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NULL,
    user_id INT NOT NULL,
    data_path VARCHAR(255) NOT NULL,
    format ENUM('sql', 'csv', 'json') DEFAULT 'sql',
    records_count INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
);
```

## 5. Seguridad

### 5.1 Autenticación y Autorización
- Implementación de JWT (JSON Web Tokens) para autenticación
- Middleware de autorización para rutas protegidas
- Validación de plan de usuario para acciones restringidas
- Protección contra CSRF

### 5.2 Validación de Input
- Sanitización de entrada de datos SQL para prevenir inyección
- Validación de esquema y estructura antes de procesar
- Límites configurados para tamaños de archivos

### 5.3 Control de Acceso
```php
// Middleware de verificación de plan
function checkPremiumPlan($request, $response, $next) {
    $user = $request->getAttribute('user');
    
    if ($user->plan_type !== 'premium') {
        return $response->withStatus(403)->withJson([
            'error' => 'Esta funcionalidad requiere plan premium'
        ]);
    }
    
    return $next($request, $response);
}
```

## 6. Algoritmos Clave

### 6.1 Generación de Datos Relacionados
El sistema usa una estrategia top-down para mantener la integridad referencial:

1. Ordenar tablas según dependencias
2. Generar datos para tablas independientes
3. Usar valores generados como referencias para tablas dependientes
4. Resolver dependencias circulares con placeholder temporales

### 6.2 Detección Automática de Tipos
```php
function detectDataType($columnName, $columnType) {
    // Mapeo inteligente basado en nombre y tipo
    $patterns = [
        'email' => generateEmail(),
        'name' => generateFullName(),
        'phone|telefono' => generatePhone(),
        'address|direccion' => generateAddress(),
        // más patrones...
    ];
    
    foreach ($patterns as $pattern => $generator) {
        if (preg_match("/$pattern/i", $columnName)) {
            return $generator;
        }
    }
    
    // Fallback al tipo básico
    return generateBasicByType($columnType);
}
```

## 7. Pruebas

### 7.1 Suite de Pruebas
- PHPUnit para pruebas unitarias y de integración
- Pruebas de extremo a extremo con Selenium
- Codeception para pruebas de aceptación

### 7.2 Cobertura
```bash
./vendor/bin/phpunit --coverage-html ./coverage
```

Resultados de cobertura:
- Controladores: 85%
- Modelos: 92%
- Generadores de datos: 88%

### 7.3 Test de Carga
Pruebas realizadas con Apache JMeter:
- 100 usuarios concurrentes
- Tiempo de respuesta promedio: 1.2s
- Generación de 1000 registros: 5.8s

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
    
    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader
    
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

## 10. Logs y Monitoreo

### 10.1 Estructura de Logs
```php
// Sistema de registro estructurado
function logSystemActivity(string $level, string $message, array $context = []) {
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'level' => $level,
        'message' => $message,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_id' => $_SESSION['user_id'] ?? 'anonymous',
        'context' => $context
    ];
    
    file_put_contents(
        'logs/system_' . date('Y-m-d') . '.log', 
        json_encode($log) . PHP_EOL, 
        FILE_APPEND
    );
}
```

### 10.2 Métricas de Rendimiento
- Tiempo promedio de generación: 0.8s por 100 registros
- Consultas DB por página: 3-5
- Memoria utilizada: 45MB promedio

---

*Documentación actualizada el 12 de junio de 2025*