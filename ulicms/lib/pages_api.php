<?php
function delete_page($id = false, $systemname = false) {
	if ($id) {
		db_query ( "DELETE FROM " . tbname ( "content" ) . " WHERE id=$id" );
		return db_affected_rows () > 0;
	}
	
	if ($systemname) {
		db_query ( "DELETE FROM " . tbname ( "content" ) . " WHERE systemname='$systemname'" );
		return db_affected_rows () > 0;
	}
	
	return false;
}
function add_page($system_title, $page_title, $page_content, $position, $activated = 1, $comments_enabled = 0, $redirection = "", $menu = "top", $parent = "NULL", $language = "de", $access = array("all"), $target = "_self", $meta_keywords = "", $meta_description = "") {
	$system_title = db_escape ( $system_title );
	$page_title = db_escape ( $page_title );
	$page_content = $page_content;
	$notinfeed = 0;
	$redirection = db_escape ( $redirection );
	$menu = db_escape ( $menu );
	$position = $position;
	
	if ($parent == "NULL")
		$parent = "NULL";
	else
		$parent = db_escape ( $parent );
	
	$access = implode ( ",", $access );
	$access = db_escape ( $access );
	$target = db_escape ( $target );
	
	$page_content = db_real_escape_String ( $page_content );
	$language = db_escape ( $language );
	
	$meta_keywords = db_real_escape_String ( $meta_keywords );
	$meta_description = db_real_escape_String ( $meta_description );
	
	if (! isset ( $_SESSION ["login_id"] )) {
		$session_id = 1;
	} else {
		$session_id = $_SESSION ["login_id"];
	}
	
	return db_query ( "INSERT INTO " . tbname ( "content" ) . " (systemname,title,content,parent, active,created,lastmodified,autor,
  comments_enabled,notinfeed,redirection,menu,position, 
  access, meta_description, meta_keywords, language, target) 
  VALUES('$system_title','$page_title','$page_content',$parent, $activated," . time () . ", " . time () . "," . $session_id . ", " . $comments_enabled . ",$notinfeed, '$redirection', '$menu', $position, '" . $access . "', 
  '$meta_description', '$meta_keywords',
  '$language', '$target')" ) !== false;
}

?>