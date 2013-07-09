<?php
db_query("CREATE TABLE IF NOT EXISTS `".tbname("search_subjects")."` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `subject` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
?>