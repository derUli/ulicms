<?php
// UliCMS Suchfunktions-Modul
// Version 0.3
// Neu: Jetzt indizierte Volltextsuche

function search_render(){
     $html_output = "";
    
     if(!empty($_GET["q"])){
         $search_subject = htmlspecialchars($_GET["q"],
             ENT_QUOTES, "UTF-8");
         }else{
         $search_subject = "";
         }
    
     if(isset($_GET["type"])){
         switch($_GET["type"]){
         case "blog":
             $type = "blog";
             break;
			 
		case "event":
             $type = "event";
             break;
         case "pages": default:
             $type = "pages";
             break;
             break;
             }
         }else{
         $type = "pages";
         }
    
     $html_output .= "<form class='search-form' action='" . get_requested_pagename() . ".html' method='get'>
	<div class=\"search_subject\">Suchbegriff: <input type='text' name='q' value='" . $search_subject . "'> <input type='submit' value='Suchen'></div>";
    
     $html_output .= '<br/><div class="search-content-type">';
     $html_output .= "<strong>Bereich:</strong><br/><input type='radio' value='pages' name='type' ";
     if($type == "pages"){
         $html_output .= " checked";
         }
     $html_output .= "> Seiten<br/>";
    
    
     if(in_array("blog", getAllModules())){
        
         $html_output .= "<input type='radio' value='blog' name='type' ";
        
         if($type == "blog"){
             $html_output .= " checked";
             }
         $html_output .= "> Blog<br/>";
        
        /**
         * * In der nächsten Version soll man dann auch Kommentare dursuchen können
         * 
         * $html_output .= "<input type='radio' value='comments' name='type' ";
         * 
         * if($type == "commments"){
         * $html_output .= " checked";
         * }
         * $html_output .= "> Kommentare<br/>";
         */
        
         }
		 
		 if(in_array("fullcalendar", getAllModules())){
        
         $html_output .= "<input type='radio' value='event' name='type' ";
        
         if($type == "event"){
             $html_output .= " checked";
             }
         $html_output .= "> Veranstaltungen<br/>";
		 
		 }
    
    
     $html_output .= "</div></form>";
    
     if(!empty($_GET["q"])){
         $search_request = $_GET["q"];
         $search_request_unencoded = $_GET["q"];
         $search_request = str_replace("&", "&amp;", $search_request);
         $search_request = str_replace("\"", "&quot;", $search_request);
         $search_request = str_replace("ö", "&ouml;", $search_request);
         $search_request = str_replace("Ö", "&Ouml;", $search_request);
         $search_request = str_replace("ü", "&uuml;", $search_request);
         $search_request = str_replace("Ü", "&Uuml;", $search_request);
         $search_request = str_replace("ä", "&auml;", $search_request);
         $search_request = str_replace("Ä", "&Auml;", $search_request);
         $search_request = str_replace("ß", "&szlig;", $search_request);
         $search_request = mysql_real_escape_string($search_request);
         $search_request_unencoded = mysql_real_escape_string($search_request_unencoded);
        
        
         if($type == "pages"){
             $search_sql_query = "SELECT systemname, title FROM " . tbname("content") .
             " WHERE MATCH (systemname, title, content, meta_description, meta_keywords) " .
             "AGAINST ('" . $search_request_unencoded . "') " .
             "";
             $results = db_query($search_sql_query);
             $result_count = mysql_num_rows($results);
             $html_output .= "<p class='search-results'><strong>$result_count</strong> Suchergebnisse gefunden</p>";
             if($result_count > 0){
                
                 $html_output .= "<hr/>
		<ul class='result-list'>";
                 while($row = mysql_fetch_assoc($results)){
                     $html_output .= "<li><a href='" . htmlspecialchars($row["systemname"], ENT_QUOTES, "UTF-8") . ".html'>" . htmlspecialchars($row["title"], ENT_QUOTES, "UTF-8") . "</a></li>";
                    
                     }
                 $html_output .= "</ul>";
                 }
            
            
             } else if($type == "blog"){
            
             $blog_page = getconfig("blog_page");
             if(!$blog_page)
                 $blog_page = "blog";
            
             $search_sql_query = "SELECT seo_shortname, title FROM " . tbname("blog") .
             " WHERE MATCH (seo_shortname, title, content_full, content_preview) " .
             "AGAINST ('" . $search_request_unencoded . "') ORDER by datum DESC" .
             "";
             $results = db_query($search_sql_query);
             $result_count = mysql_num_rows($results);
             $html_output .= "<p class='search-results'><strong>$result_count</strong> Suchergebnisse gefunden</p>";
             if($result_count > 0){
                
                 $html_output .= "<hr/>
		<ul class='result-list'>";
                 while($row = mysql_fetch_assoc($results)){
                     $html_output .= "<li><a href='" . $blog_page . ".html?single=" . htmlspecialchars($row["seo_shortname"], ENT_QUOTES, "UTF-8") . "'>" . htmlspecialchars($row["title"], ENT_QUOTES, "UTF-8") . "</a></li>";
                    
                     }
                 $html_output .= "</ul>";
                 }
            
            
            
             } else if($type == "event"){
            
             $search_sql_query = "SELECT title, url FROM " . tbname("events") .
             " WHERE MATCH (title, url) " .
             "AGAINST ('" . $search_request_unencoded . "') AND `start` > ".(time() - 60 * 60 * 23)." ORDER by `start` ASC";

             $results = db_query($search_sql_query);
             $result_count = mysql_num_rows($results);
             $html_output .= "<p class='search-results'><strong>$result_count</strong> Suchergebnisse gefunden</p>";
             if($result_count > 0){
                
                 $html_output .= "<hr/>
		<ul class='result-list'>";
                 while($row = mysql_fetch_assoc($results)){
				     $dateString = date("d.m.Y", $row["start"]);
					 
					 if($row["start"] != $row["end"]){
					    $dateString .= " - ".date("d.m.Y", $row["end"]);
					 }
				     if(!empty($row["url"])){
                        $html_output .= "<li><a href='".htmlspecialchars($row["url"], ENT_QUOTES, "UTF-8") . "'>".$dateString." ".htmlspecialchars($row["title"], ENT_QUOTES, "UTF-8") . "</a></li>";
                     } else {
					    $html_output .= "<li>".$dateString." ".htmlspecialchars($row["title"], ENT_QUOTES, "UTF-8") ."</li>";
                    
					 }
                     }
                 $html_output .= "</ul>";
                 }
            
            
            
             }
         }
     return $html_output;
    }
?>