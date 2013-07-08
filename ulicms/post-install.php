<?php 
mkdir(getModulePath("rss2blog")."etc/", $recursive = true);

if(!is_file(getModulePath("rss2blog")."etc/"."sources.txt")){
  file_put_contents(getModulePath("rss2blog")."etc/sources.ini", 
  "# Hier die URLs zu den RSS-Quellen eintragen.\r\n");
}

db_query("ALTER TABLE `".tbname("blog")."` ADD `src_link`;")
?>