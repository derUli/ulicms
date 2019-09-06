<?php
function blog_list() {
	$posts_per_page = getconfig ( "blog_posts_per_page" );
	
	if ($posts_per_page === false) {
		setconfig ( "blog_posts_per_page", "5" );
		$posts_per_page = 5;
	}
	
	$acl = new ACL ();
	
	$hasPermission = $acl->hasPermission ( "blog" );
	
	$autor_and_date = getconfig ( "blog_autor_and_date_text" );
	
	if ($autor_and_date === false) {
		setconfig ( "blog_autor_and_date_text", "<sub><strong>" . "%date% - Autor: %username%" . "</strong></sub>" );
		$autor_and_date = getconfig ( "blog_autor_and_date_text" );
	}
	
	$html = '<div class="blog-list-view">';
	
	// Wenn der Nutzer mindestens die Berechtigungen
	// eines Mitarbeiters hat, bekommt er den Link zum
	// Anlegen eines neuen Blogbeitrag angezeigt
	if ($hasPermission) {
		$html .= "<p><a class='add_blog_entry_link' href='" . buildSEOUrl ( get_requested_pagename () ) . "?blog_admin=add'>Blogeintrag anlegen</a></p>";
	}
	
	if (isset ( $_GET ["limit"] )) {
		$limit1 = intval ( $_GET ["limit"] );
		$limit2 = intval ( $_GET ["limit"] ) + $posts_per_page;
		$limit3 = $limit1 - $posts_per_page;
	} else {
		$limit1 = 0;
		$limit2 = $posts_per_page;
		$limit3 = $limit1 - $posts_per_page;
	}
	
	$count_query = db_query ( "SELECT id FROM `" . tbname ( "blog" ) . "` WHERE language='" . $_SESSION ["language"] . "' ORDER by id ASC" );
	$first_post = db_fetch_object ( $count_query );
	$oldest_post_id = $first_post->id;
	$total_entries = db_num_rows ( $count_query );
	
	if ($limit1 < 0) {
		$limit1 = 0;
	}
	$fields = " Case WHEN content_preview = '' OR content_preview IS NULL OR char_length(TRIM(content_preview)) = 0 
     THEN content_full 
     ELSE content_preview END as preview,
id, author, seo_shortname, title, datum, `views`, language, id, author, seo_shortname, title, datum, `views`, language";

	if ($hasPermission) {
		$query = db_query ( "SELECT $fields FROM `" . tbname ( "blog" ) . "` WHERE language='" . $_SESSION ["language"] . "' ORDER by datum DESC LIMIT $limit1, " . ($posts_per_page) );
	} else {
		$query = db_query ( "SELECT $fields FROM `" . tbname ( "blog" ) . "` WHERE language='" . $_SESSION ["language"] . "' AND entry_enabled = 1 ORDER by datum DESC LIMIT $limit1, " . ($posts_per_page) );
	}
	
	$html .= "";
	
	if (db_num_rows ( $query ) > 0) {
		while ( $post = db_fetch_object ( $query ) ) {
			$user = getUserById ( $post->author );
			
			$html .= "<h2 class='blog_headline'><a href='" . buildSEOUrl ( get_requested_pagename () ) . "?single=" . $post->seo_shortname . "'>" . htmlspecialchars ( $post->title ) . "</a></h2>";
			$html .= "<hr class='blog_hr'/>";
			
			$date_and_autor_string = $autor_and_date;
			
			$date_and_autor_string = str_replace ( "%date%", date ( getconfig ( "date_format" ), $post->datum ), $date_and_autor_string );
			
			$date_and_autor_string = str_replace ( "%username%", $user ["username"], $date_and_autor_string );
			
			$date_and_autor_string = str_replace ( "%firstname%", $user ["firstname"], $date_and_autor_string );
			$date_and_autor_string = str_replace ( "%lastname%", $user ["lastname"], $date_and_autor_string );
			
			$date_and_autor_string = str_replace ( "%views%", $post->views, $date_and_autor_string );
			
			$html .= $date_and_autor_string;
			$text = trim ( $post->preview );

			
			$text = apply_filter ( $text, "content" );
			
			$html .= "<div class='blog_post_content'>" . $text . "</div>";
			
			if ($_SESSION ["language"] == "de") {
				$html .= "<a href='" . buildSEOUrl ( get_requested_pagename () ) . "?single=" . $post->seo_shortname . "' class='blog-read-more-link'>weiterlesen...</a>
			 ";
			} else {
				$html .= "<br/><a href='" . buildSEOUrl ( get_requested_pagename () ) . "?single=" . $post->seo_shortname . "' class='blog-read-more-link'>read more...</a>
			";
			}
			$html .= "<br/><br/>";
			
			if ($hasPermission) {
				$html .= "<a href='" . buildSEOUrl ( get_requested_pagename () ) . "?blog_admin=edit_post&id=" . $post->id . "'>[Bearbeiten]</a> ";
				
				$html .= "<a href='" . buildSEOUrl ( get_requested_pagename () ) . "?blog_admin=delete_post&id=" . $post->id . "' onclick='return confirm(\"Diesen Post wirklich löschen?\")'>[Löschen]</a>";
				
				$html .= "<br/>";
			}
			
			$last_post_id = $post->id;
		}
		$html .= "<br/>";
		
		$html .= "<br/><div class='page_older_newer'>";
		
		$html .= "<span class='blog_pagination_newer'>";
		
		if ($limit3 > - 1) {
			
			$html .= "<a href='" . buildSEOUrl ( get_requested_pagename () ) . "?limit=" . ($limit3) . "'>";
		}
		
		if ($_SESSION ["language"] == "de") {
			$html .= "Neuere";
		} else {
			$html .= "newer";
		}
		
		if ($limit3 > - 1) {
			$html .= "</a>";
		}
		
		$html .= "</span>";
		
		$html .= "";
		
		$html .= "<span class='blog_pagination_older'>";
		
		if ($last_post_id != $oldest_post_id) {
			$html .= "<a href='" . buildSEOUrl ( get_requested_pagename () ) . "?limit=" . $limit2 . "'>";
		}
		
		if ($_SESSION ["language"] == "de") {
			$html .= "Ältere";
		} else {
			$html .= "older";
		}
		
		if ($last_post_id != $oldest_post_id) {
			$html .= "</a>";
		}
		
		$html .= "</span>";
		
		$html .= "</div>";
		$html .= "</div>";
		
		return $html;
	} else {
		$html .= "<p class='ulicms_error'>";
		
		if ($_SESSION ["language"] == "de") {
			$html .= "Es sind keine weiteren Blogeinträge vorhanden.";
		} else {
			
			$html .= "There are no other blog-entries available!";
		}
		
		$html .= "</div>";
		return $html;
	}
}
?>
