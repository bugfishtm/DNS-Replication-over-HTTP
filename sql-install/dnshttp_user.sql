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

-- Dumping structure for table kiedel.dnshttp_user
CREATE TABLE IF NOT EXISTS `dnshttp_user` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `user_name` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'undefined' COMMENT 'Users Name for Login if Ref',
  `user_pass` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Users Pass for Login',
  `user_mail` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Users Mail for Login if Ref',
  `user_mail_shadow` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Users Store for Mail if Renew',
  `user_rank` tinyint NOT NULL DEFAULT '0' COMMENT 'Users Rank',
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Date',
  `modify_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Modification Date',
  `reset_date` datetime DEFAULT NULL COMMENT 'Reset Date',
  `activation_date` datetime DEFAULT NULL COMMENT 'Activation Date',
  `mail_change_date` datetime DEFAULT NULL COMMENT 'Last Mail Change Request Date',
  `last_login` datetime DEFAULT NULL COMMENT 'Last Login Date',
  `is_confirmed` tinyint(1) DEFAULT '0' COMMENT 'User Activation Status',
  `is_blocked` tinyint(1) DEFAULT '0' COMMENT 'User Blocked/Disabled Status',
  `section` varchar(64) DEFAULT NULL COMMENT 'Users Section',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table kiedel.dnshttp_user: ~0 rows (approximately)
/*!40000 ALTER TABLE `dnshttp_user` DISABLE KEYS */;
INSERT INTO `dnshttp_user` (`id`, `user_name`, `user_pass`, `user_mail`, `user_mail_shadow`, `user_rank`, `created_date`, `modify_date`, `reset_date`, `activation_date`, `mail_change_date`, `last_login`, `is_confirmed`, `is_blocked`, `section`) VALUES
	(2, 'admin', '$2y$10$TWwkHDDET45kMU6Az/WChu6CoVcIIIhWARF/Ylk3GIDeGnXtXine6', 'undefined', NULL, 0, '2022-11-24 00:09:38', '2022-11-24 00:10:23', NULL, NULL, NULL, NULL, 1, 0, NULL);
/*!40000 ALTER TABLE `dnshttp_user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
