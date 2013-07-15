<?php 
if(!logged_in() and !is_admin_dir()){

   $visitorHash = md5($_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"]);

   $query = db_query("SELECT * FROM ".tbname("statistics"). " WHERE hash='$visitorHash'");

   if(mysql_num_rows($query) > 0){
     db_query("UPDATE ".tbname("statistics"). " SET `date` = ".time().", `views` = `views` + 1 WHERE hash ='$visitorHash'");
   } else {
     db_query("INSERT INTO ".tbname("statistics"). " (hash, date, `views`) VALUES ('$visitorHash',
     ".time().", 1)");
   }

}

?>