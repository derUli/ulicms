<?php
function template_part($name, $theme = null) {
	if (! $theme) {
		$theme = get_theme ();
	}
	$file = getTemplateDirPath ( $theme ) . $name . ".php";
}
function html5_doctype() {
	echo get_html5_doctype ();
}
function get_html5_doctype() {
	$html = '<!doctype html>';
	$html .= "\r\n";
	return $html;
}
function og_html_prefix() {
	echo get_og_html_prefix ();
}
function get_og_html_prefix() {
	$html = '<html prefix="og: http://ogp.me/ns#" lang="' . getCurrentLanguage () . '">';
	$html .= "\r\n";
	return $html;
}
function og_tags() {
	echo get_og_tags ();
}
function get_og_tags($systemname = null) {
	$html = "";
	if (is_200 ()) {
		$og_data = get_og_data ( $systemname );
		$og_title = $og_data ["og_title"];
		$og_type = $og_data ["og_type"];
		$og_image = $og_data ["og_image"];
		$og_description = $og_data ["og_description"];
		$og_url = getCurrentURL ();
		
		// Falls kein og_title für die Seite gesetzt ist, Standardtitel bzw. Headline verwenden
		if (is_null ( $og_title ) or empty ( $og_title )) {
			$og_title = get_headline ();
		}
		
		if (is_null ( $og_type ) or empty ( $og_type )) {
			$og_type = Settings::get ( "og_type" );
		}
		
		if (is_null ( $og_image ) or empty ( $og_image )) {
			$og_image = Settings::get ( "og_image" );
		}
		
		if (! $og_type) {
			$og_type = "article";
		}
		
		if (! empty ( $og_image ) and ! startsWith ( $og_image, "http" )) {
			$og_image = get_protocol_and_domain () . $og_image;
		}
		
		if (is_null ( $og_description ) or empty ( $og_description )) {
			$og_description = meta_description ();
		}
		
		$og_title = apply_filter ( $og_title, "og_title" );
		$og_type = apply_filter ( $og_type, "og_type" );
		$og_url = apply_filter ( $og_url, "og_url" );
		$og_image = apply_filter ( $og_image, "og_image" );
		$og_description = apply_filter ( $og_description, "og_description" );
		
		$html .= '<meta property="og:title" content="' . htmlspecialchars ( $og_title ) . '" />';
		$html .= "\r\n";
		
		if (! is_null ( $og_description ) and ! empty ( $og_description )) {
			$html .= '<meta property="og:description" content="' . htmlspecialchars ( $og_description ) . '" />';
			$html .= "\r\n";
		}
		
		$html .= '<meta property="og:type" content="' . htmlspecialchars ( $og_type ) . '" />';
		$html .= "\r\n";
		
		$html .= '<meta property="og:url" content="' . htmlspecialchars ( $og_url ) . '" />';
		$html .= "\r\n";
		
		$html .= '<meta property="og:image" content="' . htmlspecialchars ( $og_image ) . '" />';
		$html .= "\r\n";
		$html .= '<meta property="og:site_name" content="' . get_homepage_title () . '" />';
		$html .= "\r\n";
	}
	
	$html = apply_filter ( $html, "og_html" );
	return $html;
}
function get_og_data($systemname = "") {
	if (empty ( $systemname )) {
		$systemname = $_GET ["seite"];
	}
	
	if (empty ( $systemname )) {
		$systemname = get_frontpage ();
	}
	$query = db_query ( "SELECT og_title, og_type, og_image, og_description FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $systemname ) . "' AND language='" . db_escape ( $_SESSION ["language"] ) . "'" );
	if (db_num_rows ( $query ) > 0) {
		return db_fetch_assoc ( $query );
	} else {
		return null;
	}
}
function get_all_combined_html() {
	$html = "";
	$html .= get_combined_stylesheet_html ();
	$html .= "\r\n";
	$html .= combined_script_html ();
	$html .= "\r\n";
	return $html;
}
function edit_button() {
	echo get_edit_button ();
}
function get_edit_button() {
	$html = "";
	if (is_logged_in () and ! containsModule ()) {
		$acl = new ACL ();
		if ($acl->hasPermission ( "pages" ) and defined ( "NO_CACHE" )) {
			$id = get_ID ();
			$html .= "<div class=\"ulicms_edit\">[<a href=\"admin/index.php?action=pages_edit&page=$id\">" . get_translation ( "edit" ) . "</a>]</div>";
		}
	}
	return $html;
}
function all_combined_html() {
	echo get_all_comined_html ();
}
function get_ID() {
	if (! $page) {
		$page = get_requested_pagename ();
	}
	$result = null;
	$sql = "SELECT `id` FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $page ) . "'  AND language='" . db_escape ( $_SESSION ["language"] ) . "'";
	$query = db_query ( $sql );
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		$result = $result->id;
	}
	return $result;
}
function get_type() {
	if (! $page) {
		$page = get_requested_pagename ();
	}
	$result = "";
	$sql = "SELECT `type` FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $page ) . "'  AND language='" . db_escape ( $_SESSION ["language"] ) . "'";
	$query = db_query ( $sql );
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		$result = $result->type;
	}
	if (empty ( $result )) {
		$result = "page";
	}
	return $result;
}
function get_text_position() {
	if (! $page) {
		$page = get_requested_pagename ();
	}
	$result = "";
	$sql = "SELECT `text_position` FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $page ) . "'  AND language='" . db_escape ( $_SESSION ["language"] ) . "'";
	$query = db_query ( $sql );
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		$result = $result->text_position;
	}
	if (empty ( $result )) {
		$result = "before";
	}
	return $result;
}
function get_category_id($page = null) {
	if (! $page) {
		$page = get_requested_pagename ();
	}
	$result = null;
	$sql = "SELECT `category` FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $page ) . "'  AND language='" . db_escape ( $_SESSION ["language"] ) . "'";
	$query = db_query ( $sql );
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		$result = $result->category;
	}
	return $result;
}
function category_id($page = null) {
	echo get_category_id ( $page );
}
function get_parent($page = null) {
	if (! $page) {
		$page = get_requested_pagename ();
	}
	$result = "";
	$sql = "SELECT `parent` FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $page ) . "'  AND language='" . db_escape ( $_SESSION ["language"] ) . "'";
	$query = db_query ( $sql );
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		$result = $result->parent;
	}
	if (empty ( $result )) {
		$result = null;
	}
	return $result;
}
function get_custom_data($page = null) {
	if (! $page) {
		$page = get_requested_pagename ();
	}
	
	$sql = "SELECT `custom_data` FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $page ) . "'  AND language='" . db_escape ( $_SESSION ["language"] ) . "'";
	$query = db_query ( $sql );
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		return json_decode ( $result->custom_data, true );
	}
	return null;
}
function include_jquery() {
	if (Settings::get ( "disable_auto_include_jquery" )) {
		return;
	}
	$disabled_on_pages = Settings::get ( "jquery_disabled_on" );
	if ($disabled_on_pages) {
		$disabled_on_pages = trim ( $disabled_on_pages );
		$disabled_on_pages = explode ( ";", $disabled_on_pages );
	} else {
		$disabled_on_pages = array ();
	}
	
	if (! in_array ( get_requested_pagename (), $disabled_on_pages )) {
		?>


<script type="text/javascript" src="<?php echo get_jquery_url();?>"></script>
<?php
		add_hook ( "after_jquery_include" );
	}
}
function get_access($page = null) {
	if (! $page) {
		$page = get_requested_pagename ();
	}
	$sql = "SELECT `access` FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $page ) . "'  AND language='" . db_escape ( $_SESSION ["language"] ) . "'";
	$query = db_query ( $sql );
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		$access = explode ( ",", $result->access );
		return $access;
	}
	return null;
}
function get_redirection($page = null) {
	if (! $page) {
		$page = get_requested_pagename ();
	}
	$sql = "SELECT `redirection` FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $page ) . "'  AND language='" . db_escape ( $_SESSION ["language"] ) . "' and type='link'";
	$query = db_query ( $sql );
	if (db_num_rows ( $query ) > 0) {
		$result = db_fetch_object ( $query );
		if (! empty ( $result->redirection ) and ! is_null ( $result->redirection )) {
			return $result->redirection;
		}
		return null;
	}
	return null;
}
function get_theme($page = null) {
	if (! $page) {
		$page = get_requested_pagename ();
	}
	$theme = Settings::get ( "theme" );
	$mobile_theme = Settings::get ( "mobile_theme" );
	if ($mobile_theme and ! empty ( $mobile_theme ) and is_mobile ()) {
		$theme = $mobile_theme;
	}
	
	if (is_200 ()) {
		$sql = "SELECT `theme` FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $page ) . "'  AND language='" . db_escape ( $_SESSION ["language"] ) . "'";
		$query = db_query ( $sql );
		if ($query and db_num_rows ( $query ) > 0) {
			$data = db_fetch_object ( $query );
			if (isset ( $data->theme ) and ! empty ( $data->theme ) and ! is_null ( $data->theme )) {
				$theme = $data->theme;
			}
		}
	}
	$theme = apply_filter ( $theme, "theme" );
	return $theme;
}
function signature() {
	$signature = get_signature ();
	if (! empty ( $signature )) {
		echo '<div class="signature">';
		echo htmlspecialchars ( $signature );
		echo '</div>';
	}
}
function get_signature($page = null) {
	if (! is_200 ()) {
		return "";
	}
	
	if (! $page) {
		$page = get_requested_pagename ();
	}
	
	if (containsModule ( $page )) {
		return "";
	}
	
	$sql = "SELECT `signature` FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $page ) . "'  AND language='" . db_escape ( $_SESSION ["language"] ) . "'";
	$query = db_query ( $sql );
	if (db_num_rows ( $query ) > 0) {
		$data = db_fetch_object ( $query );
		return $data->signature;
	}
	
	return "";
}
function delete_custom_data($var = null, $page = null) {
	if (! $page) {
		$page = get_requested_pagename ();
	}
	$data = get_custom_data ( $page );
	if (is_null ( $data )) {
		$data = array ();
	}
	// Wenn $var gesetzt ist, nur $var aus custom_data löschen
	if ($var) {
		if (isset ( $data [$var] )) {
			unset ( $data [$var] );
		}
	}  // Wenn $var nicht gesetzt ist, alle Werte von custom_data löschen
else {
		
		$data = array ();
	}
	
	$json = json_encode ( $data );
	
	return db_query ( "UPDATE " . tbname ( "content" ) . " SET custom_data = '" . db_escape ( $json ) . "' WHERE systemname='" . db_escape ( $page ) . "'" );
}
function set_custom_data($var, $value, $page = null) {
	if (! $page) {
		$page = get_requested_pagename ();
	}
	
	$data = get_custom_data ( $page );
	if (is_null ( $data )) {
		$data = array ();
	}
	
	$data [$var] = $value;
	$json = json_encode ( $data );
	
	return db_query ( "UPDATE " . tbname ( "content" ) . " SET custom_data = '" . db_escape ( $json ) . "' WHERE systemname='" . db_escape ( $page ) . "'" );
}
function language_selection() {
	$query = db_query ( "SELECT * FROM " . tbname ( "languages" ) . " ORDER by name" );
	echo "<ul class='language_selection'>";
	while ( $row = db_fetch_object ( $query ) ) {
		$domain = getDomainByLanguage ( $row->language_code );
		if ($domain) {
			echo "<li>" . "<a href='http://" . $domain . "'>" . $row->name . "</a></li>";
		} else {
			echo "<li>" . "<a href='./?language=" . $row->language_code . "'>" . $row->name . "</a></li>";
		}
	}
	echo "</ul>";
}
function get_category() {
	$current_page = get_page ();
	return categories::getCategoryById ( $current_page ["category"] );
}
function category() {
	echo get_category ();
}
function get_body_classes() {
	$str = "page-id-" . get_ID () . " ";
	if (is_frontpage ()) {
		$str .= "home ";
	}
	
	if (is_404 ()) {
		$str .= "error404 ";
	}
	
	if (is_403 ()) {
		$str .= "error403 ";
	}
	
	if (is_404 () or is_403 ()) {
		$str .= "errorPage ";
	} else {
		$str .= "page ";
	}
	
	if (is_mobile ()) {
		$str .= "mobile ";
	} else {
		$str .= "desktop ";
	}
	
	if (containsModule ( get_requested_pagename () )) {
		$str .= "containsModule ";
	}
	$str = trim ( $str );
	return $str;
}
function body_classes() {
	echo get_body_classes ();
}

