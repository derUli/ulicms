<?php
function blog_title_filter($txt) {
	$single = db_escape ( $_GET ["single"] );
	$query = db_query ( "SELECT title FROM `" . tbname ( "blog" ) . "` WHERE seo_shortname='$single'" );
	$title = false;
	
	if (! $query)
		return $txt;
	
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_assoc ( $query );
		$title = $result ["title"];
	}
	
	if (! containsModule ( get_requested_pagename (), "blog" ) or ! $single or ! $title) {
		return $txt;
	}
	
	return $title;
}
?>