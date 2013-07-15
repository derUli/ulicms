<?php 
if(!function_exists("crawlerDetect")){

function crawlerDetect()
{
 if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
    return TRUE;
  }
  else {
    return FALSE;
  }
}
 
 
}

if(!is_admin_dir() and !crawlerDetect() and isset($_GET["q"])){

   $subject = trim($_GET["q"]);
   $subject = mysql_real_escape_string($subject);

   $query = db_query("SELECT * FROM ".tbname("search_subjects"). " WHERE `subject` = '$subject'");


   if(mysql_num_rows($query) > 0){
     db_query("UPDATE ".tbname("search_subjects"). " SET `amount` = `amount` + 1 WHERE `subject` = '$subject'");
   } else {
     db_query("INSERT INTO ".tbname("search_subjects"). " (`subject`, `amount`) VALUES ('$subject', 1)");
   }

}

?>