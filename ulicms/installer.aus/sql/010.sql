CREATE TABLE IF NOT EXISTS `{prefix}languages` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL,
`language_code` varchar(6) NOT NULL,
UNIQUE(`language_code`),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT charset=utf8mb4 AUTO_INCREMENT=3;
