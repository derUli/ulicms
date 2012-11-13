<?php 
function blog_do_install(){
mysql_query("CREATE TABLE IF NOT EXISTS `".tbname("blog")."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` bigint(20) NOT NULL,
  `title` varchar(200) NOT NULL,
  `seo_shortname` varchar(200) NOT NULL,
  `comments_enabled` tinyint(1) NOT NULL,
  `language` varchar(6) NOT NULL,
  `entry_enabled` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `content_full` longtext NOT NULL,
  `content_preview` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
}
?>