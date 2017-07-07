CREATE TABLE IF NOT EXISTS `{prefix}lists` (
`content_id` int(11) NOT NULL,
`language` varchar(50) DEFAULT NULL,
`category_id` int(11) DEFAULT NULL,
`menu` varchar(10) DEFAULT NULL,
`parent_id` int(11) DEFAULT NULL,
`order_by` varchar(30) DEFAULT 'title',
`order_direction` varchar(30) DEFAULT 'asc',
`limit` int(11) default null,
`use_pagination` tinyint(1) NOT NULL DEFAULT '0',
`type` varchar(50) DEFAULT NULL,
UNIQUE KEY `content_id` (`content_id`)
) ENGINE=InnoDB DEFAULT charset=utf8mb4;
