<?php

use UliCMS\Security\Permissions\PagePermissions;
use UliCMS\Models\Content\Comment;
use UliCMS\Models\Content\VCS;

class Page extends Content {

    public $id = null;
    public $slug = "";
    public $title = "";
    public $alternate_title = "";
    public $target = "_self";
    public $category_id = 1;
    public $content = "";
    public $language = "de";
    public $menu_image = null;
    public $active = 1;
    public $created = 0;
    public $lastmodified = 0;
    public $author_id = null;
    public $group_id = null;
    public $lastchangeby = 1;
    public $views = 0;
    public $menu = "top";
    public $position = 0;
    public $cache_control = "auto";
    public $parent_id = null;
    public $access = "all";
    public $meta_description = null;
    public $meta_keywords = null;
    private $deleted_at = null;
    public $theme = null;
    public $robots = null;
    public $custom_data = null;
    public $type = "page";
    public $og_title = "";
    public $og_image = "";
    public $og_description = "";
    public $hidden = 0;
    public $comments_enabled = null;
    private $permissions;
    public $show_headline = true;

    public function __construct($id = null) {
        if ($this->custom_data === null) {
            $this->custom_data = [];
        }
        $this->permissions = new PagePermissions();
        if ($id) {
            $this->loadByID($id);
        }
    }

    protected function fillVars($result = null) {
        $this->id = intval($result->id);
        $this->slug = $result->slug;
        $this->title = $result->title;
        $this->alternate_title = $result->alternate_title;
        $this->target = $result->target;
        $this->category_id = $result->category_id;
        $this->content = $result->content;
        $this->language = $result->language;
        $this->menu_image = $result->menu_image;
        $this->active = $result->active;
        $this->created = $result->created;
        $this->lastmodified = $result->lastmodified;
        $this->author_id = $result->author_id;
        $this->group_id = $result->group_id;
        $this->lastchangeby = $result->lastchangeby;
        $this->views = $result->views;
        $this->menu = $result->menu;
        $this->position = $result->position;
        $this->parent_id = $result->parent_id;
        $this->access = $result->access;
        $this->meta_description = $result->meta_description;
        $this->meta_keywords = $result->meta_keywords;
        $this->deleted_at = $result->deleted_at;
        $this->theme = $result->theme;
        $this->robots = $result->robots;
        if ($this->customData === null) {
            $this->custom_data = [];
        }
        $this->custom_data = json_decode($result->custom_data, false);

        $this->type = $result->type;
        $this->og_image = $result->og_image;
        $this->og_description = $result->og_description;
        $this->cache_control = $result->cache_control;
        $this->hidden = $result->hidden;
        $this->show_headline = boolval($result->show_headline);
        $this->comments_enabled = !is_null($result->comments_enabled) ?
                boolval($result->comments_enabled) : null;

        // fill page permissions object
        $resultArray = (array) $result;
        foreach ($resultArray as $key => $value) {
            preg_match("/only_([a-z]+)_can_edit/", $key, $matches);
            if (count($matches) >= 2) {
                $object = $matches[1];
                $this->permissions->setEditRestriction($object, $value);
            }
        }
    }

    public function loadByID($id) {
        $result = Database::pQuery("SELECT * FROM `{prefix}content` "
                        . "where id = ?", array(
                    intval($id)
                        ), true);
        if (Database::getNumRows($result) > 0) {
            $result = Database::fetchObject($result);
            $this->fillVars($result);
        } else {
            throw new Exception("No content with id $id");
        }
    }

    public function loadBySlugAndLanguage($name, $language) {
        $name = Database::escapeValue($name);
        $language = Database::escapeValue($language);
        $result = Database::query("SELECT * FROM `" . tbname("content") .
                        "` where `slug` = '$name' and "
                        . "`language` = '$language'");
        if (Database::getNumRows($result) > 0) {
            $dataset = Database::fetchObject($result);
            $this->fillVars($dataset);
            return;
        }
        throw new Exception("No such page");
    }

