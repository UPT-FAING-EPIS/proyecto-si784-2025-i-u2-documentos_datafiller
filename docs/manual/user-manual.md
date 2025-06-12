# Manual de Usuario - DataFiller

## 1. Introducción

DataFiller es una plataforma web diseñada para automatizar la generación de datos de prueba realistas para bases de datos. Este manual guiará al usuario en la utilización de todas las funcionalidades del sistema.

## 2. Acceso al Sistema

### URL del Sistema
- **Producción**: [https://datafiller3.sytes.net/](https://datafiller3.sytes.net/)
- **Documentación**: [https://datafiller2-b2cbeph0h3a3hfgy.eastus-01.azurewebsites.net/docs/](https://datafiller2-b2cbeph0h3a3hfgy.eastus-01.azurewebsites.net/docs/)

### Credenciales
- **Usuario**: admin@datafiller.com
- **Contraseña**: datafiller2025

## 3. Video Tutorial

A continuación se presenta un video tutorial completo que muestra las principales funcionalidades del sistema:

[![DataFiller Video Tutorial](https://img.youtube.com/vi/SzGoWlZsskU/0.jpg)](https://youtu.be/SzGoWlZsskU)

[Ver video tutorial completo](https://youtu.be/SzGoWlZsskU)

## 4. Funcionalidades Principales

### 4.1 Generación de Datos de Prueba

#### Paso 1: Subir Script SQL
1. Desde el dashboard, seleccione "Nueva Generación"
2. Pegue su script SQL en el área de texto o use el botón "Subir archivo"
3. Haga clic en "Analizar Estructura"

#### Paso 2: Personalizar la Generación
1. Revise las tablas detectadas automáticamente
2. Configure la cantidad de registros a generar para cada tabla
3. Personalice tipos de datos específicos si lo desea
4. Para usuarios premium: elija perfiles de datos específicos por industria

#### Paso 3: Generar y Descargar
1. Haga clic en "Generar Datos"
2. Espere a que el proceso se complete
3. Descargue los resultados en el formato deseado:
   - SQL (plan gratuito y premium)
   - CSV (sólo premium)
   - JSON (sólo premium)

### 4.2 Gestión de Proyectos de Datos

#### Guardar Proyectos
1. Después de configurar la estructura, haga clic en "Guardar proyecto"
2. Asigne un nombre y descripción
3. El proyecto quedará disponible en su dashboard para uso futuro

#### Administrar Proyectos Guardados
- En la sección "Mis Proyectos" del dashboard
- Opciones disponibles: Editar, Duplicar, Eliminar, Compartir (premium)

### 4.3 Administración de Cuenta

#### Actualización de Plan
1. Vaya a "Mi Cuenta" → "Plan Actual"
2. Revise las limitaciones actuales y opciones disponibles
3. Haga clic en "Actualizar a Premium" si desea eliminar restricciones
4. Complete el formulario de pago (S/9.99 mensual)

#### Gestión de Perfil
- Cambiar contraseña
- Actualizar información de contacto
- Ver historial de generaciones
- Administrar método de pago (usuarios premium)

## 5. Planes y Restricciones

### Plan Gratuito
- 3 generaciones diarias
- Máximo 10 registros por tabla
- Solo formato SQL
- Sin guardado de proyectos

### Plan Premium (S/9.99/mes)
- Generaciones ilimitadas
- Hasta 1000 registros por tabla
- Todos los formatos disponibles (SQL, CSV, JSON)
- Guardado de proyectos ilimitado
- Datos específicos por industria
- Soporte prioritario

## 6. Solución de Problemas

### Scripts SQL no reconocidos
- Verifique la sintaxis del script SQL
- Asegúrese que contiene sentencias CREATE TABLE válidas
- Elimine sentencias complejas como procedimientos almacenados

### Límite de generaciones alcanzado
- Espere 24 horas para nuevas generaciones (plan gratuito)
- Considere actualizar a plan premium para generaciones ilimitadas

### Problemas de relaciones entre tablas
- Asegúrese que las claves foráneas estén correctamente definidas en el script
- Verifique que no existan relaciones circulares complejas

## 7. Trazas de Usuario Registradas

El sistema registra automáticamente las acciones del usuario para garantizar la integridad y ofrecer un histórico de operaciones:

```
[2025-06-12 10:30:15] INFO: Usuario admin@datafiller.com inició sesión
[2025-06-12 10:31:20] INFO: Script SQL analizado exitosamente (5 tablas)
[2025-06-12 10:32:45] INFO: Generados 50 registros para tabla 'clientes'
[2025-06-12 10:33:10] INFO: Archivo SQL descargado (250KB)
```

## 8. Contacto y Soporte

- **Email**: soporte@datafiller.com
- **Horario de atención**: Lunes a Viernes de 9:00 a 18:00 (GMT-5)
- **Tiempo de respuesta**: 
  - Plan Gratuito: 48 horas hábiles
  - Plan Premium: 12 horas hábiles

---

*Manual actualizado el 12 de junio de 2025*