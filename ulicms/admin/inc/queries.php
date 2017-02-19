<?php
$acl = new ACL ();
add_hook ( "query" );

include_once ULICMS_ROOT . "/classes/vcs.php";

if ($_REQUEST ["action"] == "install-sin-package" and isNotNullOrEmpty ( $_REQUEST ["file"] ) and $acl->hasPermission ( "install_packages" )) {
	$file = basename ( $_POST ["file"] );
	$path = Path::resolve ( "ULICMS_TMP/$file" );
	$pkg = new SinPackageInstaller ( $path );
	$pkg->installPackage ();
	@unlink ( $path );
	Request::redirect ( "index.php?action=sin_package_install_ok&file=$file" );
}
if ($_GET ["action"] == "save_settings" && isset ( $_POST ["save_settings"] ) && $acl->hasPermission ( "settings_simple" )) {
	add_hook ( "before_safe_simple_settings" );
	setconfig ( "registered_user_default_level", intval ( $_POST ["registered_user_default_level"] ) );
	setconfig ( "homepage_owner", db_escape ( $_POST ["homepage_owner"] ) );
	setconfig ( "language", db_escape ( $_POST ["language"] ) );
	setconfig ( "visitors_can_register", intval ( isset ( $_POST ["visitors_can_register"] ) ) );
	setconfig ( "maintenance_mode", intval ( isset ( $_POST ["maintenance_mode"] ) ) );
	setconfig ( "email", db_escape ( $_POST ["email"] ) );
	setconfig ( "max_news", ( int ) $_POST ["max_news"] );
	setconfig ( "logo_disabled", db_escape ( $_POST ["logo_disabled"] ) );
	setconfig ( "timezone", db_escape ( $_POST ["timezone"] ) );
	setconfig ( "robots", db_escape ( $_POST ["robots"] ) );
	
	if (! isset ( $_POST ["disable_password_reset"] )) {
		setconfig ( "disable_password_reset", "disable_password_reset" );
	} else {
		Settings::delete ( "disable_password_reset" );
	}
	
	add_hook ( "after_safe_simple_settings" );
	header ( "Location: index.php?action=settings_simple" );
	exit ();
}

if ($_GET ["action"] == "view_website" or $_GET ["action"] == "frontpage") {
	add_hook ( "before_view_website" );
	header ( "Location: ../" );
	exit ();
}

if (isset ( $_GET ["clear_cache"] )) {
	clearCache ();
}

if ($_GET ["action"] == "undelete_page" && $acl->hasPermission ( "pages" ) && get_request_method () == "POST") {
	$page = intval ( $_GET ["page"] );
	add_hook ( "before_undelete_page" );
	db_query ( "UPDATE " . tbname ( "content" ) . " SET `deleted_at` = NULL" . " WHERE id=$page" );
	add_hook ( "after_undelete_page" );
	header ( "Location: index.php?action=pages" );
	exit ();
}

if ($_GET ["action"] == "pages_delete" && $acl->hasPermission ( "pages" ) && get_request_method () == "POST") {
	$page = intval ( $_GET ["page"] );
	add_hook ( "before_delete_page" );
	db_query ( "UPDATE " . tbname ( "content" ) . " SET `deleted_at` = " . time () . " WHERE id=$page" );
	add_hook ( "after_delete_page" );
	header ( "Location: index.php?action=pages" );
	exit ();
}

if ($_GET ["action"] == "spam_filter" and isset ( $_POST ["submit_spamfilter_settings"] ) and $acl->hasPermission ( "spam_filter" ) and get_request_method () == "POST") {
	
	add_hook ( "before_save_spamfilter_settings" );
	
	if ($_POST ["spamfilter_enabled"] == "yes") {
		Settings::set ( "spamfilter_enabled", "yes" );
	} else {
		Settings::set ( "spamfilter_enabled", "no" );
	}
	
	if (isset ( $_POST ["country_blacklist"] )) {
		Settings::set ( "country_blacklist", $_POST ["country_blacklist"] );
	}
	
	if (isset ( $_POST ["check_for_spamhaus"] )) {
		Settings::set ( "check_for_spamhaus", "check" );
	} else {
		Settings::delete ( "check_for_spamhaus" );
	}
	
	if (isset ( $_POST ["spamfilter_words_blacklist"] )) {
		$blacklist = $_POST ["spamfilter_words_blacklist"];
		$blacklist = str_replace ( "\r\n", "||", $blacklist );
		$blacklist = str_replace ( "\n", "||", $blacklist );
		Settings::set ( "spamfilter_words_blacklist", $blacklist );
	}
	
	if (isset ( $_POST ["disallow_chinese_chars"] ))
		Settings::set ( "disallow_chinese_chars", "disallow" );
	else
		Settings::delete ( "disallow_chinese_chars" );
	add_hook ( "after_save_spamfilter_settings" );
}

