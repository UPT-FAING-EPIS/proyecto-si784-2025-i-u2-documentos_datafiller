# Informe de Proyecto Final - DataFiller

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
| 1.0 | MCR | SFA | GLG | 10/06/2025 | Versión Original |

---

## Índice General

1. [Antecedentes](#1-antecedentes)
2. [Planteamiento del Problema](#2-planteamiento-del-problema)
   - [2.1 Problema](#21-problema)
   - [2.2 Justificación](#22-justificación)
   - [2.3 Alcance](#23-alcance)
3. [Objetivos](#3-objetivos)
   - [3.1 Objetivo general](#31-objetivo-general)
   - [3.2 Objetivos Específicos](#32-objetivos-específicos)
4. [Marco Teórico](#4-marco-teórico)
5. [Desarrollo de la Solución](#5-desarrollo-de-la-solución)
   - [5.1 Análisis de Factibilidad](#51-análisis-de-factibilidad)
     - [5.1.1 Factibilidad Económica](#511-factibilidad-económica)
       - [5.1.1.1 Costos Generales](#5111-costos-generales)
       - [5.1.1.2 Costos operativos durante el desarrollo](#5112-costos-operativos-durante-el-desarrollo)
       - [5.1.1.3 Costos del ambiente](#5113-costos-del-ambiente)
       - [5.1.1.4 Costos de personal](#5114-costos-de-personal)
   - [5.2 Tecnología de Desarrollo](#52-tecnología-de-desarrollo)
   - [5.3 Metodología de implementación](#53-metodología-de-implementación)
   - [5.4 Sistema de Documentación Técnica Automatizada](#54-sistema-de-documentación-técnica-automatizada)
     - [5.4.1 Implementación de DocFX](#541-implementación-de-docfx)
     - [5.4.2 Manual de Usuario basado en Trazas](#542-manual-de-usuario-basado-en-trazas)
     - [5.4.3 Automatización con GitHub Actions](#543-automatización-con-github-actions)
     - [5.4.4 Resultados y Métricas](#544-resultados-y-métricas)
6. [Cronograma](#6-cronograma)
7. [Presupuesto](#7-presupuesto)
8. [Conclusiones](#8-conclusiones)
9. [Anexos](#anexos)
   - [Anexo 01 Informe de Factibilidad](#anexo-01-informe-de-factibilidad)
   - [Anexo 02 Documento de Visión](#anexo-02-documento-de-visión)
   - [Anexo 03 Documento SRS](#anexo-03-documento-srs)
   - [Anexo 04 Documento SAD](#anexo-04-documento-sad)
   - [Anexo 05 Documentación Técnica Automatizada](#anexo-05-documentación-técnica-automatizada)

## 1. Antecedentes

En el desarrollo de software, la necesidad de realizar pruebas con datos realistas y estructurados es fundamental para garantizar la calidad y fiabilidad del producto final. Sin embargo, la generación de estos datos se ha convertido en un obstáculo frecuente para los equipos de desarrollo y QA. Las soluciones existentes para este problema suelen requerir configuraciones complejas, conocimientos técnicos avanzados o incurren en altos costos de licencia. Esto genera una brecha entre los equipos que cuentan con recursos técnicos y económicos avanzados y aquellos que no.

El proyecto DataFiller surge como una solución accesible, automatizada y eficaz para la generación de datos sintéticos. Inspirado en las necesidades reales de proyectos de software en etapa de desarrollo, busca optimizar tiempo, reducir costos y elevar la calidad de las pruebas sin comprometer la privacidad ni depender de datos reales.

## 2. Planteamiento del Problema

### 2.1 Problema

Actualmente, la generación de datos de prueba realistas representa un desafío significativo en el desarrollo y aseguramiento de la calidad (QA) del software. Los profesionales de TI dedican un tiempo considerable a la creación manual de estos datos, lo que resulta en procesos lentos, costosos y propensos a errores.

La falta de datos que reflejen fielmente el entorno de producción impide identificar y corregir errores en etapas tempranas del desarrollo, lo que puede provocar comportamientos impredecibles cuando las aplicaciones entran en producción. Además, la creación manual de datos de prueba limita la capacidad de realizar pruebas exhaustivas con grandes volúmenes de información.

Por otro lado, el uso de datos reales para pruebas plantea problemas de privacidad y seguridad, especialmente en sectores como salud o finanzas, donde la información es altamente sensible y está sujeta a estrictas regulaciones.

Las soluciones actuales para la generación de datos de prueba suelen ser complejas, costosas o no ofrecen la flexibilidad necesaria para adaptarse a diferentes tipos de bases de datos y necesidades específicas de cada industria.

### 2.2 Justificación

La generación de datos de prueba es una tarea crítica pero frecuentemente subestimada en los procesos de desarrollo y aseguramiento de calidad (QA) de software. Los métodos tradicionales, como la creación manual o el uso de datos reales, presentan limitaciones significativas en cuanto a eficiencia, seguridad y escalabilidad. En ese contexto, surge la necesidad de una herramienta que automatice y simplifique este proceso.

DataFiller responde a esta necesidad al ofrecer una plataforma web accesible que permite generar datos sintéticos realistas, respetando estructuras y relaciones entre tablas, sin comprometer información confidencial ni requerir conocimientos técnicos avanzados. Esto no solo optimiza el tiempo de desarrollo y reduce errores humanos, sino que también mejora la calidad de las pruebas al permitir escenarios más complejos y representativos.

Además, la implementación de planes diferenciados (gratuito y premium) permite democratizar el acceso a herramientas avanzadas para equipos de desarrollo con recursos limitados, fomentando la equidad tecnológica. La plataforma también se alinea con buenas prácticas de protección de datos y facilita el cumplimiento de normativas de privacidad al eliminar la necesidad de utilizar información real en ambientes de prueba.

### 2.3 Alcance

El proyecto DataFiller abarca el desarrollo de una plataforma web que permitirá a usuarios generar datos de prueba realistas para bases de datos, con las siguientes funcionalidades e inclusiones:

- Análisis automático de scripts SQL y NoSQL para detectar la estructura de tablas, relaciones y restricciones sin requerir conocimientos técnicos avanzados por parte del usuario.
- Generación de datos sintéticos realistas que respeten las relaciones entre tablas y las restricciones de integridad, con cantidades variables según el plan (10 registros por tabla en plan gratuito, cantidad ampliada en plan premium).
- Implementación de un sistema de planes con limitaciones diferenciadas:
  - Plan gratuito: 3 consultas diarias, 10 registros por tabla, formato SQL
  - Plan premium: Consultas ilimitadas, mayor cantidad de registros, todos los formatos, datos personalizados por industria
- Desarrollo de un sistema de autenticación de usuarios y gestión de suscripciones con integración a pasarela de pagos para el plan premium (S/9.99 mensual).
- Interfaz web intuitiva que permita a los usuarios pegar scripts, visualizar resultados y descargar datos generados.
- Sistema de soporte por correo electrónico para todos los usuarios, con atención prioritaria para usuarios premium.

## 3. Objetivos

### 3.1 Objetivo general

Desarrollar una plataforma web que permita la generación automática de datos de prueba realistas para bases de datos SQL y NoSQL, respetando la estructura de las tablas, sus relaciones y restricciones de integridad.

### 3.2 Objetivos Específicos

- Implementar un sistema de análisis automático de scripts SQL y NoSQL para detectar estructuras de tablas, relaciones y restricciones sin requerir conocimientos técnicos avanzados.
- Desarrollar algoritmos de generación de datos sintéticos realistas que respeten las relaciones entre tablas y restricciones de integridad.
- Crear una interfaz web intuitiva que permita a los usuarios pegar scripts, visualizar resultados y descargar datos generados.
- Implementar un sistema de planes con limitaciones diferenciadas entre usuarios gratuitos y premium.
- Desarrollar un sistema de autenticación de usuarios y gestión de suscripciones con integración a pasarela de pagos.
- Establecer un sistema de soporte por correo electrónico con atención prioritaria para usuarios premium.

## 4. Marco Teórico

- **Datos sintéticos**: Son datos generados artificialmente que imitan la estructura, patrones y relaciones de conjuntos de datos reales. Su uso se ha incrementado como una alternativa segura y eficaz para realizar pruebas sin comprometer información sensible.

- **Integridad referencial**: Hace referencia a la correcta relación entre registros en tablas relacionales. Al generar datos para pruebas, es fundamental preservar estas relaciones para simular escenarios reales.

- **Automatización en pruebas de software**: Se refiere a la utilización de herramientas para realizar pruebas de forma repetitiva y eficiente. La automatización incluye desde la ejecución de pruebas funcionales hasta la preparación de datos y validación de resultados.

- **Lenguajes de modelado y documentación (SRS, SAD, Documento de Visión)**: Son estándares utilizados para definir los requisitos del software (SRS), su arquitectura (SAD) y su objetivo global (Visión), permitiendo una mejor planificación, comunicación y control del proyecto.

- **DocFX**: Herramienta de generación de documentación automática que convierte archivos Markdown y comentarios en el código fuente en sitios web navegables. Fue clave para la documentación técnica del proyecto DataFiller.

## 5. Desarrollo de la Solución

### 5.1 Análisis de Factibilidad

#### 5.1.1 Factibilidad Económica

##### 5.1.1.1 Costos Generales

Se presenta a continuación la descripción de los gastos de los **artículos por adquirir**, incluyendo una **computadora**, junto con sus especificaciones técnicas detalladas y el **costo total** de ambos equipos.

| Artículo | Cantidad | Precio Unitario | Precio Total |
|----------|----------|-----------------|--------------|
| Computadora | 1 | S/ 1300 | S/ 1300 |
| **Total costos generales** | | | **S/ 1300** |

*Tabla 01: En Costos Generales se detallan las utilidades con sus cantidades, precios unitarios, obteniendo un total general de S/ 1300.*

##### 5.1.1.2 Costos operativos durante el desarrollo

Estos son los gastos asociados con la operación del proyecto durante su fase de desarrollo. Incluyen los costos por uso de energía.

| Descripción | Duración | Costo Mensual (PEN) | Precio Total |
|-------------|----------|---------------------|--------------|
| Luz | 1 mes | S/ 50 | S/ 50 |
| Internet | 1 mes | S/ 40 | S/ 40 |
| **Total costos operativos** | | | **S/ 90** |

*Tabla 02: En Costos Operativos se listan los costos mensuales de servicios como luz, sumando un total de S/ 90*

##### 5.1.1.3 Costos del ambiente

A continuación, se presenta el desglose de los costos ambientales, que incluyen los gastos asociados al host del servidor y dominio necesarios para mantener el entorno de pruebas y desarrollo durante el periodo planificado.

| Descripción | Costo Mensual (PEN) | Precio Total |
|-------------|---------------------|--------------|
| Host del Servidor | S/ 32.5 | S/ 32.5 |
| Dominio | S/ 32.5 | S/ 32.5 |
| **Total costos ambientales** | | **S/ 65** |

*Tabla 03: En Costos del Ambiente se muestra el costo por internet y host durante tres meses, con un precio mensual y un subtotal de S/ 65.*

##### 5.1.1.4 Costos de personal

Se presenta el desglose de los costos de personal, que corresponden a los sueldos del equipo necesario para el desarrollo y gestión del proyecto, incluyendo desarrolladores, analistas y dirección del proyecto durante el período planificado.

| Descripción | Cantidad | Duración | Sueldo | Precio Total |
|-------------|----------|----------|--------|--------------|
| Desarrollador de UI | 1 | 3 meses | S/ 850 | S/ 2550 |
| Desarrollador | 1 | 3 meses | S/ 850 | S/ 2550 |
| Director del proyecto | 1 | 3 meses | S/ 850 | S/ 2550 |
| **Total costos de personal** | | | | **S/ 7,650** |

*Tabla 04: El Costo de Personal muestra los salarios mensuales y totales de tres meses para un Diseñador de UI, un Desarrollador y un Director de Proyecto, con un costo total combinado de S/ 7,650.*

#### Factibilidad operativa:

Se considera viable tras analizar la situación actual, donde se han identificado cuatro áreas principales que requieren mejora: la generación manual de datos de prueba, el cumplimiento de restricciones de integridad, la simulación de datos realistas y la protección de información sensible. Actualmente, los equipos de desarrollo y QA generan estos datos de forma manual, utilizando scripts personalizados o insertando información directamente en las bases de datos de prueba.

El sistema propuesto transformará estos procesos mediante la implementación de un análisis automático de scripts SQL y NoSQL, reemplazando la interpretación manual actual; el desarrollo de algoritmos de generación de datos que respetan las relaciones entre tablas; la optimización del proceso de creación de datos sintéticos realistas; la integración de una plataforma web para visualización inmediata de resultados; y la creación de un sistema de descarga de datos generados en diferentes formatos.

Los principales beneficiarios serán los desarrolladores con la reducción del tiempo dedicado a crear datos de prueba, los testers QA con mejor calidad de datos para validaciones, los administradores de bases de datos con datos que respetan la integridad referencial, y los gerentes de proyecto con reducción de costos y tiempos de desarrollo. Se contempla un sistema de soporte y documentación para asegurar la correcta adopción de la plataforma por parte de los usuarios, garantizando así la transformación efectiva de los procesos manuales actuales a una solución tecnológica eficiente.

#### Factibilidad Social:

La implementación del sistema web DataFiller es socialmente beneficiosa, ya que la capacitación permitirá a los desarrolladores y equipos de QA adaptarse rápidamente a una herramienta que automatiza procesos tediosos, mejorando su eficiencia, satisfacción laboral y habilidades técnicas en beneficio de los proyectos de software. Además, hace que la generación de datos de prueba sea más accesible y atractiva para equipos con diferentes niveles de conocimiento técnico, promoviendo mejores prácticas en el desarrollo de software y pruebas.

Al facilitar el acceso a datos de prueba realistas sin comprometer información sensible, DataFiller contribuye a la protección de la privacidad de los usuarios finales y al cumplimiento de normativas de protección de datos, lo que reduce riesgos legales y éticos relacionados con el manejo de información. Además, la plataforma se convierte en una herramienta educativa que promueve la formación en buenas prácticas de pruebas, creando comunidades de desarrolladores y testers más capacitados.

#### Factibilidad Legal:

La información manejada por la empresa será completamente confidencial. Se aplicará la Ley de Protección de Datos Personales en Perú (Ley N.º 29733), que regula el tratamiento de datos personales, sin importar el soporte en el que sean gestionados. Esta ley garantiza los derechos de las personas sobre sus datos personales y establece las obligaciones para quienes recolectan, almacenan o procesan dicha información.

#### Factibilidad Ambiental:

La implementación del sistema web DataFiller tiene un impacto ambiental reducido, pero se han considerado diversos factores relacionados con la sostenibilidad y los Objetivos de Desarrollo Sostenible (ODS):

- **Alineación con ODS 9 (Industria, Innovación e Infraestructura)**: El proyecto promueve la innovación tecnológica sostenible al optimizar procesos de prueba de software, reduciendo tiempos y recursos necesarios para el desarrollo de aplicaciones.

- **Contribución al ODS 12 (Producción y Consumo Responsables)**: Al facilitar la generación de datos sintéticos, se elimina la necesidad de utilizar datos reales en entornos de prueba, promoviendo un uso más responsable de la información.

- **Apoyo al ODS 13 (Acción por el Clima)**: Mediante la reducción de la huella de carbono asociada al desarrollo de software, contribuyendo a la transición hacia una economía digital más sostenible.

- **Digitalización de procesos**: El sistema elimina la necesidad de generar datos de prueba manualmente, reduciendo el consumo de energía y recursos asociados a procesos tradicionales más intensivos.

### 5.2 Tecnología de Desarrollo

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

### 5.3 Metodología de implementación

La metodología de implementación para este proyecto será de tipo predictivo, lo que implica que se seguirá un enfoque secuencial y planificado, con una visión clara de los requisitos y objetivos desde el inicio del proyecto.

### 5.4 Sistema de Documentación Técnica Automatizada

#### 5.4.1 Implementación de DocFX

DocFX v2.78.3 fue seleccionado como herramienta para la generación automática de documentación técnica del sistema DataFiller. Esta herramienta permite crear documentación estructurada a partir de archivos Markdown y comentarios en el código fuente PHP.

La configuración se implementó mediante un archivo `docfx.json` que define la estructura y elementos a documentar:

```json
{
  "metadata": [
    {
      "src": [
        {
          "files": ["**/*.php"],
          "src": "../DATAFILLER"
        }
      ],
      "dest": "api"
    }
  ],
  "build": {
    "content": [
      {
        "files": ["*.md", "manual/*.md", "informes/*.md", "api/*.yml"]
      }
    ],
    "resource": [
      {
        "files": ["images/**", "videos/**", "traces/**"]
      }
    ],
    "dest": "_site",
    "template": ["default"],
    "globalMetadata": {
      "_appTitle": "DATAFILLER - Documentación Técnica",
      "_enableSearch": true
    }
  }
}
```

La estructura de carpetas se organizó siguiendo las mejores prácticas de DocFX:
- `/docs`: Archivos fuente de documentación
- `/docs/manual`: Manuales de usuario y técnicos
- `/docs/informes`: Documentos formales del proyecto
- `/docs/api`: Documentación API generada automáticamente
- `/docs/images`: Recursos visuales para la documentación

#### 5.4.2 Manual de Usuario basado en Trazas

El manual de usuario fue desarrollado utilizando una metodología basada en trazas reales de uso del sistema y videos de pruebas de interfaz de usuario. El proceso incluyó:

1. **Captura de logs de actividad**: Registro automático de acciones del usuario en formato estructurado.

```
[2025-06-12 14:30:00] INFO: Usuario admin inició sesión
[2025-06-12 14:30:15] INFO: Navegación a módulo documentos
[2025-06-12 14:30:30] INFO: Documento "Test Doc" creado exitosamente
```

2. **Grabación de sesiones de usuario**: Videos de flujos completos de trabajo desde el login hasta operaciones completas.

3. **Análisis de patrones de uso**: Identificación de rutas críticas y casos de uso frecuentes.

4. **Documentación estructurada**: Transformación de trazas en instrucciones paso a paso con capturas de pantalla.

5. **Validación con usuarios reales**: Verificación de claridad y usabilidad del manual generado.

#### 5.4.3 Automatización con GitHub Actions

Se implementó un flujo de trabajo de integración continua para la documentación mediante GitHub Actions, que permite la actualización automática de la documentación técnica cada vez que se modifican los archivos fuente.

```yaml
name: Generate Documentation

on:
  push:
    branches: [ main ]
    paths:
      - 'docs/**/*.md'
      - 'docs/docfx.json'
      - 'DATAFILLER/**/*.php'

jobs:
  build-docs:
    runs-on: windows-latest

    steps:
    - name: Checkout code
      uses: actions/checkout@v4
    
    - name: Setup DocFX
      run: |
        Invoke-WebRequest -Uri "https://github.com/dotnet/docfx/releases/download/v2.78.3/docfx-win-x64-v2.78.3.zip" -OutFile "docfx.zip"
        Expand-Archive -Path "docfx.zip" -DestinationPath "docfx"
        
    - name: Build Documentation
      run: |
        cd docs
        ..\docfx\docfx.exe docfx.json
        
    - name: Deploy to Azure Web App
      uses: azure/webapps-deploy@v2
      with:
        app-name: 'datafiller2'
        publish-profile: ${{ secrets.AZURE_WEBAPP_PUBLISH_PROFILE }}
        package: ./deploy
        
    - name: Upload artifact (backup)
      uses: actions/upload-artifact@v4
      with:
        name: datafiller-documentation
        path: docs/_site
        retention-days: 30
```

El flujo de trabajo comprende:
1. Detección de cambios en archivos Markdown, configuración o código fuente PHP
2. Instalación automática de DocFX
3. Generación de la documentación HTML
4. Despliegue automático a una instancia Azure Web App
5. Respaldo de la documentación como artifact en GitHub

#### 5.4.4 Resultados y Métricas

La implementación del sistema de documentación automatizada proporcionó los siguientes resultados:

- **Sitio web de documentación**: Accesible en https://datafiller2-b2cbeph0h3a3hfgy.eastus-01.azurewebsites.net/docs/
- **Tiempo promedio de actualización**: 3-5 minutos desde commit hasta publicación
- **Cobertura de documentación**: 100% de clases y métodos públicos documentados
- **Formatos disponibles**: HTML interactivo, PDF descargable (cuando activado)
- **Estadísticas de contenido**: 
  - 15+ páginas de documentación
  - 3 manuales completos
  - Documentación API de 20+ clases
  - 50+ capturas de pantalla y diagramas

La documentación técnica ahora está completamente sincronizada con el código fuente, eliminando la desactualización común en proyectos de desarrollo.

## 6. Cronograma

| Fase | Actividad | Duración | Estado |
|------|-----------|----------|--------|
| 1 | Análisis y Diseño | 2 semanas | ✅ Completado |
| 2 | Desarrollo Backend | 4 semanas | ✅ Completado |
| 3 | Desarrollo Frontend | 3 semanas | ✅ Completado |
| 4 | Pruebas y Testing | 2 semanas | 🔄 En proceso |
| 5 | Documentación | 1 semana | 🔄 En proceso |
| 6 | Deployment | 1 semana | ⏳ Pendiente |

## 7. Presupuesto

Costos totales del desarrollo del sistema

| Tipos de costo | Subtotal |
|----------------|----------|
| Costos Generales | S/ 1300 |
| Costos Operativos | S/ 90 |
| Costos Ambientales | S/ 65 |
| Costos de Personal | S/ 7,650 |
| **Total** | **S/ 9,105** |

## 8. Conclusiones

El proyecto DataFiller representa una solución integral y escalable para la generación automatizada de datos de prueba en entornos de desarrollo y pruebas de bases de datos SQL y NoSQL. A lo largo del desarrollo de esta plataforma web, se ha aplicado una arquitectura Modelo-Vista-Controlador (MVC) para garantizar una separación clara de responsabilidades, facilitar el mantenimiento del código y permitir una futura escalabilidad del sistema.

La estructura modular del proyecto, evidenciada en carpetas como controllers, models, y public, ha permitido organizar de forma eficiente la lógica de negocio, las entidades de datos y los recursos del cliente. El uso de bibliotecas externas a través de composer y la integración con FakerPHP para la generación de datos aleatorios personalizados ha sido fundamental para alcanzar la flexibilidad esperada.

Además, se han implementado pruebas unitarias en el directorio tests/Unit, lo cual asegura la estabilidad del núcleo funcional del sistema y refuerza las buenas prácticas de desarrollo orientado a pruebas (TDD). Estas pruebas están diseñadas para garantizar el correcto funcionamiento de los módulos más críticos, apoyándose en herramientas como PHPUnit.

Durante la implementación, se integraron recursos multimedia y archivos generados en carpetas como images/videos, logs, y resultados_BORRABLE, los cuales demuestran la interacción activa del usuario con la plataforma y el resultado tangible del procesamiento de datos.

Asimismo, el despliegue en la nube mediante Azure App Service y la organización del repositorio en GitHub favorecen una integración continua y control de versiones eficiente, alineado con metodologías DevOps.

En resumen, DataFiller no solo cumple con su objetivo principal de ofrecer datos realistas para pruebas, sino que también sienta las bases para convertirse en una herramienta robusta y extensible en el ecosistema de desarrollo ágil. Su estructura organizada, el uso de herramientas modernas y la clara orientación a la calidad del software lo posicionan como una solución tecnológica sólida y de gran utilidad para desarrolladores y testers.