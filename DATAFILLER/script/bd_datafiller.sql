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

-- Volcando estructura para tabla datafiller.tbusuario
CREATE TABLE IF NOT EXISTS `tbusuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido_paterno` varchar(25) DEFAULT NULL,
  `apellido_materno` varchar(25) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla datafiller.tbusuario: ~4 rows (aproximadamente)
INSERT INTO `tbusuario` (`id`, `nombre`, `apellido_paterno`, `apellido_materno`, `password`) VALUES
	(4, 'Victor', 'Cruz', 'Mamani', '$2y$10$gR5lv7mmv9mnMQ6/fDuLy.rX6ljEJHMvOdy3AumOZwzMHmcVhcVJy'),
	(5, 'Sebastian', 'Fuentes', 'Avalos', '$2y$10$kkdTnyj38wWqoBQaw2GjO.IKam0sT5YGhrtBS4W6SocVMw2uyiqka'),
	(6, 'Gabriela', 'Gutierrez', 'Mamani', '$2y$10$8bbD/qZ9CcGbzutlopVtn.3WY/G1pmO0TAPrsh9FSH1sXAo0LqgBq'),
	(7, 'Patrick', 'Cuadros', 'Comehuevos', '$2y$10$wFCxUqD36XVqalivtQsUkuULB9bJPoCbYIVSVL1BCcC4zlRNOk4/a');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
