<?php

db_query("ALTER TABLE " . tbname("content") . " ADD fulltext(systemname, title, content, meta_description, meta_keywords)");

if(in_array("blog", getAllModules())){
     db_query("ALTER TABLE " . tbname("blog") . " ENGINE=MyISAM;");
     db_query("ALTER TABLE " . tbname("blog") . " ADD fulltext(seo_shortname, title, content_full, content_preview)");
    }

?>