// Gibt "Diese Seite läuft mit UliCMS" aus
function poweredByUliCMS() {
	translation ( "POWERED_BY_ULICMS" );
}

// Einen zufälligen Banner aus der Datenbank ausgeben
function random_banner() {
	$query = db_query ( "SELECT * FROM " . tbname ( "banner" ) . " WHERE language='all' OR language='" . db_escape ( $_SESSION ["language"] ) . "'ORDER BY RAND() LIMIT 1" );
	if (db_num_rows ( $query ) > 0) {
		while ( $row = db_fetch_object ( $query ) ) {
			$type = "gif";
			if (isset ( $row->type )) {
				if (! empty ( $row->type )) {
					$type = $row->type;
				}
			}
			if ($type == "gif") {
				$title = Template::getEscape ( $row->name );
				$link_url = Template::getEscape ( $row->link_url );
				$image_url = Template::getEscape ( $row->image_url );
				echo "<a href='$link_url' target='_blank'><img src='$image_url' title='$title' alt='$title' border=0></a>";
			} else if ($type == "html") {
				echo $row->html;
			}
		}
	}
}
function logo() {
	if (! Settings::get ( "logo_image" )) {
		setconfig ( "logo_image", "" );
	}
	if (! Settings::get ( "logo_disabled" )) {
		setconfig ( "logo_disabled", "no" );
	}
	
	$logo_path = "content/images/" . Settings::get ( "logo_image" );
	
	if (Settings::get ( "logo_disabled" ) == "no" and file_exists ( $logo_path )) {
		echo '<img class="website_logo" src="' . $logo_path . '" alt="' . htmlspecialchars ( Settings::get ( "homepage_title" ), ENT_QUOTES, "UTF-8" ) . '"/>';
	}
}
function year() {
	echo date ( "Y" );
}
function homepage_owner() {
	echo Settings::get ( "homepage_owner" );
}
function get_homepage_title() {
	$homepage_title = Settings::get ( "homepage_title_" . $_SESSION ["language"] );
	if (! $homepage_title) {
		$homepage_title = Settings::get ( "homepage_title" );
	}
	return htmlspecialchars ( $homepage_title, ENT_QUOTES, "UTF-8" );
}
function homepage_title() {
	echo get_homepage_title ();
}
$status = check_status ();
function meta_keywords($ipage = null) {
	$status = check_status ();
	$ipage = db_escape ( $_GET ["seite"] );
	$query = db_query ( "SELECT meta_keywords FROM " . tbname ( "content" ) . " WHERE systemname='$ipage' AND language='" . db_escape ( $_SESSION ["language"] ) . "'" );
	
	if (db_num_rows ( $query ) > 0) {
		while ( $row = db_fetch_object ( $query ) ) {
			if (! empty ( $row->meta_keywords )) {
				return $row->meta_keywords;
			}
		}
	}
	$meta_keywords = Settings::get ( "meta_keywords_" . $_SESSION ["language"] );
	if (! $meta_keywords) {
		$meta_keywords = Settings::get ( "meta_keywords" );
	}
	
	return $meta_keywords;
}
function meta_description($ipage = null) {
	$status = check_status ();
	$ipage = db_escape ( $_GET ["seite"] );
	$query = db_query ( "SELECT meta_description FROM " . tbname ( "content" ) . " WHERE systemname='$ipage' AND language='" . db_escape ( $_SESSION ["language"] ) . "'", $connection );
	if ($ipage == "") {
		$query = db_query ( "SELECT meta_description FROM " . tbname ( "content" ) . " ORDER BY id LIMIT 1", $connection );
	}
	if (db_num_rows ( $query ) > 0) {
		while ( $row = db_fetch_object ( $query ) ) {
			if (! empty ( $row->meta_description )) {
				return $row->meta_description;
			}
		}
	}
	$meta_description = Settings::get ( "meta_description_" . $_SESSION ["language"] );
	if (! $meta_description) {
		$meta_description = Settings::get ( "meta_description" );
	}
	
	return $meta_description;
}
function get_title($ipage = null, $headline = false) {
	$status = check_status ();
	if ($status == "404 Not Found") {
		return TRANSLATION_PAGE_NOT_FOUND;
	} else if ($status == "403 Forbidden") {
		return TRANSLATION_FORBIDDEN;
	}
	
	$ipage = db_escape ( $_GET ["seite"] );
	$query = db_query ( "SELECT alternate_title, title FROM " . tbname ( "content" ) . " WHERE systemname='$ipage' AND language='" . db_escape ( $_SESSION ["language"] ) . "'", $connection );
	if ($ipage == "") {
		$query = db_query ( "SELECT * FROM " . tbname ( "content" ) . " ORDER BY id LIMIT 1" );
	}
	if (db_num_rows ( $query ) > 0) {
		while ( $row = db_fetch_object ( $query ) ) {
			if ($headline and isset ( $row->alternate_title ) and ! empty ( $row->alternate_title )) {
				$title = $row->alternate_title;
			} else {
				$title = $row->title;
			}
			
			$title = apply_filter ( $title, "title" );
			return $title;
		}
	}
}
function title($ipage = null) {
	echo stringHelper::real_htmlspecialchars ( get_title ( $ipage ) );
}
function get_headline($ipage = null) {
	return get_title ( $ipage, true );
}
function headline($ipage = null) {
	echo stringHelper::real_htmlspecialchars ( get_headline ( $ipage ) );
}
function import($ipage) {
	$ipage = db_escape ( $ipage );
	if ($ipage == "") {
		$query = db_query ( "SELECT content FROM " . tbname ( "content" ) . " WHERE language='" . db_escape ( $_SESSION ["language"] ) . "' ORDER BY id LIMIT 1" );
	} else {
		$query = db_query ( "SELECT content FROM " . tbname ( "content" ) . " WHERE systemname='$ipage' AND language='" . db_escape ( $_SESSION ["language"] ) . "'" );
	}
	
	if (db_num_rows ( $query ) == 0) {
		return false;
	} else {
		while ( $row = db_fetch_object ( $query ) ) {
			$row->content = replaceShortcodesWithModules ( $row->content );
			$row->content = apply_filter ( $row->content, "content" );
			$row->content = correctHTMLValidationErrors ( $row->content );
			echo $row->content;
			return true;
		}
	}
}

