
ALTER TABLE `{prefix}banner`
CHANGE `name` `name` text NULL,
CHANGE `link_url` `link_url` text NULL,
CHANGE `image_url` `image_url` text NULL;

ALTER TABLE `{prefix}content`
CHANGE `content` `content` mediumtext DEFAULT NULL,
CHANGE `views` `views` int(11) NOT NULL DEFAULT '0',
CHANGE `redirection` `redirection` varchar(255) DEFAULT NULL,
CHANGE `meta_description` `meta_description` varchar(255) DEFAULT NULL,
CHANGE `meta_keywords` `meta_keywords` varchar(255) DEFAULT NULL;
