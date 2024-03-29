<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants;
use App\Constants\LinkTarget;
use App\CoreContent\Models\ViewModels\DiffViewModel;
use App\CoreContent\PageTableRenderer;
use App\Exceptions\DatasetNotFoundException;
use App\Helpers\ModuleHelper;
use App\HTML\ListItem;
use App\Models\Content\TypeMapper;
use App\Models\Content\Types\DefaultContentTypes;
use App\Models\Content\VCS;
use App\Security\Permissions\PermissionChecker;
use App\Security\XSSProtection;
use App\Utils\CacheUtil;
use Jfcherng\Diff\Differ;
use Jfcherng\Diff\DiffHelper;
use Jfcherng\Diff\Renderer\RendererConstant;
use Rakit\Validation\Validator;

use function App\HTML\stringContainsHtml;

class PageController extends \App\Controllers\Controller {
    public const MODULE_NAME = 'core_content';

    public function _getPagesListView(): string {
        return $_SESSION['pages_list_view'] ?? 'default';
    }

    public function recycleBin(): void {
        $this->_recycleBin();
        $url = ModuleHelper::buildActionURL('pages');
        Response::redirect($url);
    }

    public function _recycleBin(): void {
        $_SESSION['pages_list_view'] = 'recycle_bin';
    }

    public function pages(): void {
        $this->_pages();
        $url = ModuleHelper::buildActionURL('pages');
        Response::redirect($url);
    }

    public function _pages(): void {
        $_SESSION['pages_list_view'] = 'default';
    }

    public function createPost(): void {
        $model = $this->_createPost();
        if ($model && $model->isPersistent()) {
            Response::redirect(
                ModuleHelper::buildActionURL(
                    'pages_edit',
                    "page={$model->getID()}"
                )
            );
        }

        Response::redirect(ModuleHelper::buildActionURL('pages'));
    }

    public function _createPost(): ?AbstractContent {
        $permissionChecker = new PermissionChecker(get_user_id());
        $model = TypeMapper::getModel(Request::getVar('type'));

        if ($model) {
            $this->_fillAndSaveModel($model, $permissionChecker);
        }

        do_event('after_create_page');

        CacheUtil::clearPageCache();

        if ($model && $permissionChecker->hasPermission('pages_edit_own') && $model->getID()) {
            return $model;
        }

        return null;
    }

    public function editPost(): void {
        $success = $this->_editPost();

        $id = Request::getVar('page_id', null, 'int');
        $url = ModuleHelper::buildActionURL(
            'pages_edit',
            "page={$id}"
        );

        $httpStatus = $success ?
                \App\Constants\HttpStatusCode::OK : \App\Constants\HttpStatusCode::UNPROCESSABLE_ENTITY;
        Response::sendHttpStatusCodeResultIfAjax(
            $httpStatus,
            $url
        );
    }

    public function _editPost(): bool {
        $permissionChecker = new PermissionChecker(get_user_id());
        $model = TypeMapper::getModel(Request::getVar('type'));
        if (! $model) {
            return false;
        }
        try {
            $model->loadById(Request::getVar('page_id', null, 'int'));
        } catch (DatasetNotFoundException $e) {
            return false;
        }

        $model->type = Request::getVar('type');

        $authorId = Request::getVar('author_id', $model->author_id, 'int');
        $groupId = Request::getVar('group_id', $model->group_id, 'int');

        $this->_fillAndSaveModel($model, $permissionChecker, $authorId, $groupId);

        do_event('after_edit_page');

        CacheUtil::clearPageCache();

        return ! $model->hasChanges();
    }

    public function undeletePost(): void {
        $id = Request::getVar('id', null, 'int');
        do_event('before_undelete_page');

        if (! $id) {
            ExceptionResult(
                get_translation('not_found'),
                \App\Constants\HttpStatusCode::UNPROCESSABLE_ENTITY
            );
            return;
        }

        if (! $this->_undeletePost($id)) {
            ExceptionResult(
                get_translation('not_found'),
                \App\Constants\HttpStatusCode::NOT_FOUND
            );

            return;
        }

        Response::sendHttpStatusCodeResultIfAjax(
            \App\Constants\HttpStatusCode::OK,
            ModuleHelper::buildActionURL('pages')
        );
    }

