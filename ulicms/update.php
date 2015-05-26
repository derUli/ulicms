<?php
define ("SKIP_TABLE_CHECK", true);
include_once "init.php";

db_query("CREATE TABLE IF NOT EXISTS `".tbname("mails")."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headers` TEXT NOT NULL,
  `to` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` mediumtext NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

db_query("CREATE TABLE IF NOT EXISTS `".tbname("history")."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

$comments_dir = ULICMS_ROOT."/comments";

@SureRemoveDir($comments_dir, true);

setconfig ("db_schema_version", "9.0.1");

//  @unlink ("update.php");
ulicms_redirect ("admin/");
