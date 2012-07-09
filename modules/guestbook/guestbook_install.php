<?php 
function guestbook_install(){
	// install database tables
	mysql_query("CREATE TABLE IF NOT EXISTS `".tbname("guestbook_entries")."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `ort` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `date` datetime NOT NULL,
  `homepage` varchar(500) NOT NULL,
  `content` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");	
	}
?>