    public function _undeletePost(int $id): bool {
        try {
            $content = ContentFactory::getByID($id);
        } catch (DatasetNotFoundException $e) {
            return false;
        }

        $content->undelete();

        do_event('after_undelete_page');

        CacheUtil::clearPageCache();
        return ! $content->isDeleted();
    }

    public function deletePost(): void {
        $id = Request::getVar('id', null, 'int');
        do_event('before_delete_page');

        if (! $id) {
            ExceptionResult(
                get_translation('not_found'),
                \App\Constants\HttpStatusCode::UNPROCESSABLE_ENTITY
            );
            return;
        }

        if (! $this->_deletePost($id)) {
            ExceptionResult(
                get_translation('not_found'),
                \App\Constants\HttpStatusCode::NOT_FOUND
            );

            return;
        }

        Response::sendHttpStatusCodeResultIfAjax(
            \App\Constants\HttpStatusCode::OK,
            ModuleHelper::buildActionURL('pages')
        );
    }

    public function _deletePost(int $id): bool {
        try {
            $content = ContentFactory::getByID($id);
        } catch (DatasetNotFoundException $e) {
            return false;
        }

        $content->delete();

        do_event('after_delete_page');

        CacheUtil::clearPageCache();
        return $content->isDeleted();
    }

    public function emptyTrash(): void {
        $this->_emptyTrash();
        Response::redirect(ModuleHelper::buildActionURL('pages'));
    }

    public function _emptyTrash(): void {
        do_event('before_empty_trash');
        AbstractContent::emptyTrash();
        do_event('after_empty_trash');

        CacheUtil::clearPageCache();
    }

    public function getContentTypes(): void {
        $json = $this->_getContentTypes();
        RawJSONResult($json);
    }

    public function _getContentTypes(): string {
        return json_encode(
            DefaultContentTypes::getAll(),
            JSON_UNESCAPED_SLASHES
        );
    }

    public function _diffContents(
        ?int $historyId = null,
        ?int $contentId = null
    ): DiffViewModel {
        $historyId = $historyId ?: (int)$_GET['history_id'];
        $contentId = $contentId ?: (int)$_GET['content_id'];

        $currentVersion = getPageByID($contentId);
        $oldVersion = VCS::getRevisionByID($historyId);

        $old = $oldVersion->content;
        $new = $currentVersion->content;

        $current_version_date = date(
            'Y-m-d H:i:s',
            (int)$currentVersion->lastmodified
        );
        $oldVersionData = $oldVersion->date;
        // the Diff class options
        $differOptions = [
            // show how many neighbor lines
            // Differ::CONTEXT_ALL can be used to show the whole file
            'context' => 3,
            // ignore case difference
            'ignoreCase' => false,
            // ignore whitespace difference
            'ignoreWhitespace' => true,
        ];

        // the renderer class options
        $rendererOptions = [
            // how detailed the rendered HTML in-line diff is? (none, line, word, char)
            'detailLevel' => 'word',
            // renderer language: eng, cht, chs, jpn, ...
            // or an array which has the same keys with a language file
            // check the "Custom Language" section in the readme for more advanced usage
            'language' => 'eng', // TODO: by current backend language
            // show line numbers in HTML renderers
            'lineNumbers' => true,
            // show a separator between different diff hunks in HTML renderers
            'separateBlock' => false,
            // show the (table) header
            'showHeader' => true,
            // the frontend HTML could use CSS "white-space: pre;" to visualize consecutive whitespaces
            // but if you want to visualize them in the backend with "&nbsp;", you can set this to true
            'spacesToNbsp' => true,
            // HTML renderer tab width (negative = do not convert into spaces)
            'tabSize' => 4,
            // this option is currently only for the Combined renderer.
            // it determines whether a replace-type block should be merged or not
            // depending on the content changed ratio, which values between 0 and 1.
            'mergeThreshold' => 0.8,
            // this option is currently only for the Unified and the Context renderers.
            // RendererConstant::CLI_COLOR_AUTO = colorize the output if possible (default)
            // RendererConstant::CLI_COLOR_ENABLE = force to colorize the output
            // RendererConstant::CLI_COLOR_DISABLE = force not to colorize the output
            'cliColorization' => RendererConstant::CLI_COLOR_AUTO,
            // this option is currently only for the Json renderer.
            // internally, ops (tags) are all int type but this is not good for human reading.
            // set this to "true" to convert them into string form before outputting.
            'outputTagAsString' => false,
            // this option is currently only for the Json renderer.
            // it controls how the output JSON is formatted.
            // see available options on https://www.php.net/manual/en/function.json-encode.php
            'jsonEncodeFlags' => \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
            // this option is currently effective when the "detailLevel" is "word"
            // characters listed in this array can be used to make diff segments into a whole
            // for example, making "<del>good</del>-<del>looking</del>" into "<del>good-looking</del>"
            // this should bring better readability but set this to empty array if you do not want it
            'wordGlues' => [' ', '-'],
            // change this value to a string as the returned diff if the two input strings are identical
            'resultForIdenticals' => null,
            // extra HTML classes added to the DOM of the diff container
            'wrapperClasses' => ['diff-wrapper'],
        ];

        $html = DiffHelper::calculate($old, $new, 'Inline', $differOptions, $rendererOptions);

        return new DiffViewModel(
            $html,
            $current_version_date,
            $oldVersionData,
            $contentId,
            $historyId
        );
    }

