<?php

function blog_prev_next_render(){
     if(!isset($_GET["single"]))
         return "";
    
     $html = "";
    
    
    
     $single = mysql_real_escape_string($_GET["single"]);
    
     if(empty($single))
         return "";
    
     $query = mysql_query("SELECT datum FROM " . tbname("blog") . " WHERE seo_shortname='" . $single . "'");
     $thisQuery = mysql_fetch_object($query);
    
     $prevQuery = mysql_query("SELECT title, seo_shortname FROM " . tbname("blog") . " WHERE datum < " . $thisQuery -> datum . " ORDER by datum DESC LIMIT 1");
    
    
     $nextQuery = mysql_query("SELECT title, seo_shortname FROM " . tbname("blog") . " WHERE datum > " . $thisQuery -> datum . " ORDER by datum ASC LIMIT 1");
    
     if(mysql_num_rows($prevQuery) == 0 and mysql_num_rows($nextQuery) == 0)
         return "";
    
     $html .= "<div class=\"blogArticlePrevNext\">";
    
    
    
     if(mysql_num_rows($prevQuery) > 0){
         $html .= "<span class=\"blog_article_prev\">";
         $results = mysql_fetch_object($prevQuery);
         $html .= "<a href=\"" . get_requested_pagename() . ".html?single=" . htmlspecialchars($results -> seo_shortname, ENT_QUOTES, "UTF-8") . "\">&laquo; " . htmlspecialchars($results -> title, ENT_QUOTES, "UTF-8") . "</a>";
         $html .= "</span>";
         }
    
    
    
     if(mysql_num_rows($nextQuery) > 0){
         $html .= "<span class=\"blog_article_next\">";
         $results = mysql_fetch_object($nextQuery);
         $html .= "<a href=\"" . get_requested_pagename() . ".html?single=" . htmlspecialchars($results -> seo_shortname, ENT_QUOTES, "UTF-8") . "\">" . htmlspecialchars($results -> title, ENT_QUOTES, "UTF-8") . " &raquo;</a>";
         $html .= "</span>";
         }
    
    
     $html .= "</div>";
    
     return $html;
    
    }
?>