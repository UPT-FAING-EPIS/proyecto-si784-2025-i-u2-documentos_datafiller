-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para datafiller
CREATE DATABASE IF NOT EXISTS `datafiller` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `datafiller`;

-- Volcando estructura para tabla datafiller.tbauditoria_consultas
CREATE TABLE IF NOT EXISTS `tbauditoria_consultas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `tipo_consulta` varchar(50) NOT NULL,
  `cantidad_registros` int(11) DEFAULT NULL,
  `formato_exportacion` varchar(20) DEFAULT NULL,
  `fecha_consulta` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_usuario` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usuario_auditoria` (`usuario_id`),
  KEY `idx_fecha_consulta` (`fecha_consulta`),
  CONSTRAINT `fk_usuario_auditoria` FOREIGN KEY (`usuario_id`) REFERENCES `tbusuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla datafiller.tbauditoria_consultas: ~48 rows (aproximadamente)
INSERT INTO `tbauditoria_consultas` (`id`, `usuario_id`, `tipo_consulta`, `cantidad_registros`, `formato_exportacion`, `fecha_consulta`, `ip_usuario`) VALUES
	(1, 1, 'analisis_estructura', 6, NULL, '2025-06-10 16:29:03', '::1'),
	(2, 1, 'generacion_datos', 300, 'sql', '2025-06-10 16:30:09', '::1'),
	(3, 1, 'descarga_datos', 15655, 'sql', '2025-06-10 16:30:36', '::1'),
	(4, 1, 'analisis_estructura', 6, NULL, '2025-06-10 16:51:22', '::1'),
	(5, 1, 'generacion_datos', 300, 'sql', '2025-06-10 16:52:07', '::1'),
	(6, 1, 'descarga_datos', 15534, 'sql', '2025-06-10 16:52:10', '::1'),
	(7, 1, 'analisis_estructura', 6, NULL, '2025-06-10 16:56:22', '::1'),
	(8, 1, 'analisis_estructura', 6, NULL, '2025-06-10 16:56:43', '::1'),
	(9, 1, 'generacion_datos', 300, 'sql', '2025-06-10 16:57:02', '::1'),
	(10, 1, 'descarga_datos', 43232, 'sql', '2025-06-10 16:57:17', '::1'),
	(11, 1, 'analisis_estructura', 6, NULL, '2025-06-10 17:05:08', '::1'),
	(12, 1, 'generacion_datos', 300, 'sql', '2025-06-10 17:05:30', '::1'),
	(13, 1, 'descarga_datos', 3477, 'sql', '2025-06-10 17:05:42', '::1'),
	(14, 1, 'analisis_estructura', 6, NULL, '2025-06-10 17:09:51', '::1'),
	(15, 1, 'generacion_datos', 300, 'sql', '2025-06-10 17:09:56', '::1'),
	(16, 1, 'descarga_datos', 41892, 'sql', '2025-06-10 17:09:57', '::1'),
	(17, 1, 'analisis_estructura', 6, NULL, '2025-06-10 17:38:05', '::1'),
	(18, 1, 'generacion_datos', 300, 'sql', '2025-06-10 17:38:24', '::1'),
	(19, 1, 'descarga_datos', 47755, 'sql', '2025-06-10 17:38:32', '::1'),
	(20, 1, 'analisis_estructura', 3, NULL, '2025-06-10 17:54:46', '::1'),
	(21, 1, 'generacion_datos', 150, 'sql', '2025-06-10 17:54:59', '::1'),
	(22, 1, 'descarga_datos', 7049, 'sql', '2025-06-10 17:55:01', '::1'),
	(23, 1, 'analisis_estructura', 3, NULL, '2025-06-10 17:58:04', '::1'),
	(24, 1, 'analisis_estructura', 3, NULL, '2025-06-10 18:03:42', '::1'),
	(25, 1, 'generacion_datos', 150, 'sql', '2025-06-10 18:03:54', '::1'),
	(26, 1, 'descarga_datos', 6994, 'sql', '2025-06-10 18:03:56', '::1'),
	(27, 1, 'analisis_estructura', 3, NULL, '2025-06-10 18:26:27', '::1'),
	(28, 1, 'generacion_datos', 150, 'sql', '2025-06-10 18:26:33', '::1'),
	(29, 1, 'descarga_datos', 6962, 'sql', '2025-06-10 18:26:37', '::1'),
	(30, 1, 'analisis_estructura', 3, NULL, '2025-06-10 18:34:31', '::1'),
	(31, 1, 'generacion_datos', 150, 'sql', '2025-06-10 18:34:39', '::1'),
	(32, 1, 'descarga_datos', 6961, 'sql', '2025-06-10 18:34:41', '::1'),
	(33, 1, 'analisis_estructura', 3, NULL, '2025-06-10 18:37:39', '::1'),
	(34, 1, 'generacion_datos', 150, 'sql', '2025-06-10 18:37:42', '::1'),
	(35, 1, 'descarga_datos', 6926, 'sql', '2025-06-10 18:37:44', '::1'),
	(36, 1, 'descarga_datos', 6926, 'sql', '2025-06-10 18:37:59', '::1'),
	(37, 1, 'analisis_estructura', 3, NULL, '2025-06-10 18:57:23', '::1'),
	(38, 1, 'analisis_estructura', 3, NULL, '2025-06-10 19:05:28', '::1'),
	(39, 1, 'analisis_estructura', 3, NULL, '2025-06-10 19:11:10', '::1'),
	(40, 1, 'generacion_datos', 150, 'sql', '2025-06-10 19:11:23', '::1'),
	(41, 1, 'descarga_datos', 7369, 'sql', '2025-06-10 19:11:25', '::1'),
	(42, 1, 'analisis_estructura', 3, NULL, '2025-06-10 19:26:01', '::1'),
	(43, 1, 'analisis_estructura', 3, NULL, '2025-06-10 19:32:25', '::1'),
	(44, 1, 'analisis_estructura', 3, NULL, '2025-06-10 19:34:22', '::1'),
	(45, 1, 'analisis_estructura', 3, NULL, '2025-06-10 19:38:53', '::1'),
	(46, 1, 'analisis_estructura', 3, NULL, '2025-06-10 19:46:53', '::1'),
	(47, 1, 'analisis_estructura', 3, NULL, '2025-06-10 20:02:07', '::1'),
	(48, 1, 'analisis_estructura', 3, NULL, '2025-06-10 20:07:47', '::1'),
	(49, 1, 'analisis_estructura', 3, NULL, '2025-06-10 20:15:28', '::1'),
	(50, 1, 'generacion_datos', 150, 'sql', '2025-06-10 20:15:32', '::1'),
	(51, 1, 'descarga_datos', 7661, 'sql', '2025-06-10 20:15:33', '::1'),
	(52, 1, 'analisis_estructura', 3, NULL, '2025-06-10 20:32:03', '::1'),
	(53, 1, 'generacion_datos', 150, 'sql', '2025-06-10 20:36:18', '::1'),
	(54, 1, 'descarga_datos', 7639, 'sql', '2025-06-10 20:36:25', '::1'),
	(55, 1, 'analisis_estructura', 3, NULL, '2025-06-10 21:01:07', '::1'),
	(56, 1, 'generacion_datos', 150, 'sql', '2025-06-10 21:01:14', '::1'),
	(57, 1, 'descarga_datos', 7617, 'sql', '2025-06-10 21:01:21', '::1');

