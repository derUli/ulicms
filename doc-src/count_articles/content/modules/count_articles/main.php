<?php
function count_articles_render(){
   $args = array("article", getCurrentLanguage(true), 1);
   $data = Database::pQuery("select id from {prefix}content where type = ? and language = ? and active = ?", $args, true);
   return Database::getNumRows($data);
}
