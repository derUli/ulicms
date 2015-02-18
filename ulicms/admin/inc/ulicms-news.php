<?php
include_once "../../init.php";
@session_start();

$rss = new DOMDocument();
$feeds = array();
$feeds["de"] = "http://www.ulicms.de/blog_rss.php?s=aktuelles&lang=de";

$feeds["en"] = "http://en.ulicms.de/blog_rss.php?s=aktuelles&lang=en";

if(isset($feeds[$_SESSION["system_language"]])){
     $feed_url = $feeds[$_SESSION["system_language"]];
     }else{
     $feed_url = $feeds["en"];
     }

$rss -> load($feed_url);
$feed = array();
foreach ($rss -> getElementsByTagName('item') as $node){
     $item = array (
        'title' => $node -> getElementsByTagName('title') -> item(0) -> nodeValue,
         'desc' => $node -> getElementsByTagName('description') -> item(0) -> nodeValue,
         'link' => $node -> getElementsByTagName('link') -> item(0) -> nodeValue,
         'date' => $node -> getElementsByTagName('pubDate') -> item(0) -> nodeValue,
        );
     array_push($feed, $item);
     }

$limit = 5;

header("Content-Type: text/html; charset=UTF-8");
for($x = 0;$x < $limit;$x++){
     $title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
     $link = $feed[$x]['link'];
     $description = $feed[$x]['desc'];
     $date = date('l F d, Y', strtotime($feed[$x]['date']));
     echo '<p><strong><a href="' . $link . '" title="' . $title . '">' . $title . '</a></strong><br />';
     echo '<small><em>Posted on ' . $date . '</em></small></p>';
     echo '<p>' . $description . '</p>';
     }

 