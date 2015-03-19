<?php
define("SKIP_TABLE_CHECK", true);
include_once "init.php";

setconfig("locale_de", "de_DE.UTF-8; de_DE; deu_deu");
setconfig("locale_en", "en_US.UTF-8; en_GB.UTF-8; en_US; en_GB; english-uk; eng; uk");

db_query("CREATE TABLE IF NOT EXISTS `" . tbname("installed_patches") . "` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `date` DATETIME NOT NULL,
   PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

db_query("CREATE TABLE IF NOT EXISTS `" . tbname("videos") . "` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");


db_query("CREATE TABLE IF NOT EXISTS `" . tbname("audio") . "` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `mp3_file` varchar(255) DEFAULT NULL,
  `ogg_file` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created` bigint(20) NOT NULL,
  `updated` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");


setconfig("db_schema_version", "9.0.0");

//  @unlink ("update.php");
ulicms_redirect("admin/");
