<?php

use UliCMS\CoreContent\Models\ViewModels\DiffViewModel;

class PageController extends Controller {

    // @FIXME: Content-Model statt SQL verwenden
    public function createPost() {
        $acl = new ACL();
        if ($_POST["systemname"] != "") {
            $systemname = db_escape($_POST["systemname"]);
            $page_title = db_escape($_POST["page_title"]);
            $alternate_title = db_escape($_POST["alternate_title"]);
            $activated = intval($_POST["activated"]);
            $hidden = intval($_POST["hidden"]);
            $page_content = $_POST["page_content"];
            $group = Group::getCurrentGroup();
            if (Stringhelper::isNotNullOrWhitespace($group->getAllowableTags())) {
                $page_content = strip_tags($page_content, $group->getAllowableTags());
            }
            $page_content = Database::escapeValue($page_content);
            $category = intval($_POST["category"]);
            $redirection = db_escape($_POST["redirection"]);
            $menu = db_escape($_POST["menu"]);
            $position = (int) $_POST["position"];
            $menu_image = db_escape($_POST["menu_image"]);
            $custom_data = db_escape($_POST["custom_data"]);
            $theme = db_escape($_POST["theme"]);
            $type = db_escape($_POST["type"]);
            if ($type == "node") {
                $redirection = "#";
            }
            $cache_control = db_escape($_POST["cache_control"]);

            if ($_POST["parent"] == "NULL") {
                $parent = "NULL";
            } else {
                $parent = intval($_POST["parent"]);
            }
            $access = implode(",", $_POST["access"]);
            $access = db_escape($access);
            $target = db_escape($_POST["target"]);

            // Open Graph
            $og_title = db_escape($_POST["og_title"]);
            $og_description = db_escape($_POST["og_description"]);
            $og_type = db_escape($_POST["og_type"]);
            $og_image = db_escape($_POST["og_image"]);

            $meta_description = Database::escapeValue($_POST["meta_description"]);
            $meta_keywords = Database::escapeValue($_POST["meta_keywords"]);

            $language = db_escape($_POST["language"]);
            $module = "NULL";

            if (isset($_POST["module"]) and $_POST["module"] !== "null") {
                $module = "'" . Database::escapeValue($_POST["module"]) . "'";
            }

            $video = "NULL";
            if (isset($_POST["video"]) and ! empty($_POST["video"])) {
                $video = intval($_POST["video"]);
            }

            $audio = "NULL";
            if (isset($_POST["audio"]) and ! empty($_POST["audio"])) {
                $audio = intval($_POST["audio"]);
            }

            $text_position = Database::escapeValue($_POST["text_position"]);

            $pages_activate_own = $acl->hasPermission("pages_activate_own");

            $image_url = "NULL";
            if (isset($_POST["image_url"]) and $_POST["image_url"] !== "") {
                $image_url = "'" . Database::escapeValue($_POST["image_url"]) . "'";
            }

            $approved = 1;
            if (!$pages_activate_own and $activated == 0) {
                $approved = 0;
            }

            $article_author_name = Database::escapeValue($_POST["article_author_name"]);
            $article_author_email = Database::escapeValue($_POST["article_author_email"]);
            $article_image = Database::escapeValue($_POST["article_image"]);
            $article_date = StringHelper::isNotNullOrEmpty($_POST["article_date"]) ? "'" . date('Y-m-d H:i:s', strtotime($_POST["article_date"])) . "'" : "Null";
            $excerpt = Database::escapeValue($_POST["excerpt"]);
            $only_admins_can_edit = intval(Settings::get("only_admins_can_edit"));
            $only_group_can_edit = intval(Settings::get("only_group_can_edit"));
            $only_owner_can_edit = intval(Settings::get("only_owner_can_edit"));
            $only_others_can_edit = intval(Settings::get("only_others_can_edit"));

            $comment_homepage = Database::escapeValue($_POST["comment_homepage"]);
            $link_to_language = StringHelper::isNotNullOrWhitespace(Request::getVar("link_to_language")) ? intval(Request::getVar("link_to_language")) : "NULL";

            $comments_enabled = $_POST["comments_enabled"] !== "null" ? intval($_POST["comments_enabled"]) : null;
            $comments_enabled = Database::escapeValue($comments_enabled);

            $show_headline = intval($_POST["show_headline"]);

            do_event("before_create_page");
            db_query("INSERT INTO " . tbname("content") . " (systemname, title, content, parent, active, created, lastmodified, autor, `group_id`,
  redirection,menu,position,
  access, meta_description, meta_keywords, language, target, category, `alternate_title`, `menu_image`, `custom_data`, `theme`,
  `og_title`, `og_description`, `og_type`, `og_image`, `type`, `module`, `video`, `audio`, `text_position`, `image_url`, `approved`, `show_headline`, `cache_control`, `article_author_name`, `article_author_email`,
				`article_date`, `article_image`, `excerpt`, `hidden`,
				`only_admins_can_edit`, `only_group_can_edit`, `only_owner_can_edit`, `only_others_can_edit`,
				`comment_homepage`, `link_to_language`, `comments_enabled` )

  VALUES('$systemname','$page_title','$page_content',$parent, $activated," . time() . ", " . time() . "," . $_SESSION["login_id"] . "," . $_SESSION["group_id"] . ", '$redirection', '$menu', $position, '" . $access . "',
  '$meta_description', '$meta_keywords',
  '$language', '$target', '$category', '$alternate_title',
  '$menu_image', '$custom_data', '$theme', '$og_title',
  '$og_description', '$og_type', '$og_image',
  '$type', $module, $video, $audio, '$text_position',
   $image_url, $approved, $show_headline, '$cache_control',
   '$article_author_name', '$article_author_email',
   $article_date, '$article_image', '$excerpt',
   $hidden, $only_admins_can_edit, $only_group_can_edit, $only_owner_can_edit, $only_others_can_edit,
				'$comment_homepage', $link_to_language, $comments_enabled)") or die(db_error());

            $user_id = get_user_id();
            $content_id = db_insert_id();

            if ($type == "list") {
                $list_language = $_POST["list_language"];
                if (empty($list_language)) {
                    $list_language = null;
                }
                $list_category = $_POST["list_category"];
                if (empty($list_category)) {
                    $list_category = null;
                }

                $list_menu = $_POST["list_menu"];
                if (empty($list_menu)) {
                    $list_menu = null;
                }

                $list_parent = $_POST["list_parent"];
                if (empty($list_parent)) {
                    $list_parent = null;
                }

                $list_order_by = Database::escapeValue($_POST["list_order_by"]);
                $list_order_direction = Database::escapeValue($_POST["list_order_direction"]);

                $list_use_pagination = intval($_POST["list_use_pagination"]);

                $limit = intval($_POST["limit"]);

                $list_type = $_POST["list_type"];
                if (empty($list_type) or $list_type == "null") {
                    $list_type = null;
                }

                $list = new List_Data($content_id);
                $list->language = $list_language;
                $list->category_id = $list_category;
                $list->menu = $list_menu;
                $list->parent_id = $list_parent;
                $list->order_by = $list_order_by;
                $list->order_direction = $list_order_direction;
                $list->limit = $limit;
                $list->use_pagination = $list_use_pagination;
                $list->type = $list_type;
                $list->save();
            }
            $content = $unescaped_content;
            VCS::createRevision($content_id, $content, $user_id);

            $type = DefaultContentTypes::get($type);
            foreach ($type->customFields as $field) {
                $field->name = "{$_POST['type']}_{$field->name}";
                $value = null;
                if (isset($_POST[$field->name])) {
                    $value = $_POST[$field->name];
                }

                CustomFields::set($field->name, $value, $content_id, false);
            }

            do_event("after_create_page");
            // header("Location: index.php?action=pages_edit&page=".db_insert_id()."#bottom");

            if ($acl->hasPermission("pages_edit_own") and $content_id) {
                Request::redirect(ModuleHelper::buildActionURL("pages_edit", "page=$content_id"));
            }
            Request::redirect(ModuleHelper::buildActionURL("pages"));
        }
    }

