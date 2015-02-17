<?php
include_once "init.php";
db_query("CREATE TABLE IF NOT EXISTS `" . tbname("packages") . "`  (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 `version` varchar(255) DEFAULT NULL,
 `installed_at` bigint(11) NOT NULL,
 `updated_at` bigint(20) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

setconfig("db_schema", "8.0.1");

//  @unlink ("update.php");
ulicms_redirect("admin/");
