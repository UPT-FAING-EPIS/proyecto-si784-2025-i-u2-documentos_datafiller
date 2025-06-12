# Documentación Técnica - DATAFILLER

## Arquitectura del Sistema

### Stack Tecnológico
- **Backend**: PHP 8.0.30
- **Base de Datos**: MySQL 8.0
- **Servidor**: Apache 2.4 (XAMPP)
- **Frontend**: HTML5, CSS3, JavaScript

### Estructura de Archivos
```
DATAFILLER/
├── config/
│   └── database.php
├── controllers/
│   ├── DocumentController.php
│   └── UserController.php
├── models/
│   ├── Document.php
│   └── User.php
├── views/
│   ├── documents/
│   └── users/
└── public/
    ├── css/
    ├── js/
    └── uploads/
```

## API Endpoints

### Documentos
- `GET /api/documents` - Listar documentos
- `POST /api/documents` - Crear documento
- `PUT /api/documents/{id}` - Actualizar documento
- `DELETE /api/documents/{id}` - Eliminar documento

### Base de Datos

#### Tabla: documents
```sql
CREATE TABLE documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Configuración

### Variables de Entorno
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'datafiller');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Deployment
- URL Producción: https://datafiller3.sytes.net/
- URL Local: http://localhost/ga/proyecto-si784-2025-i-u2-documentos_datafiller/

## Trazas del Sistema

### Log de Operaciones
```
[2025-06-12 10:30:15] INFO: Usuario admin inició sesión
[2025-06-12 10:31:20] INFO: Documento "Informe Q1" creado
[2025-06-12 10:32:45] INFO: Documento ID:5 actualizado
[2025-06-12 10:33:10] ERROR: Falló subida de archivo - tamaño excedido
```

### Métricas de Rendimiento
- Tiempo promedio de carga: 1.2s
- Consultas por página: 3-5
- Memoria utilizada: 45MB promedio

## Testing

### Pruebas Unitarias
```bash
./vendor/bin/phpunit tests/
```

### Cobertura de Código
- Controladores: 85%
- Modelos: 92%
- Vistas: 70%