    public function editPost() {
        $acl = new ACL();
        // @FIXME: Berechtigungen pages_edit_own und pages_edit_others prüfen.
        $systemname = db_escape($_POST["systemname"]);
        $page_title = db_escape($_POST["page_title"]);
        $activated = intval($_POST["activated"]);
        $unescaped_content = $_POST["page_content"];
        $page_content = $_POST["page_content"];
        $group = Group::getCurrentGroup();
        if (Stringhelper::isNotNullOrWhitespace($group->getAllowableTags())) {
            $page_content = strip_tags($page_content, $group->getAllowableTags());
        }
        $page_content = Database::escapeValue($page_content);
        $category = intval($_POST["category"]);
        $redirection = db_escape($_POST["redirection"]);
        $menu = db_escape($_POST["menu"]);
        $position = (int) $_POST["position"];

        $type = db_escape($_POST["type"]);
        if ($type == "node") {
            $redirection = "#";
        }
        $menu_image = db_escape($_POST["menu_image"]);
        $custom_data = db_escape($_POST["custom_data"]);
        $theme = db_escape($_POST["theme"]);

        $cache_control = db_escape($_POST["cache_control"]);

        $alternate_title = db_escape($_POST["alternate_title"]);

        $parent = "NULL";
        if ($_POST["parent"] != "NULL") {
            $parent = intval($_POST["parent"]);
        }
        // Open Graph
        $og_title = db_escape($_POST["og_title"]);
        $og_description = db_escape($_POST["og_description"]);
        $og_type = db_escape($_POST["og_type"]);
        $og_image = db_escape($_POST["og_image"]);

        $user = $_SESSION["login_id"];
        $id = intval($_POST["page_id"]);
        $access = implode(",", $_POST["access"]);
        $access = db_escape($access);
        $target = db_escape($_POST["target"]);
        $meta_description = db_escape($_POST["meta_description"]);
        $meta_keywords = db_escape($_POST["meta_keywords"]);
        $language = db_escape($_POST["language"]);

        $module = "NULL";

        if (isset($_POST["module"]) and $_POST["module"] !== "null") {
            $module = "'" . Database::escapeValue($_POST["module"]) . "'";
        }

        $video = "NULL";
        if (isset($_POST["video"]) and ! empty($_POST["video"])) {
            $video = intval($_POST["video"]);
        }

        $audio = "NULL";
        if (isset($_POST["audio"]) and ! empty($_POST["audio"])) {
            $audio = intval($_POST["audio"]);
        }

        $text_position = Database::escapeValue($_POST["text_position"]);
        $actived_sql = "";

        $autor = intval($_POST["autor"]);
        $group_id = intval($_POST["group_id"]);
        $approved_sql = "";

        if ($activated) {
            $approved_sql = ", approved = 1";
        }

        $image_url = "NULL";
        if (isset($_POST["image_url"]) and $_POST["image_url"] !== "") {
            $image_url = "'" . Database::escapeValue($_POST["image_url"]) . "'";
        }

        $show_headline = intval($_POST["show_headline"]);

        $article_author_name = Database::escapeValue($_POST["article_author_name"]);
        $article_author_email = Database::escapeValue($_POST["article_author_email"]);
        $article_image = Database::escapeValue($_POST["article_image"]);

        $article_date = StringHelper::isNotNullOrEmpty($_POST["article_date"]) ? "'" . date('Y-m-d H:i:s', strtotime($_POST["article_date"])) . "'" : "Null";
        $excerpt = Database::escapeValue($_POST["excerpt"]);
        $only_admins_can_edit = intval(isset($_POST["only_admins_can_edit"]));
        $only_group_can_edit = intval(isset($_POST["only_group_can_edit"]));
        $only_owner_can_edit = intval(isset($_POST["only_owner_can_edit"]));
        $only_others_can_edit = intval(isset($_POST["only_others_can_edit"]));
        $hidden = intval($_POST["hidden"]);

        $comment_homepage = Database::escapeValue($_POST["comment_homepage"]);
        $link_to_language = StringHelper::isNotNullOrWhitespace(Request::getVar("link_to_language")) ? intval(Request::getVar("link_to_language")) : "NULL";

        $comments_enabled = $_POST["comments_enabled"] !== "null" ? intval($_POST["comments_enabled"]) : null;
        $comments_enabled = Database::escapeValue($comments_enabled);

        do_event("before_edit_page");
        $sql = "UPDATE " . tbname("content") . " SET systemname = '$systemname' , title='$page_title', `alternate_title`='$alternate_title', parent=$parent, content='$page_content', active=$activated, lastmodified=" . time() . ", redirection = '$redirection', menu = '$menu', position = $position, lastchangeby = $user, language='$language', access = '$access', meta_description = '$meta_description', meta_keywords = '$meta_keywords', target='$target', category='$category', menu_image='$menu_image', custom_data='$custom_data', theme='$theme',
	og_title = '$og_title', og_type ='$og_type', og_image = '$og_image', og_description='$og_description', `type` = '$type', `module` = $module, `video` = $video, `audio` = $audio, text_position = '$text_position', autor = $autor, `group_id` = $group_id, image_url = $image_url, show_headline = $show_headline, cache_control ='$cache_control' $approved_sql,
	article_author_name='$article_author_name', article_author_email = '$article_author_email', article_image = '$article_image',  article_date = $article_date, excerpt = '$excerpt',
	only_admins_can_edit = $only_admins_can_edit, `only_group_can_edit` = $only_group_can_edit,
	only_owner_can_edit = $only_owner_can_edit, only_others_can_edit = $only_others_can_edit,
	hidden = $hidden, comment_homepage = '$comment_homepage',
	link_to_language = $link_to_language, comments_enabled = $comments_enabled WHERE id=$id";
        db_query($sql);

        $user_id = get_user_id();
        $content_id = $id;

        if ($type == "list") {
            $list_language = $_POST["list_language"];
            if (empty($list_type) or $list_type == "null") {
                $list_type = null;
            }
            $list_category = $_POST["list_category"];
            if (empty($list_category)) {
                $list_category = null;
            }

            $list_menu = $_POST["list_menu"];
            if (empty($list_menu)) {
                $list_menu = null;
            }

            $list_parent = $_POST["list_parent"];
            if (empty($list_parent)) {
                $list_parent = null;
            }

            $list_order_by = Database::escapeValue($_POST["list_order_by"]);
            $list_order_direction = Database::escapeValue($_POST["list_order_direction"]);
            $limit = intval($_POST["limit"]);
            $list_use_pagination = intval($_POST["list_use_pagination"]);
            $list_type = $_POST["list_type"];

            if (empty($list_type)) {
                $list_type = null;
            }

            $list = new List_Data($content_id);
            $list->language = $list_language;
            $list->category_id = $list_category;
            $list->menu = $list_menu;
            $list->parent_id = $list_parent;
            $list->order_by = $list_order_by;
            $list->order_direction = $list_order_direction;
            $list->limit = $limit;
            $list->use_pagination = $list_use_pagination;
            $list->type = $list_type;
            $list->save();
        }

        $content = $unescaped_content;
        VCS::createRevision($content_id, $content, $user_id);

        $type = DefaultContentTypes::get($type);
        foreach ($type->customFields as $field) {
            $field->name = "{$_POST['type']}_{$field->name}";
            $value = null;
            if (isset($_POST[$field->name])) {
                $value = $_POST[$field->name];
            }

            CustomFields::set($field->name, $value, $content_id, false);
        }

        do_event("after_edit_page");

        // if called by ajax return no content to improve performance
        if (Request::isAjaxRequest()) {
            HTTPStatusCodeResult(HttpStatusCode::OK);
        }

        Response::redirect(ModuleHelper::buildActionURL("pages"));
    }

