CREATE TABLE IF NOT EXISTS `{prefix}mails` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`headers` text NOT NULL,
`to` varchar(255) NOT NULL,
`subject` varchar(255) NOT NULL,
`body` mediumtext NOT NULL,
`date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT charset={db_encoding} AUTO_INCREMENT=1 ;
