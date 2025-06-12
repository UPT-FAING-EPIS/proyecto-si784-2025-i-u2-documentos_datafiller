# Manual de Usuario - DATAFILLER

## Acceso al Sistema

### URL del Sistema
- **Producción**: https://datafiller3.sytes.net/
- **Local**: http://localhost/ga/proyecto-si784-2025-i-u2-documentos_datafiller/

### Credenciales
- **Usuario**: admin
- **Contraseña**: admin123

## Funcionalidades Principales

### 1. Gestión de Documentos

#### Crear Documento
1. Ir a "Documentos" → "Nuevo"
2. Llenar formulario:
   - Título (obligatorio)
   - Descripción
   - Archivo (opcional)
3. Guardar

#### Listar Documentos
- Vista tabular con paginación
- Búsqueda por título
- Filtros por fecha y tipo

#### Editar/Eliminar
- Botones de acción en cada fila
- Confirmación para eliminación

### 2. Exportación
- Formatos: PDF, Excel, CSV
- Botón "Exportar" en lista de documentos

## Capturas de Interfaz

### Pantalla de Login
![Login](../images/login.png)

### Dashboard Principal
![Dashboard](../images/dashboard.png)

### Gestión de Documentos
![Documents](../images/documents.png)

## Videos de Pruebas

### Flujo Completo de Usuario
![Video: Crear Documento](../videos/create-document.mp4)

### Pruebas de Interfaz
![Video: Navegación](../videos/navigation-test.mp4)

## Trazas de Pruebas

### Registro de Actividades
```
[TEST] 2025-06-12 14:30:00 - Login exitoso
[TEST] 2025-06-12 14:30:15 - Navegación a documentos
[TEST] 2025-06-12 14:30:30 - Creación de documento "Test Doc"
[TEST] 2025-06-12 14:30:45 - Búsqueda por título
[TEST] 2025-06-12 14:31:00 - Exportación a PDF
[TEST] 2025-06-12 14:31:15 - Logout exitoso
```

### Resultados de Pruebas
- ✅ Login/Logout: PASÓ
- ✅ CRUD Documentos: PASÓ
- ✅ Búsqueda: PASÓ
- ✅ Exportación: PASÓ
- ❌ Subida archivos grandes: FALLÓ (>10MB)

## Solución de Problemas

### Error de Conexión
- Verificar XAMPP ejecutándose
- Comprobar base de datos

### Archivos no se suben
- Revisar permisos de carpeta uploads/
- Verificar límite de tamaño PHP