-- Volcando estructura para tabla datafiller.tbconfiguraciones
CREATE TABLE IF NOT EXISTS `tbconfiguraciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla datafiller.tbconfiguraciones: ~4 rows (aproximadamente)
INSERT INTO `tbconfiguraciones` (`id`, `clave`, `valor`, `descripcion`, `fecha_actualizacion`) VALUES
	(1, 'limite_consultas_gratuito', '3', 'Límite de consultas diarias para usuarios gratuitos', '2025-06-08 15:51:16'),
	(2, 'limite_registros_gratuito', '10', 'Límite de registros por tabla para usuarios gratuitos', '2025-06-08 15:51:16'),
	(3, 'precio_premium', '9.99', 'Precio mensual del plan premium', '2025-06-08 15:51:16'),
	(4, 'email_soporte', 'soporte@datafiller.com', 'Email de soporte técnico', '2025-06-08 15:51:16');

-- Volcando estructura para tabla datafiller.tbpagos
CREATE TABLE IF NOT EXISTS `tbpagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `estado_pago` enum('pendiente','completado','fallido','cancelado') DEFAULT 'pendiente',
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_vencimiento` date NOT NULL,
  `referencia_pago` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usuario_pago` (`usuario_id`),
  KEY `fk_plan_pago` (`plan_id`),
  CONSTRAINT `fk_plan_pago` FOREIGN KEY (`plan_id`) REFERENCES `tbplanes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_usuario_pago` FOREIGN KEY (`usuario_id`) REFERENCES `tbusuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla datafiller.tbpagos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla datafiller.tbplanes
CREATE TABLE IF NOT EXISTS `tbplanes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `precio_mensual` decimal(10,2) NOT NULL,
  `consultas_diarias` int(11) DEFAULT -1 COMMENT '-1 = ilimitadas',
  `registros_por_tabla` int(11) DEFAULT 10,
  `formatos_disponibles` text NOT NULL,
  `datos_personalizados` tinyint(1) DEFAULT 0,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla datafiller.tbplanes: ~2 rows (aproximadamente)
