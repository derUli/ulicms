<?php 
@mkdir(getModulePath("rss2blog")."etc/", $recursive = true);
if(!is_file(getModulePath("rss2blog")."etc/"."sources.ini")){
  file_put_contents(getModulePath("rss2blog")."etc/sources.ini", 
  "# Hier die URLs zu den RSS-Quellen eintragen.\r\n");
}

setconfig("rss2blog_bot_user_id", "1");
db_query("ALTER TABLE `".tbname("blog")."` ADD TEXT `src_link` TEXT;")
?>