// Todo: nicht W3-konformen HTML-Code korrigieren
function correctHTMLValidationErrors($txt) {
	if (Settings::get ( "disable_html_validation" )) {
		return $txt;
	}
	
	// Ersetze & durch &amp;
	$txt = preg_replace ( '/[&](?![A-Za-z]+[;])/', "&amp;", $txt );
	
	// replaced deprecated HTML-Tags
	$txt = str_ireplace ( "<center>", "<div style=\"text-align:center\">", $txt );
	$txt = str_ireplace ( "</center>", "</div>", $txt );
	$txt = str_ireplace ( "<strike>", "<del>", $txt );
	$txt = str_ireplace ( "</strike>", "</del>", $txt );
	$txt = str_ireplace ( "<s>", "<del>", $txt );
	$txt = str_ireplace ( "</s>", "</del>", $txt );
	$txt = str_ireplace ( "<tt>", "<code>", $txt );
	$txt = str_ireplace ( "</tt>", "</code>", $txt );
	$txt = str_ireplace ( "<dir>", "<ul>", $txt );
	$txt = str_ireplace ( "</dir>", "</ul>", $txt );
	$txt = str_ireplace ( "<acronym>", "<abbr>", $txt );
	$txt = str_ireplace ( "</acronym>", "</abbr>", $txt );
	
	return $txt;
}
function apply_filter($text, $type) {
	$modules = getAllModules ();
	for($i = 0; $i < count ( $modules ); $i ++) {
		$module_content_filter_file1 = getModulePath ( $modules [$i] ) . $modules [$i] . "_" . $type . "_filter.php";
		$module_content_filter_file2 = getModulePath ( $modules [$i] ) . "filters/" . $type . ".php";
		if (file_exists ( $module_content_filter_file1 )) {
			include_once $module_content_filter_file1;
			if (function_exists ( $modules [$i] . "_" . $type . "_filter" )) {
				$text = call_user_func ( $modules [$i] . "_" . $type . "_filter", $text );
			}
		} else if (file_exists ( $module_content_filter_file2 )) {
			include_once $module_content_filter_file2;
			if (function_exists ( $modules [$i] . "_" . $type . "_filter" )) {
				$text = call_user_func ( $modules [$i] . "_" . $type . "_filter", $text );
			}
		}
	}
	return $text;
}
function get_motto() {
	// Existiert ein Motto für diese Sprache? z.B. motto_en
	$motto = Settings::get ( "motto_" . $_SESSION ["language"] );
	
	// Ansonsten Standard Motto
	if (! $motto) {
		$motto = Settings::get ( "motto" );
	}
	return htmlspecialchars ( $motto, ENT_QUOTES, "UTF-8" );
}
function motto() {
	echo get_motto ();
}
function get_frontpage() {
	setLanguageByDomain ();
	if (isset ( $_SESSION ["language"] )) {
		$frontpage = Settings::get ( "frontpage_" . $_SESSION ["language"] );
		if ($frontpage) {
			return $frontpage;
		}
	}
	return Settings::get ( "frontpage" );
}
function get_requested_pagename() {
	$value = db_escape ( $_GET ["seite"] );
	if ($value == "") {
		$value = get_frontpage ();
	}
	return $value;
}
function is_frontpage() {
	return get_requested_pagename () === get_frontpage ();
}
function is_200() {
	return check_status () == "200 OK";
}
function is_404() {
	return check_status () == "404 Not Found";
}
function is_403() {
	return check_status () == "403 Forbidden";
}
function buildtree($src_arr, $parent_id = 0, $tree = array()) {
	foreach ( $src_arr as $idx => $row ) {
		if ($row ['parent'] == $parent_id) {
			foreach ( $row as $k => $v )
				$tree [$row ['id']] [$k] = $v;
			unset ( $src_arr [$idx] );
			$tree [$row ['id']] ['children'] = buildtree ( $src_arr, $row ['id'] );
		}
	}
	ksort ( $tree );
	return $tree;
}
function parent_item_contains_current_page($id) {
	$retval = false;
	$id = intval ( $id );
	$language = $_SESSION ["language"];
	$sql = "SELECT id, systemname, parent FROM " . tbname ( "content" ) . " WHERE language = '$language' AND active = 1 AND `deleted_at` IS NULL";
	$r = db_query ( $sql );
	
	$data = array ();
	while ( $row = db_fetch_assoc ( $r ) ) {
		$data [] = $row;
	}
	
	$tree = buildtree ( $data, $id );
	foreach ( $tree as $key ) {
		if ($key ["systemname"] == get_requested_pagename ()) {
			$retval = true;
		}
	}
	return $retval;
}
function get_menu($name = "top", $parent = null, $recursive = true, $order = "position") {
	$html = "";
	$name = db_escape ( $name );
	$language = $_SESSION ["language"];
	$sql = "SELECT id, systemname, access, redirection, title, alternate_title, menu_image, target FROM " . tbname ( "content" ) . " WHERE menu='$name' AND language = '$language' AND active = 1 AND `deleted_at` IS NULL AND parent ";
	
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
		$html .= "<ul class='menu_" . $name . " navmenu'>\n";
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
			if ($containsCurrentItem)
				$additional_classes .= "contains-current-page ";
			
			if (get_requested_pagename () != $row->systemname)
				$html .= "  <li class='" . trim ( $additional_classes ) . "'>";
			else
				$html .= "  <li class='menu_active_list_item" . rtrim ( $additional_classes ) . "'>";
			if (! empty ( $row->alternate_title ))
				$title = $row->alternate_title;
			else
				$title = $row->title;
			if (get_requested_pagename () != $row->systemname) {
				$html .= "<a href='" . buildSEOUrl ( $row->systemname, $row->redirection ) . "' target='" . $row->target . "' class='" . trim ( $additional_classes ) . "'>";
			} else {
				$html .= "<a class='menu_active_link" . rtrim ( $additional_classes ) . "' href='" . buildSEOUrl ( $row->systemname, $row->redirection ) . "' target='" . $row->target . "'>";
			}
			if (! is_null ( $row->menu_image ) and ! empty ( $row->menu_image )) {
				$html .= '<img src="' . $row->menu_image . '" alt="' . htmlentities ( $row->title, ENT_QUOTES, "UTF-8" ) . '"/>';
			} else {
				$html .= htmlentities ( $row->title, ENT_QUOTES, "UTF-8" );
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
function menu($name = "top", $parent = null, $recursive = true, $order = 'position') {
	echo get_menu ( $name, $parent, $recursive, $order );
}
function get_base_metas() {
	ob_start ();
	base_metas ();
	return ob_get_clean ();
}
function output_favicon_code() {
	echo get_output_favicon_code ();
}
function get_output_favicon_code() {
	$url = "content/images/favicon.ico";
	$path = ULICMS_ROOT . "/" . $url;
	$html = "";
	if (is_file ( $path )) {
		$html = '<link rel="icon" href="' . $url . '" type="image/x-icon" />' . "\r\n" . '<link rel="shortcut icon" href="' . $url . '" type="image/x-icon" />';
	}
	return $html;
}
function base_metas() {
	$title_format = Settings::get ( "title_format" );
	if ($title_format) {
		$title = $title_format;
		$title = str_ireplace ( "%homepage_title%", get_homepage_title (), $title );
		$title = str_ireplace ( "%title%", get_title (), $title );
		$title = str_ireplace ( "%motto%", get_motto (), $title );
		
		$title = apply_filter ( $title, "title_tag" );
		$title = htmlentities ( $title, ENT_QUOTES, "UTF-8" );
		
		echo "<title>" . $title . "</title>\r\n";
	}
	
	echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>';
	echo "\r\n";
	
	echo '<meta charset="utf-8"/>';
	echo "\r\n";
	
	if (! Settings::get ( "disable_no_format_detection" )) {
		echo '<meta name="format-detection" content="telephone=no"/>';
		echo "\r\n";
	}
	
	$dir = dirname ( $_SERVER ["SCRIPT_NAME"] );
	$dir = str_replace ( "\\", "/", $dir );
	
	if (endsWith ( $dir, "/" ) == false) {
		$dir .= "/";
	}
	
	$robots = Settings::get ( "robots" );
	if ($robots) {
		$robots = apply_filter ( $robots, "meta_robots" );
		echo '<meta name="robots" content="' . $robots . '"/>';
		echo "\r\n";
	}
	if (! Settings::get ( "hide_meta_generator" )) {
		$powered_by = ULICMS_ROOT . "/powered-by.php";
		if (file_exists ( $powered_by ))
			@include $powered_by;
		
		echo '<meta name="generator" content="UliCMS ' . cms_version () . '"/>';
		echo "\r\n";
		
		output_favicon_code ();
		echo "\r\n";
	}
	
	if (! Settings::get ( "hide_shortlink" ) and (is_200 () or is_403 ())) {
		$shortlink = get_shortlink ();
		if ($shortlink) {
			echo '<link rel="shortlink" href="' . $shortlink . '"/>';
			echo "\r\n";
		}
	}
	
	if (! Settings::get ( "hide_canonical" ) and (is_200 () or is_403 ())) {
		$canonical = get_canonical ();
		if ($canonical) {
			echo '<link rel="canonical"  href="' . $canonical . '"/>';
			echo "\r\n";
		}
	}
	if (! Settings::get ( "no_autoembed_core_css" )) {
		echo '<link rel="stylesheet" type="text/css" href="core.min.css"/>';
		echo "\r\n";
	}
	
	$style_file = getTemplateDirPath ( get_theme () ) . "style.css";
	if (is_file ( $style_file )) {
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$style_file\"/>";
	}
	echo "\r\n";
	$keywords = meta_keywords ();
	if (! $keywords) {
		$keywords = Settings::get ( "meta_keywords" );
	}
	if ($keywords != "" && $keywords != false) {
		if (! Settings::get ( "hide_meta_keywords" )) {
			$keywords = apply_filter ( $keywords, "meta_keywords" );
			$keywords = htmlentities ( $keywords, ENT_QUOTES, "UTF-8" );
			echo '<meta name="keywords" content="' . $keywords . '"/>';
			echo "\r\n";
		}
	}
	$description = meta_description ();
	if (! $description) {
		$description = Settings::get ( "meta_description" );
	}
	if ($description != "" && $description != false) {
		
		$description = apply_filter ( $description, "meta_description" );
		
		$$description = htmlentities ( $description, ENT_QUOTES, "UTF-8" );
		if (! Settings::get ( "hide_meta_description" )) {
			echo '<meta name="description" content="' . $description . '"/>';
			echo "\r\n";
		}
	}
	$zoom = Settings::get ( "zoom" );
	if ($zoom === false) {
		setconfig ( "zoom", 100 );
		$zoom = 100;
	}
	
	if (! Settings::get ( "disable_custom_layout_options" )) {
		$font = Settings::get ( "default-font" );
		if ($font == "google") {
			$google_font = Settings::get ( "google-font" );
			if ($google_font) {
				echo '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=' . urlencode ( $google_font ) . '"/>';
				echo "\r\n";
				$font = "'$google_font'";
			}
		}
		echo "
<style type=\"text/css\">
body{
zoom:" . $zoom . "%;
font-family:" . $font . ";
font-size:" . Settings::get ( "font-size" ) . ";
background-color:" . Settings::get ( "body-background-color" ) . ";
color:" . Settings::get ( "body-text-color" ) . ";
}
</style>
";
		
		if (Settings::get ( "video_width_100_percent" )) {
			echo "<style type=\"text/css\">
video {
  width: 100% !important;
  height: auto !important;
  }
           </style>
        ";
		}
	}
	include_jquery ();
	add_hook ( "head" );
}
function head() {
	base_metas ();
}
function get_head() {
	return get_base_metas ();
}
function autor() {
	echo get_autor ();
}
function get_autor() {
	$seite = $_GET ["seite"];
	if (empty ( $seite )) {
		$query = db_query ( "SELECT systemname FROM " . tbname ( "content" ) . " ORDER BY id LIMIT 1" );
		$result = db_fetch_object ( $query );
		$seite = $result->systemname;
	}
	
	if (check_status () != "200 OK") {
		return;
	}
	
	$query = db_query ( "SELECT systemname, autor FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $seite ) . "' AND language='" . db_escape ( $_SESSION ["language"] ) . "'", $connection );
	if (db_num_rows ( $query ) < 1) {
		return;
	}
	$result = db_fetch_assoc ( $query );
	if ($result ["systemname"] == "kontakt" || $result ["systemname"] == "impressum" || StartsWith ( $result ["systemname"], "menu_" )) {
		return;
	}
	$query2 = db_query ( "SELECT firstname, lastname, username FROM " . tbname ( "users" ) . " WHERE id=" . $result ["autor"], $connection );
	$result2 = db_fetch_array ( $query2 );
	if (db_num_rows ( $query2 ) == 0) {
		return;
	}
	$datum = $result ["created"];
	$out = Settings::get ( "autor_text" );
	$out = str_replace ( "Vorname", $result2 ["firstname"], $out );
	$out = str_replace ( "Nachname", $result2 ["lastname"], $out );
	$out = str_replace ( "Username", $result2 ["username"], $out );
	if (! is_403 () or $_SESSION ["group"] >= 20) {
		return $out;
	}
}
function get_page($systemname = "") {
	if (empty ( $systemname )) {
		$systemname = $_GET ["seite"];
	}
	
	if (empty ( $systemname ))
		$systemname = get_frontpage ();
	$query = db_query ( "SELECT * FROM " . tbname ( "content" ) . " WHERE systemname='" . db_escape ( $systemname ) . "' AND language='" . db_escape ( $_SESSION ["language"] ) . "'" );
	if (db_num_rows ( $query ) > 0) {
		return db_fetch_assoc ( $query );
	} else {
		return null;
	}
}
function content() {
	$theme = Settings::get ( "theme" );
	$status = check_status ();
	if ($status == "404 Not Found") {
		if (file_exists ( getTemplateDirPath ( $theme ) . "404.php" )) {
			include getTemplateDirPath ( $theme ) . "404.php";
		} else {
			echo TRANSLATION_PAGE_NOT_FOUND_CONTENT;
		}
		return false;
	} else if ($status == "403 Forbidden") {
		if (file_exists ( getTemplateDirPath ( $theme ) . "403.php" )) {
			include getTemplateDirPath ( $theme ) . "403.php";
		} else {
			echo TRANSLATION_FORBIDDEN_COTENT;
		}
		return false;
	}
	
	if (! is_logged_in ()) {
		db_query ( "UPDATE " . tbname ( "content" ) . " SET views = views + 1 WHERE systemname='" . Database::escapeValue ( $_GET ["seite"] ) . "' AND language='" . db_escape ( $_SESSION ["language"] ) . "'" );
	}
	return import ( $_GET ["seite"] );
}
function checkforAccessForDevice($access) {
	$access = explode ( ",", $access );
	$allowed = false;
	if (in_array ( "mobile", $access ) and is_mobile ()) {
		$allowed = true;
	}
	if (in_array ( "desktop", $access ) and ! is_mobile ()) {
		$allowed = true;
	}
	if (! in_array ( "mobile", $access ) and ! in_array ( "desktop", $access )) {
		$allowed = true;
	}
	return $allowed;
}
function checkAccess($access = "") {
	$access_for_device = checkforAccessForDevice ( $access );
	if (! $access_for_device) {
		return null;
	}
	$access = explode ( ",", $access );
	if (in_array ( "all", $access )) {
		return "all";
	}
	if (in_array ( "registered", $access ) and is_logged_in ()) {
		return "registered";
	}
	
	for($i = 0; $i < count ( $access ); $i ++) {
		if (is_numeric ( $access [$i] ) and isset ( $_SESSION ["group_id"] ) and $access [$i] == $_SESSION ["group_id"]) {
			return $access [$i];
		}
	}
	return null;
}
function check_status() {
	if ($_GET ["seite"] == "") {
		$_GET ["seite"] = get_frontpage ();
	}
	
	$page = $_GET ["seite"];
	$cached_page_path = buildCacheFilePath ( $page );
	$status = apply_filter ( "", "status" );
	if (isset ( $_SERVER ["ulicms_send_304"] )) {
		header ( "HTTP/1.1 304 Not Modified" );
		exit ();
	}
	if (! empty ( $status )) {
		return $status;
	}
	if (file_exists ( $cached_page_path ) and ! is_logged_in ()) {
		$last_modified = filemtime ( $cached_page_path );
		if (time () - $last_modified < CACHE_PERIOD) {
			return "200 OK";
		}
	}
	
	$test = get_page ( $_GET ["seite"] );
	if (! $test or ! is_null ( $test ["deleted_at"] )) {
		no_cache ();
		return "404 Not Found";
	}
	$access = checkAccess ( $test ["access"] );
	if ($access) {
		if ($access != "all") {
			no_cache ();
		}
		return "200 OK";
	}
	no_cache ();
	return "403 Forbidden";
}
