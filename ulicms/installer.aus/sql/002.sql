CREATE TABLE IF NOT EXISTS `{prefix}groups` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`permissions` mediumtext NOT NULL,
`allowable_tags` TEXT NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT charset=utf8mb4 AUTO_INCREMENT=1 ;