    public function toggleShowPositions(): void {
        $this->_toggleShowPositions();
        HTTPStatusCodeResult(\App\Constants\HttpStatusCode::OK);
    }

    public function _toggleShowPositions(): bool {
        $settingsName = 'user/' . get_user_id() . '/show_positions';
        if (Settings::get($settingsName)) {
            Settings::delete($settingsName);
            return false;
        }
        Settings::set($settingsName, '1');
        return true;

    }

    public function nextFreeSlug(): void {
        $slug = $_REQUEST['slug'];
        $language = $_REQUEST['language'];
        $id = isset($_REQUEST['id']) ?
                (int)$_REQUEST['id'] : 0;

        TextResult($this->_nextFreeSlug($slug, $language, $id));
    }

    // TODO: move this to the Content class
    public function _nextFreeSlug(
        string $originalSlug,
        string $language,
        int $id
    ): string {
        $slug = $originalSlug;
        if (! $this->_checkIfSlugIsFree(
            $slug,
            $language,
            $id
        )) {
            $counter = 2;
            while (true) {
                $slug = "{$originalSlug}-{$counter}";
                if ($this->_checkIfSlugIsFree($slug, $language, $id)) {
                    break;
                }
                $counter++;
            }
        }
        return $slug;
    }

    // returns true if this slug is unused in a language
    // if $id is set the content with the id will be excluded from this check
    // to prevent the slug field to be marked as error when editing a page
    public function _checkIfSlugIsFree(
        string $slug,
        string $language,
        int $id
    ): bool {
        if (empty($slug)) {
            return true;
        }
        $slug = Database::escapeValue($slug);
        $language = Database::escapeValue($language);

        $sql = 'SELECT id FROM ' . Database::tableName('content') .
                " where slug='{$slug}' and language = '{$language}' ";
        if ($id > 0) {
            $sql .= "and id <> {$id}";
        }
        $result = Database::query($sql);
        return Database::getNumRows($result) <= 0;
    }

    public function filterParentPages(): void {
        $lang = $_REQUEST['mlang'];
        $menu = $_REQUEST['mmenu'];
        $parent_id = Request::getVar('mparent', null, 'int');

        $html = $this->_filterParentPages($lang, $menu, $parent_id);
        HTMLResult($html);
    }

    // FIXME: There should be no html code in controller
    public function _filterParentPages(
        ?string $lang = null,
        ?string $menu = null,
        ?int $parent_id = null
    ): string {
        ob_start();
        ?>
        <option selected="selected" value="NULL">
            [<?php translate('none'); ?>]
        </option>
        <?php
        $pages = getAllPages($lang, 'title', false, $menu);
        foreach ($pages as $key => $page) {
            ?>
            <option value="<?php echo $page['id']; ?>" <?php
            if ($page['id'] == $parent_id) {
                echo 'selected';
            }
            ?>>
                    <?php esc($page['title']); ?>

                <?php if (! Request::getVar('no_id')) {
                    ?>
                    (ID: <?php echo $page['id']; ?>)
                <?php }
                ?>
            </option>
            <?php
        }

        return ob_get_clean();
    }