    public function undeletePost() {
        $page = Request::getVar("page");
        do_event("before_undelete_page");
        $content = ContentFactory::getByID($page);
        if ($content->id === null) {
            ExceptionResult(get_translation("not_found"));
        }
        $content->undelete();
        do_event("after_undelete_page");
        Response::sendHttpStatusCodeResultIfAjax(HTTPStatusCode::OK, ModuleHelper::buildActionURL("pages"));
    }

    public function deletePost() {
        $page = Request::getVar("page");
        do_event("before_delete_page");
        $content = ContentFactory::getByID($page);
        if ($content->id === null) {
            ExceptionResult(get_translation("not_found"));
        }
        $content->delete();

        do_event("after_delete_page");
        Response::sendHttpStatusCodeResultIfAjax(HTTPStatusCode::OK, ModuleHelper::buildActionURL("pages"));
    }

    public function emptyTrash() {
        do_event("before_empty_trash");
        Content::emptyTrash();
        do_event("after_empty_trash");
        Request::redirect(ModuleHelper::buildActionURL("pages"));
    }

    public function resetFilters() {
        // reset all filters
        foreach ($_SESSION as $key => $value) {
            if (startsWith($key, "filter_")) {
                unset($_SESSION[$key]);
            }
        }

        Request::redirect(ModuleHelper::buildActionURL("pages"));
    }

