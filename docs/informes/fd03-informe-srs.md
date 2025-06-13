![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.001.png)		![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.002.png)

![C:\Users\EPIS\Documents\upt.png](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.003.png)

**UNIVERSIDAD PRIVADA DE TACNA**

**FACULTAD DE INGENIERÍA**

**Escuela Profesional de Ingeniería de Sistemas**


` `**Proyecto “DataFiller”**

Curso: *Pruebas y Calidad de Software*


Docente: *Mag. Patrick Cuadros Quiroga*


Integrantes:

[***SEBASTIAN NICOLAS FUENTES AVALOS](mailto:sf2022073902@virtual.upt.pe)		***(2022073902)***

[***MAYRA FERNANDA CHIRE RAMOS](mailto:mc2021072620@virtual.upt.pe)			***(2021072620)***

[***GABRIELA LUZKALID GUTIERREZ MAMANI](mailto:gg2022074263@virtual.upt.pe) 	***(2022074263)***


**Tacna – Perú**

***2025***


|CONTROL DE VERSIONES||||||
| :-: | :- | :- | :- | :- | :- |
|Versión|Hecha por|Revisada por|Aprobada por|Fecha|Motivo|
|1\.0|MPV|ELV|ARV|10/10/2020|Versión Original|


Sistema *DataFiller*

Documento de Especificación de Requerimientos de Software

Versión *{1.0}*


|CONTROL DE VERSIONES||||||
| :-: | :- | :- | :- | :- | :- |
|Versión|Hecha por|Revisada por|Aprobada por|Fecha|Motivo|
|1\.0|MPV|ELV|ARV|10/10/2020|Versión Original|

**ÍNDICE GENERAL**