    public function getPages(): void {
        $data = $this->_getPages();
        $json = json_encode($data, JSON_UNESCAPED_SLASHES);
        RawJSONResult($json);
    }

    public function _getPages(): array {
        $start = Request::getVar('start', 0, 'int');
        $length = Request::getVar('length', 25, 'int');
        $draw = Request::getVar('draw', 1, 'int');
        $search = isset($_REQUEST['search']) &&
                isset($_REQUEST['search']['value']) ?
                $_REQUEST['search']['value'] : null;
        $filters = isset($_REQUEST['filters']) &&
                is_array($_REQUEST['filters']) ? $_REQUEST['filters'] : [];

        // if the client requested sorting apply it
        $order = isset($_REQUEST['order']) && is_array($_REQUEST['order']) ?
                $_REQUEST['order'][0] : null;

        $renderer = new PageTableRenderer();

        $data = $renderer->getData(
            $start,
            $length,
            $draw,
            $search,
            $filters,
            $this->_getPagesListView(),
            $order
        );
        return $data;
    }

    public function _validateInput(): ?string {
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
        if (Request::hasVar('slug') &&
                stringContainsHtml(Request::getVar('slug'))) {
            return get_translation('no_html_allowed');
        }

        if ($validation->fails()) {
            $html = '<ul>';
            foreach ($errors as $error) {
                $html .= $error;
            }
            $html .= '</ul>';
            return $html;
        }
        return null;
    }

    // this is used for the Link feature of the CKEditor
    // The user can select an internal page from a dropdown list for linking
    public function getCKEditorLinkList(): void {
        $data = $this->_getCKEditorLinkList();
        JSONResult($data, \App\Constants\HttpStatusCode::OK, true);
    }

    public function _getCKEditorLinkList(): array {
        return getAllPagesWithTitle();
    }

    public function toggleFilters(): void {
        JSONResult($this->_toggleFilters());
    }

    public function _toggleFilters(): bool {
        $settingsName = 'user/' . get_user_id() . '/show_filters';
        if (Settings::get($settingsName)) {
            Settings::delete($settingsName);
            return false;
        }
        Settings::set($settingsName, '1');
        return true;

    }

    public function _getLanguageSelection(): array {
        $languages = getAllUsedLanguages();

        $selectItems = [];

        $userLanguages = $this->_getGroupAssignedLanguages();

        $selectItems[] = new ListItem(null, '[' . get_translation('all') . ']');
        foreach ($languages as $language) {
            $item = new ListItem(
                $language,
                getLanguageNameByCode($language)
            );
            if (count($userLanguages) && ! in_array($language, $userLanguages)) {
                continue;
            }

            $selectItems[] = $item;
        }
        return $selectItems;
    }

    public function _getTypeSelection(): array {
        $types = get_used_post_types();
        $selectItems = [];
        $selectItems[] = new ListItem(null, '[' . get_translation('all') . ']');

        foreach ($types as $type) {
            $item = new ListItem(
                $type,
                get_translation($type)
            );
            $selectItems[] = $item;
        }
        return $selectItems;
    }

    public function _getMenuSelection(): array {
        $menus = get_all_used_menus();
        $selectItems = [];
        $selectItems[] = new ListItem(null, '[' . get_translation('all') . ']');

        foreach ($menus as $menu) {
            $item = new ListItem(
                $menu,
                get_translation($menu)
            );
            $selectItems[] = $item;
        }
        return $selectItems;
    }

    public function _getCategorySelection(): array {
        $selectItems = [];
        $selectItems[] = new ListItem(null, '[' . get_translation('all') . ']');

        $query = Database::selectAll(
            'categories',
            ['id', 'name'],
            'id in (select category_id from {prefix}content)',
            [],
            true,
            'name'
        );

        while ($row = Database::fetchObject($query)) {
            $selectItems[] = new ListItem($row->id, $row->name);
        }
        return $selectItems;
    }

