CREATE TABLE IF NOT EXISTS `{prefix}log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`request_uri` varchar(255) DEFAULT NULL,
`useragent` varchar(255) DEFAULT NULL,
`referrer` varchar(255) DEFAULT NULL,
`request_method` varchar(10) DEFAULT NULL,
`http_host` varchar(100) DEFAULT NULL,
`ip` varchar(255) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT charset=utf8mb4 AUTO_INCREMENT=1 ;
