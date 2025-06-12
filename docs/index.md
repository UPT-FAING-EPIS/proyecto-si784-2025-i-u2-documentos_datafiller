# DATAFILLER - Documentación Técnica

## Información del Proyecto
- **Materia:** SI784 - Sistemas de Información  
- **Gestión:** 2025-I
- **Unidad:** U2 - Documentos DataFiller
- **Tecnología:** PHP 8.0.30

## Descripción del Sistema

DATAFILLER es un sistema web desarrollado en PHP para la gestión integral de documentos.

## 📚 Documentación Disponible

### Informes del Proyecto
- [📄 FD01 - Informe de Factibilidad](informes/fd01-informe-factibilidad.md)

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
- **Base de Datos:** MySQL/MariaDB  
- **Servidor Web:** Apache (XAMPP)
- **Frontend:** HTML5, CSS3, JavaScript
- **Documentación:** DocFX
- **CI/CD:** GitHub Actions

### Estructura del Proyecto
```
DATAFILLER/
├── config/          # Configuración del sistema
├── controllers/     # Controladores MVC
├── models/         # Modelos de datos  
├── views/          # Vistas y templates
├── public/         # Archivos públicos
├── tests/          # Pruebas unitarias
├── docs/           # Documentación técnica
└── FD01-EPIS-*.md  # Informes del proyecto
```

## Capturas del Sistema

![Interfaz Principal](images/main-interface.png)

---

*Documentación generada automáticamente con DocFX - Actualizada: {{ site.time }}*