<?php
// Version Control System for pages
class VCS{
   public static function createRevision($content_id, $content, $user_id){
       $content_id = intval($content_id);
       $content = db_escape($content);       
       $user_id = intval($user_id);
       db_query("INSERT INTO `".tbname("history")."` (content_id, content, user_id) VALUES($content_id, '$content', $user_id)");
         }
}