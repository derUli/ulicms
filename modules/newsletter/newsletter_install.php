<?php 
function newsletter_check_install(){
   $query = mysql_query("SELECT * FROM ".tbname("newsletter_subscribers"));
   if(!$query){
      newsletter_do_install();   
   }
}

function newsletter_do_install(){
  mysql_query("CREATE TABLE IF NOT EXISTS ".tbname("newsletter_subscribers")." (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `subscribe_date` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

}

?>