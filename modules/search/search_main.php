<?php 
// UliCMS Suchfunktions-Modul
// Version 0.3
// Neu: Jetzt indizierte Volltextsuche


function search_render(){
        // check if database has fulltext
        include getModulePath("search")."search_install.php";
        search_check_install();
        
	$html_output = "";
	
	if(!empty($_GET["q"])){
          $search_subject = htmlspecialchars($_GET["q"], 
          ENT_QUOTES);
	} else{
	  $search_subject = "";
	}
	
	$html_output .= "<form class='search-form' action='".get_requested_pagename().".html' method='get'>

	Suchbegriff: <input type='text' name='q' value='".$search_subject."'> <input type='submit' value='Suchen'>
	</form>
	";
	
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


                /* Old query without fulltext
		$search_sql_query = 'SELECT * FROM '.tbname("content").' WHERE content LIKE "%'.$search_request.'%" 
		OR title LIKE "'.$search_request_unencoded.'" OR systemname LIKE "'.$search_request_unencoded.'"';
		*/
		
		// New Serach Query using MySQL-Fulltext
		$search_sql_query = "SELECT systemname, title FROM ".tbname("content").
		" WHERE MATCH (systemname, title, content, meta_description, meta_keywords) ".
		"AGAINST ('".$search_request_unencoded."') ".
		"";
		$results = mysql_query($search_sql_query);
		$result_count = mysql_num_rows($results);
		$html_output.= "<p class='search-results'><strong>$result_count</strong> Suchergebnisse gefunden</p>";
		if($result_count>0){
		
		$html_output.="<hr/>
		<ul class='result-list'>";
		while($row = mysql_fetch_assoc($results)){
			$html_output.= "<li><a href='".$row["systemname"].".html'>".$row["title"]."</a></li>";
			
		}
		$html_output .= "</ul>";
		}
	}
	return $html_output;
}
?>