    public function _getParentIds(
        ?string $language = null,
        ?string $menu = null
    ): array {
        $where = 'parent_id is not null';

        if ($menu) {
            $where .= " and menu = '" . Database::escapeValue($menu) . "'";
        }

        if ($language) {
            $where .= " and language = '" . Database::escapeValue($language) . "'";
        }

        $groupLanguages = $this->_getGroupAssignedLanguages();
        if (count($groupLanguages)) {
            $groupLanguages = array_map(static function($lang) {
                return "'" . Database::escapeValue($lang) . "'";
            }, $groupLanguages);
            $where .= ' and language in (' . implode(',', $groupLanguages) . ')';
        }

        $query = Database::selectAll(
            'content',
            ['distinct parent_id as id'],
            $where
        );
        $parentIds = [];
        while ($row = Database::fetchObject($query)) {
            $parentIds[] = (int)$row->id;
        }
        return $parentIds;
    }

    public function getParentSelection(): void {
        $language = Request::getVar('language', null, 'str');
        $menu = Request::getVar('menu', null, 'str');

        $html = $this->_getParentSelection($language, $menu);
        HTMLResult($html);
    }

    public function _getParentSelection(
        ?string $language = null,
        ?string $menu = null
    ): string {
        $parentIds = $this->_getParentIds($language, $menu);

        $selectItems = [];
        $selectItems[] = new ListItem('all', '[' . get_translation('all') . ']');
        $selectItems[] = new ListItem('0', '[' . get_translation('none') . ']');

        foreach ($parentIds as $parentId) {
            $item = new ListItem(
                $parentId,
                _esc(getPageTitleByID($parentId))
            );
            $selectItems[] = $item->getHtml();
        }
        return implode('', $selectItems);
    }

    public function getParentPageId(): void {
        $id = Request::getVar('id', 0, 'int');

        try {
            JSONResult($this->_getParentPageId($id));
        } catch (DatasetNotFoundException $e) {
            HTTPStatusCodeResult(\App\Constants\HttpStatusCode::NOT_FOUND);
        }

    }

    public function _getParentPageId(int $pageId): object {
        $page = ContentFactory::getByID($pageId);

        $obj = new stdClass();
        $obj->id = $page->parent_id;
        return $obj;
    }

    public function _getBooleanSelection(): array {
        return [
            new ListItem(null, '[' . get_translation('all') . ']'),
            new ListItem('1', get_translation('yes')),
            new ListItem('0', get_translation('no'))
        ];
    }