    public function save() {
        $retval = null;
        if ($this->id === null) {
            $retval = $this->create();
        } else {
            $retval = $this->update();
        }
        return $retval;
    }

    public function create() {
        $sql = "INSERT INTO `" . tbname("content") . "` (slug, title,
            alternate_title, target, category_id,
				content, language, menu_image, active, created,
                                lastmodified, author_id,
				`group_id`, lastchangeby, views, menu, position,
                                parent_id, access, meta_description,
                                meta_keywords, deleted_at,
				theme, robots,
                                custom_data, `type`, og_title, og_image,
                                og_description, cache_control, hidden,
                                comments_enabled, show_headline) VALUES (";

        $sql .= "'" . Database::escapeValue($this->slug) . "',";
        $sql .= "'" . Database::escapeValue($this->title) . "',";
        $sql .= "'" . Database::escapeValue($this->alternate_title) . "',";
        $sql .= "'" . Database::escapeValue($this->target) . "',";
        $sql .= intval($this->category_id) . ",";
        $sql .= "'" . Database::escapeValue($this->content) . "',";
        $sql .= "'" . Database::escapeValue($this->language) . "',";

        if ($this->menu_image === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= "'" . Database::escapeValue($this->menu_image) . "',";
        }

        $sql .= intval($this->active) . ",";
        $this->created = time();
        $this->lastmodified = $this->created;
        $sql .= intval($this->created) . ",";
        $sql .= intval($this->lastmodified) . ",";
        $sql .= intval($this->author_id) . ",";
        $sql .= intval($this->group_id) . ",";
        $sql .= intval($this->lastchangeby) . ",";
// Views
        $sql .= "0,";

        $sql .= "'" . Database::escapeValue($this->menu) . "',";
        $sql .= intval($this->position) . ",";
        if ($this->parent_id === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= intval($this->parent_id) . ",";
        }

        $sql .= "'" . Database::escapeValue($this->access) . "',";
        if (is_present($this->meta_description)) {
            $sql .= "'" . Database::escapeValue($this->meta_description) . "',";
        } else {
            $sql .= "NULL,";
        }

        if (is_present($this->meta_keywords)) {
            $sql .= "'" . Database::escapeValue($this->meta_keywords) . "',";
        } else {
            $sql .= "NULL,";
        }
        if ($this->deleted_at === null) {
            $sql .= " NULL,";
        } else {
            $sql .= intval($this->deleted_at) . ",";
        }

        if ($this->theme === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= "'" . Database::escapeValue($this->theme) . "',";
        }

        if ($this->robots === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= "'" . Database::escapeValue($this->robots) . "',";
        }

        if ($this->custom_data === null) {
            $this->custom_data = [];
        }

        $json = json_encode(
                $this->custom_data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT
        );

        $sql .= "'" . Database::escapeValue($json) . "',";

        $sql .= "'" . Database::escapeValue($this->type) . "',";

        $sql .= "'" . Database::escapeValue($this->og_title) . "',";
        $sql .= "'" . Database::escapeValue($this->og_image) . "',";
        $sql .= "'" . Database::escapeValue($this->og_description) . "', ";
        $sql .= "'" . Database::escapeValue($this->cache_control) . "', ";
        $sql .= Database::escapeValue($this->hidden) . ", ";
        $sql .= Database::escapeValue($this->comments_enabled) . ",";
        $sql .= Database::escapeValue($this->show_headline);
        $sql .= ")";

        $result = Database::query($sql) or die(Database::error());
        $this->id = Database::getLastInsertID();
        $this->permissions->save($this->id);
        return $result;
    }

