CREATE TABLE IF NOT EXISTS `{prefix}modules` (
  `name` varchar(100) NOT NULL,
  `version` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT charset=utf8mb4;