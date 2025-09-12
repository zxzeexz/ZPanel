--sql-files/install.sql
--ZPanel db schema
--Revision 1 [9-12-2025]
--Zee ^_~

--cp_sessions
CREATE TABLE IF NOT EXISTS `cp_sessions` (
  `session_id` VARCHAR(64) NOT NULL,
  `account_id` INT(11) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `expires_at` INT(11) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--cp_accounts
CREATE TABLE IF NOT EXISTS `cp_accounts` (
  `account_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(23) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(100) NOT NULL,
  `sex` ENUM('M','F') NOT NULL,
  `birthdate` DATE NOT NULL,
  `reg_ip` VARCHAR(45) NOT NULL,
  `verified` TINYINT(1) NOT NULL DEFAULT 0,
  `activation_code` VARCHAR(64) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