    public function update() {
        $result = null;
        if ($this->id === null) {
            return $this->create();
        }

        $this->lastmodified = time();

        if (get_user_id() > 0) {
            $this->lastchangeby = get_user_id();
        }

        $sql = "UPDATE " . tbname("content") . " ";

        $sql .= "set slug='" . Database::escapeValue($this->slug) . "',";
        $sql .= "title='" . Database::escapeValue($this->title) . "',";
        $sql .= "alternate_title='" .
                Database::escapeValue($this->alternate_title) . "',";
        $sql .= "target='" . Database::escapeValue($this->target) . "',";
        $sql .= "category_id = " . intval($this->category_id) . ",";
        $sql .= "content='" . Database::escapeValue($this->content) . "',";
        $sql .= "language='" . Database::escapeValue($this->language) . "',";

        if ($this->menu_image === null) {
            $sql .= "menu_image = NULL ,";
        } else {
            $sql .= "menu_image =  '" .
                    Database::escapeValue($this->menu_image) . "',";
        }

        $sql .= "active=" . intval($this->active) . ",";
        $sql .= "lastmodified=" . intval($this->lastmodified) . ",";
        $sql .= "author_id=" . intval($this->author_id) . ",";
        $sql .= "`group_id`=" . intval($this->group_id) . ",";
        $sql .= "lastchangeby=" . intval($this->lastchangeby) . ",";

        $sql .= "menu='" . Database::escapeValue($this->menu) . "',";
        $sql .= "position=" . intval($this->position) . ",";
        if ($this->parent_id === null) {
            $sql .= "parent_id = NULL ,";
        } else {
            $sql .= "parent_id=" . intval($this->parent_id) . ",";
        }

        $sql .= "access='" . Database::escapeValue($this->access) . "',";
        if (is_present($this->meta_description)) {
            $sql .= "meta_description='" .
                    Database::escapeValue($this->meta_description) . "',";
        } else {
            $sql .= "meta_description = null,";
        }

        if (is_present($this->meta_keywords)) {
            $sql .= "meta_keywords='" .
                    Database::escapeValue($this->meta_keywords) . "',";
        } else {
            $sql .= "meta_keywords = null,";
        }

        if ($this->deleted_at === null) {
            $sql .= "deleted_at=NULL ,";
        } else {
            $sql .= "deleted_at=" . intval($this->deleted_at) . ",";
        }

        if ($this->theme === null) {
            $sql .= "theme=NULL ,";
        } else {
            $sql .= "theme='" . Database::escapeValue($this->theme) . "',";
        }

        if ($this->robots === null) {
            $sql .= "robots=NULL ,";
        } else {
            $sql .= "robots='" . Database::escapeValue($this->robots) . "',";
        }

        if ($this->custom_data === null) {
            $this->custom_data = [];
        }

        $json = json_encode(
                $this->custom_data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT
        );

        $sql .= "custom_data='" . Database::escapeValue($json) . "',";

        $sql .= "type='" . Database::escapeValue($this->type) . "',";

        $sql .= "og_title='" . Database::escapeValue($this->og_title) . "',";
        $sql .= "og_image='" . Database::escapeValue($this->og_image) . "',";
        $sql .= "og_description='" . Database::escapeValue(
                        $this->og_description
                ) . "', ";
        $sql .= "hidden='" . Database::escapeValue($this->hidden) . "', ";
        $sql .= "comments_enabled=" . Database::escapeValue(
                        $this->comments_enabled) . ", ";
        $sql .= "show_headline=" . Database::escapeValue($this->show_headline)
                . ",";
        $sql .= "cache_control='" . Database::escapeValue($this->cache_control)
                . "' ";

        $sql .= " WHERE id = " . $this->id;

        $result = Database::query($sql);

        $this->permissions->save($this->id);

        return $result;
    }

    public function delete() {
        if ($this->deleted_at === null) {
            $this->deleted_at = time();
        }
        $this->save();
    }

    public function undelete(): void {
        $this->deleted_at = null;
        $this->save();
    }

    // returns true if this page contains a module
    public function containsModule(?string $module = null): bool {
        $content = $this->content;
        $content = str_replace("&quot;", "\"", $content);
        if ($module) {
            return stringContainsShortCodes($content, $module);
        }
        return stringContainsShortCodes($content);
    }

