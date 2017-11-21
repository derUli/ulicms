CREATE TABLE IF NOT EXISTS `{prefix}installed_patches` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`description` text NOT NULL,
`url` varchar(255) NOT NULL,
`date` DATETIME NOT NULL,
PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT charset=utf8mb4;