if ($_GET ["action"] == "empty_trash") {
	add_hook ( "before_empty_trash" );
	db_query ( "DELETE FROM " . tbname ( "content" ) . " WHERE deleted_at IS NOT NULL" );
	add_hook ( "after_empty_trash" );
	header ( "Location: index.php?action=pages" );
	exit ();
}

if ($_GET ["action"] == "key_delete" and $acl->hasPermission ( "expert_settings" ) and get_request_method () == "POST") {
	add_hook ( "before_delete_key" );
	Settings::delete ( $_GET ["key"] );
	add_hook ( "after_delete_key" );
	header ( "Location: index.php?action=settings" );
	exit ();
}

if ($_GET ["action"] == "languages" and ! empty ( $_GET ["delete"] ) and $acl->hasPermission ( "languages" ) and get_request_method () == "POST") {
	add_hook ( "before_delete_language" );
	db_query ( "DELETE FROM " . tbname ( "languages" ) . " WHERE id = " . intval ( $_GET ["delete"] ) );
	add_hook ( "after_delete_language" );
}

if ($_GET ["action"] == "languages" and ! empty ( $_GET ["default"] ) and $acl->hasPermission ( "languages" )) {
	add_hook ( "before_set_default_language" );
	setconfig ( "default_language", db_escape ( $_GET ["default"] ) );
	setconfig ( "system_language", db_escape ( $_GET ["default"] ) );
	add_hook ( "after_set_default_language" );
}

