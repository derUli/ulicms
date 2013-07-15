<?php
db_query("CREATE TABLE IF NOT EXISTS `".tbname("statistics")."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(60) NOT NULL,
  `date` bigint(20) NOT NULL,
  `views` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

?>