<?php
class PageController extends Controller {
	// @FIXME: Content-Model statt SQL verwenden
	public function createPost() {
		if ($_POST ["system_title"] != "") {
			$system_title = db_escape ( $_POST ["system_title"] );
			$page_title = db_escape ( $_POST ["page_title"] );
			$alternate_title = db_escape ( $_POST ["alternate_title"] );
			$activated = intval ( $_POST ["activated"] );
			$hidden = intval ( $_POST ["hidden"] );
			$page_content = $_POST ["page_content"];
			$group = new Group ();
			$group->getCurrentGroup ();
			if (Stringhelper::isNotNullOrWhitespace ( $group->getAllowableTags () )) {
				$page_content = strip_tags ( $page_content, $group->getAllowableTags () );
			}
			$page_content = Database::escapeValue ( $page_content );
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
			$article_date = StringHelper::isNotNullOrEmpty ( $_POST ["article_date"] ) ? "'" . date ( 'Y-m-d H:i:s', strtotime ( $_POST ["article_date"] ) ) . "'" : "Null";
			$excerpt = Database::escapeValue ( $_POST ["excerpt"] );
			$only_admins_can_edit = intval ( Settings::get ( "only_admins_can_edit" ) );
			$only_group_can_edit = intval ( Settings::get ( "only_group_can_edit" ) );
			$only_owner_can_edit = intval ( Settings::get ( "only_owner_can_edit" ) );
			$only_others_can_edit = intval ( Settings::get ( "only_others_can_edit" ) );
			
			$comment_homepage = Database::escapeValue ( $_POST ["comment_homepage"] );
			$link_to_language = StringHelper::isNotNullOrWhitespace ( Request::getVar ( "link_to_language" ) ) ? intval ( Request::getVar ( "link_to_language" ) ) : "NULL";
			$show_headline = intval ( $_POST ["show_headline"] );
			
			add_hook ( "before_create_page" );
			db_query ( "INSERT INTO " . tbname ( "content" ) . " (systemname,title,content,parent, active,created,lastmodified,autor,
  redirection,menu,position,
  access, meta_description, meta_keywords, language, target, category, `html_file`, `alternate_title`, `menu_image`, `custom_data`, `theme`,
  `og_title`, `og_description`, `og_type`, `og_image`, `type`, `module`, `video`, `audio`, `text_position`, `image_url`, `approved`, `show_headline`, `cache_control`, `article_author_name`, `article_author_email`,
				`article_date`, `article_image`, `excerpt`, `hidden`,
				`only_admins_can_edit`, `only_group_can_edit`, `only_owner_can_edit`, `only_others_can_edit`,
				`comment_homepage`, `link_to_language` )
					
  VALUES('$system_title','$page_title','$page_content',$parent, $activated," . time () . ", " . time () . "," . $_SESSION ["login_id"] . ", '$redirection', '$menu', $position, '" . $access . "',
  '$meta_description', '$meta_keywords',
  '$language', '$target', '$category', '$html_file', '$alternate_title',
  '$menu_image', '$custom_data', '$theme', '$og_title',
  '$og_description', '$og_type', '$og_image',
  '$type', $module, $video, $audio, '$text_position',
   $image_url, $approved, $show_headline, '$cache_control',
   '$article_author_name', '$article_author_email',
   $article_date, '$article_image', '$excerpt',
   $hidden, $only_admins_can_edit, $only_group_can_edit, $only_owner_can_edit, $only_others_can_edit,
				'$comment_homepage', $link_to_language)" ) or die ( db_error () );
			
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
			
			if ($acl->hasPermission ( "pages_edit_own" ) and $content_id) {
				Request::redirect ( ModuleHelper::buildActionURL ( "pages_edit", "page=$content_id" ) );
			}
			Request::redirect ( ModuleHelper::buildActionURL ( "pages" ) );
		}
	}
	public function editPost() {
		// @FIXME: Berechtigungen pages_edit_own und pages_edit_others prüfen.
		$system_title = db_escape ( $_POST ["system_title"] );
		$page_title = db_escape ( $_POST ["page_title"] );
		$activated = intval ( $_POST ["activated"] );
		$unescaped_content = $_POST ["page_content"];
		$page_content = $_POST ["page_content"];
		$group = new Group ();
		$group->getCurrentGroup ();
		if (Stringhelper::isNotNullOrWhitespace ( $group->getAllowableTags () )) {
			$page_content = strip_tags ( $page_content, $group->getAllowableTags () );
		}
		$page_content = Database::escapeValue ( $page_content );
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
		
		$article_date = StringHelper::isNotNullOrEmpty ( $_POST ["article_date"] ) ? "'" . date ( 'Y-m-d H:i:s', strtotime ( $_POST ["article_date"] ) ) . "'" : "Null";
		$excerpt = Database::escapeValue ( $_POST ["excerpt"] );
		$only_admins_can_edit = intval ( isset ( $_POST ["only_admins_can_edit"] ) );
		$only_group_can_edit = intval ( isset ( $_POST ["only_group_can_edit"] ) );
		$only_owner_can_edit = intval ( isset ( $_POST ["only_owner_can_edit"] ) );
		$only_others_can_edit = intval ( isset ( $_POST ["only_others_can_edit"] ) );
		$hidden = intval ( $_POST ["hidden"] );
		
		$comment_homepage = Database::escapeValue ( $_POST ["comment_homepage"] );
		$link_to_language = StringHelper::isNotNullOrWhitespace ( Request::getVar ( "link_to_language" ) ) ? intval ( Request::getVar ( "link_to_language" ) ) : "NULL";
		
		add_hook ( "before_edit_page" );
		$sql = "UPDATE " . tbname ( "content" ) . " SET `html_file` = '$html_file', systemname = '$system_title' , title='$page_title', `alternate_title`='$alternate_title', parent=$parent, content='$page_content', active=$activated, lastmodified=" . time () . ", redirection = '$redirection', menu = '$menu', position = $position, lastchangeby = $user, language='$language', access = '$access', meta_description = '$meta_description', meta_keywords = '$meta_keywords', target='$target', category='$category', menu_image='$menu_image', custom_data='$custom_data', theme='$theme',
	og_title = '$og_title', og_type ='$og_type', og_image = '$og_image', og_description='$og_description', `type` = '$type', `module` = $module, `video` = $video, `audio` = $audio, text_position = '$text_position', autor = $autor, image_url = $image_url, show_headline = $show_headline, cache_control ='$cache_control' $approved_sql,
	article_author_name='$article_author_name', article_author_email = '$article_author_email', article_image = '$article_image',  article_date = $article_date, excerpt = '$excerpt',
	only_admins_can_edit = $only_admins_can_edit, `only_group_can_edit` = $only_group_can_edit,
	only_owner_can_edit = $only_owner_can_edit, only_others_can_edit = $only_others_can_edit,
	hidden = $hidden, comment_homepage = '$comment_homepage',
	link_to_language = $link_to_language WHERE id=$id";
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
		
		Request::redirect ( ModuleHelper::buildActionURL ( "pages" ) );
	}
	public function undeletePost() {
		$page = intval ( $_GET ["page"] );
		add_hook ( "before_undelete_page" );
		db_query ( "UPDATE " . tbname ( "content" ) . " SET `deleted_at` = NULL" . " WHERE id=$page" );
		add_hook ( "after_undelete_page" );
		Request::redirect ( ModuleHelper::buildActionURL ( "pages" ) );
	}
}