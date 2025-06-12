# FD01 - Informe de Factibilidad

## Información del Documento

**UNIVERSIDAD PRIVADA DE TACNA**
**FACULTAD DE INGENIERÍA**
**Escuela Profesional de Ingeniería de Sistemas**

### Proyecto "DataFiller"

- **Curso:** Pruebas y Calidad de Software
- **Docente:** Mag. Patrick Cuadros Quiroga

### Integrantes:
- **SEBASTIAN NICOLAS FUENTES AVALOS** (2022073902)
- **MAYRA FERNANDA CHIRE RAMOS** (2021072620)

---

## Control de Versiones

| Versión | Hecha por | Revisada por | Aprobada por | Fecha | Motivo |
|---------|-----------|--------------|--------------|-------|---------|
| 1.0 | MCR | SFA | GLG | 20/03/2025 | Versión Original |

---

## Índice General

1. [Descripción del Proyecto](#descripcion-del-proyecto)
   - 1.1 Nombre del proyecto
   - 1.2 Duración del proyecto  
   - 1.3 Descripción

## 1. Descripción del Proyecto {#descripcion-del-proyecto}

### 1.1 Nombre del proyecto
**DataFiller** - Sistema de Gestión de Documentos

### 1.2 Duración del proyecto
- **Inicio:** Marzo 2025
- **Fin:** Junio 2025
- **Duración total:** 4 meses

### 1.3 Descripción
DataFiller es un sistema web desarrollado en PHP 8.0.30 para la gestión integral de documentos empresariales. El sistema permite:

- Crear, editar y eliminar documentos
- Gestión de usuarios y permisos
- Búsqueda avanzada de documentos
- Exportación de datos en múltiples formatos
- Interfaz web responsive

## 2. Análisis de Factibilidad

### 2.1 Factibilidad Técnica
✅ **Viable** - Tecnologías disponibles y equipo capacitado

### 2.2 Factibilidad Económica  
✅ **Viable** - Presupuesto dentro de límites establecidos

### 2.3 Factibilidad Operativa
✅ **Viable** - Usuario final acepta el sistema propuesto

## 3. Tecnologías Utilizadas

- **Backend:** PHP 8.0.30
- **Base de Datos:** MySQL 8.0
- **Servidor Web:** Apache 2.4 (XAMPP)
- **Frontend:** HTML5, CSS3, JavaScript
- **Hosting:** Azure Web App
- **Control de Versiones:** Git/GitHub

## 4. Arquitectura del Sistema

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │    Backend      │    │   Base de       │
│   HTML/CSS/JS   │◄──►│    PHP 8.0      │◄──►│   Datos MySQL   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## 5. Funcionalidades Principales

### 5.1 Gestión de Documentos
- Crear nuevos documentos
- Editar documentos existentes
- Eliminar documentos
- Búsqueda y filtrado

### 5.2 Gestión de Usuarios
- Autenticación de usuarios
- Control de permisos
- Perfiles de usuario

### 5.3 Reportes y Exportación
- Exportación a PDF
- Exportación a Excel
- Reportes personalizados

## 6. Cronograma del Proyecto

| Fase | Actividad | Duración | Estado |
|------|-----------|----------|---------|
| 1 | Análisis y Diseño | 2 semanas | ✅ Completado |
| 2 | Desarrollo Backend | 4 semanas | ✅ Completado |
| 3 | Desarrollo Frontend | 3 semanas | ✅ Completado |
| 4 | Pruebas y Testing | 2 semanas | 🔄 En proceso |
| 5 | Documentación | 1 semana | 🔄 En proceso |
| 6 | Deployment | 1 semana | ⏳ Pendiente |

## 7. Riesgos Identificados

### 7.1 Riesgos Técnicos
- **Compatibilidad de versiones PHP:** Mitigado usando Docker
- **Rendimiento base de datos:** Mitigado con índices optimizados

### 7.2 Riesgos de Proyecto
- **Cambios en requerimientos:** Metodología ágil implementada
- **Disponibilidad del equipo:** Plan de contingencia definido

## 8. Conclusiones

El proyecto DataFiller es **FACTIBLE** desde todos los aspectos analizados:

1. ✅ **Técnicamente viable** con las tecnologías seleccionadas
2. ✅ **Económicamente rentable** dentro del presupuesto
3. ✅ **Operativamente aceptable** por los usuarios finales

## 9. Recomendaciones

1. Proceder con el desarrollo según cronograma establecido
2. Mantener comunicación constante con usuarios finales
3. Implementar pruebas automatizadas desde el inicio
4. Documentar todos los procesos para mantenimiento futuro

---

*Documento generado para el proyecto DataFiller - SI784 2025-I*
*Fecha de creación: Marzo 2025*