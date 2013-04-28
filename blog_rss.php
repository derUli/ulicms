<?php 
require_once "init.php";

$blog_feed_max_items = getconfig("blog_feed_max_items");
if($blog_feed_max_items === false)
   setconfig("blog_feed_max_items", 10);
   $blog_feed_max_items = 10;

$seite = basename($_GET["s"]);

if(!empty($_GET["lang"]))
   $lang = basename($_GET["lang"]);
else
   $lang = getconfig("default_language");


function rootDirectory() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 $dirname = dirname($_SERVER["REQUEST_URI"]);
 $dirname = str_replace("\\", "/", $dirname);
 $dirname = trim($dirname, "/");
 if($dirname != ""){
    $dirname = "/".$dirname."/";
 } else {
   $dirname = "/";
 }
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$dirname;
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$dirname;
 }
 return $pageURL;
}


$query = mysql_query("SELECT datum, seo_shortname, content_preview, title FROM ".tbname("blog"). " WHERE entry_enabled = 1 AND language='$lang' ORDER by datum DESC LIMIT $blog_feed_max_items");

header("Content-Type: text/xml; charset=UTF-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo "\n";
echo '<rss version="2.0">';
echo "\n";

echo "<channel>";

echo "<title>".getconfig("homepage_title")."</title>\n";
echo "<link>".rootDirectory()."</link>\n";
echo "<description>".getconfig("motto")."</description>\n";



while($row = mysql_fetch_object($query)){
  echo "<item>\n";
  echo "<title>".$row->title."</title>\n";
  echo "<link>".rootDirectory().$seite.".html?single=".$row->seo_shortname."</link>\n";
  echo "<description>".htmlspecialchars($row->content_preview)."</description>\n";
 echo "<pubDate>".date("r", $row->datum)."</pubDate>\n"; 
  echo "</item>\n";
}

echo "</channel>\n";
echo "</rss>";

?>