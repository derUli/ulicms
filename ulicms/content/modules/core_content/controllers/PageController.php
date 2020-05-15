<?php
declare(strict_types=1);

use UliCMS\CoreContent\Models\ViewModels\DiffViewModel;
use UliCMS\CoreContent\PageTableRenderer;
use UliCMS\Models\Content\VCS;
use UliCMS\Models\Content\Types\DefaultContentTypes;
use Rakit\Validation\Validator;
use UliCMS\Security\PermissionChecker;
use function UliCMS\Security\XSSProtection\stripTags;
use UliCMS\Models\Content\TypeMapper;
use UliCMS\Constants\LinkTarget;
use UliCMS\Utils\CacheUtil;
use zz\Html\HTMLMinify;
use function UliCMS\HTML\stringContainsHtml;
use const UliCMS\Constants\HTML5_ALLOWED_TAGS;
use UliCMS\HTML\ListItem;

class PageController extends Controller {

    const MODULE_NAME = "core_content";

    public function getPagesListView(): string {
        return $_SESSION["pages_list_view"] ?? "default";
    }

    public function recycleBin(): void {
        $_SESSION["pages_list_view"] = "recycle_bin";

        $url = ModuleHelper::buildActionURL("pages");
        Request::redirect($url);
    }

    public function pages(): void {
        $_SESSION["pages_list_view"] = "default";

        $url = ModuleHelper::buildActionURL("pages");
        Request::redirect($url);
    }

    public function createPost(): void {
        $this->validateInput();

        $permissionChecker = new PermissionChecker(get_user_id());
        $model = TypeMapper::getModel(Request::getVar("type"));

        $this->fillAndSaveModel($model, $permissionChecker);

        do_event("after_create_page");

        CacheUtil::clearPageCache();

        if ($permissionChecker->hasPermission("pages_edit_own")
                and $model->getID()) {
            Request::redirect(ModuleHelper::buildActionURL(
                            "pages_edit",
                            "page={$model->getID()}")
            );
        }

        Request::redirect(ModuleHelper::buildActionURL("pages"));
    }

    public function editPost(): void {
        $this->validateInput();

        $permissionChecker = new PermissionChecker(get_user_id());
        $model = TypeMapper::getModel(Request::getVar("type"));
        $model->loadById(Request::getVar("page_id", null, "int"));

        $model->type = Request::getVar("type");

        $authorId = Request::getVar("author_id", $model->author_id, "int");
        $groupId = Request::getVar("group_id", $model->group_id, "int");

        $this->fillAndSaveModel($model, $permissionChecker, $authorId, $groupId);

        do_event("after_edit_page");

        CacheUtil::clearPageCache();

        // if called by ajax return no content to improve performance
        if (Request::isAjaxRequest()) {
            HTTPStatusCodeResult(HttpStatusCode::OK);
        }

        Response::redirect(ModuleHelper::buildActionURL("pages"));
    }

