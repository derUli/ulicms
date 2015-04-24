<?php
$page = get_page();
$parent = $page["parent"];
   $parent_name = getPageSystemnameByID($parent);
$html = "<a href=\"".buildSEOUrl($page["systemname"])."\">".htmlspecialchars($page["title"]). "</a>";
while($parent != null){

   $page = get_page();
   $parent = $page["parent"];
   $parent_name = getPageSystemnameByID($parent);

   $html = "<a href=\"".buildSEOUrl($page["systemname"])."\">".htmlspecialchars($page["title"]). "</a>". " &gt; ".$html;
}

$html = '<div class="breadcrumb_nav">'.$html."</div>";

echo $html;
