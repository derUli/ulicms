CREATE TABLE IF NOT EXISTS `{prefix}settings` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`value` longtext NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT charset=utf8mb4 AUTO_INCREMENT=1 ;
