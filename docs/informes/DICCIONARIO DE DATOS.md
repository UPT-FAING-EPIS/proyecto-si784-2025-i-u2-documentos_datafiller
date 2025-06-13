![C:\Users\EPIS\Documents\upt.png](Aspose.Words.4d03e7c5-8fae-4adb-9f14-e43be78b819a.001.png)

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


**Sistema DataFiller**

**Diccionario de Datos**

**Versión *{1.0}***

**ÍNDICE GENERAL**

[**1.**](#_heading=h.30j0zll)[	](#_heading=h.30j0zll)[**Modelo Entidad / relación**	4](#_heading=h.30j0zll)

[**1.1.**](#_heading=h.1fob9te)[	](#_heading=h.1fob9te)[**Diseño lógico**	4](#_heading=h.1fob9te)

[**1.2.**](#_heading=h.3znysh7)[	](#_heading=h.3znysh7)[**Diseño Físico**	4](#_heading=h.3znysh7)

[**2.**](#_heading=h.tyjcwt)[	](#_heading=h.tyjcwt)[**DICCIONARIO DE DATOS**	4](#_heading=h.tyjcwt)

[**2.1.**](#_heading=h.3dy6vkm)[	](#_heading=h.3dy6vkm)[**Tablas**	4](#_heading=h.3dy6vkm)

[**1.2.**](#_heading=h.1t3h5sf)[	](#_heading=h.1t3h5sf)[**Lenguaje de Definición de Datos (DDL)**	5](#_heading=h.1t3h5sf)

[**1.3.**](#_heading=h.41mghml)[	](#_heading=h.41mghml)[**Lenguaje de Manipulación de Datos (DML)**	5](#_heading=h.41mghml)


**Diccionario de Datos**

1. [**Modelo Entidad / relación**](#_heading=h.30j0zll)[	](#_heading=h.30j0zll)

1. DISEÑO LÓGICO

![](Aspose.Words.4d03e7c5-8fae-4adb-9f14-e43be78b819a.002.png)


1. DISEÑO FÍSICO:

![](Aspose.Words.4d03e7c5-8fae-4adb-9f14-e43be78b819a.003.png)

1. DICCIONARIO DE DATOS	

   1. Tablas

|Nombre de la Tabla:|tbauditoria\_consultas||||||
| :- | :- | :- | :- | :- | :- | :- |
|Descripción de la Tabla:|Registra todas las consultas realizadas por los usuarios del sistema DataFiller, incluyendo análisis de estructura, generación y descarga de datos.||||||
|Objetivo:|Mantener un registro completo de las actividades de los usuarios para auditoría, control de límites de uso y análisis de comportamiento.||||||
|Relaciones con otras tablas:|tbusuario||||||
|Descripción de los campos|||||||
|Nro.|Nombre del Campo|Tipo dato longitud|Permite nulos|Clave primaria|Clave foránea|Descripción del campo|
|1|`  `id|int(11)|No|Si|No|PK de la tabla tbauditoria\_consultaso|
|2|usuario\_id|int(11)|No|No|Si|FK que referencia al usuario que realizó la consulta.|
|3|tipo\_consulta|varchar(50)|No|No|No|Tipo de consulta realizada (analisis\_estructura, generacion\_datos, descarga\_datos)|
|4|cantidad\_registros|int(11)|Si|No|No|Número de registros procesados en la consulta|
|5|formato\_exportacion|varchar(20)|Si|No|No|Número de registros procesados en la consulta|
|6|fecha\_consulta|timestamp|No|No|No|Fecha y hora cuando se realizó la consulta|
|7|ip\_usuario|varchar(45)|Si|No|No|Dirección IP del usuario al momento de la consulta|

|Nombre de la Tabla:|tbconfiguraciones||||||
| :- | :- | :- | :- | :- | :- | :- |
|Descripción de la Tabla:|Registra todas las consultas realizadas por los usuarios del sistema DataFiller, incluyendo análisis de estructura, generación y descarga de datos.||||||
|Objetivo:|Mantener un registro completo de las actividades de los usuarios para auditoría, control de límites de uso y análisis de comportamiento.||||||
|Relaciones con otras tablas:|` `tbusuario||||||
|Descripción de los campos|||||||
|Nro.|Nombre del Campo|Tipo dato longitud|Permite nulos|Clave primaria|Clave foránea|Descripción del campo|
|1|id|int(11)|No|Si|No|PK de la tabla tbconfiguraciones|
|2|clave|varchar(100)|No|No|No|Clave única del parámetro de configuración|
|3|valor|text|No|No|No|Valor del parámetro de configuración|
|4|descripcion|text|Si|No|No|Descripción del parámetro y su propósito|
|5|fecha\_actualizacion|timestamp|No|No|No1|Fecha y hora de la última actualización del parámetro|



|Nombre de la Tabla:|tbpagos||||||
| :- | :- | :- | :- | :- | :- | :- |
|Descripción de la Tabla:|Registra todas las transacciones de pago realizadas por los usuarios para suscripciones premium.||||||
|Objetivo:|Controlar el historial de pagos, estado de suscripciones y fechas de vencimiento de los planes premium.||||||
|Relaciones con otras tablas:|tbusuario, tbplanes||||||
|Descripción de los campos|||||||
|Nro.|Nombre del Campo|Tipo dato longitud|Permite nulos|Clave primaria|Clave foránea|Descripción del campo|
|1|id|int(11)|No|Sí|No|PK de la tabla tbpagos|
|2|usuario\_id|int(11)|No|No|Sí|FK que referencia al usuario que realizó el pago|
|3|plan\_id|int(11)|No|No|Sí|FK que referencia al plan contratado|
|4|monto|decimal(10,2)|No|No|No|Monto pagado por la suscripción|
|5|metodo\_pago|varchar(50)|No|No|No|Método de pago utilizado (tarjeta, paypal, etc.)|
|6|estado\_pago|enum|Sí|No|No|Estado del pago (pendiente, completado, fallido, cancelado)|
|7|fecha\_pago|timestamp|No|No|No|Fecha y hora cuando se realizó el pago|
|8|fecha\_vencimiento|date|No|No|No|Fecha de vencimiento de la suscripción|
|9|referencia\_pago|varchar(100)|Sí|No|No|Referencia externa del procesador de pagos|


|Nombre de la Tabla:|` `tbplanes||||||
| :- | :- | :- | :- | :- | :- | :- |
|Descripción de la Tabla:|Define los diferentes planes de suscripción disponibles en DataFiller con sus características y limitaciones.||||||
|Objetivo:|` `Gestionar los tipos de planes disponibles, sus precios, limitaciones y características específicas.||||||
|Relaciones con otras tablas:|tbpagos||||||
|Descripción de los campos|||||||
|Nro.|Nombre del Campo|Tipo dato longitud|Permite nulos|Clave primaria|Clave foránea|Descripción del campo|
|1|id|int(11)|No|Sí|No|PK de la tabla tbplanes|
|2|nombre|varchar(50)|No|No|No|Nombre del plan (Gratuito, Premium)|
|3|precio\_mensual|decimal(10,2)|No|No|No|Precio mensual del plan|
|4|consultas\_diarias|int(11)|Sí|No|No|Límite de consultas diarias (-1 = ilimitadas)|
|5|registros\_por\_tabla|int(11)|Sí|No|No|Límite de registros por tabla que se pueden generar|
|6|formatos\_disponibles|text|No|No|No|Formatos de exportación disponibles para el plan|
|7|datos\_personalizados|tinyint(1)|Sí|No|No|Indica si permite datos personalizados (0=No, 1=Sí)|
|8|descripcion|text|Sí|No|No|Descripción detallada del plan|
|9|activo|tinyint(1)|Sí|No|No|Estado del plan (0=Inactivo, 1=Activo)|
|10|fecha\_creacion|timestamp||No|No|Fecha y hora de creación del plan|


|Nombre de la Tabla:|tbrecuperacion\_password||||||
| :- | :- | :- | :- | :- | :- | :- |
|Descripción de la Tabla:|` `Gestiona los tokens de recuperación de contraseña para los usuarios del sistema.||||||
|Objetivo:|Proporcionar un mecanismo seguro para la recuperación de contraseñas mediante tokens temporales con fecha de expiración.||||||
|Relaciones con otras tablas:|tbusuario||||||
|Descripción de los campos|||||||
|Nro.|Nombre del Campo|Tipo dato longitud|Permite nulos|Clave primaria|Clave foránea|Descripción del campo|
|1|id|int(11)|No|Sí|No|PK de la tabla tbrecuperacion\_password|
|2|usuario\_id|int(11)|No|No|Sí|FK que referencia al usuario que solicita recuperación|
|3|token|varchar(64)|No|No|No|Token único de recuperación generado de forma segura|
|4|fecha\_expiracion|datetime|No|No|No|Fecha y hora de expiración del token|
|5|usado|tinyint(1)|Sí|No|No|Indica si el token ya fue utilizado (0=No, 1=Sí)|
|6|fecha\_creacion|timestamp|No|No|No|Fecha y hora de creación del token|


|Nombre de la Tabla:|tbusuario||||||
| :- | :- | :- | :- | :- | :- | :- |
|Descripción de la Tabla:|Almacena la información completa de los usuarios registrados en el sistema DataFiller.||||||
|Objetivo:|Gestionar los datos personales, credenciales, tipo de plan y estado de los usuarios del sistema.||||||
|Relaciones con otras tablas:|` `tbauditoria\_consultas, tbpagos, tbrecuperacion\_password||||||
|Descripción de los campos|||||||
|Nro.|Nombre del Campo|Tipo dato longitud|Permite nulos|Clave primaria|Clave foránea|Descripción del campo|
|1|id|int(11)|No|Sí|No|PK de la tabla tbusuario|
|2|nombre|varchar(50)|No|No|No|Nombre de usuario único en el sistema|
|3|apellido\_paterno|varchar(50)|No|No|No|Apellido paterno del usuario|
|4|apellido\_materno|varchar(50)|No|No|No|Apellido materno del usuario|
|5|email|varchar(100)|Sí|No|No|Correo electrónico del usuario|
|6|password|varchar(255)|No|No|No|Contraseña encriptada del usuario|
|7|tipo\_plan|enum|Sí|No|No|Tipo de plan activo (gratuito, premium)|
|8|fecha\_suscripcion|date|Sí|No|No|Fecha de inicio de la suscripción premium|
|9|consultas\_diarias|int(11)|Sí|No|No|Contador de consultas realizadas en el día actual|
|10|fecha\_ultima\_consulta|date|Sí|No|No|Fecha de la última consulta realizada|
|11|estado|enum|Sí|No|No|Estado del usuario (activo, inactivo, suspendido)|
|12|fecha\_registro|timestamp|No|No|No|Fecha y hora de registro del usuario|
|13|fecha\_actualizacion|timestamp|No|No|No|Fecha y hora de la última actualización de datos|


1. Lenguaje de Definición de Datos (DDL)	


   CREATE DATABASE IF NOT EXISTS `datafiller` /\*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4\_general\_ci \*/;

   USE `datafiller`;

   -- Volcando estructura para tabla datafiller.tbauditoria\_consultas

   CREATE TABLE IF NOT EXISTS `tbauditoria\_consultas` (

   `  ``id` int(11) NOT NULL AUTO\_INCREMENT,

   `  ``usuario\_id` int(11) NOT NULL,

   `  ``tipo\_consulta` varchar(50) NOT NULL,

   `  ``cantidad\_registros` int(11) DEFAULT NULL,

   `  ``formato\_exportacion` varchar(20) DEFAULT NULL,

   `  ``fecha\_consulta` timestamp NOT NULL DEFAULT current\_timestamp(),

   `  ``ip\_usuario` varchar(45) DEFAULT NULL,

   `  `PRIMARY KEY (`id`),

   `  `KEY `fk\_usuario\_auditoria` (`usuario\_id`),

   `  `KEY `idx\_fecha\_consulta` (`fecha\_consulta`),

   `  `CONSTRAINT `fk\_usuario\_auditoria` FOREIGN KEY (`usuario\_id`) REFERENCES `tbusuario` (`id`) ON DELETE CASCADE

   ) ENGINE=InnoDB AUTO\_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4\_unicode\_ci;

 

   -- Volcando estructura para tabla datafiller.tbconfiguraciones

   CREATE TABLE IF NOT EXISTS `tbconfiguraciones` (

   `  ``id` int(11) NOT NULL AUTO\_INCREMENT,

   `  ``clave` varchar(100) NOT NULL,

   `  ``valor` text NOT NULL,

   `  ``descripcion` text DEFAULT NULL,

   `  ``fecha\_actualizacion` timestamp NOT NULL DEFAULT current\_timestamp() ON UPDATE current\_timestamp(),

   `  `PRIMARY KEY (`id`),

   `  `UNIQUE KEY `clave` (`clave`)

   ) ENGINE=InnoDB AUTO\_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4\_unicode\_ci;

   -- Volcando datos para la tabla datafiller.tbconfiguraciones: ~4 rows (aproximadamente)

   INSERT INTO `tbconfiguraciones` (`id`, `clave`, `valor`, `descripcion`, `fecha\_actualizacion`) VALUES

   `	`(1, 'limite\_consultas\_gratuito', '3', 'Límite de consultas diarias para usuarios gratuitos', '2025-06-08 15:51:16'),

   `	`(2, 'limite\_registros\_gratuito', '10', 'Límite de registros por tabla para usuarios gratuitos', '2025-06-08 15:51:16'),

   `	`(3, 'precio\_premium', '9.99', 'Precio mensual del plan premium', '2025-06-08 15:51:16'),

   `	`(4, 'email\_soporte', 'soporte@datafiller.com', 'Email de soporte técnico', '2025-06-08 15:51:16');

   -- Volcando estructura para tabla datafiller.tbpagos

   CREATE TABLE IF NOT EXISTS `tbpagos` (

   `  ``id` int(11) NOT NULL AUTO\_INCREMENT,

   `  ``usuario\_id` int(11) NOT NULL,

   `  ``plan\_id` int(11) NOT NULL,

   `  ``monto` decimal(10,2) NOT NULL,

   `  ``metodo\_pago` varchar(50) NOT NULL,

   `  ``estado\_pago` enum('pendiente','completado','fallido','cancelado') DEFAULT 'pendiente',

   `  ``fecha\_pago` timestamp NOT NULL DEFAULT current\_timestamp(),

   `  ``fecha\_vencimiento` date NOT NULL,

   `  ``referencia\_pago` varchar(100) DEFAULT NULL,

   `  `PRIMARY KEY (`id`),

   `  `KEY `fk\_usuario\_pago` (`usuario\_id`),

   `  `KEY `fk\_plan\_pago` (`plan\_id`),

   `  `CONSTRAINT `fk\_plan\_pago` FOREIGN KEY (`plan\_id`) REFERENCES `tbplanes` (`id`) ON DELETE CASCADE,

   `  `CONSTRAINT `fk\_usuario\_pago` FOREIGN KEY (`usuario\_id`) REFERENCES `tbusuario` (`id`) ON DELETE CASCADE

   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4\_unicode\_ci;

   -- Volcando datos para la tabla datafiller.tbpagos: ~0 rows (aproximadamente)

   -- Volcando estructura para tabla datafiller.tbplanes

   CREATE TABLE IF NOT EXISTS `tbplanes` (

   `  ``id` int(11) NOT NULL AUTO\_INCREMENT,

   `  ``nombre` varchar(50) NOT NULL,

   `  ``precio\_mensual` decimal(10,2) NOT NULL,

   `  ``consultas\_diarias` int(11) DEFAULT -1 COMMENT '-1 = ilimitadas',

   `  ``registros\_por\_tabla` int(11) DEFAULT 10,

   `  ``formatos\_disponibles` text NOT NULL,

   `  ``datos\_personalizados` tinyint(1) DEFAULT 0,

   `  ``descripcion` text DEFAULT NULL,

   `  ``activo` tinyint(1) DEFAULT 1,

   `  ``fecha\_creacion` timestamp NOT NULL DEFAULT current\_timestamp(),

   `  `PRIMARY KEY (`id`)

   ) ENGINE=InnoDB AUTO\_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4\_unicode\_ci;

   -- Volcando estructura para tabla datafiller.tbrecuperacion\_password

   CREATE TABLE IF NOT EXISTS `tbrecuperacion\_password` (

   `  ``id` int(11) NOT NULL AUTO\_INCREMENT,

   `  ``usuario\_id` int(11) NOT NULL,

   `  ``token` varchar(64) NOT NULL,

   `  ``fecha\_expiracion` datetime NOT NULL,

   `  ``usado` tinyint(1) DEFAULT 0,

   `  ``fecha\_creacion` timestamp NOT NULL DEFAULT current\_timestamp(),

   `  `PRIMARY KEY (`id`),

   `  `KEY `fk\_usuario\_recuperacion` (`usuario\_id`),

   `  `KEY `idx\_token` (`token`),

   `  `CONSTRAINT `fk\_usuario\_recuperacion` FOREIGN KEY (`usuario\_id`) REFERENCES `tbusuario` (`id`) ON DELETE CASCADE

   ) ENGINE=InnoDB AUTO\_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4\_general\_ci;


   -- Volcando estructura para tabla datafiller.tbusuario

   CREATE TABLE IF NOT EXISTS `tbusuario` (

   `  ``id` int(11) NOT NULL AUTO\_INCREMENT,

   `  ``nombre` varchar(50) NOT NULL,

   `  ``apellido\_paterno` varchar(50) NOT NULL,

   `  ``apellido\_materno` varchar(50) NOT NULL,

   `  ``email` varchar(100) DEFAULT NULL,

   `  ``password` varchar(255) NOT NULL,

   `  ``tipo\_plan` enum('gratuito','premium') DEFAULT 'gratuito',

   `  ``fecha\_suscripcion` date DEFAULT NULL,

   `  ``consultas\_diarias` int(11) DEFAULT 0,

   `  ``fecha\_ultima\_consulta` date DEFAULT NULL,

   `  ``estado` enum('activo','inactivo','suspendido') DEFAULT 'activo',

   `  ``fecha\_registro` timestamp NOT NULL DEFAULT current\_timestamp(),

   `  ``fecha\_actualizacion` timestamp NOT NULL DEFAULT current\_timestamp() ON UPDATE current\_timestamp(),

   `  `PRIMARY KEY (`id`),

   `  `UNIQUE KEY `nombre` (`nombre`),

   `  `KEY `idx\_nombre` (`nombre`),

   `  `KEY `idx\_email` (`email`),

   `  `KEY `idx\_tipo\_plan` (`tipo\_plan`)

   ) ENGINE=InnoDB AUTO\_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4\_unicode\_ci;


1. Lenguaje de Manipulación de Datos (DML)



-- Volcando datos para la tabla datafiller.tbauditoria\_consultas: ~48 rows (aproximadamente)

INSERT INTO `tbauditoria\_consultas` (`id`, `usuario\_id`, `tipo\_consulta`, `cantidad\_registros`, `formato\_exportacion`, `fecha\_consulta`, `ip\_usuario`) VALUES

`	`(1, 1, 'analisis\_estructura', 6, NULL, '2025-06-10 16:29:03', '::1'),

`	`(2, 1, 'generacion\_datos', 300, 'sql', '2025-06-10 16:30:09', '::1'),

`	`(3, 1, 'descarga\_datos', 15655, 'sql', '2025-06-10 16:30:36', '::1'),

`	`(4, 1, 'analisis\_estructura', 6, NULL, '2025-06-10 16:51:22', '::1'),

`	`(5, 1, 'generacion\_datos', 300, 'sql', '2025-06-10 16:52:07', '::1'),

`	`(6, 1, 'descarga\_datos', 15534, 'sql', '2025-06-10 16:52:10', '::1'),

`	`(7, 1, 'analisis\_estructura', 6, NULL, '2025-06-10 16:56:22', '::1'),

`	`(8, 1, 'analisis\_estructura', 6, NULL, '2025-06-10 16:56:43', '::1'),

`	`(9, 1, 'generacion\_datos', 300, 'sql', '2025-06-10 16:57:02', '::1'),

`	`(10, 1, 'descarga\_datos', 43232, 'sql', '2025-06-10 16:57:17', '::1'),

`	`(11, 1, 'analisis\_estructura', 6, NULL, '2025-06-10 17:05:08', '::1'),

`	`(12, 1, 'generacion\_datos', 300, 'sql', '2025-06-10 17:05:30', '::1'),

`	`(13, 1, 'descarga\_datos', 3477, 'sql', '2025-06-10 17:05:42', '::1'),

`	`(14, 1, 'analisis\_estructura', 6, NULL, '2025-06-10 17:09:51', '::1'),

`	`(15, 1, 'generacion\_datos', 300, 'sql', '2025-06-10 17:09:56', '::1'),

`	`(16, 1, 'descarga\_datos', 41892, 'sql', '2025-06-10 17:09:57', '::1'),

`	`(17, 1, 'analisis\_estructura', 6, NULL, '2025-06-10 17:38:05', '::1'),

`	`(18, 1, 'generacion\_datos', 300, 'sql', '2025-06-10 17:38:24', '::1'),

`	`(19, 1, 'descarga\_datos', 47755, 'sql', '2025-06-10 17:38:32', '::1'),

`	`(20, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 17:54:46', '::1'),

`	`(21, 1, 'generacion\_datos', 150, 'sql', '2025-06-10 17:54:59', '::1'),

`	`(22, 1, 'descarga\_datos', 7049, 'sql', '2025-06-10 17:55:01', '::1'),

`	`(23, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 17:58:04', '::1'),

`	`(24, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 18:03:42', '::1'),

`	`(25, 1, 'generacion\_datos', 150, 'sql', '2025-06-10 18:03:54', '::1'),

`	`(26, 1, 'descarga\_datos', 6994, 'sql', '2025-06-10 18:03:56', '::1'),

`	`(27, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 18:26:27', '::1'),

`	`(28, 1, 'generacion\_datos', 150, 'sql', '2025-06-10 18:26:33', '::1'),

`	`(29, 1, 'descarga\_datos', 6962, 'sql', '2025-06-10 18:26:37', '::1'),

`	`(30, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 18:34:31', '::1'),

`	`(31, 1, 'generacion\_datos', 150, 'sql', '2025-06-10 18:34:39', '::1'),

`	`(32, 1, 'descarga\_datos', 6961, 'sql', '2025-06-10 18:34:41', '::1'),

`	`(33, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 18:37:39', '::1'),

`	`(34, 1, 'generacion\_datos', 150, 'sql', '2025-06-10 18:37:42', '::1'),

`	`(35, 1, 'descarga\_datos', 6926, 'sql', '2025-06-10 18:37:44', '::1'),

`	`(36, 1, 'descarga\_datos', 6926, 'sql', '2025-06-10 18:37:59', '::1'),

`	`(37, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 18:57:23', '::1'),

`	`(38, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 19:05:28', '::1'),

`	`(39, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 19:11:10', '::1'),

`	`(40, 1, 'generacion\_datos', 150, 'sql', '2025-06-10 19:11:23', '::1'),

`	`(41, 1, 'descarga\_datos', 7369, 'sql', '2025-06-10 19:11:25', '::1'),

`	`(42, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 19:26:01', '::1'),

`	`(43, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 19:32:25', '::1'),

`	`(44, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 19:34:22', '::1'),

`	`(45, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 19:38:53', '::1'),

`	`(46, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 19:46:53', '::1'),

`	`(47, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 20:02:07', '::1'),

`	`(48, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 20:07:47', '::1'),

`	`(49, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 20:15:28', '::1'),

`	`(50, 1, 'generacion\_datos', 150, 'sql', '2025-06-10 20:15:32', '::1'),

`	`(51, 1, 'descarga\_datos', 7661, 'sql', '2025-06-10 20:15:33', '::1'),

`	`(52, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 20:32:03', '::1'),

`	`(53, 1, 'generacion\_datos', 150, 'sql', '2025-06-10 20:36:18', '::1'),

`	`(54, 1, 'descarga\_datos', 7639, 'sql', '2025-06-10 20:36:25', '::1'),

`	`(55, 1, 'analisis\_estructura', 3, NULL, '2025-06-10 21:01:07', '::1'),

`	`(56, 1, 'generacion\_datos', 150, 'sql', '2025-06-10 21:01:14', '::1'),

`	`(57, 1, 'descarga\_datos', 7617, 'sql', '2025-06-10 21:01:21', '::1');

-- Volcando datos para la tabla datafiller.tbconfiguraciones: ~4 rows (aproximadamente)

INSERT INTO `tbconfiguraciones` (`id`, `clave`, `valor`, `descripcion`, `fecha\_actualizacion`) VALUES

`	`(1, 'limite\_consultas\_gratuito', '3', 'Límite de consultas diarias para usuarios gratuitos', '2025-06-08 15:51:16'),

`	`(2, 'limite\_registros\_gratuito', '10', 'Límite de registros por tabla para usuarios gratuitos', '2025-06-08 15:51:16'),

`	`(3, 'precio\_premium', '9.99', 'Precio mensual del plan premium', '2025-06-08 15:51:16'),

`	`(4, 'email\_soporte', 'soporte@datafiller.com', 'Email de soporte técnico', '2025-06-08 15:51:16');

-- Volcando datos para la tabla datafiller.tbplanes: ~2 rows (aproximadamente)

INSERT INTO `tbplanes` (`id`, `nombre`, `precio\_mensual`, `consultas\_diarias`, `registros\_por\_tabla`, `formatos\_disponibles`, `datos\_personalizados`, `descripcion`, `activo`, `fecha\_creacion`) VALUES

`	`(1, 'Gratuito', 0.00, 3, 10, 'SQL', 0, 'Plan gratuito con limitaciones básicas', 1, '2025-06-08 15:51:16'),

`	`(2, 'Premium', 9.99, -1, 1000, 'SQL,CSV,JSON,XML', 1, 'Plan premium con acceso completo', 1, '2025-06-08 15:51:16');

-- Volcando datos para la tabla datafiller.tbrecuperacion\_password: ~2 rows (aproximadamente)

INSERT INTO `tbrecuperacion\_password` (`id`, `usuario\_id`, `token`, `fecha\_expiracion`, `usado`, `fecha\_creacion`) VALUES

`	`(3, 3, '2b6437fd8302faee84c3ed76aa9f188663d7686d07ea8c8dd14470f8c2363e8c', '2025-06-08 21:14:15', 0, '2025-06-08 18:14:15'),

`	`(4, 2, '192b88bc501f09c5126c2bad02dfd2bb748c84e264d95d08b305f6d6940f9574', '2025-06-08 21:15:26', 0, '2025-06-08 18:15:26');

-- Volcando datos para la tabla datafiller.tbusuario: ~3 rows (aproximadamente)

INSERT INTO `tbusuario` (`id`, `nombre`, `apellido\_paterno`, `apellido\_materno`, `email`, `password`, `tipo\_plan`, `fecha\_suscripcion`, `consultas\_diarias`, `fecha\_ultima\_consulta`, `estado`, `fecha\_registro`, `fecha\_actualizacion`) VALUES

`	`(1, 'sebastian', 'Fuentes', 'Avalos', 'sf2022073902@virtual.upt.pe', '$2y$10$n3jKwSXIebdxfkmD9OGrMOFI46Wgk.az9gBPbFdQ4odt8QcPwP6Zi', 'gratuito', NULL, 3, '2025-06-10', 'activo', '2025-06-08 15:56:10', '2025-06-10 21:01:07'),

`	`(2, 'gabriela', 'Gutierrez', 'Mamane', 'gg2022074263@virtual.upt.pe', '$2y$10$MM8fUwQSsu7lhXrFEejpAO2WbuaTWOXjusygdQVB4FXCfwaA4lDYC', 'gratuito', NULL, 0, NULL, 'activo', '2025-06-08 16:37:34', '2025-06-08 18:15:18'),

`	`(3, 'mayra', 'Chire', 'Ramos', 'mc2021072620@virtual.upt.pe', '$2y$10$TvTdy/7EfeTci8M1adIaHuaEpdcUMoBS4c8ucjo0vgbasiy2D9RfC', 'gratuito', NULL, 0, NULL, 'activo', '2025-06-08 18:13:55', '2025-06-08 18:13:55');
