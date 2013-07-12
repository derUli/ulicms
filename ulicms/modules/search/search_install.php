<?php 
function search_check_install(){
   $query = db_query("SELECT fulltext FROM ".tbname("content"));
      if(!$query)
          search_do_install();
}

function search_do_install(){
   db_query("ALTER TABLE ".tbname("content")." ADD fulltext(systemname, title, content, meta_description, meta_keywords)");

}

?>