<?php 
function intramail_install(){
	// create config variable for allowed HTML-Code
	if(!getconfig("allowed_html")){
    setconfig("allowed_html", "<i><b><strong><em><ul><li><ol><a>");
  }
  
  mysql_query("CREATE TABLE `".tbname("messages")."` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`mail_from` VARCHAR( 500 ) NOT NULL ,
`mail_to` VARCHAR( 500 ) NOT NULL ,
`subject` VARCHAR( 78 ) NOT NULL ,
`message` LONGTEXT NOT NULL ,
`date` BIGINT NOT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8;
");
  
  mysql_query("ALTER TABLE `".tbname("messages").
  "` ADD `read` BOOL NOT NULL AFTER `date` ");
  
}
  
  
    
?>