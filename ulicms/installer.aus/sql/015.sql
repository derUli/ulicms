CREATE TABLE IF NOT EXISTS `{prefix}videos` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`mp4_file` varchar(255) DEFAULT NULL,
`ogg_file` varchar(255) DEFAULT NULL,
`webm_file` varchar(255) DEFAULT NULL,
`width` int(11) NOT NULL,
`height` int(11) NOT NULL,
`category_id` int(11) DEFAULT NULL,
`created` bigint(20) NOT NULL,
`updated` bigint(20) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT charset=utf8mb4 AUTO_INCREMENT=1 ;
