<?php

use UliCMS\Exceptions\NotImplementedException;
use UliCMS\Data\Content\Comment;

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
    // TODO: Rename this column to author_id to make it fit conventions
    public $author_id = null;
    public $group_id = null;
    public $lastchangeby = 1;
    public $views = 0;
    public $menu = "top";
    public $position = 0;
    public $cache_control = "auto";
    public $parent = null;
    public $access = "all";
    public $meta_description = "";
    public $meta_keywords = "";
    private $deleted_at = null;
    public $theme = null;
    public $custom_data = null;
    public $type = "page";
    public $og_title = "";
    public $og_type = "";
    public $og_image = "";
    public $og_description = "";
    public $hidden = 0;
    public $comments_enabled = null;
    private $permissions;

    public function __construct($id = null) {
        if ($this->custom_data === null) {
            $this->custom_data = array();
        }
        $this->permissions = new PagePermissions();
        if ($id) {
            $this->loadByID($id);
        }
    }

    protected function fillVarsByResult($result) {
        $this->id = $result->id;
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
        $this->parent = $result->parent;
        $this->access = $result->access;
        $this->meta_description = $result->meta_description;
        $this->meta_keywords = $result->meta_keywords;
        $this->deleted_at = $result->deleted_at;
        $this->theme = $result->theme;
        if ($this->customData === null) {
            $this->custom_data = array();
        }
        $this->custom_data = json_decode($result->custom_data, false);

        $this->type = $result->type;
        $this->og_title = $result->og_title;
        $this->og_type = $result->og_type;
        $this->og_image = $result->og_image;
        $this->og_description = $result->og_description;
        $this->cache_control = $result->cache_control;
        $this->hidden = $result->hidden;
        $this->comments_enabled = !is_null($result->comments_enabled) ? boolval($result->comments_enabled) : null;

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
        $query = Database::pQuery("SELECT * FROM `{prefix}content` where id = ?", array(
                    intval($id)
                        ), true);
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->fillVarsByResult($result);
        } else {
            throw new Exception("No content with id $id");
        }
    }

    public function loadBySlugAndLanguage($name, $language) {
        $name = Database::escapeValue($name);
        $language = Database::escapeValue($language);
        $query = Database::query("SELECT * FROM `" . tbname("content") . "` where `slug` = '$name' and `language` = '$language'");
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->fillVarsByResult($result);
        } else {
            throw new Exception("No such page");
        }
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
        $sql = "INSERT INTO `" . tbname("content") . "` (slug, title, alternate_title, target, category_id,
				content, language, menu_image, active, created, lastmodified, author_id,
				`group_id`, lastchangeby, views, menu, position, parent, access, meta_description, meta_keywords, deleted_at,
				theme, custom_data, `type`, og_title, og_type, og_image, og_description, cache_control, hidden, comments_enabled) VALUES (";

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
        if ($this->parent === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= intval($this->parent) . ",";
        }

        $sql .= "'" . Database::escapeValue($this->access) . "',";
        $sql .= "'" . Database::escapeValue($this->meta_description) . "',";
        $sql .= "'" . Database::escapeValue($this->meta_keywords) . "',";

        if ($this->deleted_at === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= intval($this->deleted_at) . ",";
        }

        if ($this->theme === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= "'" . Database::escapeValue($this->theme) . "',";
        }

        if ($this->custom_data === null) {
            $this->custom_data = array();
        }

        $json = json_encode($this->custom_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT);

        $sql .= "'" . Database::escapeValue($json) . "',";

        $sql .= "'" . Database::escapeValue($this->type) . "',";

        $sql .= "'" . Database::escapeValue($this->og_title) . "',";
        $sql .= "'" . Database::escapeValue($this->og_type) . "',";
        $sql .= "'" . Database::escapeValue($this->og_image) . "',";
        $sql .= "'" . Database::escapeValue($this->og_description) . "', ";
        $sql .= "'" . Database::escapeValue($this->cache_control) . "', ";
        $sql .= Database::escapeValue($this->hidden) . ", ";
        $sql .= Database::escapeValue($this->comments_enabled);
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
        $sql .= "alternate_title='" . Database::escapeValue($this->alternate_title) . "',";
        $sql .= "target='" . Database::escapeValue($this->target) . "',";
        $sql .= "category_id = " . intval($this->category_id) . ",";
        $sql .= "content='" . Database::escapeValue($this->content) . "',";
        $sql .= "language='" . Database::escapeValue($this->language) . "',";

        if ($this->menu_image === null) {
            $sql .= "menu_image = NULL ,";
        } else {
            $sql .= "menu_image =  '" . Database::escapeValue($this->menu_image) . "',";
        }

        $sql .= "active=" . intval($this->active) . ",";
        $sql .= "lastmodified=" . intval($this->lastmodified) . ",";
        $sql .= "author_id=" . intval($this->author_id) . ",";
        $sql .= "`group_id`=" . intval($this->group_id) . ",";
        $sql .= "lastchangeby=" . intval($this->lastchangeby) . ",";

        $sql .= "menu='" . Database::escapeValue($this->menu) . "',";
        $sql .= "position=" . intval($this->position) . ",";
        if ($this->parent === null) {
            $sql .= "parent = NULL ,";
        } else {
            $sql .= "parent=" . intval($this->parent) . ",";
        }

        $sql .= "access='" . Database::escapeValue($this->access) . "',";
        $sql .= "meta_description='" . Database::escapeValue($this->meta_description) . "',";
        $sql .= "meta_keywords='" . Database::escapeValue($this->meta_keywords) . "',";

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

        if ($this->custom_data === null) {
            $this->custom_data = array();
        }

        $json = json_encode($this->custom_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT);

        $sql .= "custom_data='" . Database::escapeValue($json) . "',";

        $sql .= "type='" . Database::escapeValue($this->type) . "',";

        $sql .= "og_title='" . Database::escapeValue($this->og_title) . "',";
        $sql .= "og_type='" . Database::escapeValue($this->og_type) . "',";
        $sql .= "og_image='" . Database::escapeValue($this->og_image) . "',";
        $sql .= "og_description='" . Database::escapeValue($this->og_description) . "', ";
        $sql .= "hidden='" . Database::escapeValue($this->hidden) . "', ";
        $sql .= "comments_enabled=" . Database::escapeValue($this->comments_enabled) . ", ";
        $sql .= "cache_control='" . Database::escapeValue($this->cache_control) . "' ";

        $sql .= " WHERE id = " . $this->id;

        $result = Database::query($sql) or die(Database::getLastError());

        $this->permissions->save($this->id);

        return $result;
    }

    public function delete() {
        if ($this->deleted_at === null) {
            $this->deleted_at = time();
        }
        $this->save();
    }

    public function undelete() {
        $this->deleted_at = null;
        $this->save();
    }

    public function containsModule($module = false) {
        $content = $this->content;
        $content = str_replace("&quot;", "\"", $content);
        if ($module) {
            return preg_match("/\[module=\"" . preg_quote($module) . "\"\]/", $content);
        } else {
            return preg_match("/\[module=\".+\"\]/", $content);
        }
    }

    public function getEmbeddedModules() {
        $result = array();
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

    public function getPermissions() {
        return $this->permissions;
    }

    public function setPermissions($permissions) {
        $this->permissions = $permissions;
    }

    // returns if the comments for the page are enabled
    // if "Comments enabled" has "[Default]" selected
    // then it returns if the comments are enabled in
    // the global settings
    public function areCommentsEnabled() {
        $commentsEnabled = false;
        if (is_null($this->comments_enabled)) {
            $commentsEnabled = boolval(Settings::get("comments_enabled"));

            $commentable_content_types = Settings::get("commentable_content_types");
            if ($commentable_content_types) {
                $commentable_content_types = splitAndTrim($commentable_content_types);

                if (count($commentable_content_types) > 0 and ! faster_in_array($this->type, $commentable_content_types)) {
                    $commentsEnabled = false;
                }
            }
        } else {
            $commentsEnabled = boolval($this->comments_enabled);
        }
        return $commentsEnabled;
    }

    public function hasComments() {
        // TODO: write a more ressource friendly implementation
        // which doesn't load all comment datasets into the memory
        return count($this->getComments()) > 0;
    }

    // this returns an array of all comments of this content
    public function getComments($order_by = "date desc") {
        return Comment::getAllByContentId($this->id, $order_by);
    }

    public function getUrl($suffix = null) {
        return ModuleHelper::getFullPageURLByID($this->id, $suffix);
    }

    public function checkAccess() {
        return checkAccess($this->access);
    }

}
