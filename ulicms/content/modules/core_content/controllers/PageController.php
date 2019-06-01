<?php

use UliCMS\CoreContent\Models\ViewModels\DiffViewModel;
use UliCMS\Models\Content\VCS;
use UliCMS\Models\Content\Types\DefaultContentTypes;
use Rakit\Validation\Validator;
use UliCMS\Security\PermissionChecker;
use UliCMS\Models\Content\TypeMapper;
use UliCMS\Constants\LinkTarget;

class PageController extends Controller {

// @FIXME: Content-Model statt SQL verwenden
    public function createPost() {

        $this->validateInput();

        $permissionChecker = new PermissionChecker(get_user_id());

        $model = TypeMapper::getModel(Request::getVar("type"));
        $model->slug = Request::getVar(
                        "model",
                        StringHelper::cleanString(
                                Request::getVar("title")
                        )
        );
        $model->title = Request::getVar("title");
        $model->alternate_title = Request::getVar("alternate_title");
        $model->active = Request::getVar("active", true, "bool");
        $model->hidden = Request::getVar("hidden", false, "bool");
        $model->content = Request::getVar("content");

        $group = Group::getCurrentGroup();
        if (Stringhelper::isNotNullOrWhitespace($group->getAllowableTags())) {
            $model->content = strip_tags($model->content, $group->getAllowableTags());
        }
        $model->category_id = Request::getVar("category_id", 1, "int");
        $model->redirection = Request::getVar("redirection", NULL, "str");
        $model->menu = Request::getVar("menu", "not_in_menu", "str");
        $model->position = Request::getVar("position", 0, "int");

        $model->menu_image = Request::getVar("menu_image", NULL, "str");
        $model->custom_data = json_decode(
                Request::getVar("custom_data", "{}", "str"),
                false
        );
        $model->theme = Request::getVar("theme", NULL, "str");

        if ($model instanceof Node) {
            $model->redirection = "#";
        }

        $model->cache_control = Request::getVar("cache_control", "auto", "str");

        $parent_id = Request::getVar("parent_id", null, "str");
        $model->parent_id = $parent_id and $parent_id !== "NULL" ? intval($parent_id) : null;
        if (Request::getVar("access")) {
            $model->access = implode(",", Request::getVar("access"));
        }

        $model->target = Request::getVar("target", LinkTarget::TARGET_SELF, "str");

// Open Graph
        $model->og_title = Request::getVar("og_title");
        $model->og_description = Request::getVar("og_description");
        $model->og_type = Request::getVar("og_type");
        $model->og_image = Request::getVar("og_image");

        $model->meta_description = Request::getVar("meta_description");
        $model->meta_keywords = Request::getVar("meta_keywords");
        $model->language = Request::getVar("language");

        if ($model instanceof Module_Page) {
            $model->module = Request::getVar("module", null, "str");
        }

        if ($model instanceof Video_Page) {
            $model->video = Request::getVar("video", null, "str");
        }
        if ($model instanceof Audio_Page) {
            $model->audio = Request::getVar("audio", null, "str");
        }

        $model->text_position = Request::getVar("text_position", "before", "str");

        $pages_activate_own = $permissionChecker->hasPermission("pages_activate_own");

        if ($model instanceof Image_Page) {
            $model->image_url = Request::getVar("image_url", null, "str");
        }

        $approved = 1;
        if (!$pages_activate_own and $model->active == 0) {
            $approved = 0;
        }

        $model->approved = !$pages_activate_own and $model->active == 0;

        if ($model instanceof Article) {
            $model->article_author_name = Request::getVar("article_author_name");
            $model->article_author_email = Request::getVar("article_author_email");
            $model->article_image = Request::getVar("article_image");
            $model->article_date = Request::getVar("article_date") ? strtotime(
                            Request::getVar("article_image")
                    ) : null;
            $model->excerpt = Request::getVar("excerpt");
        }


        $permissionObjects = array("admins", "group", "owner", "other");
        foreach ($permissionObjects as $object) {
            $model->getPermissions()->setEditRestriction($object,
                    Request::getVar("only_{$object}_can_edit"), false, "bool");
        }

        $model->comment_homepage = Request::getVar("comment_homepage");
        $model->link_to_language = Request::getVar("link_to_language", null, "int");

        $model->comments_enabled = Request::getVar("commens_enabled") !== "null" ? Request::getVar("comments_enabled", false, "bool") : null;

        $model->show_headline = Request::getVar("show_headline", 1, "bool");

        $model->author_id = get_user_id();
        $model->group_id = get_group_id();

        do_event("before_create_page");

        $model->save();

        $user_id = get_user_id();
        $content_id = $model->getId();

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

        if ($permissionChecker->hasPermission("pages_edit_own") and $content_id) {
            Request::redirect(ModuleHelper::buildActionURL("pages_edit", "page=$content_id"));
        }
        Request::redirect(ModuleHelper::buildActionURL("pages"));
    }

