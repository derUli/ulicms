<?php 
echo "test";

@include_once "lib/file_get_contents_wrapper.php";
$srclist = getModulePath("rss2blog")."etc/sources.ini";
if(!is_file($srclist))
  die();
 
if(!class_exists("lastRSS")){
   @include_once getModulePath("rss2blog")."lib/lastRSS.php";
}
  
ini_set('max_execution_time', 0);
set_time_limit(0);
// ignore_user_abort(true);
  
$srclist = file_get_contents($srclist);
$srclist = str_replace("\r\n", "\n", $srclist);
$srclist = explode("\n", $srclist);
for($i=0; $i < count($srclist); $i++){

  $currentLine = trim($srclist[$i]);
  if(!startsWith($currentLine, "#")){
        $rss = new lastRSS();
        $rss->cache_dir = 'content/cache';        
        $cache_time = getconfig("rss2blog_cache_time");
        if(!$cache_time)
           $cache_time = 60 * 60 * 2;
        
        $rss->cache_time = $cache_time;
        
        $rssdata = $rss->get($currentLine);
         if($rssdata){
            $items = $rssdata["items"];
            for($i=0; $i < count($items); $i++){
               $article = $items[$i];
               $title = $article["title"];
               $link = $article["link"];
               $description = $article["description"];
               $pubDate = time();
               
            }
      }


     
  }
}
?>