<?php 
if(in_array("blog", getAllModules()))
   db_query("UPDATE ".tbname("blog"). " SET comments_enabled = 0 WHERE comments_enabled = 1");