    // TODO: This method is too long
    // Split this in multiple methods
    protected function _fillAndSaveModel(
        $model,
        PermissionChecker $permissionChecker,
        ?int $userId = null,
        ?int $groupId = null
    ): void {
        $this->validateInput();

        $model->slug = Request::getVar('slug', '', 'str');
        $model->title = Request::getVar('title');
        $model->alternate_title = Request::getVar('alternate_title');

        $model->author_id = $userId ?: get_user_id();

        // if the user is not permitted to change page status
        // then select2 is disabled which causes the "active" value
        // to not be submitted
        // In this case set active to false on create page
        // and don't change it's value on update
        if (! $model->isPersistent()) {
            $model->active = Request::hasVar('active') ?
                    Request::getVar('active', true, 'bool') : false;
            $model->approved = Request::hasVar('active');
        } elseif (Request::hasVar('active')) {
            $model->active = Request::getVar('active', true, 'bool');
            if ($model->active) {
                $model->approved = true;
            }
        }

        $model->hidden = Request::getVar('hidden', false, 'bool');
        $model->content = Request::getVar('content');

        $user = User::fromSessionData();
        $groupCollection = $user->getGroupCollection();

        // get allowed tags of all groups assigned to the current user
        $allowedTags = $groupCollection ?
                $groupCollection->getAllowableTags() : Constants\DefaultValues::ALLOWED_TAGS;

        // remove all html tags except the explicitly allowed tags
        if (! empty($allowedTags)) {
            $model->content = XSSProtection::stripTags($model->content, $allowedTags);
        }

        $model->category_id = Request::getVar('category_id', 1, 'int');
        $model->link_url = Request::getVar('link_url', null, 'str');
        $model->menu = Request::getVar('menu', 'not_in_menu', 'str');
        $model->position = Request::getVar('position', 0, 'int');

        $model->menu_image = Request::getVar('menu_image', null, 'str');
        $model->custom_data = json_decode(
            Request::getVar('custom_data', '{}', 'str'),
            false
        );

        $model->theme = Request::getVar('theme', null, 'str');

        if ($model instanceof Node) {
            $model->link_url = '#';
        }

        $model->cache_control = Request::getVar('cache_control', 'auto', 'str');

        $parent_id = Request::getVar('parent_id', null, 'str');
        $model->parent_id = (int)$parent_id > 0 ? (int)$parent_id : null;

        if (Request::hasVar('access')) {
            $model->access = implode(',', Request::getVar('access'));
        }

        $model->target = Request::getVar(
            'target',
            LinkTarget::TARGET_SELF,
            'str'
        );

        // Open Graph
        $model->og_title = Request::getVar('og_title');
        $model->og_description = Request::getVar('og_description');
        $model->og_image = Request::getVar('og_image');

        $model->meta_description = Request::getVar('meta_description');
        $model->meta_keywords = Request::getVar('meta_keywords');
        $model->robots = Request::getVar('robots', null, 'str');

        $model->language = Request::getVar('language');

        if ($model instanceof Module_Page) {
            $model->module = Request::getVar('module', null, 'str');
        }

        if ($model instanceof Video_Page) {
            $model->video = Request::getVar('video', null, 'int');
        }
        if ($model instanceof Audio_Page) {
            $model->audio = Request::getVar('audio', null, 'int');
        }

        $model->text_position = Request::getVar(
            'text_position',
            'before',
            'str'
        );

        if ($model instanceof Image_Page) {
            $model->image_url = Request::getVar('image_url', null, 'str');
        }

        if ($model instanceof Article) {
            $model->article_author_name = Request::getVar(
                'article_author_name'
            );
            $model->article_author_email = Request::getVar(
                'article_author_email'
            );
            $model->article_image = Request::getVar(
                'article_image'
            );
            $model->article_date = Request::getVar('article_date') ?
                    strtotime(Request::getVar('article_date')) : null;

            $model->excerpt = Request::getVar('excerpt');
        }

        $permissionObjects = ['admins', 'group', 'owner', 'others'];
        foreach ($permissionObjects as $object) {
            $permission = Request::getVar(
                "only_{$object}_can_edit",
                false,
                'bool'
            );
            $model->getPermissions()->setEditRestriction(
                $object,
                (bool)$permission
            );
        }

        $model->link_to_language = Request::getVar('link_to_language', null, 'int');
        $model->comments_enabled = Request::getVar(
            'comments_enabled'
        ) !== 'null' ?
                Request::getVar('comments_enabled', false, 'bool') : null;

        $model->show_headline = Request::getVar('show_headline', 1, 'bool');
        $model->group_id = $groupId ?: Group::getCurrentGroupId();

        do_event('before_create_page');

        $model->save();

        $user_id = get_user_id();
        $content_id = $model->getId();

        if ($model instanceof Content_List) {
            $list_language = Request::getVar('list_language', '', 'str');
            if (empty($list_language)) {
                $list_language = null;
            }

            $list_category = Request::getVar('list_category', '', 'str');
            if (empty($list_category)) {
                $list_category = null;
            }

            $list_menu = Request::getVar('list_menu', '', 'str');
            if (empty($list_menu)) {
                $list_menu = null;
            }

            $list_parent = Request::getVar('list_parent', '', 'str');
            if (empty($list_parent)) {
                $list_parent = null;
            }

            $list_order_by = Database::escapeValue(
                Request::getVar('list_order_by', 'id', 'str')
            );

            $list_order_direction = Request::getVar(
                'list_order_direction',
                'asc',
                'str'
            );
            $list_order_direction = Database::escapeValue($list_order_direction);

            $list_use_pagination = Request::getVar('list_use_pagination', 0, 'int');

            $limit = Request::getVar('limit', 0, 'int');
            $list_type = Request::getVar('list_type', 'null', 'str');

            if (empty($list_type) || $list_type == 'null') {
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
        VCS::createRevision(
            (int)$content_id,
            $content,
            (int)$user_id
        );

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

    protected function validateInput(): void {
        $validationErrors = $this->_validateInput();
        if ($validationErrors) {
            ExceptionResult($validationErrors, \App\Constants\HttpStatusCode::UNPROCESSABLE_ENTITY);
        }
    }

    protected function _getGroupAssignedLanguages(): array {
        $permissionChecker = new PermissionChecker(get_user_id());
        return array_map(
            static function($lang) {
                return $lang->getLanguageCode();
            },
            $permissionChecker->getLanguages()
        );
    }
}
