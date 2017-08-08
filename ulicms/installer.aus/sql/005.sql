CREATE TABLE IF NOT EXISTS `{prefix}banner` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` text NOT NULL,
`link_url` text NOT NULL,
`image_url` text NOT NULL,
`category` int(11) DEFAULT '1',
`type` varchar(255) DEFAULT 'gif',
`html` text DEFAULT '',
`language` VARCHAR( 255 ) NULL DEFAULT  'all',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT charset=utf8mb4 AUTO_INCREMENT=1 ;
