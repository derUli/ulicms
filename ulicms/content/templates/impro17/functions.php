<?php
function jumbotron_get_menu($name = "top", $parent = null, $recursive = true, $order = "position") {
	$html = "";
	$name = db_escape ( $name );
	$language = $_SESSION ["language"];
	$sql = "SELECT id, systemname, access, redirection, title, alternate_title, menu_image, target, position FROM " . tbname ( "content" ) . " WHERE menu='$name' AND language = '$language' AND active = 1 AND `deleted_at` IS NULL AND parent ";
	
	if (is_null ( $parent )) {
		$sql .= " IS NULL ";
	} else {
		$sql .= " = " . intval ( $parent ) . " ";
	}
	$sql .= " ORDER by " . $order;
	$query = db_query ( $sql );
	
	if (db_num_rows ( $query ) == 0) {
		return $html;
	}
	
	if (is_null ( $parent )) {
		$html .= "<ul class='nav nav-pills pull-right menu_top'>\n";
	} else {
		$containsCurrentItem = parent_item_contains_current_page ( $parent );
		
		$classes = "sub_menu";
		
		if ($containsCurrentItem) {
			$classes .= " contains-current-page";
		}
		$html .= "<ul class='" . $classes . "'>\n";
	}
	
	while ( $row = db_fetch_object ( $query ) ) {
		if (checkAccess ( $row->access )) {
			$containsCurrentItem = parent_item_contains_current_page ( $row->id );
			
			$additional_classes = " menu-link-to-" . $row->id . " ";
			if ($containsCurrentItem) {
				$additional_classes .= "active ";
			}
			
			if (get_requested_pagename () != $row->systemname) {
				$html .= "  <li class='" . trim ( $additional_classes ) . "'>";
			} else {
				$html .= "  <li class='active" . rtrim ( $additional_classes ) . "'>";
			}
			
			$title = $row->title;
			// Show page positions in menu if user has the "pages" permission.
			if (is_logged_in ()) {
				$acl = new ACL ();
				if ($acl->hasPermission ( "pages" )) {
					$title .= " ({$row->position})";
				}
			}
			
			if (get_requested_pagename () != $row->systemname) {
				$html .= "<a href='" . buildSEOUrl ( $row->systemname, $row->redirection ) . "' target='" . $row->target . "' class='" . trim ( $additional_classes ) . "'>";
			} else {
				$html .= "<a class='active" . rtrim ( $additional_classes ) . "' href='" . buildSEOUrl ( $row->systemname, $row->redirection ) . "' target='" . $row->target . "'>";
			}
			if (! is_null ( $row->menu_image ) and ! empty ( $row->menu_image )) {
				$html .= '<img src="' . $row->menu_image . '" alt="' . htmlentities ( $title, ENT_QUOTES, "UTF-8" ) . '"/>';
			} else {
				$html .= htmlentities ( $title, ENT_QUOTES, "UTF-8" );
			}
			$html .= "</a>\n";
			
			if ($recursive) {
				$html .= get_menu ( $name, $row->id, true, $order );
			}
			
			$html .= "</li>";
		}
	}
	$html .= "</ul>";
	return $html;
}