INSERT INTO `tbplanes` (`id`, `nombre`, `precio_mensual`, `consultas_diarias`, `registros_por_tabla`, `formatos_disponibles`, `datos_personalizados`, `descripcion`, `activo`, `fecha_creacion`) VALUES
	(1, 'Gratuito', 0.00, 3, 10, 'SQL', 0, 'Plan gratuito con limitaciones básicas', 1, '2025-06-08 15:51:16'),
	(2, 'Premium', 9.99, -1, 1000, 'SQL,CSV,JSON,XML', 1, 'Plan premium con acceso completo', 1, '2025-06-08 15:51:16');

-- Volcando estructura para tabla datafiller.tbrecuperacion_password
CREATE TABLE IF NOT EXISTS `tbrecuperacion_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_usuario_recuperacion` (`usuario_id`),
  KEY `idx_token` (`token`),
  CONSTRAINT `fk_usuario_recuperacion` FOREIGN KEY (`usuario_id`) REFERENCES `tbusuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla datafiller.tbrecuperacion_password: ~2 rows (aproximadamente)
INSERT INTO `tbrecuperacion_password` (`id`, `usuario_id`, `token`, `fecha_expiracion`, `usado`, `fecha_creacion`) VALUES
	(3, 3, '2b6437fd8302faee84c3ed76aa9f188663d7686d07ea8c8dd14470f8c2363e8c', '2025-06-08 21:14:15', 0, '2025-06-08 18:14:15'),
	(4, 2, '192b88bc501f09c5126c2bad02dfd2bb748c84e264d95d08b305f6d6940f9574', '2025-06-08 21:15:26', 0, '2025-06-08 18:15:26');

-- Volcando estructura para tabla datafiller.tbusuario
CREATE TABLE IF NOT EXISTS `tbusuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_plan` enum('gratuito','premium') DEFAULT 'gratuito',
  `fecha_suscripcion` date DEFAULT NULL,
  `consultas_diarias` int(11) DEFAULT 0,
  `fecha_ultima_consulta` date DEFAULT NULL,
  `estado` enum('activo','inactivo','suspendido') DEFAULT 'activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_nombre` (`nombre`),
  KEY `idx_email` (`email`),
  KEY `idx_tipo_plan` (`tipo_plan`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla datafiller.tbusuario: ~3 rows (aproximadamente)
INSERT INTO `tbusuario` (`id`, `nombre`, `apellido_paterno`, `apellido_materno`, `email`, `password`, `tipo_plan`, `fecha_suscripcion`, `consultas_diarias`, `fecha_ultima_consulta`, `estado`, `fecha_registro`, `fecha_actualizacion`) VALUES
	(1, 'sebastian', 'Fuentes', 'Avalos', 'sf2022073902@virtual.upt.pe', '$2y$10$n3jKwSXIebdxfkmD9OGrMOFI46Wgk.az9gBPbFdQ4odt8QcPwP6Zi', 'gratuito', NULL, 3, '2025-06-10', 'activo', '2025-06-08 15:56:10', '2025-06-10 21:01:07'),
	(2, 'gabriela', 'Gutierrez', 'Mamane', 'gg2022074263@virtual.upt.pe', '$2y$10$MM8fUwQSsu7lhXrFEejpAO2WbuaTWOXjusygdQVB4FXCfwaA4lDYC', 'gratuito', NULL, 0, NULL, 'activo', '2025-06-08 16:37:34', '2025-06-08 18:15:18'),
	(3, 'mayra', 'Chire', 'Ramos', 'mc2021072620@virtual.upt.pe', '$2y$10$TvTdy/7EfeTci8M1adIaHuaEpdcUMoBS4c8ucjo0vgbasiy2D9RfC', 'gratuito', NULL, 0, NULL, 'activo', '2025-06-08 18:13:55', '2025-06-08 18:13:55');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
