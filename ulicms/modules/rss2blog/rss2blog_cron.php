<?php 


 function rsstotime($rss_time) {
        $day = substr($rss_time, 5, 2);
        $month = substr($rss_time, 8, 3);
        $month = date('m', strtotime("$month 1 2011"));
        $year = substr($rss_time, 12, 4);
        $hour = substr($rss_time, 17, 2);
        $min = substr($rss_time, 20, 2);
        $second = substr($rss_time, 23, 2);
        $timezone = substr($rss_time, 26);

        $timestamp = mktime($hour, $min, $second, $month, $day, $year);

        date_default_timezone_set('UTC');

        if(is_numeric($timezone)) {
            $hours_mod = $mins_mod = 0;
            $modifier = substr($timezone, 0, 1);
            $hours_mod = (int) substr($timezone, 1, 2);
            $mins_mod = (int) substr($timezone, 3, 2);
            $hour_label = $hours_mod>1 ? 'hours' : 'hour';
            $strtotimearg = $modifier.$hours_mod.' '.$hour_label;
            if($mins_mod) {
                $mins_label = $mins_mod>1 ? 'minutes' : 'minute';
                $strtotimearg .= ' '.$mins_mod.' '.$mins_label;
            }
            $timestamp = strtotime($strtotimearg, $timestamp);
        }

        return $timestamp;
}

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
               $pubDate = rsstotime($article["pubDate"]);
               $query = db_query("SELECT * FROM ".tbname("blog"). " WHERE `src_link` = '".mysql_real_escape_string($link)."'");
               

 
               if(mysql_num_rows($query) == 0) {
  
                               
               }
                
                                              
               
            }
      }


     
  }
}

               
               
?>