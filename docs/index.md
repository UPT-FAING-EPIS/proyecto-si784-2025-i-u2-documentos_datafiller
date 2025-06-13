# DATAFILLER - Documentación Técnica

## Información del Proyecto
- **Materia:** SI784 - Sistemas de Información  
- **Gestión:** 2025-I
- **Unidad:** U2 - Documentos DataFiller
- **Tecnología:** PHP 8.0.30

## Descripción del Sistema

DataFiller es una plataforma web diseñada para automatizar la generación de datos de prueba realistas para bases de datos SQL y NoSQL. Desarrollada en PHP 8.0, permite a desarrolladores y equipos de QA obtener datos sintéticos que respetan la estructura, relaciones y restricciones de integridad de sus bases de datos.

## 📹 Video Demostrativo

[![DataFiller Demo](https://img.youtube.com/vi/SzGoWlZsskU/0.jpg)](https://youtu.be/SzGoWlZsskU)

[Ver demostración completa del sistema](https://youtu.be/SzGoWlZsskU)

## 📚 Documentación Disponible

### Informes del Proyecto
- [📄 FD01 - Informe de Factibilidad](informes/fd01-informe-factibilidad.md)
- [📄 FD02 - Informe de Vision](informes/fd02-informe-vision.md)
- [📄 FD03 - Informe de SRS](informes/fd03-informe-srs.md)
- [📄 FD04 - Informe de SAD](informes/fd04-informe-sad.md)
- [📄 FD05 - Informe de Proyecto Final](informes/fd05-informe-final.md)
- [📄 FD   - DICCIONARIO DE DATOS](informes/DICCIONARIO_DE_DATOS.md)


### Manuales Técnicos
- [📖 Manual de Usuario](manual/user-manual.md)
- [🔧 Documentación Técnica](manual/technical.md)
- [📋 FD05 - Informe de Documentación](manual/fd05-informe.md)

### Enlaces del Sistema
- **Producción:** https://datafiller3.sytes.net/
- **Documentación:** https://datafiller2-b2cbeph0h3a3hfgy.eastus-01.azurewebsites.net/docs/

## Arquitectura del Sistema

### Tecnologías Utilizadas
- **Backend:** PHP 8.0.30
- **Base de Datos:** MySQL 8.0
- **Servidor Web:** Apache 2.4 (XAMPP)
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap
- **Documentación:** DocFX 2.78.3
- **CI/CD:** GitHub Actions

### Estructura del Proyecto
```
DATAFILLER/
├── config/          # Configuración del sistema
├── controllers/     # Controladores MVC
├── models/          # Modelos de datos  
├── views/           # Vistas y templates
├── public/          # Archivos públicos
├── tests/           # Pruebas unitarias
├── docs/            # Documentación técnica
└── .github/         # Workflows de CI/CD
```

## Características Principales

### 1. Generación de Datos
- Análisis automático de scripts SQL
- Generación de datos respetando relaciones
- Varios formatos de salida (SQL, CSV, JSON)

### 2. Planes de Usuario
- Plan gratuito (limitado)
- Plan premium con funcionalidades avanzadas
- Gestión de suscripciones integrada

### 3. Implementación Técnica
- Arquitectura MVC escalable
- Sistema de logs y trazas integrado
- Tests automatizados para componentes críticos

---

*Documentación generada automáticamente con DocFX - Actualizada: {{ site.time }}*