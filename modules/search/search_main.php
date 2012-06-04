<?php 
// UliCMS Suchfunktions-Modul
// Version 0.2

function search_render(){
	$html_output = "";
	
	$html_output .= "<form class='search-form' action='./' method='get'>
	<input type='hidden' name='seite' value='".get_requested_pagename()."'>
	Suchbegriff: <input type='text' name='q' value=''> <input type='submit'>
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

		$search_sql_query = 'SELECT * FROM '.tbname("content").' WHERE content LIKE "%'.$search_request.'%" 
		OR title LIKE "'.$search_request_unencoded.'" OR systemname LIKE "'.$search_request_unencoded.'"';
		$results = mysql_query($search_sql_query);
		$result_count = mysql_num_rows($results);
		$html_output.= "<p class='search-results'><strong>$result_count</strong> Suchergebnisse gefunden</p>";
		if($result_count>0){
		
		$html_output.="<hr/>
		<ul class='result-list'>";
		while($row = mysql_fetch_assoc($results)){
			$html_output.= "<li><a href='?seite=".$row["systemname"]."'>".$row["title"]."</a></li>";
			
		}
		$html_output .= "</ul>";
		}
	}
	return $html_output;
}
?>