    public function getContentTypes() {
        $json = json_encode(DefaultContentTypes::getAll(), JSON_UNESCAPED_SLASHES);

        RawJSONResult($json);
    }

    public function diffContents($history_id = null, $content_id = null) {
        $history_id = !$history_id ? $_GET ["history_id"] : $history_id;
        $content_id = !$content_id ? $_GET ["content_id"] : $content_id;

        $current_version = getPageByID($content_id);
        $old_version = VCS::getRevisionByID($history_id);

        $from_text = $current_version->content;
        $to_text = $old_version->content;

        $current_version_date = date("Y-m-d H:i:s", $current_version->lastmodified);
        $old_version_date = $old_version->date;

        $from_text = mb_convert_encoding($from_text, 'HTML-ENTITIES', 'UTF-8');
        $to_text = mb_convert_encoding($to_text, 'HTML-ENTITIES', 'UTF-8');
        $opcodes = FineDiff::getDiffOpcodes($from_text, $to_text, FineDiff::$wordGranularity);

        $html = FineDiff::renderDiffToHTMLFromOpcodes($from_text, $opcodes);

        return new DiffViewModel($html, $current_version_date, $old_version_date, $content_id, $history_id);
    }

    public function toggleFilters() {
        $settingsName = "user/" . get_user_id() . "/show_filters";
        if (Settings::get($settingsName)) {
            Settings::delete($settingsName);
        } else {
            Settings::set($settingsName, "1");
        }
        HTTPStatusCodeResult(HttpStatusCode::OK);
    }

    public function toggleShowPositions() {
        $settingsName = "user/" . get_user_id() . "/show_positions";
        if (Settings::get($settingsName)) {
            Settings::delete($settingsName);
        } else {
            Settings::set($settingsName, "1");
        }
        HTTPStatusCodeResult(HttpStatusCode::OK);
    }

}
