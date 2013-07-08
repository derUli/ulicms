<?php 

@include_once "lib/string_functions.php";

$bot_user_id = getconfig("rss2blog_bot_user_id");

if(!$bot_user_id)
   $bot_user_id = 1;

$rss2blog_src_link_format = getconfig("rss2blog_src_link_format");

if(!$rss2blog_src_link_format)
  $rss2blog_src_link_format = "Quelle: %title%";


if(!function_exists("rsstotime")){

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

}

/**
 * cleanString
 * 
 * Cleans up a string from special characters and makes it ready for SEO url's
 * 
 * @param string $string String to be cleaned up
 * @param string $separator Separator that will be used instead of spaces
 * @return string Cleaned up string, ready for SEO
 */
 
 if(!function_exists("cleanString")){
function cleanString($string, $separator = '-'){
	$accents = array('Š' => 'S', 'š' => 's', 'Ð' => 'Dj','Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss','à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f');
	$string = strtr($string, $accents);
	$string = strtolower($string);
	$string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
	$string = preg_replace('{ +}', ' ', $string);
	$string = trim($string);
	$string = str_replace(' ', $separator, $string);
 
	return $string;
}

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
for($i=0; $i <= count($srclist); $i++){

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
            $page_title = $rssdata["title"];
         
            $items = $rssdata["items"];
            for($i=0; $i < count($items); $i++){
               $article = $items[$i];
               $title = mysql_real_escape_string($article["title"]);
               $link = $article["link"];
               $article["description"] = html_entity_decode($article["description"], ENT_QUOTES, "UTF-8");
               $src_text = $rss2blog_src_link_format;
               $src_text = str_ireplace("%title%", $page_title, $src_text);
               
               $article["description"] .= "<p class=\"src_link_p\"><a href=\"".real_htmlspecialchars($link)."\" class=\"src_link\">$src_text</a></p>";
               $description = mysql_real_escape_string($article["description"]);
               
               $link = mysql_real_escape_string($link);
               $pubDate = rsstotime($article["pubDate"]);
               $query = db_query("SELECT * FROM ".tbname("blog"). " WHERE `src_link` = '".$link."'");
               
               
               $link = mysql_real_escape_string($link);
               
               $seo_shortname = cleanString($title)."-".uniqid();
 
               if(mysql_num_rows($query) === 0) {
               
               
               $insert_query = "INSERT INTO `".tbname("blog")."` (datum, ".
               "title, seo_shortname, comments_enabled, language, 
                entry_enabled, author, 
                content_full, content_preview, src_link) VALUES ($pubDate, '$title', 
                '$seo_shortname', 1, 'de', 1,
                $bot_user_id, '$description', '$description', '$link')";
  
                 db_query($insert_query)or die(mysql_error());
  
                               
               }
                
                                              
               
            }
      }


     
  }
}

               
               
?>