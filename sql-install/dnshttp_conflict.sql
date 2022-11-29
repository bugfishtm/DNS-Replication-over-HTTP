-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.31-0ubuntu0.20.04.1 - (Ubuntu)
-- Server OS:                    Linux
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table kiedel.dnshttp_conflict
CREATE TABLE IF NOT EXISTS `dnshttp_conflict` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `domain` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Domain Name',
  `servers` TEXT DEFAULT NULL COMMENT 'Array with Conflicting Servers',
  `fk_server_ovr` int DEFAULT NULL COMMENT 'Related Relay to Use in Favor',
  `creation` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Date',
  `modification` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Modification Date',
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=1243 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kiedel.dnshttp_conflict: ~0 rows (approximately)
/*!40000 ALTER TABLE `dnshttp_conflict` DISABLE KEYS */;
/*!40000 ALTER TABLE `dnshttp_conflict` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
