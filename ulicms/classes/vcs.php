<?php
// Version Control System for pages
class VCS{
   public static function createRevision($content_id, $content, $user_id){
       $content_id = intval($content_id);
       $content = db_escape($content);       
       $user_id = intval($user_id);
       return db_query("INSERT INTO `".tbname("history")."` (content_id, content, user_id) VALUES($content_id, '$content', $user_id)");
         }
         
         
       public static function getRevisionByID($history_id){
   $history_id = intval($history_id);
   $query = db_query("SELECT * FROM ".tbname("history"). " WHERE id = ".$history_id);
    if(db_num_rows($query) > 0){
      return db_fetch_object($query);
    } else {
      return null;    
    }
    }     
    

   public static function restoreRevision($history_id){
   $history_id = intval($history_id);
   $query = db_query("SELECT * FROM ".tbname("history"). " WHERE id = ".$history_id);
    if(db_num_rows($query) > 0){
      $row = db_fetch_object($query);
      $content_id = intval($row->content_id);
      $lastmodified = time();
      $content = db_escape($row->content);
      return db_query("UPDATE ".tbname("content"). " SET content='$content', lastmodified = $lastmodified where id = $content_id");
      
    } else {
      return null;    
    }
    }     
                           
   public static function getRevisionsByContentID($content_id, $order = "date DESC"){
   $content_id = intval($content_id);
   $query = db_query("SELECT * FROM ".tbname("history"). " WHERE content_id = ".$content_id. " ORDER BY " .$order);
   $retval = array();
   while($row=db_fetch_object($query)){
       $retval[] = $row;   
   }
   return $retval;
   }
}