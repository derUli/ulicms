CREATE TABLE IF NOT EXISTS `{prefix}modules` (
  `name` varchar(255) NOT NULL,
  `version` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;