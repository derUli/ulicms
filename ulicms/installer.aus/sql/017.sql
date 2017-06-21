CREATE TABLE IF NOT EXISTS `{prefix}forms` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`email_to` varchar(255) NOT NULL,
`subject` varchar(255) NOT NULL,
`category_id` int(11) DEFAULT NULL,
`fields` text,
`mail_from_field` varchar(255) NULL,
`target_page_id` int(11) DEFAULT NULL,
`created` bigint(20) DEFAULT NULL,
`updated` bigint(20) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT charset={db_encoding} AUTO_INCREMENT=1 ;