    // returns all modules contained in this page
    public function getEmbeddedModules(): array {
        $result = [];
        $content = str_ireplace("&quot;", '"', $this->content);
        preg_match_all("/\[module=\"([a-z_\-0-9]+)\"]/i", $content, $match);
        if (count($match) > 0) {
            for ($i = 0; $i <= count($match); $i ++) {
                $id = unhtmlspecialchars($match[1][$i]);
                if (!faster_in_array($id, $result)) {
                    $result[] = $id;
                }
            }
        }
        return $result;
    }

    // returns the parent page
    public function getParent(): ?Content {
        if (!$this->parent_id) {
            return null;
        }
        return ContentFactory::getByID($this->parent_id);
    }

    // returns the change history of this page
    public function getHistory(string $order = "date DESC"): array {
        if (!$this->getID()) {
            return [];
        }
        return VCS::getRevisionsByContentID($this->getID(), $order);
    }

    public function getPermissions(): PagePermissions {
        return $this->permissions;
    }

    public function setPermissions(PagePermissions $permissions): void {
        $this->permissions = $permissions;
    }

    // returns if the comments for the page are enabled
    // if "Comments enabled" has "[Default]" selected
    // then it returns if the comments are enabled in
    // the global settings
    public function areCommentsEnabled(): bool {
        $commentsEnabled = false;
        if (is_null($this->comments_enabled)) {
            $commentsEnabled = boolval(Settings::get("comments_enabled"));

            $commentable_content_types = Settings::get(
                            "commentable_content_types"
            );
            if ($commentable_content_types) {
                $commentable_content_types = splitAndTrim(
                        $commentable_content_types
                );

                if (count($commentable_content_types) > 0
                        and ! faster_in_array($this->type,
                                $commentable_content_types)) {
                    $commentsEnabled = false;
                }
            }
        } else {
            $commentsEnabled = boolval($this->comments_enabled);
        }
        return $commentsEnabled;
    }

    // TODO: write a more ressource friendly implementation
    // which doesn't load all comment datasets into the memory
    public function hasComments(): bool {
        return count($this->getComments()) > 0;
    }

    // this returns an array of all comments of this content
    public function getComments($order_by = "date desc"): array {
        return Comment::getAllByContentId($this->id, $order_by);
    }

    // returns the url of this page
    public function getUrl(?string $suffix = null): string {
        return ModuleHelper::getFullPageURLByID($this->id, $suffix);
    }

    public function checkAccess(): ?string {
        return checkAccess($this->access);
    }

    // set this page as frontpage
    public function makeFrontPage(): void {
        Settings::setLanguageSetting("frontpage", $this->slug, $this->language);
    }

    // returns true if this page is the frontpage
    public function isFrontPage(): bool {
        $frontPage = Settings::getLang("frontpage",
                        $this->language);
        return $frontPage === $this->slug;
    }

    public function getDeletedAt(): ?int {
        return $this->deleted_at;
    }

    public function isDeleted(): bool {
        return !is_null($this->getDeletedAt());
    }

    // returns true if this page is configured as the 403 error page
    public function isErrorPage403(): bool {
        $errorPage403 = intval(Settings::getLanguageSetting(
                        "error_page_403",
                        $this->language)
        );
        return $this->getID() && $this->getID() == $errorPage403;
    }

    // returns true if this page is configured as the 404 error page
    public function isErrorPage404(): bool {
        $errorPage404 = intval(
                Settings::getLanguageSetting("error_page_404",
                        $this->language)
        );
        return $this->getID() && $this->getID() == $errorPage404;
    }

    // returns true if this page is configured as an error page
    public function isErrorPage(): bool {
        return $this->isErrorPage403() or $this->isErrorPage404();
    }

    // set this page as error page for http status 403
    public function makeErrorPage403(bool $enabled = true): void {
        Settings::setLanguageSetting("error_page_403",
                $enabled ? $this->getID() : null,
                $this->language);
    }

    // set this page as error page for http status 404
    public function makeErrorPage404(bool $enabled = true): void {
        Settings::setLanguageSetting("error_page_404",
                $enabled ? $this->getID() : null,
                $this->language);
    }

}
