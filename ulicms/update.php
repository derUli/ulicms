<?php
define("SKIP_TABLE_CHECK", true);
include_once "init.php";

setconfig("locale_de", "de_DE.UTF-8; de_DE; deu_deu");
setconfig("locale_en", "en_US.UTF-8; en_GB.UTF-8; en_US; en_GB; english-uk; eng; uk");

db_query("CREATE TABLE IF NOT EXISTS `".tbname("installed_patches")."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(255) NOT NULL,
   PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;")or die(db_error());


setconfig("db_schema_version", "8.0.2");

// @unlink("update.php");
ulicms_redirect("admin/");