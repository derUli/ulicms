CREATE TABLE IF NOT EXISTS `{prefix}users` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`username` varchar(255) NOT NULL,
`lastname` varchar(255) NOT NULL,
`firstname` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL,
`password` varchar(255) NOT NULL,
`old_encryption` boolean NOT NULL DEFAULT '0',
`skype_id` varchar(32) NOT NULL,
`icq_id` varchar(20) NULL,
`twitter` varchar(15) NULL,
`homepage` text NULL,
`about_me` text NULL,
`last_action` bigint(20) NOT NULL DEFAULT 0,
`last_login` bigint(20) DEFAULT NULL,
`password_changed` DATETIME NULL,
`group_id` int(11) NULL,
`notify_on_login` tinyint(1) NOT NULL DEFAULT '0',
`locked` tinyint(1) NOT NULL DEFAULT '0',
`html_editor` varchar(100) NULL DEFAULT 'ckeditor',
`require_password_change` tinyint(1) NULL DEFAULT '0',
`admin` tinyint(1) NULL DEFAULT '0',
`failed_logins` int(11) DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT charset={db_encoding} AUTO_INCREMENT=2 ;
