CREATE TABLE IF NOT EXISTS `{prefix}history` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`content_id` int(11) NOT NULL,
`content` longtext NOT NULL,
`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`user_id` int(11) NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
