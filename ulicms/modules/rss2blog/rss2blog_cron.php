<?php 
flush();

$beginn = microtime(true); 


if(is_admin_dir())
   die();

@include_once "lib/string_functions.php";

$default_bot_user_id = getconfig("rss2blog_bot_user_id");

if(!$bot_user_id)
   $bot_user_id = 1;

$rss2blog_src_link_format = getconfig("rss2blog_src_link_format");

if(!$rss2blog_src_link_format)
  $rss2blog_src_link_format = "Quelle: %title%";


if(!function_exists("rsstotime")){

 function rsstotime($rss_time) {
        return strtotime($rss_time);
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
  
$srclist = file_get_contents($srclist);
$srclist = str_replace("\r\n", "\n", $srclist);
$srclist = explode("\n", $srclist);

$max_import_amount = 10;
$imported = 0;
$query = db_query("SELECT `src_link` FROM ".tbname("blog")." ORDER by `datum` DESC");

$allLinks = array();
while($row = mysql_fetch_object($query)){
      array_push($allLinks, $row->src_link);
}


$cache_time = getconfig("cache_period");
if(!$cache_time)
   $cache_time = 60 * 60 * 24; // 24 Stunden

for($n = 0; $n < count($srclist); $n++){
  $currentLine = trim($srclist[$n]);
  if(!startsWith($currentLine, "#") and !empty($currentLine)){
  
        $splittedLine = explode("\t", $currentLine);
        if(count($splittedLine) > 1){
           $currentLine = $splittedLine[0];
           $bot_user_id = intval($splittedLine[1]);
        } else {
           $currentLine = $splittedLine[0];
           $bot_user_id = $default_bot_user_id;        
        }
        
        $rss = new lastRSS();
        $rss->cache_dir = 'content/cache';    
        $rss->CDATA = "content";
        $rss->cp = "UTF-8";
        $rss->cache_time = $cache_time;
      
        $rssdata = $rss->get($currentLine);
         if($rssdata){
         $page_title = $rssdata["title"];
            $items = $rssdata["items"];
            for($a=0; $a < count($items); $a++){
 
               $article = $items[$a];
               $title = mysql_real_escape_string($article["title"]);
               $link = $article["link"];
               $article["description"] = html_entity_decode($article["description"], ENT_QUOTES, "UTF-8");
               $src_text = $rss2blog_src_link_format;
               $src_text = str_ireplace("%title%", $page_title, $src_text);
               
               $article["description"] .= "<p class=\"src_link_p\"><a href=\"".real_htmlspecialchars($link)."\" class=\"src_link\">$src_text</a></p>";
               $description = mysql_real_escape_string($article["description"]);
               
               $link = mysql_real_escape_string($link);
               $pubDate = intval(rsstotime($article["pubDate"]));
                              
               $seo_shortname = cleanString($title)."-".uniqid();
 
               if(!in_array($link, $allLinks)) {
               
               array_push($allLinks, $link);
               
               
               $insert_query = "INSERT INTO `".tbname("blog")."` (datum, ".
               "title, seo_shortname, comments_enabled, language, 
                entry_enabled, author, 
                content_full, content_preview, src_link) VALUES (".$pubDate.", '$title', 
                '$seo_shortname', 1, 'de', 1,
                $bot_user_id, '$description', '$description', '$link')";
                
                 db_query($insert_query);
                 
                 $imported += 1;
                 $laufdauer = microtime(true) - $beginn; 
                 $max = intval(ini_get("max_execution_time")) - 4;
                 
                 if($max < 1)
                    $max = 60;
                 if($imported >= $max_import_amount || $laufdauer > $max){
                    $n = count($srclist);
                    $a = count($items);
                 }
                               
               }
                
                               
               
            }
      }


     
  }
}

?>