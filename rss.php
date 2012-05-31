<?php 
header("Content-Type: application/rss+xml; charset=UTF-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<rss version="2.0">
<channel>
<title><?php print_env("homepage_title");?></title>
<link><?php 
echo "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_NAME"]);
?></link>
<description>RSS Feed von <?php print_env("homepage_title");?></description>
<language><?php print_env("language");?></language>
<?php if(!env("hide_meta_generator")){
?>
<generator>UliCMS Release <?php echo cms_version();?></generator>
<?php 
}
$itemcount = env("items_in_rss_feed");
if($itemcount == ""){
$itemcount = 10;
}
$items=mysql_query("SELECT * FROM ".tbname("content")." WHERE active=1 AND notinfeed = FALSE ORDER BY lastmodified DESC LIMIT $itemcount",$connection);
while($row=mysql_fetch_object($items)){
echo "<item>\n";
echo "<title><![CDATA[".$row->title."]]></title>\n";
$tmp=explode("\n",$row->content);
$tmp2="";

if(env("rss_item_count")){
$rss_item_count = env("rss_item_count");
}
else{
$rss_item_count = 10;
}




for($i=0;$i<$rss_item_count;$i++){
if(isset($tmp[$i])){
$tmp2.=trim(strip_tags($tmp[$i]))."\n";
}
}
$tmp2=trim($tmp2);


$dir=dirname($_SERVER["SCRIPT_NAME"]);


if(endsWith($dir,"/")==false){
$dir.="/";
}

echo "<description><![CDATA[".$tmp2."]]></description>\n";
echo "<link>"."http://".$_SERVER["HTTP_HOST"].$dir.
"?seite=".$row->systemname."</link>\n";
echo "<pubDate>".date("r",$row->lastmodified)."</pubDate>\n";

echo "</item>\n";
}
?>
</channel>
</rss>