    // TODO: This method is too long
    // Split this in multiple methods
    private function fillAndSaveModel(
            $model,
            PermissionChecker $permissionChecker,
            ?int $userId = null,
            ?int $groupId = null
    ): void {
        $model->slug = Request::getVar(
                        "slug",
                        StringHelper::cleanString(
                                Request::getVar("title")
                        )
        );
        $model->title = Request::getVar("title");
        $model->alternate_title = Request::getVar("alternate_title");

        $model->author_id = $userId ? $userId : get_user_id();

        // if the user is not permitted to change page status
        // then select2 is disabled which causes the "active" value
        // to not be submitted
        // In this case set active to false on create page
        // and don't change it's value on update
        if (!$model->isPersistent()) {
            $model->active = Request::hasVar("active") ?
                    Request::getVar("active", true, "bool") : false;
            $model->approved = Request::hasVar("active");
        } else if (Request::hasVar("active")) {
            $model->active = Request::getVar("active", true, "bool");
            if ($model->active) {
                $model->approved = true;
            }
        }

        $model->hidden = Request::getVar("hidden", false, "bool");
        $model->content = Request::getVar("content");

        $user = User::fromSessionData();
        $groupCollection = $user->getGroupCollection();

        // get allowed tags of all groups assigned to the current user
        $allowedTags = $groupCollection ?
                $groupCollection->getAllowableTags() : HTML5_ALLOWED_TAGS;

        // remove all html tags except the explicitly allowed tags
        if (Stringhelper::isNotNullOrWhitespace($allowedTags)) {
            $model->content = stripTags($model->content, $allowedTags);
        }

        $model->category_id = Request::getVar("category_id", 1, "int");
        $model->link_url = Request::getVar("link_url", NULL, "str");
        $model->menu = Request::getVar("menu", "not_in_menu", "str");
        $model->position = Request::getVar("position", 0, "int");

        $model->menu_image = Request::getVar("menu_image", NULL, "str");
        $model->custom_data = json_decode(
                Request::getVar("custom_data", "{}", "str"),
                false
        );

        $model->theme = Request::getVar("theme", NULL, "str");

        if ($model instanceof Node) {
            $model->link_url = "#";
        }

        $model->cache_control = Request::getVar("cache_control", "auto", "str");

        $parent_id = Request::getVar("parent_id", null, "str");
        $model->parent_id = intval($parent_id) > 0 ? intval($parent_id) : null;
        if (Request::getVar("access")) {
            $model->access = implode(",", Request::getVar("access"));
        }

        $model->target = Request::getVar(
                        "target",
                        LinkTarget::TARGET_SELF,
                        "str"
        );

        // Open Graph
        $model->og_title = Request::getVar("og_title");
        $model->og_description = Request::getVar("og_description");
        $model->og_image = Request::getVar("og_image");

        $model->meta_description = Request::getVar("meta_description");
        $model->meta_keywords = Request::getVar("meta_keywords");
        $model->robots = Request::getVar("robots", null, "str");

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

        $model->text_position = Request::getVar(
                        "text_position",
                        "before",
                        "str"
        );

        $pages_approve_own = $permissionChecker->hasPermission(
                "pages_approve_own"
        );

        if ($model instanceof Image_Page) {
            $model->image_url = Request::getVar("image_url", null, "str");
        }


        if ($model instanceof Article) {
            $model->article_author_name = Request::getVar(
                            "article_author_name"
            );
            $model->article_author_email = Request::getVar(
                            "article_author_email"
            );
            $model->article_image = Request::getVar(
                            "article_image"
            );
            $model->article_date = Request::getVar("article_date") ?
                    strtotime(
                            Request::getVar("article_image")
                    ) : null;
            $model->excerpt = Request::getVar("excerpt");
        }


        $permissionObjects = array("admins", "group", "owner", "others");
        foreach ($permissionObjects as $object) {
            $model->getPermissions()->setEditRestriction(
                    $object,
                    boolval(
                            Request::getVar(
                                    "only_{$object}_can_edit", false, "bool"
                            )
                    )
            );
        }

        $model->link_to_language = Request::getVar("link_to_language", null, "int");
        $model->comments_enabled = Request::getVar(
                        "comments_enabled"
                ) !== "null" ?
                Request::getVar("comments_enabled", false, "bool") : null;

        $model->show_headline = Request::getVar("show_headline", 1, "bool");
        $model->group_id = $groupId ? $groupId : get_group_id();

        do_event("before_create_page");

        $model->save();

        $user_id = get_user_id();
        $content_id = $model->getId();

        if ($model instanceof Content_List) {
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
            $list_order_direction = Database::escapeValue(
                            $_POST["list_order_direction"]
            );

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
        $content = $model->content;
        VCS::createRevision(intval($content_id), $content,
                intval($user_id));

        $type = DefaultContentTypes::get($model->type);
        foreach ($type->customFields as $field) {
            $field->name = "{$_POST['type']}_{$field->name}";
            $value = null;
            if (isset($_POST[$field->name])) {
                $value = $_POST[$field->name];
            }

            CustomFields::set($field->name, $value, $content_id, false);
        }
    }

    public function undeletePost(): void {
        $id = Request::getVar("id", null, "int");
        do_event("before_undelete_page");
        $content = ContentFactory::getByID($id);
        if ($content->id === null) {
            ExceptionResult(get_translation("not_found"));
        }
        $content->undelete();
        do_event("after_undelete_page");

        CacheUtil::clearPageCache();

        Response::sendHttpStatusCodeResultIfAjax(
                HTTPStatusCode::OK,
                ModuleHelper::buildActionURL("pages")
        );
    }

    public function deletePost(): void {
        $page = Request::getVar("id", null, "int");
        do_event("before_delete_page");

        $content = ContentFactory::getByID($page);
        if ($content->id === null) {
            ExceptionResult(get_translation("not_found"));
        }
        $content->delete();

        do_event("after_delete_page");

        CacheUtil::clearPageCache();

        Response::sendHttpStatusCodeResultIfAjax(
                HTTPStatusCode::OK,
                ModuleHelper::buildActionURL("pages")
        );
    }

    public function emptyTrash(): void {
        do_event("before_empty_trash");
        Content::emptyTrash();
        do_event("after_empty_trash");

        CacheUtil::clearPageCache();

        Request::redirect(ModuleHelper::buildActionURL("pages"));
    }

    public function getContentTypes(): void {
        $json = json_encode(
                DefaultContentTypes::getAll(),
                JSON_UNESCAPED_SLASHES
        );

        RawJSONResult($json);
    }

    public function diffContents(
            ?int $history_id = null,
            ?int $content_id = null
    ): DiffViewModel {
        $history_id = intval(!$history_id ?
                $_GET ["history_id"] : $history_id);
        $content_id = intval(!$content_id ?
                $_GET ["content_id"] : $content_id);

        $current_version = getPageByID($content_id);
        $old_version = VCS::getRevisionByID($history_id);

        $from_text = $current_version->content;
        $to_text = $old_version->content;

        $current_version_date = date(
                "Y-m-d H:i:s",
                intval($current_version->lastmodified)
        );
        $old_version_date = $old_version->date;

        $from_text = mb_convert_encoding($from_text, 'HTML-ENTITIES', 'UTF-8');
        $to_text = mb_convert_encoding($to_text, 'HTML-ENTITIES', 'UTF-8');
        $opcodes = FineDiff::getDiffOpcodes(
                        $from_text,
                        $to_text,
                        FineDiff::$wordGranularity
        );

        $html = FineDiff::renderDiffToHTMLFromOpcodes($from_text, $opcodes);

        return new DiffViewModel(
                $html,
                $current_version_date,
                $old_version_date,
                $content_id,
                $history_id
        );
    }

    public function toggleShowPositions(): void {
        $settingsName = "user/" . get_user_id() . "/show_positions";
        if (Settings::get($settingsName)) {
            Settings::delete($settingsName);
        } else {
            Settings::set($settingsName, "1");
        }
        HTTPStatusCodeResult(HttpStatusCode::OK);
    }

    public function nextFreeSlug(): void {
        $originalSlug = $_REQUEST["slug"];
        $slug = $originalSlug;

        if ($this->checkIfSlugIsFree(
                        $slug,
                        $_REQUEST["language"],
                        isset($_REQUEST["id"]) ?
                                intval($_REQUEST["id"]) : 0
                )) {
            TextResult($slug);
        } else {
            $counter = 1;
            while (true) {
                $slug = "{$originalSlug}-$counter";
                if ($this->checkIfSlugIsFree(
                                $slug,
                                $_REQUEST["language"],
                                isset($_REQUEST["id"]) ?
                                        intval($_REQUEST["id"]) : 0
                        )) {
                    TextResult($slug);
                }
                $counter++;
            }
        }
    }

    // returns true if this slug is unused in a language
    // if $id is set the content with the id will be excluded from this check
    // to prevent the slug field to be marked as error when editing a page

    public function checkIfSlugIsFree(
            string $slug,
            string $language,
            int $id
    ): bool {
        if (StringHelper::isNullOrWhitespace($slug)) {
            return true;
        }
        $slug = Database::escapeValue($slug);
        $language = Database::escapeValue($language);

        $sql = "SELECT id FROM " . tbname("content") .
                " where slug='$slug' and language = '$language' ";
        if ($id > 0) {
            $sql .= "and id <> $id";
        }
        $result = Database::query($sql);
        return (Database::getNumRows($result) <= 0);
    }

    // FIXME: There should be no html code in controller
    public function filterParentPages(): void {
        $lang = $_REQUEST["mlang"];
        $menu = $_REQUEST["mmenu"];
        $parent_id = $_REQUEST["mparent"];

        ob_start();
        ?>
        <option selected="selected" value="NULL">
            [<?php translate("none"); ?>]
        </option>
        <?php
        $pages = getAllPages($lang, "title", false, $menu);
        foreach ($pages as $key => $page) {
            ?>
            <option value="<?php
            echo $page["id"];
            ?>" <?php
            if ($page["id"] == $parent_id) {
                echo "selected";
            }
            ?>>
                    <?php
                    echo esc($page["title"]);
                    ?>

                        <?php if (!Request::getVar("no_id")) {
                            ?>
                    (ID: <?php echo $page["id"]; ?>)
                <?php } ?>
            </option>
                <?php
            }
            HTMLResult(ob_get_clean(), HttpStatusCode::OK,
                    HTMLMinify::OPTIMIZATION_ADVANCED);
        }

        public function getPages(): void {
            $start = Request::getVar("start", 0, "int");
            $length = Request::getVar("length", 25, "int");
            $draw = Request::getVar("draw", 1, "int");
            $search = $_REQUEST["search"]["value"];
            $filters = is_array($_REQUEST["filters"]) ? $_REQUEST["filters"] : [];

            // if the client requested sorting apply it
            $order = is_array($_REQUEST["order"]) ? $_REQUEST["order"][0] : null;

            $renderer = new PageTableRenderer();

            $data = $renderer->getData(
                    $start,
                    $length,
                    $draw,
                    $search,
                    $filters,
                    $this->getPagesListView(),
                    $order
            );

            $json = json_encode($data, JSON_UNESCAPED_SLASHES);
            RawJSONResult($json);
        }

        protected function validateInput(): void {
            $validator = new Validator();
            $validation = $validator->make($_POST + $_FILES, [
                'slug' => 'required',
                'title' => 'required',
                'language' => 'required',
                'position' => 'required|numeric',
                'menu' => 'required'
            ]);
            $validation->validate();

            $errors = $validation->errors()->all('<li>:message</li>');

            // Fix for security issue CVE-2019-11398
            if (stringContainsHtml($_POST["slug"])) {
                ExceptionResult(get_translation("no_html_allowed"));
            }

            if ($validation->fails()) {
                $html = '<ul>';
                foreach ($errors as $error) {
                    $html .= $error;
                }
                $html .= '</ul>';
                ExceptionResult($html, HttpStatusCode::UNPROCESSABLE_ENTITY);
            }
        }

        // this is used for the Link feature of the CKEditor
        // The user can select an internal page from a dropdown list for linking
        public function getCKEditorLinkList(): void {
            $data = getAllPagesWithTitle();
            JSONResult($data, HttpStatusCode::OK, true);
        }

        public function toggleFilters(): void {
            $settingsName = "user/" . get_user_id() . "/show_filters";
            if (Settings::get($settingsName)) {
                Settings::delete($settingsName);
                JsonResult(false);
            } else {
                Settings::set($settingsName, "1");
                JsonResult(true);
            }
        }

        protected function getGroupAssignedLanguages(): array {
            $permissionChecker = new PermissionChecker(get_user_id());
            return array_map(
                    function($lang) {
                return $lang->getLanguageCode();
            },
                    $permissionChecker->getLanguages());
        }

        public function getLanguageSelection(): array {
            $languages = getAllUsedLanguages();

            $selectItems = [];

            $userLanguages = $this->getGroupAssignedLanguages();

            $selectItems[] = new ListItem(null, "[" . get_translation("all") . "]");
            foreach ($languages as $language) {

                $item = new ListItem(
                        $language,
                        getLanguageNameByCode($language)
                );
                if (count($userLanguages) && !in_array($language, $userLanguages)) {
                    continue;
                }

                $selectItems[] = $item;
            }
            return $selectItems;
        }

        public function getTypeSelection(): array {
            $types = get_used_post_types();
            $selectItems = [];
            $selectItems[] = new ListItem(null, "[" . get_translation("all") . "]");

            foreach ($types as $type) {
                $item = new ListItem(
                        $type,
                        get_translation($type)
                );
                $selectItems[] = $item;
            }
            return $selectItems;
        }

        public function getMenuSelection(): array {
            $menus = get_all_used_menus();
            $selectItems = [];
            $selectItems[] = new ListItem(null, "[" . get_translation("all") . "]");

            foreach ($menus as $menu) {
                $item = new ListItem(
                        $menu,
                        get_translation($menu)
                );
                $selectItems[] = $item;
            }
            return $selectItems;
        }

        public function getCategorySelection(): array {
            $selectItems = [];
            $selectItems[] = new ListItem(null, "[" . get_translation("all") . "]");

            $query = Database::selectAll(
                            "categories",
                            ["id", "name"],
                            "id in (select category_id from {prefix}content)",
                            [],
                            true,
                            "name"
            );

            while ($row = Database::fetchObject($query)) {
                $selectItems[] = new ListItem($row->id, $row->name);
            }
            return $selectItems;
        }

        public function getParentIds(
                ?string $language = null,
                ?string $menu = null
        ): array {
            $where = "parent_id is not null";

            if ($menu) {
                $where .= " and menu = '" . Database::escapeValue($menu) . "'";
            }

            if ($language) {
                $where .= " and language = '" . Database::escapeValue($language) . "'";
            }


            $groupLanguages = $this->getGroupAssignedLanguages();
            if (count($groupLanguages)) {
                $groupLanguages = array_map(function($lang) {
                    return "'" . Database::escapeValue($lang) . "'";
                }
                        , $groupLanguages);
                $where .= " and language in (" . implode(",", $groupLanguages) . ")";
            }

            $query = Database::selectAll(
                            "content",
                            ["distinct parent_id as id"],
                            $where
            );
            $parentIds = [];
            while ($row = Database::fetchObject($query)) {

                $parentIds[] = intval($row->id);
            }
            return $parentIds;
        }

        public function getParentSelection(): void {
            $language = Request::getVar("language", null, "str");
            $menu = Request::getVar("menu", null, "str");

            $parentIds = $this->getParentIds($language, $menu);

            $selectItems = [];
            $selectItems[] = new ListItem(null, "[" . get_translation("all") . "]");

            foreach ($parentIds as $parentId) {
                $item = new ListItem(
                        $parentId,
                        _esc(getPageTitleByID($parentId))
                );
                $selectItems[] = $item->getHtml();
            }
            HTMLResult(implode("", $selectItems));
        }

        public function getBooleanSelection(): array {
            return [
                new ListItem(null, "[" . get_translation("all") . "]"),
                new ListItem("1", get_translation("yes")),
                new ListItem("0", get_translation("no"))
            ];
        }

    }
    