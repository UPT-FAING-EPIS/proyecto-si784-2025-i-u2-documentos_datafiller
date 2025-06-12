# DataFiller - Documento de Visión

**Versión 1.0**

**Universidad Privada de Tacna**  
**Facultad de Ingeniería**  
**Escuela Profesional de Ingeniería de Sistemas**

**Proyecto: DataFiller**

**Curso:** Pruebas y Calidad de Software  
**Docente:** Mag. Patrick Cuadros Quiroga

**Integrantes:**
- Sebastián Nicolás Fuentes Avalos (2022073902)
- Mayra Fernanda Chire Ramos (2021072620)
- Gabriela Luzkalid Gutierrez Mamani (2022074263)

**Tacna – Perú, 2025**

## Control de Versiones

| Versión | Hecha por | Revisada por | Aprobada por | Fecha | Motivo |
|:-----:|:-----:|:-----:|:-----:|:-----:|:-----:|
| 1.0 | MCR | SFA | GLG | 20/03/2025 | Versión Original |

## Índice

1. [Introducción](#1-introducción)
   1. [Propósito](#11-propósito)
   2. [Alcance](#12-alcance)
   3. [Definiciones, Siglas y Abreviaturas](#13-definiciones-siglas-y-abreviaturas)
   4. [Referencias](#14-referencias)
   5. [Visión General](#15-visión-general)
2. [Posicionamiento](#2-posicionamiento)
   1. [Oportunidad de negocio](#21-oportunidad-de-negocio)
   2. [Definición del problema](#22-definición-del-problema)
3. [Descripción de los interesados y usuarios](#3-descripción-de-los-interesados-y-usuarios)
   1. [Resumen de los interesados](#31-resumen-de-los-interesados)
   2. [Resumen de los usuarios](#32-resumen-de-los-usuarios)
   3. [Entorno de usuario](#33-entorno-de-usuario)
   4. [Perfiles de los interesados](#34-perfiles-de-los-interesados)
   5. [Perfiles de los Usuarios](#35-perfiles-de-los-usuarios)
   6. [Necesidades de los interesados y usuarios](#36-necesidades-de-los-interesados-y-usuarios)
4. [Vista General del Producto](#4-vista-general-del-producto)
   1. [Perspectiva del producto](#41-perspectiva-del-producto)
   2. [Resumen de capacidades](#42-resumen-de-capacidades)
   3. [Suposiciones y dependencias](#43-suposiciones-y-dependencias)
   4. [Costos y precios](#44-costos-y-precios)
5. [Características del producto](#5-características-del-producto)
6. [Restricciones](#6-restricciones)
7. [Rangos de calidad](#7-rangos-de-calidad)
8. [Precedencia y Prioridad](#8-precedencia-y-prioridad)
9. [Otros requerimientos del producto](#9-otros-requerimientos-del-producto)
10. [Estándares legales](#10-estándares-legales)
11. [Estándares de comunicación](#11-estándares-de-comunicación)
12. [Estándares de cumplimiento de la plataforma](#12-estándares-de-cumplimiento-de-la-plataforma)
13. [Estándares de calidad y seguridad](#13-estándares-de-calidad-y-seguridad)
14. [Conclusiones](#conclusiones)
15. [Recomendaciones](#recomendaciones)
16. [Bibliografía](#bibliografía)
17. [Webgrafía](#webgrafía)

## 1. Introducción

### 1.1 Propósito

El propósito de DataFiller es facilitar la generación de datos de prueba realistas y consistentes para bases de datos, permitiendo a desarrolladores y equipos de aseguramiento de calidad (QA) realizar pruebas más precisas y eficientes. Al automatizar la creación de datos sintéticos que respeten la estructura y las restricciones de las bases de datos, DataFiller contribuye a mejorar la calidad del software y reducir el tiempo de desarrollo, sin comprometer la seguridad ni la privacidad.

### 1.2 Alcance

El proyecto DataFiller abarcará el desarrollo de una plataforma web que permitirá a usuarios generar datos de prueba realistas para bases de datos, con las siguientes funcionalidades e inclusiones:

- Análisis automático de scripts SQL y NoSQL para detectar la estructura de tablas, relaciones y restricciones sin requerir conocimientos técnicos avanzados por parte del usuario.
- Generación de datos sintéticos realistas que respeten las relaciones entre tablas y las restricciones de integridad, con cantidades variables según el plan (10 registros por tabla en plan gratuito, cantidad ampliada en plan premium).
- Implementación de un sistema de planes con limitaciones diferenciadas:
  - Plan gratuito: 3 consultas diarias, 10 registros por tabla, formato SQL
  - Plan premium: Consultas ilimitadas, mayor cantidad de registros, todos los formatos, datos personalizados por industria
- Desarrollo de un sistema de autenticación de usuarios y gestión de suscripciones con integración a pasarela de pagos para el plan premium (S/9.99 mensual).
- Interfaz web intuitiva que permita a los usuarios pegar scripts, visualizar resultados y descargar datos generados.
- Sistema de soporte por correo electrónico para todos los usuarios, con atención prioritaria para usuarios premium.

### 1.3 Definiciones, Siglas y Abreviaturas

- SQL: Lenguaje de Consulta Estructurado
- NoSQL: Not Only SQL
- BD: Base de Datos
- SCPT: Scripts
- IA: Inteligencia Artificial

### 1.4 Referencias

- **Test Data Management – Best Practices**  
  La gestión de datos de prueba es fundamental para el desarrollo de software. **TDM** se refiere al proceso de creación, manejo y control de datos de prueba para asegurar que se refleje adecuadamente el comportamiento real del sistema sin comprometer la calidad de los datos. DataFiller se alinea con estas prácticas al generar datos sintéticos que permiten probar sistemas sin necesidad de usar información real.

- **PCI-DSS (Payment Card Industry Data Security Standard)**  
  Si DataFiller implementa un sistema de pagos, como la integración con una pasarela de pago para suscripciones premium, es crucial cumplir con el PCI-DSS, un conjunto de estándares que garantizan la seguridad de las transacciones financieras. Aunque no es directamente una ley, es un marco que regula cómo deben manejarse los datos financieros.

- **Synthetic Data in Machine Learning**  
  La generación de datos sintéticos es una técnica utilizada en proyectos de **machine learning** y **big data**. En particular, cuando los datos reales son limitados o sensibles, se generan datos sintéticos para entrenar modelos y hacer pruebas sin comprometer la privacidad. DataFiller toma inspiración de esta práctica al generar datos que respeten las relaciones y restricciones, garantizando su uso en pruebas y simulaciones de bases de datos.

### 1.5 Visión General

DataFiller es una plataforma web diseñada para automatizar la generación de datos de prueba realistas para bases de datos. Su principal objetivo es permitir a desarrolladores y equipos de QA ahorrar tiempo y mejorar la calidad de sus pruebas al proporcionar datos precisos y representativos de manera rápida y sencilla.

La plataforma ofrece una serie de funcionalidades esenciales que facilitan la creación de datos de prueba. Una de ellas es la generación rápida de registros, que permite optimizar el proceso de pruebas y desarrollo al automatizar la creación de información de manera instantánea. Además, los datos generados son realistas, simulando información del mundo real para obtener resultados más precisos y representativos.

Para los usuarios que requieren mayor capacidad y personalización, DataFiller ofrece un Plan Premium con beneficios exclusivos. Este plan permite realizar consultas ilimitadas, eliminando las restricciones diarias en la generación de datos. También proporciona datos personalizados por industria, adaptándose a sectores como salud, finanzas y comercio electrónico. Además, los usuarios premium cuentan con soporte prioritario, asegurando respuestas rápidas y soluciones efectivas ante cualquier inconveniente. Por último, el plan premium incluye la generación de registros ampliada, ideal para proyectos que requieren grandes volúmenes de datos.

## 2. Posicionamiento

### 2.1 Oportunidad de negocio

Dada la dificultad de generar datos de prueba realistas de manera manual, surge una oportunidad de negocio significativa para herramientas que automaticen este proceso. La implementación de una plataforma como DataFiller optimiza los procesos de prueba, reduciendo el tiempo y esfuerzo requeridos y garantizando que los datos utilizados sean representativos de escenarios reales.

Además, la creciente adopción de metodologías ágiles y DevOps en la industria del software aumenta la demanda de soluciones que soporten ciclos de desarrollo rápidos y entregas continuas. Herramientas que faciliten la generación automatizada de datos de prueba realistas se alinean con estas prácticas, ofreciendo un valor añadido al integrarse en flujos de trabajo modernos y contribuyendo a la eficiencia de los procesos de desarrollo y prueba.

Por otro lado, sectores como salud, finanzas y comercio electrónico requieren datos específicos y altamente precisos para garantizar la seguridad y efectividad de sus sistemas. La posibilidad de generar datos personalizados por industria amplía aún más el potencial de mercado de estas soluciones, brindando una ventaja competitiva clave para empresas que buscan mejorar la calidad de sus productos sin comprometer la seguridad ni la privacidad de la información.

### 2.2 Definición del problema

La generación de datos de prueba realistas es un desafío clave en el desarrollo y aseguramiento de la calidad (QA) del software. Contar con datos que reflejen fielmente el entorno de producción es esencial para identificar y corregir errores en etapas tempranas del desarrollo, mejorando la calidad del producto final y reduciendo costos asociados a fallos detectados tardíamente.

Sin embargo, la creación manual de estos datos suele ser compleja, lenta y propensa a errores, lo que puede provocar comportamientos impredecibles en las aplicaciones cuando entran en producción. Además, la falta de datos de prueba adecuados puede resultar en pruebas incompletas o ineficaces, afectando negativamente la confiabilidad y seguridad del software. Estos problemas generan la necesidad de soluciones que permitan automatizar la generación de datos de prueba con precisión y eficiencia.

## 3. Descripción de los interesados y usuarios

### 3.1 Resumen de los interesados

- **Equipo de desarrollo de DataFiller:** Responsables de crear y mantener la plataforma, interesados en el éxito técnico y comercial del producto.
- **Inversores/Patrocinadores:** Proporcionan el capital para desarrollar la plataforma, interesados en el retorno de inversión y viabilidad comercial.
- **Usuarios potenciales:** Desarrolladores, testers QA, DBAs y académicos que utilizarán la herramienta para generar datos de prueba.

### 3.2 Resumen de los usuarios

Los usuarios de DataFiller abarcan perfiles técnicos y educativos que requieren datos generados automáticamente para distintas aplicaciones. Desde desarrolladores y testers que buscan agilizar sus pruebas hasta administradores de bases de datos que necesitan simular escenarios realistas, la plataforma ofrece una solución eficiente y adaptable a diferentes necesidades. Además, su uso en el ámbito académico facilita la enseñanza práctica sin la necesidad de invertir tiempo en la creación manual de datos de prueba.

### 3.3 Entorno de usuario

DataFiller está diseñado para ser utilizado en entornos donde se requiere la generación automática de datos de prueba realistas. La plataforma es accesible a través de una interfaz web intuitiva, permitiendo a los usuarios definir parámetros de generación, seleccionar formatos de exportación y configurar datos personalizados según sus necesidades.

El sistema es compatible con bases de datos SQL y NoSQL, lo que lo hace ideal para equipos de desarrollo, QA y administración de bases de datos en empresas tecnológicas, startups, instituciones académicas y sectores que requieren simulaciones de datos realistas. Además, ofrece funcionalidades avanzadas en su versión premium, como generación ampliada de datos y personalización por industria, facilitando su integración en proyectos de cualquier escala.

### 3.4 Perfiles de los interesados

- **Desarrolladores de Software:** Utilizan la plataforma para generar datos de prueba que permitan validar el comportamiento de sus aplicaciones en distintos escenarios, asegurando su correcto funcionamiento antes del despliegue.
- **Ingenieros de QA / Testers:** Necesitan conjuntos de datos variados y realistas para ejecutar pruebas funcionales, de rendimiento y de seguridad, garantizando la estabilidad y confiabilidad del software.
- **Administradores de Bases de Datos (DBAs):** Emplean la herramienta para evaluar el rendimiento, optimizar consultas y realizar pruebas de migración con volúmenes de datos controlados y representativos.
- **Estudiantes y Profesores de Informática:** En entornos educativos, la plataforma les permite acceder a datos estructurados sin necesidad de crearlos manualmente, facilitando la enseñanza y el aprendizaje de bases de datos, desarrollo y pruebas de software.
- **Empresas y Startups Tecnológicas:** Organizaciones que buscan optimizar sus procesos de prueba y reducir costos mediante la automatización de la generación de datos de prueba adaptados a sus necesidades específicas.

### 3.5 Perfiles de los Usuarios

- **Desarrolladores de software:** Necesitan datos realistas para probar sus aplicaciones durante el ciclo de desarrollo.
- **Ingenieros QA / Testers:** Requieren conjuntos de datos variados para realizar pruebas exhaustivas y validar la funcionalidad del software.
- **Administradores de bases de datos (DBAs):** Para probar rendimiento, optimizaciones o migraciones con volúmenes controlados de datos.
- **Estudiantes y profesores de informática:** Para entornos educativos donde necesitan trabajar con datos sin crearlos manualmente.

### 3.6 Necesidades de los interesados y usuarios

**Necesidades de los Interesados:**

- **Desarrolladores de Software:**
  - Acceder a datos de prueba realistas para evaluar el funcionamiento de sus aplicaciones en diferentes escenarios.
  - Reducir el tiempo dedicado a la creación manual de datos de prueba.
  - Garantizar que los datos de prueba reflejen situaciones del mundo real para mejorar la calidad del desarrollo.

- **Ingenieros de QA / Testers:**
  - Obtener grandes volúmenes de datos variados para realizar pruebas funcionales, de rendimiento y seguridad.
  - Simular diferentes escenarios con datos representativos para identificar fallos antes del despliegue.
  - Mejorar la automatización de pruebas con datos generados dinámicamente.

- **Administradores de Bases de Datos (DBAs):**
  - Probar y optimizar el rendimiento de bases de datos con volúmenes de datos realistas.
  - Evaluar la eficiencia de consultas y procedimientos almacenados en bases de datos grandes.
  - Simular migraciones de datos y verificar la integridad de la información en entornos de prueba.

- **Estudiantes y Profesores de Informática:**
  - Disponer de datos predefinidos para prácticas y experimentos sin necesidad de crearlos manualmente.
  - Facilitar el aprendizaje de bases de datos, pruebas de software y desarrollo con ejemplos prácticos.
  - Enseñar técnicas de gestión de datos sin depender de información sensible o datos reales.

- **Empresas y Startups Tecnológicas:**
  - Reducir costos y tiempos en la generación de datos de prueba para sus productos y servicios.
  - Cumplir con regulaciones de privacidad al usar datos sintéticos en lugar de información real.
  - Mejorar la calidad del software mediante pruebas más precisas y eficientes.

**Necesidades de los Usuarios:**

- **Fácil de usar:** con una interfaz intuitiva que permita la configuración rápida de datos de prueba.
- **Flexible:** compatible con distintos tipos de bases de datos (SQL y NoSQL) y formatos de exportación (SQL, JSON).
- **Escalable:** capaz de generar grandes volúmenes de datos para diferentes entornos de prueba.
- **Precisa:** proporcionando datos representativos de escenarios reales para garantizar la efectividad de las pruebas.
- **Segura:** evitando el uso de datos reales y asegurando la protección de la información sensible.

## 4. Vista General del Producto

### 4.1 Perspectiva del producto

DataFiller es una plataforma web diseñada para la generación automatizada de datos de prueba realistas, dirigida a desarrolladores, testers, administradores de bases de datos y educadores. Su objetivo es optimizar los procesos de prueba y desarrollo mediante la creación rápida de registros estructurados, asegurando compatibilidad con bases de datos SQL y NoSQL.

El producto se distingue por su capacidad de análisis inteligente de estructuras de bases de datos, generación de datos personalizados por industria y exportación en múltiples formatos. Además, ofrece un modelo de suscripción con funcionalidades avanzadas en su versión premium, facilitando su uso en proyectos de cualquier escala.

### 4.2 Resumen de capacidades

- Generación rápida de registros para optimizar pruebas y desarrollo.
- Datos realistas que simulan información verosímil para pruebas precisas.
- Compatibilidad universal con bases de datos SQL y NoSQL.
- Análisis inteligente de estructuras para adaptar los datos generados a cada base de datos.
- Exportación en múltiples formatos, incluyendo SQL y JSON.
- Consultas ilimitadas en el Plan Premium.
- Personalización de datos por industria (salud, finanzas, comercio, etc.) en el Plan Premium.
- Soporte prioritario para usuarios Premium.
- Generación de registros ampliada para proyectos que requieren grandes volúmenes de datos.

### 4.3 Suposiciones y dependencias

- Se asume que los usuarios tienen conocimientos básicos en bases de datos para configurar la generación de datos de manera eficiente.
- La plataforma requiere una conexión a internet para su funcionamiento, ya que es un servicio basado en la nube.
- Se espera que las bases de datos con las que se integrará DataFiller sean compatibles con formatos estándar de exportación como SQL y JSON.
- Depende de un backend robusto para procesar grandes volúmenes de datos sin afectar el rendimiento.
- La seguridad de los datos generados es una prioridad, por lo que se implementarán medidas para evitar el uso de datos sensibles o reales en las simulaciones.

### 4.4 Costos y precios

**Costos totales del desarrollo del sistema**

| Tipos de costo | Subtotal |
|----------------|----------|
| Costos Generales | S/ 1300 |
| Costos Operativos | S/ 90 |
| Costos Ambientales | S/ 65 |
| Costos de Personal | S/ 7,650 |
| **Total** | **S/ 9,105** |

## 5. Características del producto

- **Generación Rápida de Registros**  
  DataFiller permite la creación instantánea de grandes volúmenes de datos de prueba, eliminando la necesidad de generar registros manualmente. Esta funcionalidad optimiza el tiempo de desarrollo y prueba, facilitando la validación de aplicaciones y bases de datos en diferentes escenarios. Gracias a su capacidad de automatización, los usuarios pueden obtener datos en segundos, mejorando significativamente la eficiencia de los equipos de desarrollo y QA.

- **Datos Realistas y Personalizables**  
  Los datos generados por DataFiller imitan información realista, garantizando resultados precisos y representativos en las pruebas. Además, la plataforma permite personalizar los datos según la industria, como salud, finanzas y comercio electrónico, asegurando que la información generada sea adecuada para cada caso de uso específico. Esta capacidad de adaptación facilita pruebas más cercanas a escenarios reales, mejorando la calidad del software.

- **Compatibilidad Universal con Bases de Datos**  
  DataFiller es compatible con una amplia variedad de bases de datos, incluyendo sistemas SQL como MySQL, PostgreSQL y SQL Server, así como bases de datos NoSQL como MongoDB y Firebase. Esta compatibilidad permite a los usuarios integrar la plataforma en cualquier entorno de trabajo sin preocuparse por restricciones técnicas. Además, la posibilidad de exportar los datos en múltiples formatos, como SQL y JSON, facilita su implementación en diferentes proyectos.

- **Análisis Inteligente de Estructuras**  
  Una de las características clave de DataFiller es su capacidad para analizar automáticamente la estructura de las bases de datos. Esto significa que la plataforma detecta las tablas, tipos de datos y relaciones existentes, generando registros adaptados a la estructura de cada base de datos. Esta funcionalidad reduce la carga de configuración manual y permite una integración más rápida y eficiente en cualquier sistema.

- **Interfaz Intuitiva y Fácil de Usar**  
  DataFiller cuenta con una interfaz basada en la web, accesible desde cualquier dispositivo con conexión a internet. Su diseño intuitivo permite que cualquier usuario, sin importar su nivel de experiencia, pueda generar datos con facilidad. Las herramientas de configuración son simples y directas, lo que facilita la personalización de los registros sin necesidad de conocimientos avanzados en bases de datos.

- **Seguridad y Protección de Datos**  
  La plataforma garantiza que todos los datos generados sean completamente sintéticos y no provengan de fuentes reales, eliminando riesgos relacionados con la privacidad y el uso indebido de información. Esto permite a las empresas y desarrolladores realizar pruebas en entornos seguros sin comprometer datos sensibles. Además, DataFiller implementa medidas de seguridad para evitar el acceso no autorizado y asegurar la integridad de la información generada.

- **Planes de Uso Ajustados a Diferentes Necesidades**  
  DataFiller ofrece diferentes planes de uso según las necesidades de cada usuario. La versión gratuita permite el acceso a funcionalidades básicas con ciertas restricciones en la cantidad de datos generados. Por otro lado, el Plan Premium proporciona beneficios adicionales como consultas ilimitadas, generación de datos personalizados por industria y soporte prioritario, lo que lo convierte en una solución escalable tanto para pequeñas empresas como para grandes organizaciones.

- **Soporte y Actualizaciones Constantes**  
  Para garantizar la mejor experiencia de usuario, DataFiller ofrece soporte técnico y actualizaciones continuas. Los usuarios del Plan Premium tienen acceso a asistencia prioritaria para resolver cualquier duda o inconveniente. Además, la plataforma se actualiza constantemente con nuevas funcionalidades, mejoras en la generación de datos y compatibilidad con más formatos y tecnologías, asegurando su vigencia en un entorno de desarrollo en constante evolución.

## 6. Restricciones

- **Generación de Datos Sintéticos Realistas**  
  No generar ni usar datos personales reales en la plataforma, porque según la Reglamento General de Protección de Datos (GDPR) de la Unión Europea y otras normativas de privacidad como la Ley de Protección de Datos Personales (LPDP) en varios países, es ilegal procesar, almacenar o utilizar datos personales sin el consentimiento explícito de la persona o sin cumplir con los principios de protección de datos.

- **Limitaciones en el Uso de Datos para Testeo en Entornos Sensibles**  
  Los usuarios no deben utilizar la plataforma para generar datos sensibles que puedan violar la privacidad en sectores como salud, finanzas o datos de tarjetas de crédito, porque el uso de datos sensibles (como datos médicos, financieros o información de tarjetas de crédito) está regulado por leyes estrictas, como la HIPAA (Health Insurance Portability and Accountability Act) en EE. UU., y la PSD2 en la UE para pagos electrónicos. Estas leyes restringen el uso y procesamiento de este tipo de datos sin garantías de privacidad y seguridad.

- **Cumplimiento con Normativas de Pago y Transacciones (PCI-DSS)**  
  El sistema de gestión de pagos para el Plan Premium debe cumplir con la Norma de Seguridad de Datos para la Industria de Tarjetas de Pago (PCI-DSS), que regula cómo deben manejarse las transacciones de pago y la información financiera. PCI-DSS es un conjunto de estándares para proteger los datos de pago, como los números de tarjeta de crédito, los detalles de facturación, etc. Cualquier plataforma que maneja pagos o información financiera debe cumplir con estos requisitos para garantizar la seguridad de las transacciones y la protección contra el fraude.

Cabe recalcar que ante el incumplimiento de estas restricciones, puede llevar a multas, pérdida de confianza del usuario y responsabilidades legales.

## 7. Rangos de calidad

El sistema DataFiller debe cumplir con los siguientes rangos de calidad, asegurando un servicio eficiente y satisfactorio para todos los usuarios, tanto de los planes gratuitos como premium:

- **Disponibilidad del sistema:** El sistema debe estar disponible al menos el 90% del tiempo, garantizando un servicio continuo durante el horario de operación, con tiempos mínimos de inactividad planificada solo para tareas de mantenimiento y actualizaciones.
- **Rendimiento:** El tiempo de respuesta para la generación de datos debe ser ágil para consultas y generaciones de registros pequeñas (hasta 10 registros por tabla). En escenarios con gran volumen de datos, como en el plan premium, el tiempo de procesamiento no debe superar los 10 segundos por consulta.
- **Facilidad de uso:** La plataforma debe ser completamente intuitiva, permitiendo a los usuarios generar datos de prueba y gestionar scripts con un máximo de 5 interacciones (clics). Los usuarios sin conocimientos técnicos deben ser capaces de usar todas las funcionalidades básicas sin dificultad.
- **Seguridad:** La plataforma debe garantizar la seguridad de los datos procesados y generados. Esto incluye la implementación de cifrado SSL, autenticación segura con contraseñas robustas y roles de usuario, y protección de datos sensibles durante las transacciones, especialmente en el plan premium que involucra pagos.
- **Escalabilidad:** El sistema debe ser capaz de escalar para generar grandes volúmenes de datos sin afectar el rendimiento ni la disponibilidad, permitiendo a los usuarios premium generar datos más complejos y en mayor cantidad sin impacto en la experiencia del usuario.
- **Precisión:** El sistema debe garantizar la precisión total en la generación y almacenamiento de datos. Los datos generados deben reflejar las relaciones y restricciones de la base de datos de manera exacta, sin errores en la estructura o duplicación de registros, para asegurar que las pruebas sean representativas y fiables.

## 8. Precedencia y Prioridad

Considerando únicamente las funcionalidades especificadas en el alcance, se establece la siguiente clasificación por prioridad:

**Prioridad Alta**
- Análisis automático de scripts SQL y NoSQL - Funcionalidad fundamental que permite a la plataforma detectar la estructura de tablas y sus relaciones.
- Generación de datos sintéticos realistas - Capacidad esencial para crear datos que respeten las relaciones entre tablas y restricciones de integridad.
- Interfaz web intuitiva - Desarrollo de la interfaz que permite a los usuarios pegar scripts, visualizar resultados y descargar datos.

**Prioridad Media**
- Sistema de autenticación de usuarios - Implementación del registro y login necesario para distinguir entre usuarios gratuitos y premium.
- Implementación del sistema de planes - Configuración de las limitaciones diferenciadas entre plan gratuito y premium.

**Prioridad Baja**
- Integración con pasarela de pagos - Sistema para procesar suscripciones al plan premium.
- Sistema de soporte por correo electrónico - Implementación del sistema de atención al cliente con diferentes niveles de prioridad.
- Datos personalizados por industria - Especialización de datos según sectores específicos para usuarios premium.

## 9. Otros requerimientos del producto

El producto DataFiller debe cumplir con varios requisitos adicionales para garantizar su funcionalidad, rendimiento y seguridad. Debe ofrecer una interfaz intuitiva, ser escalable y tener la capacidad de adaptarse a diferentes tipos de bases de datos. Además, debe permitir la carga y generación de datos de prueba según los esquemas proporcionados, optimizando el tiempo de los usuarios. La plataforma debe ser confiable y robusta, asegurando tiempos de respuesta rápidos y evitando cualquier tipo de interrupción del servicio, especialmente bajo altas demandas de usuarios simultáneos.

## 10. Estándares legales

DataFiller debe cumplir con las normativas legales aplicables, especialmente en lo que respecta a la protección de datos personales. En Perú, debe adherirse a la Ley N.º 29733 de Protección de Datos Personales, asegurando que la información generada o utilizada por los usuarios sea tratada de manera segura. Además, debe cumplir con las regulaciones internacionales de privacidad como el Reglamento General de Protección de Datos (GDPR) para usuarios de la Unión Europea. La plataforma también debe proporcionar mecanismos de consentimiento informado y control de acceso adecuados para garantizar que los usuarios puedan gestionar sus datos de manera segura.

## 11. Estándares de comunicación

La plataforma debe garantizar una comunicación segura entre los usuarios y el sistema mediante el uso de protocolos de comunicación encriptados como HTTPS. Además, los mensajes y notificaciones del sistema deben ser claros, comprensibles y útiles, para que los usuarios puedan navegar y operar el sistema sin dificultad. DataFiller también debe ofrecer soporte a través de canales accesibles, como correo electrónico, asegurando tiempos de respuesta rápidos y eficientes en la atención a consultas o problemas técnicos. Todo esto debe estar alineado con las mejores prácticas de comunicación en plataformas digitales.

## 12. Estándares de cumplimiento de la plataforma

DataFiller debe cumplir con los estándares de accesibilidad web, como las WCAG (Web Content Accessibility Guidelines), para garantizar que la plataforma sea usable por todas las personas, incluidas aquellas con discapacidades. También debe adherirse a las regulaciones de almacenamiento y manejo de datos personales, así como garantizar la confiabilidad y escalabilidad de su infraestructura. Es importante que DataFiller cumpla con las normativas locales y globales en cuanto a protección de datos y que garantice que la infraestructura soporte un crecimiento en usuarios y datos sin comprometer el rendimiento.

## 13. Estándares de calidad y seguridad

DataFiller debe ser desarrollado siguiendo rigurosos estándares de calidad, lo que incluye pruebas continuas y la validación de que los datos generados sean precisos y representativos para las pruebas. El sistema debe estar diseñado para ofrecer un alto nivel de seguridad, implementando medidas como el cifrado de datos en tránsito y reposo, y controles de acceso estrictos para asegurar que solo usuarios autorizados puedan realizar operaciones críticas. Además, se debe asegurar que la plataforma sea resiliente ante fallos y ataques, garantizando la integridad y protección de la información generada.

## Conclusiones

El proyecto DataFiller representa una solución integral y escalable para la generación automatizada de datos de prueba en entornos de desarrollo y pruebas de bases de datos SQL y NoSQL. A lo largo del desarrollo de esta plataforma web, se ha aplicado una arquitectura Modelo-Vista-Controlador (MVC) para garantizar una separación clara de responsabilidades, facilitar el mantenimiento del código y permitir una futura escalabilidad del sistema.

La estructura modular del proyecto, evidenciada en carpetas como controllers, models, y public, ha permitido organizar de forma eficiente la lógica de negocio, las entidades de datos y los recursos del cliente. El uso de bibliotecas externas a través de composer y la integración con FakerPHP para la generación de datos aleatorios personalizados ha sido fundamental para alcanzar la flexibilidad esperada.

Además, se han implementado pruebas unitarias en el directorio tests/Unit, lo cual asegura la estabilidad del núcleo funcional del sistema y refuerza las buenas prácticas de desarrollo orientado a pruebas (TDD). Estas pruebas están diseñadas para garantizar el correcto funcionamiento de los módulos más críticos, apoyándose en herramientas como PHPUnit.

Durante la implementación, se integraron recursos multimedia y archivos generados en carpetas como images/videos, logs, y resultados_BORRABLE, los cuales demuestran la interacción activa del usuario con la plataforma y el resultado tangible del procesamiento de datos.

Asimismo, el despliegue en la nube mediante Azure App Service y la organización del repositorio en GitHub favorecen una integración continua y control de versiones eficiente, alineado con metodologías DevOps.

DataFiller no solo cumple con su objetivo principal de ofrecer datos realistas para pruebas, sino que también sienta las bases para convertirse en una herramienta robusta y extensible en el ecosistema de desarrollo ágil. Su estructura organizada, el uso de herramientas modernas y la clara orientación a la calidad del software lo posicionan como una solución tecnológica sólida y de gran utilidad para desarrolladores y testers.

## Recomendaciones

- Adoptar políticas de protección de datos desde el diseño: Es fundamental implementar prácticas de "privacidad desde el diseño" (Privacy by Design) para garantizar que la protección de datos esté integrada en todos los procesos y sistemas desde su concepción.
- Mantenerse actualizado con la normativa vigente: Las organizaciones deben monitorear constantemente las actualizaciones en las normativas de protección de datos (como el GDPR o PCI DSS) y adaptar sus procedimientos conforme a los cambios legales y regulatorios.
- Formar al personal en materia de seguridad y privacidad: Se recomienda capacitar regularmente a los empleados sobre buenas prácticas de seguridad de la información, manejo adecuado de datos personales y cumplimiento normativo.
- Realizar auditorías periódicas de seguridad: La ejecución de auditorías internas y externas ayuda a identificar vulnerabilidades, garantizar el cumplimiento normativo y mejorar continuamente la postura de seguridad.
- Aplicar medidas de ciberseguridad robustas: Implementar cifrado, autenticación multifactor, control de accesos, copias de seguridad y otras prácticas de seguridad reduce significativamente los riesgos de violaciones de datos.
- Contar con un Delegado de Protección de Datos (DPO): En entornos que manejan grandes volúmenes de datos personales, es recomendable designar un DPO que supervise el cumplimiento normativo y actúe como punto de contacto con las autoridades de protección de datos.

## Bibliografía

- Solove, D. J. (2020). Understanding Privacy. Harvard University Press.
- Westin, A. F. (2003). Privacy and Freedom. Ig Publishing.
- Whitman, M. E., & Mattord, H. J. (2018). Principles of Information Security. Cengage Learning.
- Calder, A. (2021). EU General Data Protection Regulation (GDPR): An Implementation and Compliance Guide. IT Governance Publishing.
- Tipton, H. F., & Krause, M. (2007). Information Security Management Handbook. Auerbach Publications.

## Webgrafía

- [GDPR Info](https://gdpr-info.eu/) - Portal con el texto completo y actualizaciones del Reglamento General de Protección de Datos (GDPR) de la Unión Europea.
- [PowerData - GDPR](https://www.powerdata.es/gdpr-proteccion-datos) - Artículos y recursos sobre el cumplimiento del GDPR en entornos empresariales.
- [Clase10 - GDPR](https://www.clase10.com/gdpr-lo-necesitas-saber/) - Información introductoria sobre los aspectos esenciales del GDPR, orientado a pymes.
- [PCI Security Standards](https://www.pcisecuritystandards.org/minisite/es-es/) - Sitio oficial del PCI Security Standards Council, con documentación y estándares sobre protección de datos de tarjetas de pago.
- [PCI Hispano](https://www.pcihispano.com/que-es-pci-dss/) - Información detallada sobre el estándar PCI DSS en español, orientado a organizaciones que manejan información de tarjetas de crédito.