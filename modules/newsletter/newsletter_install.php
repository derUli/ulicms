<?php 
define("NEWSLETTER_MODULE_VERSION", "0.0.1");

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
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
  
  setconfig("newsletter_template_title", "Titel");
  setconfig("newsletter_template_content", "<p>Fügen Sie hier Ihren Text ein.</p>");
  setconfig("newsletter_id", "1");

}

?>