if (isset ( $_POST ["add_language"] ) and $acl->hasPermission ( "languages" )) {
	if (! empty ( $_POST ["name"] ) and ! empty ( $_POST ["language_code"] )) {
		$name = db_escape ( $_POST ["name"] );
		$language_code = db_escape ( $_POST ["language_code"] );
		add_hook ( "before_create_language" );
		db_query ( "INSERT INTO " . tbname ( "languages" ) . "(name, language_code)
      VALUES('$name', '$language_code')" );
		add_hook ( "after_create_language" );
	}
}

if ($_GET ["action"] == "banner_delete" && $acl->hasPermission ( "banners" ) && get_request_method () == "POST") {
	$banner = intval ( $_GET ["banner"] );
	
	add_hook ( "before_banner_delete" );
	$query = db_query ( "DELETE FROM " . tbname ( "banner" ) . " WHERE id='$banner'", $connection );
	add_hook ( "after_banner_delete" );
	header ( "Location: index.php?action=banner" );
	exit ();
}

if ($_GET ["action"] == "admin_delete" && (is_admin () or $acl->hasPermission ( "users" )) && get_request_method () == "POST") {
	$admin = intval ( $_GET ["admin"] );
	add_hook ( "before_admin_delete" );
	$query = db_query ( "DELETE FROM " . tbname ( "users" ) . " WHERE id='$admin'", $connection );
	add_hook ( "after_admin_delete" );
	header ( "Location: index.php?action=admins" );
	exit ();
}

if (isset ( $_GET ["do_restore_version"] ) and $acl->hasPermission ( "pages" )) {
	$do_restore_version = intval ( $_GET ["do_restore_version"] );
	$rev = VCS::getRevisionByID ( $do_restore_version );
	if ($rev) {
		VCS::restoreRevision ( $do_restore_version );
	}
	
	Request::redirect ( "index.php?action=pages_edit&page=" . $rev->content_id );
}

if ($_POST ["add_page"] == "add_page" && $acl->hasPermission ( "pages" )) {
	if ($_POST ["system_title"] != "") {
		$system_title = db_escape ( $_POST ["system_title"] );
		$page_title = db_escape ( $_POST ["page_title"] );
		$alternate_title = db_escape ( $_POST ["alternate_title"] );
		$activated = intval ( $_POST ["activated"] );
		$hidden = intval ( $_POST ["hidden"] );
		$page_content = Database::escapeValue ( $_POST ["page_content"] );
		$category = intval ( $_POST ["category"] );
		$redirection = db_escape ( $_POST ["redirection"] );
		$html_file = db_escape ( $_POST ["html_file"] );
		$menu = db_escape ( $_POST ["menu"] );
		$position = ( int ) $_POST ["position"];
		$menu_image = db_escape ( $_POST ["menu_image"] );
		$custom_data = db_escape ( $_POST ["custom_data"] );
		$theme = db_escape ( $_POST ["theme"] );
		$type = db_escape ( $_POST ["type"] );
		if ($type == "node") {
			$redirection = "#";
		}
		$cache_control = db_escape ( $_POST ["cache_control"] );
		
		if ($_POST ["parent"] == "NULL") {
			$parent = "NULL";
		} else {
			$parent = intval ( $_POST ["parent"] );
		}
		$access = implode ( ",", $_POST ["access"] );
		$access = db_escape ( $access );
		$target = db_escape ( $_POST ["target"] );
		
		// Open Graph
		$og_title = db_escape ( $_POST ["og_title"] );
		$og_description = db_escape ( $_POST ["og_description"] );
		$og_type = db_escape ( $_POST ["og_type"] );
		$og_image = db_escape ( $_POST ["og_image"] );
		
		$meta_description = Database::escapeValue ( $_POST ["meta_description"] );
		$meta_keywords = Database::escapeValue ( $_POST ["meta_keywords"] );
		
		$language = db_escape ( $_POST ["language"] );
		$module = "NULL";
		
		if (isset ( $_POST ["module"] ) and $_POST ["module"] !== "null") {
			$module = "'" . Database::escapeValue ( $_POST ["module"] ) . "'";
		}
		
		$video = "NULL";
		if (isset ( $_POST ["video"] ) and ! empty ( $_POST ["video"] )) {
			$video = intval ( $_POST ["video"] );
		}
		
		$audio = "NULL";
		if (isset ( $_POST ["audio"] ) and ! empty ( $_POST ["audio"] )) {
			$audio = intval ( $_POST ["audio"] );
		}
		
		$text_position = Database::escapeValue ( $_POST ["text_position"] );
		
		$pages_activate_own = $acl->hasPermission ( "pages_activate_own" );
		
		$image_url = "NULL";
		if (isset ( $_POST ["image_url"] ) and $_POST ["image_url"] !== "") {
			$image_url = "'" . Database::escapeValue ( $_POST ["image_url"] ) . "'";
		}
		
		$approved = 1;
		if (! $pages_activate_own and $activated == 0) {
			$approved = 0;
		}
		
		$article_author_name = Database::escapeValue ( $_POST ["article_author_name"] );
		$article_author_email = Database::escapeValue ( $_POST ["article_author_email"] );
		$article_image = Database::escapeValue ( $_POST ["article_image"] );
		$article_date = date ( 'Y-m-d H:i:s', strtotime ( $_POST ["article_date"] ) );
		$excerpt = Database::escapeValue ( $_POST ["excerpt"] );
		$comment_homepage = Database::escapeValue ( $_POST ["comment_homepage"] );
		
		$show_headline = intval ( $_POST ["show_headline"] );
		
		add_hook ( "before_create_page" );
		db_query ( "INSERT INTO " . tbname ( "content" ) . " (systemname,title,content,parent, active,created,lastmodified,autor,
  redirection,menu,position,
  access, meta_description, meta_keywords, language, target, category, `html_file`, `alternate_title`, `menu_image`, `custom_data`, `theme`,
  `og_title`, `og_description`, `og_type`, `og_image`, `type`, `module`, `video`, `audio`, `text_position`, `image_url`, `approved`, `show_headline`, `cache_control`, `article_author_name`, `article_author_email`, `article_date`, `article_image`, `excerpt`, `hidden`, `comment_homepage`)
  VALUES('$system_title','$page_title','$page_content',$parent, $activated," . time () . ", " . time () . "," . $_SESSION ["login_id"] . ", '$redirection', '$menu', $position, '" . $access . "',
  '$meta_description', '$meta_keywords',
  '$language', '$target', '$category', '$html_file', '$alternate_title',
  '$menu_image', '$custom_data', '$theme', '$og_title',
  '$og_description', '$og_type', '$og_image', '$type', $module, $video, $audio, '$text_position', $image_url, $approved, $show_headline, '$cache_control', '$article_author_name', '$article_author_email', '$article_date', '$article_image', '$excerpt', $hidden, '$comment_homepage')" ) or die ( db_error () );
		$user_id = get_user_id ();
		$content_id = db_insert_id ();
		if ($type == "list") {
			$list_language = $_POST ["list_language"];
			if (empty ( $list_language )) {
				$list_language = null;
			}
			$list_category = $_POST ["list_category"];
			if (empty ( $list_category )) {
				$list_category = null;
			}
			
			$list_menu = $_POST ["list_menu"];
			if (empty ( $list_menu )) {
				$list_menu = null;
			}
			
			$list_parent = $_POST ["list_parent"];
			if (empty ( $list_parent )) {
				$list_parent = null;
			}
			
			$list_order_by = Database::escapeValue ( $_POST ["list_order_by"] );
			$list_order_direction = Database::escapeValue ( $_POST ["list_order_direction"] );
			
			$list_use_pagination = intval ( $_POST ["list_use_pagination"] );
			
			$limit = intval ( $_POST ["limit"] );
			
			$list_type = $_POST ["list_type"];
			if (empty ( $list_type ) or $list_type == "null") {
				$list_type = null;
			}
			
			$list = new List_Data ( $content_id );
			$list->language = $list_language;
			$list->category_id = $list_category;
			$list->menu = $list_menu;
			$list->parent_id = $list_parent;
			$list->order_by = $list_order_by;
			$list->order_direction = $list_order_direction;
			$list->limit = $limit;
			$list->use_pagination = $list_use_pagination;
			$list->type = $list_type;
			$list->save ();
		}
		$content = $unescaped_content;
		VCS::createRevision ( $content_id, $content, $user_id );
		
		$fields = getFieldsForCustomType ( $type );
		foreach ( $fields as $field ) {
			if (isset ( $_POST ["cf_" . $type . "_" . $field] )) {
				$value = $_POST ["cf_" . $type . "_" . $field];
				if (empty ( $value )) {
					$value = null;
				}
				CustomFields::set ( $field, $value, $content_id );
			}
		}
		
		add_hook ( "after_create_page" );
		// header("Location: index.php?action=pages_edit&page=".db_insert_id()."#bottom");
		header ( "Location: index.php?action=pages" );
		exit ();
	}
}

if ($_POST ["add_banner"] == "add_banner" && $acl->hasPermission ( "banners" )) {
	
	$name = db_escape ( $_POST ["banner_name"] );
	$image_url = db_escape ( $_POST ["image_url"] );
	$link_url = db_escape ( $_POST ["link_url"] );
	$category = intval ( $_POST ["category"] );
	$type = db_escape ( $_POST ["type"] );
	$html = db_escape ( $_POST ["html"] );
	$language = db_escape ( $_POST ["language"] );
	
	add_hook ( "before_create_banner" );
	$query = db_query ( "INSERT INTO " . tbname ( "banner" ) . "
(name,link_url,image_url, category, `type`, html, `language`) VALUES('$name','$link_url','$image_url', '$category', '$type', '$html',
'$language')", $connection );
	
	add_hook ( "after_create_banner" );
	header ( "Location: index.php?action=banner" );
	exit ();
}

if ($_POST ["add_key"] == "add_key" and $acl->hasPermission ( "expert_settings" )) {
	
	$name = db_escape ( $_POST ["name"] );
	$value = db_escape ( $_POST ["value"] );
	add_hook ( "before_add_key" );
	$query = db_query ( "INSERT INTO " . tbname ( "settings" ) . "
(name,value) VALUES('$name','$value')", $connection );
	
	add_hook ( "after_add_key" );
	header ( "Location: index.php?action=settings" );
	exit ();
}

if ($_POST ["add_admin"] == "add_admin" && (is_admin () or $acl->hasPermission ( "users" ))) {
	$username = $_POST ["admin_username"];
	$lastname = $_POST ["admin_lastname"];
	$firstname = $_POST ["admin_firstname"];
	$password = $_POST ["admin_password"];
	$email = $_POST ["admin_email"];
	$sendMail = isset ( $_POST ["send_mail"] );
	$admin = intval ( isset ( $_POST ["admin"] ) );
	$locked = intval ( isset ( $_POST ["locked"] ) );
	$require_password_change = intval ( isset ( $_POST ["require_password_change"] ) );
	adduser ( $username, $lastname, $firstname, $email, $password, $sendMail, null, $require_password_change, $admin, $locked );
	header ( "Location: index.php?action=admins" );
	exit ();
}

if ($_POST ["edit_page"] == "edit_page" && $acl->hasPermission ( "pages" )) {
	$system_title = db_escape ( $_POST ["system_title"] );
	$page_title = db_escape ( $_POST ["page_title"] );
	$activated = intval ( $_POST ["activated"] );
	$unescaped_content = $_POST ["page_content"];
	$page_content = db_escape ( $_POST ["page_content"] );
	$category = intval ( $_POST ["category"] );
	$redirection = db_escape ( $_POST ["redirection"] );
	$menu = db_escape ( $_POST ["menu"] );
	$position = ( int ) $_POST ["position"];
	$html_file = db_escape ( $_POST ["html_file"] );
	
	$type = db_escape ( $_POST ["type"] );
	if ($type == "node") {
		$redirection = "#";
	}
	$menu_image = db_escape ( $_POST ["menu_image"] );
	$custom_data = db_escape ( $_POST ["custom_data"] );
	$theme = db_escape ( $_POST ["theme"] );
	
	$cache_control = db_escape ( $_POST ["cache_control"] );
	
	$alternate_title = db_escape ( $_POST ["alternate_title"] );
	
	$parent = "NULL";
	if ($_POST ["parent"] != "NULL") {
		$parent = intval ( $_POST ["parent"] );
	}
	// Open Graph
	$og_title = db_escape ( $_POST ["og_title"] );
	$og_description = db_escape ( $_POST ["og_description"] );
	$og_type = db_escape ( $_POST ["og_type"] );
	$og_image = db_escape ( $_POST ["og_image"] );
	
	$user = $_SESSION ["login_id"];
	$id = intval ( $_POST ["page_id"] );
	$access = implode ( ",", $_POST ["access"] );
	$access = db_escape ( $access );
	$target = db_escape ( $_POST ["target"] );
	$meta_description = db_escape ( $_POST ["meta_description"] );
	$meta_keywords = db_escape ( $_POST ["meta_keywords"] );
	$language = db_escape ( $_POST ["language"] );
	
	$module = "NULL";
	
	if (isset ( $_POST ["module"] ) and $_POST ["module"] !== "null") {
		$module = "'" . Database::escapeValue ( $_POST ["module"] ) . "'";
	}
	
	$video = "NULL";
	if (isset ( $_POST ["video"] ) and ! empty ( $_POST ["video"] )) {
		$video = intval ( $_POST ["video"] );
	}
	
	$audio = "NULL";
	if (isset ( $_POST ["audio"] ) and ! empty ( $_POST ["audio"] )) {
		$audio = intval ( $_POST ["audio"] );
	}
	
	$text_position = Database::escapeValue ( $_POST ["text_position"] );
	$actived_sql = "";
	
	$autor = intval ( $_POST ["autor"] );
	$approved_sql = "";
	
	if ($activated) {
		$approved_sql = ", approved = 1";
	}
	
	$image_url = "NULL";
	if (isset ( $_POST ["image_url"] ) and $_POST ["image_url"] !== "") {
		$image_url = "'" . Database::escapeValue ( $_POST ["image_url"] ) . "'";
	}
	
	$show_headline = intval ( $_POST ["show_headline"] );
	
	$article_author_name = Database::escapeValue ( $_POST ["article_author_name"] );
	$article_author_email = Database::escapeValue ( $_POST ["article_author_email"] );
	$article_image = Database::escapeValue ( $_POST ["article_image"] );
	$article_date = date ( 'Y-m-d H:i:s', strtotime ( $_POST ["article_date"] ) );
	$excerpt = Database::escapeValue ( $_POST ["excerpt"] );
	$only_admins_can_edit = intval ( isset ( $_POST ["only_admins_can_edit"] ) );
	$only_group_can_edit = intval ( isset ( $_POST ["only_group_can_edit"] ) );
	$only_owner_can_edit = intval ( isset ( $_POST ["only_owner_can_edit"] ) );
	$only_others_can_edit = intval ( isset ( $_POST ["only_others_can_edit"] ) );
	$hidden = intval ( $_POST ["hidden"] );
	
	$comment_homepage = Database::escapeValue ( $_POST ["comment_homepage"] );
	
	add_hook ( "before_edit_page" );
	$sql = "UPDATE " . tbname ( "content" ) . " SET `html_file` = '$html_file', systemname = '$system_title' , title='$page_title', `alternate_title`='$alternate_title', parent=$parent, content='$page_content', active=$activated, lastmodified=" . time () . ", redirection = '$redirection', menu = '$menu', position = $position, lastchangeby = $user, language='$language', access = '$access', meta_description = '$meta_description', meta_keywords = '$meta_keywords', target='$target', category='$category', menu_image='$menu_image', custom_data='$custom_data', theme='$theme',
	og_title = '$og_title', og_type ='$og_type', og_image = '$og_image', og_description='$og_description', `type` = '$type', `module` = $module, `video` = $video, `audio` = $audio, text_position = '$text_position', autor = $autor, image_url = $image_url, show_headline = $show_headline, cache_control ='$cache_control' $approved_sql,
	article_author_name='$article_author_name', article_author_email = '$article_author_email', article_image = '$article_image',  article_date = '$article_date', excerpt = '$excerpt',
	only_admins_can_edit = $only_admins_can_edit, `only_group_can_edit` = $only_group_can_edit,
	only_owner_can_edit = $only_owner_can_edit, only_others_can_edit = $only_others_can_edit, hidden = $hidden, comment_homepage = '$comment_homepage' WHERE id=$id";
	db_query ( $sql ) or die ( DB::error () );
	
	$user_id = get_user_id ();
	$content_id = $id;
	
	if ($type == "list") {
		$list_language = $_POST ["list_language"];
		if (empty ( $list_type ) or $list_type == "null") {
			$list_type = null;
		}
		$list_category = $_POST ["list_category"];
		if (empty ( $list_category )) {
			$list_category = null;
		}
		
		$list_menu = $_POST ["list_menu"];
		if (empty ( $list_menu )) {
			$list_menu = null;
		}
		
		$list_parent = $_POST ["list_parent"];
		if (empty ( $list_parent )) {
			$list_parent = null;
		}
		
		$list_order_by = Database::escapeValue ( $_POST ["list_order_by"] );
		$list_order_direction = Database::escapeValue ( $_POST ["list_order_direction"] );
		$limit = intval ( $_POST ["limit"] );
		$list_use_pagination = intval ( $_POST ["list_use_pagination"] );
		$list_type = $_POST ["list_type"];
		
		if (empty ( $list_type )) {
			$list_type = null;
		}
		
		$list = new List_Data ( $content_id );
		$list->language = $list_language;
		$list->category_id = $list_category;
		$list->menu = $list_menu;
		$list->parent_id = $list_parent;
		$list->order_by = $list_order_by;
		$list->order_direction = $list_order_direction;
		$list->limit = $limit;
		$list->use_pagination = $list_use_pagination;
		$list->type = $list_type;
		$list->save ();
	}
	
	$content = $unescaped_content;
	VCS::createRevision ( $content_id, $content, $user_id );
	
	$fields = getFieldsForCustomType ( $type );
	foreach ( $fields as $field ) {
		if (isset ( $_POST ["cf_" . $type . "_" . $field] )) {
			$value = $_POST ["cf_" . $type . "_" . $field];
			if (empty ( $value )) {
				$value = null;
			}
			CustomFields::set ( $field, $value, $content_id );
		}
	}
	
	add_hook ( "after_edit_page" );
	
	header ( "Location: index.php?action=pages" );
	exit ();
}

// Resize image
function resize_image($file, $target, $w, $h, $crop = FALSE) {
	list ( $width, $height ) = getimagesize ( $file );
	$r = $width / $height;
	if ($crop) {
		if ($width > $height) {
			$width = ceil ( $width - ($width * ($r - $w / $h)) );
		} else {
			$height = ceil ( $height - ($height * ($r - $w / $h)) );
		}
		$newwidth = $w;
		$newheight = $h;
	} else {
		if ($w / $h > $r) {
			$newwidth = $h * $r;
			$newheight = $h;
		} else {
			$newheight = $w / $r;
			$newwidth = $w;
		}
	}
	
	$src = imagecreatefromjpeg ( $file );
	
	$dst = imagecreatetruecolor ( $newwidth, $newheight );
	
	imagecopyresampled ( $dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
	
	imagejpeg ( $dst, $target, 100 );
}

// Favicon Upload
if (! empty ( $_FILES ['favicon_upload_file'] ['name'] ) and $acl->hasPermission ( "favicon" )) {
	if (! file_exists ( "../content/images" )) {
		@mkdir ( "../content/images" );
		@chmod ( "../content/images", 0777 );
	}
	
	$favicon_upload_file = $_FILES ['favicon_upload_file'];
	$type = $favicon_upload_file ['type'];
	$filename = $favicon_upload_file ['name'];
	$extension = file_extension ( $filename );
	
	if (startsWith ( $type, "image/" )) {
		
		$new_filename = "../content/images/favicon.ico";
		
		add_hook ( "before_upload_favicon" );
		
		// move_uploaded_file ( $favicon_upload_file ['tmp_name'], $new_filename );
		require_once ULICMS_ROOT . '/classes/class-php-ico.php';
		$source = $favicon_upload_file ['tmp_name'];
		$destination = $new_filename;
		
		$sizes = array (
				array (
						32,
						32 
				),
				array (
						64,
						64 
				) 
		);
		if (isset ( $_POST ["high_resolution"] )) {
			$sizes = array (
					array (
							32,
							32 
					),
					array (
							64,
							64 
					),
					array (
							128,
							128 
					) 
			);
		}
		$ico_lib = new PHP_ICO ( $source, $sizes );
		$ico_lib->save_ico ( $destination );
		
		add_hook ( "after_upload_favicon" );
		Request::redirect ( "index.php?action=favicon" );
	} else {
		$_GET ["error"] = get_translation ( "UPLOAD_WRONG_FILE_FORMAT" );
	}
}

// Logo Upload
if (! empty ( $_FILES ['logo_upload_file'] ['name'] ) and $acl->hasPermission ( "logo" )) {
	if (! file_exists ( "../content/images" )) {
		@mkdir ( "../content/images" );
		@chmod ( "../content/images", 0777 );
	}
	
	$logo_upload = $_FILES ['logo_upload_file'];
	$type = $logo_upload ['type'];
	$filename = $logo_upload ['name'];
	$extension = file_extension ( $filename );
	
	if ($type == "image/jpeg" or $type == "image/jpg" or $type == "image/gif" or $type == "image/png") {
		$hash = md5 ( file_get_contents ( $logo_upload ['tmp_name'] ) );
		$new_filename = "../content/images/" . $hash . "." . $extension;
		$logo_upload_filename = $hash . "." . $extension;
		
		add_hook ( "before_upload_logo" );
		move_uploaded_file ( $logo_upload ['tmp_name'], $new_filename );
		$image_size = getimagesize ( $new_filename );
		if ($image_size [0] <= 500 and $image_size [1] <= 100) {
			setconfig ( "logo_image", $logo_upload_filename );
			add_hook ( "after_upload_logo_successfull" );
		} else {
			header ( "Location: index.php?action=logo_upload&error=to_big" );
			add_hook ( "after_upload_logo_failed" );
			exit ();
		}
	}
	
	add_hook ( "after_upload_logo" );
}

if (($_POST ["edit_admin"] == "edit_admin" && $acl->hasPermission ( "users" )) or ($_POST ["edit_admin"] == "edit_admin" and logged_in () and $_POST ["id"] == $_SESSION ["login_id"])) {
	
	$id = intval ( $_POST ["id"] );
	$username = db_escape ( $_POST ["admin_username"] );
	$lastname = db_escape ( $_POST ["admin_lastname"] );
	$firstname = db_escape ( $_POST ["admin_firstname"] );
	$email = db_escape ( $_POST ["admin_email"] );
	$password = $_POST ["admin_password"];
	// User mit eingeschränkten Rechten darf sich nicht selbst zum Admin machen können
	if ($acl->hasPermission ( "users" )) {
		$admin = intval ( isset ( $_POST ["admin"] ) );
		if (isset ( $_POST ["group_id"] )) {
			$group_id = $_POST ["group_id"];
			if ($group_id == "-") {
				$group_id = "NULL";
			} else {
				$group_id = intval ( $group_id );
			}
		} else {
			$group_id = $_SESSION ["group_id"];
		}
	} else {
		$user = getUserById ( $id );
		$admin = $user ["admin"];
		$group_id = $user ["group_id"];
		if (is_null ( $group_id )) {
			$group_id = "NULL";
		}
	}
	
	$notify_on_login = intval ( isset ( $_POST ["notify_on_login"] ) );
	
	$twitter = db_escape ( $_POST ["twitter"] );
	$homepage = db_escape ( $_POST ["homepage"] );
	$skype_id = db_escape ( $_POST ["skype_id"] );
	$about_me = db_escape ( $_POST ["about_me"] );
	$html_editor = db_escape ( $_POST ["html_editor"] );
	$require_password_change = intval ( isset ( $_POST ["require_password_change"] ) );
	$locked = intval ( isset ( $_POST ["locked"] ) );
	
	add_hook ( "before_edit_user" );
	$sql = "UPDATE " . tbname ( "users" ) . " SET username = '$username', `group_id` = " . $group_id . ", `admin` = $admin, firstname='$firstname',
lastname='$lastname', notify_on_login='$notify_on_login', email='$email', skype_id = '$skype_id',
about_me = '$about_me', html_editor='$html_editor', require_password_change='$require_password_change', `locked`='$locked', `twitter` = '$twitter', `homepage` = '$homepage'  WHERE id=$id";
	
	db_query ( $sql );
	
	if (! empty ( $password )) {
		changePassword ( $password, $id );
	}
	
	add_hook ( "after_edit_user" );
	;
	if (! $acl->hasPermission ( "users" )) {
		header ( "Location: index.php" );
		exit ();
	} else {
		header ( "Location: index.php?action=admins" );
		exit ();
	}
}

if ($_POST ["edit_banner"] == "edit_banner" && $acl->hasPermission ( "banners" )) {
	$name = db_escape ( $_POST ["banner_name"] );
	$image_url = db_escape ( $_POST ["image_url"] );
	$link_url = db_escape ( $_POST ["link_url"] );
	$category = intval ( $_POST ["category"] );
	$id = intval ( $_POST ["id"] );
	
	$type = db_escape ( $_POST ["type"] );
	$html = db_escape ( $_POST ["html"] );
	$language = db_escape ( $_POST ["language"] );
	add_hook ( "before_edit_banner" );
	$query = db_query ( "UPDATE " . tbname ( "banner" ) . "
SET name='$name', link_url='$link_url', image_url='$image_url', category='$category', type='$type', html='$html', language='$language' WHERE id=$id" );
	
	add_hook ( "after_edit_banner" );
	header ( "Location: index.php?action=banner" );
	exit ();
}

if ($_POST ["edit_key"] == "edit_key" && $acl->hasPermission ( "expert_settings" )) {
	$name = db_escape ( $_POST ["name"] );
	$value = db_escape ( $_POST ["value"] );
	$id = intval ( $_POST ["id"] );
	add_hook ( "before_edit_key" );
	$query = db_query ( "UPDATE " . tbname ( "settings" ) . "
SET name='$name',value='$value' WHERE id=$id" );
	
	add_hook ( "after_edit_key" );
	
	header ( "Location: index.php?action=settings" );
	exit ();
}
