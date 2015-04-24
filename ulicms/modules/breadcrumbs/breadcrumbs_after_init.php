<?php
if(!function_exists("breadcrumbs")){
  function get_breadcrumbs(){
    if(!is_200())
       return "";
    $page = get_page();
    $parent = $page["parent"];
    $parent_name = getPageSystemnameByID($parent);
    $html = "<a href=\"".buildSEOUrl($page["systemname"])."\">".htmlspecialchars($page["title"]). "</a>";
    while($parent != null){

      $page = get_page($parent_name);
      $parent = $page["parent"];
      $parent_name = getPageSystemnameByID($parent);
      $html = "<a href=\"".buildSEOUrl($page["systemname"])."\">".htmlspecialchars($page["title"]). "</a>". " &gt; ".$html;
}

    $html = "<a href=\"".buildSEOUrl(get_frontpage())."\">".get_translation("frontpage"). "</a>". " &gt; ".  $html;

    $html = '<div class="breadcrumb_nav">'.$html."</div>";
   return $html;
}


}


if(!function_exists("breadcrumbs")){
function breadcrumbs(){
  echo get_breadcrumbs();
}

}