[**INTRODUCCIÓN**	](#_heading=h.8rina8o99up3)[6**](#_heading=h.8rina8o99up3)

[**1. Generalidades de la Empresa**	](#_heading=h.e4p7vmg6mu27)[6**](#_heading=h.e4p7vmg6mu27)

[**1.1. Nombre de la Empresa**	](#_heading=h.jvrjadb2lqj)[6**](#_heading=h.jvrjadb2lqj)

[**1.2. Vision**	](#_heading=h.d5s2j82zxjzd)[6**](#_heading=h.d5s2j82zxjzd)

[**1.3. Mision**	](#_heading=h.mwmq7s69wr8g)[6**](#_heading=h.mwmq7s69wr8g)

[**1.4. Organigrama**	](#_heading=h.9qwz6gqtrphw)[6**](#_heading=h.9qwz6gqtrphw)

[**2. Visionamiento de la Empresa**	](#_heading=h.5z9962ll357c)[6**](#_heading=h.5z9962ll357c)

[2.1. Descripción del Problema	](#_heading=h.ov9htzxr0od9)[6](#_heading=h.ov9htzxr0od9)

[2.2. Objetivos de Negocios	](#_heading=h.ifum8i5t58i)[6](#_heading=h.ifum8i5t58i)

[2.3. Objetivos de Diseño	](#_heading=h.egfjnfu22e4k)[6](#_heading=h.egfjnfu22e4k)

[2.4. Alcance del proyecto	](#_heading=h.jtsmw8627c20)[6](#_heading=h.jtsmw8627c20)

[2.5. Viabilidad del Sistema	](#_heading=h.1oil4v694vjz)[6](#_heading=h.1oil4v694vjz)

[2.6. Informacion obtenida del Levantamiento de Informacion	](#_heading=h.sqpa2go7u0vu)[6](#_heading=h.sqpa2go7u0vu)

[**3. Análisis de Procesos**	](#_heading=h.61rkblzakoub)[7**](#_heading=h.61rkblzakoub)

[3.1. Diagrama del Proceso Actual – Diagrama de actividades	](#_heading=h.tkuvy4ssij3q)[7](#_heading=h.tkuvy4ssij3q)

[3.2. Diagrama del Proceso Propuesto – Diagrama de actividades Inicial	](#_heading=h.5dbje2h18j3c)[7](#_heading=h.5dbje2h18j3c)

[**4. Especificacion de Requerimientos de Software**	](#_heading=h.ntu7wprmezpg)[7**](#_heading=h.ntu7wprmezpg)

[4.1. Cuadro de Requerimientos funcionales Inicial	](#_heading=h.oec6extu6by4)[7](#_heading=h.oec6extu6by4)

[4.2. Cuadro de Requerimientos No funcionales	](#_heading=h.clphpweknujs)[7](#_heading=h.clphpweknujs)

[4.3. Cuadro de Requerimientos funcionales Final	](#_heading=h.icexk13jiv08)[7](#_heading=h.icexk13jiv08)

[4.4. Reglas de Negocio	](#_heading=h.lvc7vahv5ls9)[7](#_heading=h.lvc7vahv5ls9)

[**5. Fase de Desarrollo**	](#_heading=h.onl91j2upuv7)[7**](#_heading=h.onl91j2upuv7)

[5.1. Perfiles de Usuario	](#_heading=h.klzv5gdimvud)[7](#_heading=h.klzv5gdimvud)

[5.2. Modelo Conceptual	](#_heading=h.8genfjltw9s5)[7](#_heading=h.8genfjltw9s5)

[5.3. Diagrama de Paquetes	](#_heading=h.l7mqcvhkrwvy)[7](#_heading=h.l7mqcvhkrwvy)

[5.4. Diagrama de Casos de Uso	](#_heading=h.77wveswc6uw6)[7](#_heading=h.77wveswc6uw6)

[5.5. Escenarios de Caso de Uso (narrativa)	](#_heading=h.l1tp5o3sljxc)[8](#_heading=h.l1tp5o3sljxc)

[**6. Modelo Logico**	](#_heading=h.tr3e377km79m)[8**](#_heading=h.tr3e377km79m)

[6.1. Analisis de Objetos	](#_heading=h.zezu7ov0nu42)[8](#_heading=h.zezu7ov0nu42)

[6.2. Diagrama de Actividades con objetos	](#_heading=h.7g90hvx2tq29)[8](#_heading=h.7g90hvx2tq29)

[6.3. Diagrama de Secuencia	](#_heading=h.ef62y2e022mz)[8](#_heading=h.ef62y2e022mz)

[6.4. Diagrama de Clases	](#_heading=h.myloq8vexfkl)[8](#_heading=h.myloq8vexfkl)

[**CONCLUSIONES**	](#_heading=h.ruirtx1mf0y)[8**](#_heading=h.ruirtx1mf0y)

[**RECOMENDACIONES**	](#_heading=h.lyu0zkwc8hei)[8**](#_heading=h.lyu0zkwc8hei)

[**BIBLIOGRAFIA**	](#_heading=h.14hc4ptm4huh)[8**](#_heading=h.14hc4ptm4huh)

[**WEBGRAFIA**	](#_heading=h.fna73vafwi9v)[8**](#_heading=h.fna73vafwi9v)

# <a name="_heading=h.8rina8o99up3"></a>INTRODUCCIÓN
1. # <a name="_heading=h.e4p7vmg6mu27"></a>Generalidades de la Empresa
   1. # <a name="_heading=h.jvrjadb2lqj"></a>Nombre de la Empresa
      CodeCraft
   1. # <a name="_heading=h.d5s2j82zxjzd"></a>Visión
      En CodeCraft, nuestra visión es convertirnos en un referente global en el desarrollo de soluciones tecnológicas innovadoras que optimicen y transformen los procesos de software. A través de plataformas como DataFiller, buscamos simplificar y automatizar tareas complejas para desarrolladores, testers y administradores de bases de datos, mejorando la eficiencia y calidad en el desarrollo de software. Aspiramos a ser una empresa líder en la creación de herramientas tecnológicas que respondan a las necesidades de los profesionales de TI, contribuyendo a la evolución digital de diversas industrias y promoviendo la innovación sostenible en cada uno de nuestros productos.
   1. # <a name="_heading=h.mwmq7s69wr8g"></a>Misión
      En CodeCraft, nuestra misión es empoderar a los desarrolladores, testers y administradores de bases de datos mediante DataFiller, nuestra plataforma web diseñada para automatizar la generación de datos de prueba realistas y de alta calidad. Nos comprometemos a facilitar el proceso de pruebas y desarrollo proporcionando datos precisos y representativos, lo que permite a los equipos optimizar tiempos, reducir errores y mejorar la fiabilidad del software. Ofrecemos soluciones flexibles, adaptables a diversas industrias y necesidades específicas, asegurando siempre la máxima seguridad y eficiencia en cada etapa del proceso.
   1. # <a name="_heading=h.9qwz6gqtrphw"></a>Organigrama
## ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.004.png)
1. # <a name="_heading=h.89z30a2hd1yq"></a><a name="_heading=h.5z9962ll357c"></a>Visionamiento de la Empresa
   1. ## <a name="_heading=h.ov9htzxr0od9"></a>**Descripción del Problema**
      Los equipos de desarrollo y QA enfrentan dificultades al generar datos de prueba para las bases de datos. La creación manual de estos datos es un proceso lento, propenso a errores humanos y, a menudo, no refleja con precisión las condiciones reales de producción. Este enfoque afecta la calidad de las pruebas y retrasa el ciclo de desarrollo, incrementando los costos y el tiempo necesario para completar proyectos.

      Las herramientas de generación de datos de prueba disponibles actualmente no siempre ofrecen la flexibilidad o personalización necesarias para adaptarse a diferentes estructuras de bases de datos o sectores específicos. Esto genera que las soluciones existentes no sean lo suficientemente efectivas ni especializadas para satisfacer las necesidades particulares de cada proyecto, lo que limita su utilidad en diferentes industrias.

      El uso de datos reales en pruebas puede no ser viable, especialmente cuando se requieren datos sensibles que deben ser protegidos de acuerdo con las normativas de privacidad y seguridad.
   1. ## <a name="_heading=h.ifum8i5t58i"></a>**Objetivos de Negocios**
- Ofrecer una plataforma que permita a los usuarios cargar su esquema de base de datos y generar datos de prueba realistas y específicos para sus tablas y campos de forma rápida y eficiente. Esto simplifica el proceso de creación de datos de prueba, reduciendo el tiempo y esfuerzo manual.
- Proporcionar una solución flexible que se adapte a diferentes tipos de bases de datos (SQL, NoSQL, etc.) y estructuras de tablas. Los usuarios pueden cargar esquemas personalizados y la plataforma generará los datos de acuerdo con esos esquemas específicos.
- Asegurar que los datos generados sean precisos y representativos de escenarios reales, mejorando la calidad de las pruebas y permitiendo a los equipos de desarrollo y QA detectar errores antes de la implementación en producción.
- Crear planes de suscripción que se adapten a las necesidades de empresas de cualquier tamaño, desde pequeños proyectos hasta grandes empresas que requieren grandes volúmenes de datos de prueba. Además, ofrecer una versión premium con características adicionales como consultas ilimitadas y datos personalizados por industria.
  1. ## <a name="_heading=h.egfjnfu22e4k"></a>**Objetivos de Diseño**
- El diseño debe permitir que los usuarios carguen su esquema de base de datos de manera simple, ya sea mediante archivos SQL, JSON, o conectando directamente a su base de datos. La plataforma debe procesar automáticamente el esquema y mostrar las tablas y campos de manera clara y accesible para el usuario.
- La interfaz debe ser fácil de navegar y entender, sin necesidad de formación avanzada. El flujo debe ser sencillo, permitiendo al usuario cargar su esquema, ajustar las configuraciones si lo desea, y generar los datos en pocos pasos. Las instrucciones y ayudas deben ser claras en todo momento.
- El sistema debe ser capaz de generar datos rápidamente, incluso para bases de datos grandes. El diseño debe enfocarse en la eficiencia para que la generación de registros no tome más de lo necesario, permitiendo la optimización de tiempos en el proceso de pruebas.
- El diseño debe ser escalable, permitiendo que la plataforma maneje tanto proyectos pequeños como grandes sin perder rendimiento. Esto incluye la capacidad de generar grandes volúmenes de datos o hacer personalizaciones específicas sin que se afecte la experiencia del usuario.

  1. ## <a name="_heading=h.jtsmw8627c20"></a>**Alcance del proyecto**
     El proyecto DataFiller abarcará el desarrollo de una plataforma web que permitirá a usuarios generar datos de prueba realistas para bases de datos, con las siguientes funcionalidades e inclusiones:

- Análisis automático de scripts SQL y NoSQL para detectar la estructura de tablas, relaciones y restricciones sin requerir conocimientos técnicos avanzados por parte del usuario.
- Generación de datos sintéticos realistas que respeten las relaciones entre tablas y las restricciones de integridad, con cantidades variables según el plan (10 registros por tabla en plan gratuito, cantidad ampliada en plan premium).
- implementación de un sistema de planes con limitaciones diferenciadas:
  - Plan gratuito: 3 consultas diarias, 10 registros por tabla, formato SQL
  - Plan premium: Consultas ilimitadas, mayor cantidad de registros, todos los formatos, datos personalizados por industria.
- Desarrollo de un sistema de autenticación de usuarios y gestión de suscripciones con integración a pasarela de pagos para el plan premium (S/9.99 mensual).
- Interfaz web intuitiva que permita a los usuarios pegar scripts, visualizar resultados y descargar datos generados.
- Sistema de soporte por correo electrónico para todos los usuarios, con atención prioritaria para usuarios premium.

1. ## <a name="_heading=h.1oil4v694vjz"></a>**Viabilidad del Sistema**
- Factibilidad Operativa

  El sistema es viable dado que optimiza áreas críticas como la generación manual de datos de prueba, el cumplimiento de restricciones de integridad, la simulación de datos realistas y la protección de información sensible. DataFiller automatiza estos procesos mediante la implementación de análisis automáticos de scripts y la generación de datos sintéticos que respetan la integridad referencial. Esto mejora la eficiencia de los equipos de desarrollo y QA, reduciendo el tiempo y los costos asociados a la creación de datos de prueba manualmente.

- Factibilidad Legal

  La plataforma cumplirá con la Ley de Protección de Datos Personales en Perú (Ley N.º 29733), garantizando la confidencialidad y protección de la información de los usuarios. La ley regula el tratamiento de datos personales y asegura que los datos generados sean manejados de forma segura, respetando los derechos de los individuos sobre su información.

- Factibilidad Social

  El sistema ofrece beneficios sociales al permitir que desarrolladores y testers optimicen sus procesos, mejorando su eficiencia y satisfacción laboral. DataFiller hace accesible la generación de datos realistas sin comprometer la privacidad, promoviendo mejores prácticas en el desarrollo de software y pruebas. Además, contribuye a la capacitación de profesionales y a la formación de comunidades más preparadas.

- Factibilidad Ambiental

  La implementación de DataFiller tiene un bajo impacto ambiental. Alineado con los Objetivos de Desarrollo Sostenible (ODS), promueve la innovación tecnológica (ODS 9) y el consumo responsable (ODS 12), ya que elimina la necesidad de utilizar datos reales en pruebas. También contribuye a la sostenibilidad al reducir la huella de carbono asociada con los procesos de desarrollo de software y facilita una transición hacia prácticas más digitales y responsables.

  1. ## <a name="_heading=h.sqpa2go7u0vu"></a>**Información obtenida del Levantamiento de Información**
- Entrevistas con usuarios potenciales.
- Análisis de competidores.
- Revisión de estudios de mercado y tendencias tecnológicas.
1. # <a name="_heading=h.61rkblzakoub"></a>Análisis de Procesos
   1. ## <a name="_heading=h.5dbje2h18j3c"></a>**Diagrama del Proceso Propuesto – Diagrama de actividades Inicial**
      ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.005.png)
1. # <a name="_heading=h.ntu7wprmezpg"></a>Especificación de Requerimientos de Software
   1. ## <a name="_heading=h.oec6extu6by4"></a>**Cuadro de Requerimientos funcionales Inicial**

|**ID**|**Descripción** |**Prioridad**|
| - | - | - |
|RF01|Análisis automático de scripts SQL para detectar estructuras|Alta|
|RF02|Generación de datos sintéticos realistas que respeten relaciones y restricciones|Alta|
|RF03|Interfaz web intuitiva para pegar scripts, visualizar y descargar datos|Alta|
|RF04|Sistema de autenticación de usuarios|Media|

1. ## <a name="_heading=h.clphpweknujs"></a>**Cuadro de Requerimientos No funcionales**	
|**ID. Requerimiento**|**Nombre del Requisito**|**Descripción de Requisito**|**Prioridad**|
| :- | :- | :- | :- |
|RF-001|Seguridad|El sistema debe garantizar la seguridad de los datos mediante encriptación de contraseñas|Alta|
|RF-002|Rendimiento|El sistema debe garantizar tiempos de respuesta rápidos al generar datos de prueba, incluso cuando los esquemas de base de datos sean grandes o complejos.|Alta|
|RF-003|Disponibilidad|Mantener la plataforma disponible y accesible la mayor parte del tiempo.|Alta|
|RF-004|Usabilidad|Diseñar una interfaz intuitiva y fácil de usar para los usuarios, permitiendo cargar esquemas de base de datos, personalizar configuraciones y generar datos con facilidad.|Media|
|RF-005|Mantenibilidad|Facilitar la mantenibilidad del sistema mediante código limpio, documentación adecuada y pruebas robustas.|Media|
##

1. ## <a name="_heading=h.v5wzsky2b2g4"></a><a name="_heading=h.icexk13jiv08"></a>**Cuadro de Requerimientos funcionales Final**

|**ID**|**Descripción** |**Prioridad**|
| - | - | - |
|RF01|Análisis automático de scripts SQL para detectar estructuras|Alta|
|RF02|Generación de datos sintéticos realistas que respeten relaciones y restricciones|Alta|
|RF03|Interfaz web intuitiva para pegar scripts, visualizar y descargar datos|Alta|
|RF04|Sistema de autenticación de usuarios|Media|
|RF05|Sistema de planes con limitaciones diferenciadas (gratuito/premium)|Media|
|RF06|Integración con pasarela de pagos para suscripciones premium|Baja|
|RF07|Sistema de soporte por correo electrónico con prioridad según plan|Baja|
|RF08|Datos personalizados por industria para usuarios premium|Baja|


1. ## <a name="_heading=h.lvc7vahv5ls9"></a>**Reglas de Negocio**
   **Acceso y Autenticación**

   Solo los usuarios registrados con cuentas verificadas (desarrolladores, testers QA y administradores de bases de datos) tendrán acceso completo al sistema para generar y gestionar datos de prueba.

   Todos los usuarios deberán autenticarse mediante credenciales válidas para garantizar el acceso seguro y exclusivo a las funcionalidades del sistema.

   **Generación de Datos de Prueba**

   Los usuarios pueden cargar su esquema de base de datos para generar datos de prueba personalizados. La plataforma se encargará de crear datos realistas de acuerdo con las relaciones entre tablas y las restricciones de integridad definidas en el esquema.

   Los datos generados no deben contener información sensible ni comprometer la privacidad de las personas, cumpliendo con las normativas de protección de datos personales.

1. # <a name="_heading=h.onl91j2upuv7"></a>Fase de Desarrollo
   1. ## <a name="_heading=h.klzv5gdimvud"></a>**Perfiles de Usuario**
      **Usuario Gratuito:**

      Accede a 3 consultas diarias, hasta 10 registros por tabla y exportación en formato SQL.

      **Usuario Premium:**

      Consultas ilimitadas, más registros, múltiples formatos (SQL, JSON, CSV) y datos personalizados por industria.

1. ## <a name="_heading=h.8genfjltw9s5"></a>**Modelo Conceptual**
   1. ## <a name="_heading=h.l7mqcvhkrwvy"></a>**Diagrama de Paquetes**
![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.006.png)
1. ## <a name="_heading=h.77wveswc6uw6"></a>**Diagrama de Casos de Uso**
   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.007.png)
1. ## <a name="_heading=h.l1tp5o3sljxc"></a>**Escenarios de Caso de Uso (narrativa)**
**RF-001-Autentificar usuario**

|**Registrar Medidas de Clientes**                                                                ||
| - | :- |
|**Tipo**|**Obligatorio**|
|**Autor(es)**|**Gabriela Luzkalid Gutierrez Mamani**|
|**Actores**|**Usuario Normal, Usuario PRO**|
|**Descripción**|**El usuario se debe autenticar en el módulo de inicio de sesión de la plataforma web.**|
|**Precondiciones**||
|`                                                      `**Narrativa de cada de uso**||
|`                   `**Acción del actor**|`                   `**Respuesta del sistema**|
|1\. El usuario ingresa al sistema y selecciona la opción para iniciar sesión.|2\. El sistema muestra la página de inicio de sesión.|
|3\. El usuario ingresa su nombre de usuario y contraseña.|4\. El sistema verifica la validez de las credenciales ingresadas.|
|5\. El usuario presiona el botón para iniciar sesión.|7\. El sistema autentica al usuario y muestra la interfaz principal de DataFiller con las opciones correspondientes a su tipo de suscripción.|

**RF-002- Ingresar Script Limitado**

|**Registrar Medidas de Clientes**                                                                ||
| - | :- |
|**Tipo**|**Obligatorio**|
|**Autor(es)**|**Sebastián Nicolas Fuentes Avalos**|
|**Actores**|**Usuario Normal, Usuario PRO**|
|**Descripción**|**El usuario ingresará el script de las tablas de la base de datos a la plataforma web para poder obtener la data de prueba.**|
|**Precondiciones**||
|`                                                      `**Narrativa de cada de uso**||
|`                   `**Acción del actor**|`                   `**Respuesta del sistema**|
|1\. El usuario básico accede al sistema y selecciona la opción para ingresar un script SQL.|2\. El sistema muestra la interfaz para ingresar scripts SQL.|
|3\. El usuario ingresa o pega el script SQL en el campo correspondiente.|4\. El sistema verifica que el usuario no haya superado su límite diario de consultas (3 para plan gratuito).|
|5\. El usuario presiona el botón para analizar el script.|6\. El sistema analiza el script SQL para detectar la estructura de tablas, relaciones y restricciones.|
||<p>7\. El sistema muestra la estructura detectada al usuario y permite continuar con la generación de datos.</p><p></p>|

**RF-003- Obtener data de prueba**

|**Registrar Medidas de Clientes**                                                                ||
| - | :- |
|**Tipo**|**Obligatorio**|
|**Autor(es)**|**Mayra Fernanda Chire Ramos**|
|**Actores**|**Usuario Normal, Usuario PRO**|
|**Descripción**|**El usuario obtiene la data de prueba para trabajar con su base de datos.**|
|**Precondiciones**||
|`                                                      `**Narrativa de cada de uso**||
|`                   `**Acción del actor**|`                   `**Respuesta del sistema**|
|1\. El usuario, tras haber ingresado y analizado un script SQL, selecciona la opción para generar datos de prueba.|2\. El sistema genera datos sintéticos realistas que respetan las relaciones entre tablas y las restricciones de integridad detectadas.|
|3\. El usuario confirma la generación de datos de prueba.|<p>4\. El sistema muestra los datos generados en el formato correspondiente al plan del usuario.</p><p></p>|
||5\. El sistema proporciona la opción de descargar los datos generados.|

**RF-004-Registrar usuario**

|**Registrar Medidas de Clientes**                                                                ||
| - | :- |
|**Tipo**|**Obligatorio**|
|**Autor(es)**|**Gabriela Luzkalid Gutierrez Mamani**|
|**Actores**|**Usuario Normal, Usuario PRO**|
|**Descripción**|**El usuario desea registrarse en la plataforma web “Data Filler” con el fin de utilizar sus servicios.**|
|**Precondiciones**||
|`                                                      `**Narrativa de cada de uso**||
|`                   `**Acción del actor**|`                   `**Respuesta del sistema**|
|1\. El visitante ingresa al sistema y selecciona la opción para registrarse.|2\. El sistema muestra el formulario de registro con los campos requeridos.|
|3\. El visitante completa los campos de información personal (nombre, correo electrónico, contraseña).|4\. El sistema verifica que el correo electrónico no esté registrado previamente.|
|5\. El visitante acepta los términos y condiciones.|6\. El sistema registra al nuevo usuario en la base de datos.|
|7\. El visitante presiona el botón para completar el registro.|8\. El sistema envía un correo de verificación al usuario.|
||9\. El sistema muestra un mensaje de registro exitoso y solicita al usuario verificar su correo.|

**RF-005- Elegir Suscripción Free**

|**Registrar Medidas de Clientes**                                                                ||
| - | :- |
|**Tipo**|**Obligatorio**|
|**Autor(es)**|**Sebastián Nicolas Fuentes Avalos**|
|**Actores**|**Usuario Normal, Usuario PRO**|
|**Descripción**|**El usuario desea registrarse en el sistema con los servicios de “Data Filler” FREE.**|
|**Precondiciones**||
|`                                                      `**Narrativa de cada de uso**||
|`                   `**Acción del actor**|`                   `**Respuesta del sistema**|
|1\. El usuario nuevo o existente accede al sistema y selecciona la opción para administrar su suscripción.|2\. El sistema muestra las opciones de suscripción disponibles.|
|3\. El usuario selecciona la opción de suscripción Free.|4\. El sistema registra la selección del usuario.|
|5\. El usuario confirma su selección.|6\. El sistema actualiza la suscripción del usuario a Free.|
||7\. El sistema muestra una confirmación de la actualización y las limitaciones del plan gratuito (3 consultas diarias, 10 registros por tabla, formato SQL únicamente).|

**RF-006-Elegir Suscripción PRO**

|**Registrar Medidas de Clientes**                                                                ||
| - | :- |
|**Tipo**|**Obligatorio**|
|**Autor(es)**|**Mayra Fernanda Chire Ramos**|
|**Actores**|**Usuario Normal, Usuario PRO**|
|**Descripción**|**El usuario desea registrarse en el sistema con los servicios de “Data Filler” PRO.**|
|**Precondiciones**||
|`                                                      `**Narrativa de cada de uso**||
|`                   `**Acción del actor**|`                   `**Respuesta del sistema**|
|1\. El usuario accede al sistema y selecciona la opción para administrar su suscripción.|2\. El sistema muestra las opciones de suscripción disponibles.|
|3\. El usuario selecciona la opción de suscripción Premium (S/9.99 mensual).|4\. El sistema registra la selección del usuario.|
|5\. El usuario ingresa los datos de pago necesarios.|6\. El sistema procesa el pago ingresado a través de la pasarela de pagos.|
|7\. El usuario confirma su selección.|8\. El sistema actualiza la suscripción del usuario a Premium.|
||9\. El sistema muestra una confirmación de la actualización y los beneficios del plan premium (consultas ilimitadas, mayor cantidad de registros, todos los formatos, datos personalizados por industria).|

**RF-007- Configurar formato de salida Limitada**

|**Registrar Medidas de Clientes**                                                                ||
| - | :- |
|**Tipo**|**Obligatorio**|
|**Autor(es)**|**Gabriela Luzkalid Gutierrez Mamani**|
|**Actores**|**Usuario Normal, Usuario PRO**|
|**Descripción**|**El usuario desea configurar la cantidad de registros y el tipo de salida de sus datos (CSV, .SQL).**|
|**Precondiciones**||
|`                                                      `**Narrativa de cada de uso**||
|`                   `**Acción del actor**|`                   `**Respuesta del sistema**|
|1\. El usuario con plan gratuito, tras haber generado datos de prueba, busca la opción para cambiar el formato de salida.|2\. El sistema verifica el tipo de suscripción del usuario.|
|3\. El usuario intenta seleccionar un formato diferente a SQL|4\. El sistema muestra un mensaje informando que la opción de cambiar formatos está disponible solo para usuarios premium.|
||5\. El sistema ofrece información sobre cómo actualizar a premium para acceder a esta funcionalidad.|

**RF-008- Renovar Suscripción**

|**Registrar Medidas de Clientes**                                                                ||
| - | :- |
|**Tipo**|**Obligatorio**|
|**Autor(es)**|**Mayra Fernanda Chire Ramos**|
|**Actores**|**Usuario PRO**|
|**Descripción**|**El usuario desea renovar el tipo de usuario en la que se encuentra para conservar la calidad de servicio que está recibiendo.**|
|**Precondiciones**||
|`                                                      `**Narrativa de cada de uso**||
|`                   `**Acción del actor**|`                   `**Respuesta del sistema**|
|1\. El usuario premium accede al sistema y selecciona la opción para renovar su suscripción.|2\. El sistema muestra los detalles de la suscripción actual del usuario.|
|3\. El usuario verifica los detalles de la suscripción a renovar.|4\. El sistema muestra la fecha de expiración actual y la nueva fecha después de la renovación.|
|5\. El usuario ingresa los datos de pago necesarios.|6\. El sistema procesa el pago ingresado.|
|7\. El usuario confirma la renovación de la suscripción.|8\. El sistema actualiza la fecha de vencimiento de la suscripción.|
||9\. El sistema muestra una confirmación de la renovación exitosa.|

**RF-009- Cancelar Suscripción**

|**Registrar Medidas de Clientes**                                                                ||
| - | :- |
|**Tipo**|**Obligatorio**|
|**Autor(es)**|**Sebastián Nicolas Fuentes Avalos**|
|**Actores**|**Usuario PRO**|
|**Descripción**|**El usuario desea cancelar el tipo de usuario que se encuentra, con el fin de cortar los servicios que tiene.**|
|**Precondiciones**||
|`                                                      `**Narrativa de cada de uso**||
|`                   `**Acción del actor**|`                   `**Respuesta del sistema**|
|1\. El usuario premium accede al sistema y selecciona la opción para cancelar su suscripción.|2\. El sistema muestra los detalles de la suscripción actual del usuario.|
|3\. El usuario verifica los detalles de la suscripción a cancelar.|4\. El sistema solicita confirmación para proceder con la cancelación.|
|5\. El usuario confirma la cancelación de la suscripción.|6\. El sistema procesa la solicitud de cancelación.|
||7\. El sistema actualiza el estado de la suscripción a "cancelada" pero mantiene los beneficios hasta la fecha de expiración|
||8\. El sistema muestra una confirmación de la cancelación y la fecha hasta la cual el usuario seguirá disfrutando de los beneficios premium.|

**RF-010- Ingresar Script Ilimitado**

|**Registrar Medidas de Clientes**                                                                ||
| - | :- |
|**Tipo**|**Obligatorio**|
|**Autor(es)**|**Gabriela Luzkalid Gutierrez Mamani**|
|**Actores**|**Usuario PRO**|
|**Descripción**|**Los usuarios con categoría PRO desean ingresar el script de sus tablas de la base de datos con una capacidad ilimitada.**|
|**Precondiciones**||
|`                                                      `**Narrativa de cada de uso**||
|`                   `**Acción del actor**|`                   `**Respuesta del sistema**|
|1\. El usuario premium accede al sistema y selecciona la opción para ingresar un script SQL.|2\. El sistema muestra la interfaz para ingresar scripts SQL.|
|3\. El usuario ingresa o pega el script SQL en el campo correspondiente.|4\. El sistema verifica que el usuario tiene un plan premium con consultas ilimitadas.|
|5\. El usuario presiona el botón para analizar el script.|6\. El sistema analiza el script SQL para detectar la estructura de tablas, relaciones y restricciones.|
||7\. El sistema muestra la estructura detectada al usuario y permite continuar con la generación de datos.|
||8\. El sistema ofrece opciones adicionales exclusivas para usuarios premium, como personalización por industria.|

1. # <a name="_heading=h.tr3e377km79m"></a>Modelo Lógico
   1. ## <a name="_heading=h.zezu7ov0nu42"></a>**Analisis de Objetos**
Diagrama de Objetos del CUS Autenticar Usuario 

![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.008.png)

Diagrama de Objetos del CUS Elegir Suscripción FREE

![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.009.png)

Diagrama de Objetos del CUS Elegir Suscripción PRO 

![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.010.png)
1. ## <a name="_heading=h.7g90hvx2tq29"></a>**Diagrama de Actividades con objetos**
1. ## <a name="_heading=h.ef62y2e022mz"></a>**Diagrama de Secuencia**
   Autentificar Usuario

   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.011.png)

   Ingresar Script Limitado

   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.012.png)

   Obtener Data de Prueba

   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.013.png)

   Registrar usuario

   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.014.png)

   Elegir Suscripcion Free

   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.015.png)

   Elegir Suscripcion PRO

   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.016.png)

   Configurar Formato de Salida Limitada

   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.017.png)

   Renovar Suscripción

   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.018.png)

   Cancelar Suscripción

   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.019.png)

   Ingresar Script Ilimitado

   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.020.png)

   Validar Login

   ![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.021.png)
1. ## <a name="_heading=h.myloq8vexfkl"></a>**Diagrama de Clases**
![](Aspose.Words.04a20666-2cab-4295-9db4-5fecf35bc292.022.png)
# <a name="_heading=h.ruirtx1mf0y"></a>CONCLUSIONES
DataFiller se consolida como una solución eficaz e innovadora para automatizar la generación de datos de prueba, respondiendo a las necesidades reales de desarrolladores, testers y administradores de bases de datos. Su modelo freemium, con planes gratuitos y premium, permite adaptarse a distintos niveles de uso, ofreciendo desde funcionalidades básicas hasta personalizaciones avanzadas según la industria. La plataforma destaca por su interfaz intuitiva, su compatibilidad con múltiples formatos y su enfoque en la protección de datos, cumpliendo con normativas legales y promoviendo buenas prácticas. Además, se ha demostrado su viabilidad técnica, legal, social y ambiental, lo que respalda su implementación y potencial de impacto en el mercado tecnológico, contribuyendo a la eficiencia, calidad e innovación en los procesos de desarrollo de software.

# <a name="_heading=h.lyu0zkwc8hei"></a>RECOMENDACIONES
Se recomienda realizar pruebas periódicas de carga para asegurar que el sistema pueda manejar grandes volúmenes de datos sin afectar el rendimiento, especialmente bajo alta demanda. Además, sería útil permitir exportar los datos en más formatos, como Excel o XML, para que los usuarios puedan trabajar con los datos de manera más flexible y adaptada a sus necesidades.

Otra recomendación es agregar la opción de generar datos más específicos según la industria del usuario. Esto podría incluir plantillas o configuraciones prediseñadas que ajusten los datos a diferentes sectores. También sería beneficioso ofrecer la plataforma en varios idiomas, lo que facilitaría el acceso a usuarios internacionales y mejoraría la experiencia global.
# <a name="_heading=h.14hc4ptm4huh"></a>BIBLIOGRAFÍA
Solove, D. J. (2020). Understanding Privacy. Harvard University Press.

Westin, A. F. (2003). Privacy and Freedom. Ig Publishing.

Whitman, M. E., & Mattord, H. J. (2018). Principles of Information Security. Cengage Learning.

Calder, A. (2021). EU General Data Protection Regulation (GDPR): An Implementation and Compliance Guide. IT Governance Publishing.

Tipton, H. F., & Krause, M. (2007). Information Security Management Handbook. Auerbach Publications.
# <a name="_heading=h.fna73vafwi9v"></a>WEBGRAFÍA
https://gdpr-info.eu/

Portal con el texto completo y actualizaciones del Reglamento General de Protección de Datos (GDPR) de la Unión Europea.

https://www.powerdata.es/gdpr-proteccion-datos

Artículos y recursos sobre el cumplimiento del GDPR en entornos empresariales.

https://www.clase10.com/gdpr-lo-necesitas-saber/

Información introductoria sobre los aspectos esenciales del GDPR, orientado a pymes.

https://www.pcisecuritystandards.org/minisite/es-es/

Sitio oficial del PCI Security Standards Council, con documentación y estándares sobre protección de datos de tarjetas de pago.

https://www.pcihispano.com/que-es-pci-dss/

Información detallada sobre el estándar PCI DSS en español, orientado a organizaciones que manejan información de tarjetas de crédito.



