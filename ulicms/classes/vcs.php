<?php
// Version Control System for pages
class VCS{
   public static function createRevision($content_id, $content, $user_id){
       $content_id = intval($content_id);
       $content = db_escape($content);       
       $user_id = intval($user_id);
       return db_query("INSERT INTO `".tbname("history")."` (content_id, content, user_id) VALUES($content_id, '$content', $user_id)");
         }
         
   public static function getRevisionsByContentID($content_id, $order = "date DESC"){
   $content_id = intval($content_id);
   $query = db_query("SELECT * FROM ".tbname("history"). " WHERE content_id = ".$content_id. " ORDER BY " .$order);
   $retval = array();
   while($row=db_fetch_object($query)){
       $retval = $row;   
   }
   return $retval;
   }
}