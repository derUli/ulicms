<?php
if (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Google App Engine') !== false) {
    header("HTTP/1.1 501 Not Implemented");
    die("RSS Feed is not supported on Google Cloud.");
}

require_once "init.php";

$blog_feed_max_items = getconfig("blog_feed_max_items");
if ($blog_feed_max_items === false) {
    setconfig("blog_feed_max_items", 10);
    $blog_feed_max_items = 10;
}

$seite = basename($_GET["s"]);

if (! empty($_GET["lang"])) {
    $lang = basename($_GET["lang"]);
} else {
    $lang = getconfig("default_language");
}

$lang = db_escape($lang);

$query = db_query("SELECT id, datum, seo_shortname, content_preview, content_full, title FROM " . tbname("blog") . " WHERE entry_enabled = 1 AND language='$lang' ORDER by datum DESC LIMIT $blog_feed_max_items");

header("Content-Type: text/xml; charset=UTF-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo "\n";
echo '<rss version="2.0">';
echo "\n";

echo "<channel>";
echo "<title>" . Settings::getLang("homepage_title", $lang) . "</title>\n";
echo "<link>" . rootDirectory() . "</link>\n";
echo "<description>" .  Settings::getLang("motto", $lang) . "</description>\n";

if (! getconfig("hide_meta_generator")) {
    $generator = "UliCMS " . cms_version();
    echo "<generator>" . $generator . "</generator>\n";
}

echo "<pubDate>" . date("r") . "</pubDate>\n";

while ($row = db_fetch_object($query)) {
    
    $servername = $_SERVER["SERVER_NAME"];
    
    if ($_SERVER['HTTPS'])
        $url = "https://";
    else
        $url = "http://";
    
    $url .= $servername;
    
    $description = trim($row->content_preview);
    if (empty($description)) {
        $description = trim($row->content_full);
    }
    
    // Replace Relative URLs
    $description = str_replace("<a href=\"/", "<a href=\"$url/", $description);
    
    $description = str_replace("<a href='/", "<a href='$url/", $description);
    
    $description = str_replace(" src=\"/", " src=\"$url/", $description);
    
    $description = str_replace(" href=\"?seite=", " href=\"$url/?seite=", $description);
    
    echo "<item>\n";
    echo "<title>" . htmlspecialchars($row->title) . "</title>\n";
    $page = is_frontpage() ? "" : buildSEOUrl($seite);
    $link = rootDirectory() . $page . "?single=" . $row->seo_shortname;
    
    echo "<link>" . $link . "</link>\n";
    echo "<description>" . htmlspecialchars($description) . "</description>\n";
    echo "<pubDate>" . date("r", $row->datum) . "</pubDate>\n";
    echo "<guid isPermaLink=\"false\">" . $row->seo_shortname . "</guid>\n";
    echo "</item>\n";
}

echo "</channel>\n";
echo "</rss>";

?>