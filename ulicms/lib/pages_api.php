<?php 
function delete_page($id = false, $systemname = false){
   if($id){
      mysql_query("DELETE FROM ".tbname("content")." WHERE id=$id");
      return mysql_affected_rows() > 0;
   }

   if($systemname){
      mysql_query("DELETE FROM ".tbname("content")." WHERE systemname='$systemname'");
      return mysql_affected_rows() > 0;
   }

   return false;
}
?>