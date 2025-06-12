# Documento de Visión - DataFiller

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
- **GABRIELA LUZKALID GUTIERREZ MAMANI** (2022074263)

**Tacna – Perú**  
**2025**

---

## Control de Versiones

| Versión | Hecha por | Revisada por | Aprobada por | Fecha | Motivo |
|---------|-----------|--------------|--------------|-------|--------|
| 1.0 | MCR | SFA | GLG | 20/03/2025 | Versión Original |

---

## 1. Descripción del Proyecto

### 1.1 Nombre del proyecto
*DataFiller*

### 1.2 Duración del proyecto
3 meses

### 1.3 Descripción
DataFiller es una plataforma web diseñada para automatizar la generación de datos de prueba realistas para bases de datos. Este proyecto responde a la necesidad crítica que tienen desarrolladores, testers QA y administradores de bases de datos de contar con datos de prueba que reflejen fielmente entornos de producción sin comprometer información sensible.

La importancia de este proyecto radica en que optimiza uno de los procesos más tediosos y propensos a errores en el desarrollo de software: la creación manual de datos de prueba. Al automatizar este proceso, se mejora significativamente la eficiencia de los equipos de desarrollo y QA, permitiéndoles enfocarse en tareas de mayor valor.

El proyecto se desarrollará en un contexto técnico orientado principalmente a profesionales de TI, pero con una interfaz lo suficientemente intuitiva para ser utilizada por usuarios con conocimientos básicos de bases de datos.

### 1.4 Objetivos

#### 1.4.1 Objetivo general
Desarrollar una plataforma web que permita la generación automática de datos de prueba realistas para bases de datos SQL y NoSQL, respetando la estructura de las tablas, sus relaciones y restricciones de integridad.

#### 1.4.2 Objetivos Específicos
- Implementar un sistema de análisis automático de scripts SQL y NoSQL para detectar estructuras de tablas, relaciones y restricciones sin requerir conocimientos técnicos avanzados.
- Desarrollar algoritmos de generación de datos sintéticos realistas que respeten las relaciones entre tablas y restricciones de integridad.
- Crear una interfaz web intuitiva que permita a los usuarios pegar scripts, visualizar resultados y descargar datos generados.
- Implementar un sistema de planes con limitaciones diferenciadas entre usuarios gratuitos y premium.
- Desarrollar un sistema de autenticación de usuarios y gestión de suscripciones con integración a pasarela de pagos.
- Establecer un sistema de soporte por correo electrónico con atención prioritaria para usuarios premium.

## 2. Riesgos

### Riesgos técnicos:
- Problemas de compatibilidad con diferentes sistemas SQL y NoSQL
- Limitaciones en el análisis automático de scripts complejos
- Dificultades de integración con pasarelas de pago

### Riesgos financieros:
- Baja conversión de usuarios gratuitos a premium

### Riesgos operativos:
- Problemas de rendimiento con grandes volúmenes de datos
- Dificultades de mantenimiento o escalabilidad del sistema

### Riesgos de seguridad:
- Exposición de información sensible en el proceso de análisis de scripts

### Riesgos legales:
- Incumplimiento involuntario de regulaciones de privacidad
- Problemas con la implementación de la Ley de Protección de Datos Personales (Ley N.º 29733)

### Riesgos de calidad:
- Generación de datos que no reflejen adecuadamente ambientes reales

## 3. Análisis de la Situación actual

### 3.1 Planteamiento del problema
Actualmente, la generación de datos de prueba realistas representa un desafío significativo en el desarrollo y aseguramiento de la calidad (QA) del software. Los profesionales de TI dedican un tiempo considerable a la creación manual de estos datos, lo que resulta en procesos lentos, costosos y propensos a errores.

La falta de datos que reflejen fielmente el entorno de producción impide identificar y corregir errores en etapas tempranas del desarrollo, lo que puede provocar comportamientos impredecibles cuando las aplicaciones entran en producción. Además, la creación manual de datos de prueba limita la capacidad de realizar pruebas exhaustivas con grandes volúmenes de información.

Por otro lado, el uso de datos reales para pruebas plantea problemas de privacidad y seguridad, especialmente en sectores como salud o finanzas, donde la información es altamente sensible y está sujeta a estrictas regulaciones.

Las soluciones actuales para la generación de datos de prueba suelen ser complejas, costosas o no ofrecen la flexibilidad necesaria para adaptarse a diferentes tipos de bases de datos y necesidades específicas de cada industria.

### 3.2 Consideraciones de hardware y software
Para el desarrollo del sistema se hará uso de la siguiente tecnología:

| **Hardware** | |
|-------------|---|
| Servidores | 1 servidor dedicado con Windows Server (Elastika) |
| Estaciones de trabajo | 3 computadoras para el equipo de desarrollo |
| Red y Conectividad | Conexión de red LAN y acceso a internet de alta velocidad |
| **Software** | |
| Sistema Operativo | Windows 10 para estaciones de trabajo |
| Base de Datos | MySQL 8 para gestionar los datos |
| Control de Versiones | Git (GitHub) |
| Navegadores Compatibles | Google Chrome, Mozilla Firefox |
| **Tecnologías de desarrollo** | |
| Lenguaje de Programación | PHP versión 8 |
| Backend | Desarrollo utilizando PHP versión 8 |
| Frontend | HTML5, CSS3, JavaScript, Bootstrap |
| Plataforma de Desarrollo | IDEs como Visual Studio Code |

## 4. Arquitectura Propuesta

### 4.1 Arquitectura del Sistema

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │    Backend      │    │   Base de       │
│   HTML/CSS/JS   │◄──►│    PHP 8.0      │◄──►│   Datos MySQL   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### 4.2 Funcionalidades Principales

#### 4.2.1 Gestión de Documentos
- Crear nuevos documentos
- Editar documentos existentes
- Eliminar documentos
- Búsqueda y filtrado

#### 4.2.2 Gestión de Usuarios
- Autenticación de usuarios
- Control de permisos
- Perfiles de usuario

#### 4.2.3 Reportes y Exportación
- Exportación a PDF
- Exportación a Excel
- Reportes personalizados

## 5. Cronograma del Proyecto

| Fase | Actividad | Duración | Estado |
|------|-----------|----------|--------|
| 1 | Análisis y Diseño | 2 semanas | ✅ Completado |
| 2 | Desarrollo Backend | 4 semanas | ✅ Completado |
| 3 | Desarrollo Frontend | 3 semanas | ✅ Completado |
| 4 | Pruebas y Testing | 2 semanas | 🔄 En proceso |
| 5 | Documentación | 1 semana | 🔄 En proceso |
| 6 | Deployment | 1 semana | ⏳ Pendiente |

## 6. Conclusiones

DataFiller representa una solución innovadora y necesaria para optimizar la generación de datos de prueba en entornos de desarrollo y aseguramiento de calidad. Su implementación no solo aborda una problemática común en el ámbito tecnológico, sino que también ofrece beneficios económicos, operativos y estratégicos que justifican plenamente la inversión.

A través del análisis de factibilidad técnica, operativa, financiera y ambiental, se ha demostrado que el proyecto es viable, con indicadores positivos como un B/C mayor a 1, un VAN favorable y una TIR del 12%, superando la tasa de descuento.

Al automatizar procesos críticos y eliminar tareas manuales propensas a errores, DataFiller permitirá a los profesionales de TI enfocarse en actividades de mayor valor, mejorando la calidad del software y cumpliendo con estándares de seguridad y normativas vigentes.

Se recomienda firmemente su desarrollo e implementación, al tratarse de una herramienta escalable, rentable y alineada con las necesidades actuales del sector tecnológico.