    public function editPost() {

        $this->validateInput();

        $permissionChecker = new PermissionChecker();
// @FIXME: Berechtigungen pages_edit_own und pages_edit_others prüfen.
        $slug = db_escape($_POST["slug"]);
        $page_title = db_escape($_POST["title"]);
        $active = intval($_POST["active"]);
        $unescaped_content = $_POST["content"];
        $content = $_POST["content"];
        $group = Group::getCurrentGroup();
        if (Stringhelper::isNotNullOrWhitespace($group->getAllowableTags())) {
            $content = strip_tags($content, $group->getAllowableTags());
        }
        $content = Database::escapeValue($content);
        $category_id = intval($_POST["category_id"]);
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

        $parent_id = "NULL";
        if ($_POST["parent_id"] != "NULL") {
            $parent_id = intval($_POST["parent_id"]);
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

        $author_id = intval($_POST["author_id"]);
        $group_id = intval($_POST["group_id"]);
        $approved_sql = "";

        if ($active) {
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
        $sql = "UPDATE " . tbname("content") . " SET slug = '$slug' , title='$page_title', `alternate_title`='$alternate_title', parent_id=$parent_id, content='$content', active=$active, lastmodified=" . time() . ", redirection = '$redirection', menu = '$menu', position = $position, lastchangeby = $user, language='$language', access = '$access', meta_description = '$meta_description', meta_keywords = '$meta_keywords', target='$target', category_id = $category_id, menu_image='$menu_image', custom_data='$custom_data', theme='$theme',
	og_title = '$og_title', og_type ='$og_type', og_image = '$og_image', og_description='$og_description', `type` = '$type', `module` = $module, `video` = $video, `audio` = $audio, text_position = '$text_position', author_id = $author_id, `group_id` = $group_id, image_url = $image_url, show_headline = $show_headline, cache_control ='$cache_control' $approved_sql,
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

    public function checkSlugFree() {
        if ($this->checkIfSlugIsFree($_REQUEST["slug"], $_REQUEST["language"], intval($_REQUEST["id"]))) {
            TextResult("yes");
        }
        TextResult("");
    }

    private function checkIfSlugIsFree($slug, $language, $id) {
        if (StringHelper::isNullOrWhitespace($slug)) {
            return true;
        }
        $slug = Database::escapeValue($slug);
        $language = Database::escapeValue($language);
        $id = intval($id);
        $sql = "SELECT id FROM " . tbname("content") . " where slug='$slug' and language = '$language' ";
        if ($id > 0) {
            $sql .= "and id <> $id";
        }
        $result = Database::query($sql);
        return (Database::getNumRows($result) <= 0);
    }

// FIXME: There should be no html code in controller
    public function filterParentPages() {
        $lang = $_REQUEST["mlang"];
        $menu = $_REQUEST["mmenu"];
        $parent_id = $_REQUEST["mparent"];
        ?>
        <option selected="selected" value="NULL">
            [
            <?php
            translate("none");
            ?>
            ]
        </option>
        <?php
        $pages = getAllPages($lang, "title", false, $menu);
        foreach ($pages as $key => $page) {
            ?>
            <option value="<?php
            echo $page["id"];
            ?>"
                    <?php if ($page["id"] == $parent_id) echo "selected"; ?>>
                        <?php
                        echo esc($page["title"]);
                        ?>
                (ID:
                <?php
                echo $page["id"];
                ?>
                )
            </option>
            <?php
        }
    }

    protected function validateInput() {
        $validator = new Validator;
        $validation = $validator->make($_POST + $_FILES, [
            'slug' => 'required',
            'title' => 'required',
            'language' => 'required',
            'position' => 'required|numeric',
            'menu' => 'required'
        ]);
        $validation->validate();

        $errors = $validation->errors()->all('<li>:message</li>');

        if ($validation->fails()) {
            $html = '<ul>';
            foreach ($errors as $error) {
                $html .= $error;
            }
            $html .= '</ul>';
            ExceptionResult($html, HttpStatusCode::UNPROCESSABLE_ENTITY);
        }
    }

}
