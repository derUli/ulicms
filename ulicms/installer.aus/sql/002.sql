CREATE TABLE IF NOT EXISTS `{prefix}groups` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`permissions` mediumtext NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT charset={db_encoding} AUTO_INCREMENT=1 ;
