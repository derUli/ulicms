<?php 
require_once "init.php";

$blog_feed_max_items = getconfig("blog_feed_max_items");
if($blog_feed_max_items === false)
   setconfig("blog_feed_max_items", 10);
   $blog_feed_max_items = 10;


function rootDirectory() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 $dirname = dirname($_SERVER["REQUEST_URI"]);
 $dirname = str_replace("\\", "/", $dirname);
 $dirname = trim($dirname, "/");
 $dirname = "/".$dirname."/";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$dirname;
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$dirname;
 }
 return $pageURL;
}


$query = mysql_query("SELECT seo_shortname, content_preview, title FROM ".tbname("blog"). " WHERE entry_enabled = 1 ORDER by datum DESC LIMIT $blog_feed_max_items");

header("Content-Type: text/xml; charset=UTF-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo "\n";
echo '<rss version="2.0">';
echo "\n";
echo "<channel>";
while($row = mysql_fetch_object($query)){
  echo "<title>".$row->title."</title>\n";
  echo "<link>".rootDirectory().$row->seo_shortname.".html</link>\n";
  echo "<description>".htmlspecialchars($row->content_preview)."</description>\n";
}

echo "</channel>";
echo "</rss>";

?>