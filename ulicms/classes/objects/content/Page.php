<?php

class Page extends Content
{

    public $id = null;

    public $systemname = "";

    public $title = "";

    public $alternate_title = "";

    public $target = "_self";

    public $category = 1;

    public $content = "";

    public $language = "de";

    public $menu_image = null;

    public $active = 1;

    public $created = 0;

    public $lastmodified = 0;

    public $autor = null;


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

    public $html_file = null;

    public $theme = null;

    public $custom_data = null;

    public $type = "page";

    public $og_title = "";

    public $og_type = "";

    public $og_image = "";
    public $og_description = "";

    public $hidden = 0;

    private $permissions;

    public function __construct()
    {
        if ($this->custom_data === null) {
            $this->custom_data = array();
        }
        $this->permissions = new EntityPermissions("content");
    }

    protected function fillVarsByResult($result)
    {
        $this->id = $result->id;
        $this->systemname = $result->systemname;
        $this->title = $result->title;
        $this->alternate_title = $result->alternate_title;
        $this->target = $result->target;
        $this->category = $result->category;
        $this->content = $result->content;
        $this->language = $result->language;
        $this->menu_image = $result->menu_image;
        $this->active = $result->active;
        $this->created = $result->created;
        $this->lastmodified = $result->lastmodified;
        $this->autor = $result->autor;
        $this->lastchangeby = $result->lastchangeby;
        $this->views = $result->views;
        $this->menu = $result->menu;
        $this->position = $result->position;
        $this->parent = $result->parent;
        $this->access = $result->access;
        $this->meta_description = $result->meta_description;
        $this->meta_keywords = $result->meta_keywords;
        $this->deleted_at = $result->deleted_at;
        $this->html_file = $result->html_file;
        $this->theme = $result->theme;
        if ($this->customData === null) {
            $this->custom_data = array();
        }
        $this->custom_data = json_decode($result->custom_data, true);
        
        $this->type = $result->type;
        $this->og_title = $result->og_title;
        $this->og_type = $result->og_type;
        $this->og_image = $result->og_image;
        $this->og_description = $result->og_description;
        $this->cache_control = $result->cache_control;
        $this->hidden = $result->hidden;
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

    public function loadByID($id)
    {
        $query = DB::pQuery("SELECT * FROM `{prefix}content` where id = ?", array(
            intval($id)
        ), true);
        if (DB::getNumRows($query) > 0) {
            $result = DB::fetchObject($query);
            $this->fillVarsByResult($result);
        } else {
            throw new Exception("No content with id $id");
        }
    }

    public function loadBySystemnameAndLanguage($name, $language)
    {
        $name = DB::escapeValue($name);
        $language = DB::escapeValue($language);
        $query = DB::query("SELECT * FROM `" . tbname("content") . "` where `systemname` = '$name' and `language` = '$language'");
        if (DB::getNumRows($query) > 0) {
            $result = DB::fetchObject($query);
            $this->fillVarsByResult($result);
        } else {
            throw new Exception("No such page");
        }
    }

    public function save()
    {
        $retval = null;
        if ($this->id === null) {
            $retval = $this->create();
        } else {
            $retval = $this->update();
        }
        return $retval;
    }

    public function create()
    {
        $sql = "INSERT INTO `" . tbname("content") . "` (systemname, title, alternate_title, target, category,
				content, language, menu_image, active, created, lastmodified, autor, 
				lastchangeby, views, menu, position, parent, access, meta_description, meta_keywords, deleted_at,
				html_file, theme, custom_data, `type`, og_title, og_type, og_image, og_description, cache_control, hidden) VALUES (";
        
        $sql .= "'" . DB::escapeValue($this->systemname) . "',";
        $sql .= "'" . DB::escapeValue($this->title) . "',";
        $sql .= "'" . DB::escapeValue($this->alternate_title) . "',";
        $sql .= "'" . DB::escapeValue($this->target) . "',";
        $sql .= intval($this->category) . ",";
        $sql .= "'" . DB::escapeValue($this->content) . "',";
        $sql .= "'" . DB::escapeValue($this->language) . "',";
        
        if ($this->menu_image === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= "'" . DB::escapeValue($this->menu_image) . "',";
        }
        
        $sql .= intval($this->active) . ",";
        $this->created = time();
        $this->lastmodified = $this->created;
        $sql .= intval($this->created) . ",";
        $sql .= intval($this->lastmodified) . ",";
        $sql .= intval($this->autor) . ",";
        $sql .= intval($this->lastchangeby) . ",";
        // Views
        $sql .= "0,";
        
        $sql .= "'" . DB::escapeValue($this->menu) . "',";
        $sql .= intval($this->position) . ",";
        if ($this->parent === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= intval($this->parent) . ",";
        }
        
        $sql .= "'" . DB::escapeValue($this->access) . "',";
        $sql .= "'" . DB::escapeValue($this->meta_description) . "',";
        $sql .= "'" . DB::escapeValue($this->meta_keywords) . "',";
        
        if ($this->deleted_at === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= intval($this->deleted_at) . ",";
        }
        
        if ($this->html_file === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= "'" . DB::escapeValue($this->html_file) . "',";
        }
        
        if ($this->theme === null) {
            $sql .= " NULL ,";
        } else {
            $sql .= "'" . DB::escapeValue($this->theme) . "',";
        }
        
        if ($this->custom_data === null) {
            $this->custom_data = array();
        }
        
        $json = json_encode($this->custom_data, JSON_FORCE_OBJECT);
        
        $sql .= "'" . DB::escapeValue($json) . "',";
        
        $sql .= "'" . DB::escapeValue($this->type) . "',";
        
        $sql .= "'" . DB::escapeValue($this->og_title) . "',";
        $sql .= "'" . DB::escapeValue($this->og_type) . "',";
        $sql .= "'" . DB::escapeValue($this->og_image) . "',";
        $sql .= "'" . DB::escapeValue($this->og_description) . "', ";
        $sql .= "'" . DB::escapeValue($this->cache_control) . "', ";
        $sql .= DB::escapeValue($this->hidden);
        $sql .= ")";
        
        $result = DB::Query($sql) or die(DB::error());
        $this->id = DB::getLastInsertID();
        $this->permissions->save($this->id);
        return $result;
    }

    public function update()
    {
        $result = null;
        if ($this->id === null) {
            return $this->create();
        }
        
        $this->lastmodified = time();
        
        if (get_user_id() > 0) {
            $this->lastchangeby = get_user_id();
        }
        
        $sql = "UPDATE " . tbname("content") . " ";
        
        $sql .= "set systemname='" . DB::escapeValue($this->systemname) . "',";
        $sql .= "title='" . DB::escapeValue($this->title) . "',";
        $sql .= "alternate_title='" . DB::escapeValue($this->alternate_title) . "',";
        $sql .= "target='" . DB::escapeValue($this->target) . "',";
        $sql .= "category = " . intval($this->category) . ",";
        $sql .= "content='" . DB::escapeValue($this->content) . "',";
        $sql .= "language='" . DB::escapeValue($this->language) . "',";
        
        if ($this->menu_image === null) {
            $sql .= "menu_image = NULL ,";
        } else {
            $sql .= "menu_image =  '" . DB::escapeValue($this->menu_image) . "',";
        }
        
        $sql .= "active=" . intval($this->active) . ",";
        $sql .= "lastmodified=" . intval($this->lastmodified) . ",";
        $sql .= "autor=" . intval($this->autor) . ",";
        $sql .= "lastchangeby=" . intval($this->lastchangeby) . ",";
        
        $sql .= "menu='" . DB::escapeValue($this->menu) . "',";
        $sql .= "position=" . intval($this->position) . ",";
        if ($this->parent === null) {
            $sql .= "parent = NULL ,";
        } else {
            $sql .= "parent=" . intval($this->parent) . ",";
        }
        
        $sql .= "access='" . DB::escapeValue($this->access) . "',";
        $sql .= "meta_description='" . DB::escapeValue($this->meta_description) . "',";
        $sql .= "meta_keywords='" . DB::escapeValue($this->meta_keywords) . "',";
        
        if ($this->deleted_at === null) {
            $sql .= "deleted_at=NULL ,";
        } else {
            $sql .= "deleted_at=" . intval($this->deleted_at) . ",";
        }
        
        if ($this->html_file === null) {
            $sql .= "html_file=NULL ,";
        } else {
            $sql .= "html_file='" . DB::escapeValue($this->html_file) . "',";
        }
        
        if ($this->theme === null) {
            $sql .= "theme=NULL ,";
        } else {
            $sql .= "theme='" . DB::escapeValue($this->theme) . "',";
        }
        
        if ($this->custom_data === null) {
            $this->custom_data = array();
        }
        
        $json = json_encode($this->custom_data, JSON_FORCE_OBJECT);
        
        $sql .= "custom_data='" . DB::escapeValue($json) . "',";
        
        $sql .= "type='" . DB::escapeValue($this->type) . "',";
        
        $sql .= "og_title='" . DB::escapeValue($this->og_title) . "',";
        $sql .= "og_type='" . DB::escapeValue($this->og_type) . "',";
        $sql .= "og_image='" . DB::escapeValue($this->og_image) . "',";
        $sql .= "og_description='" . DB::escapeValue($this->og_description) . "', ";
        $sql .= "hidden='" . DB::escapeValue($this->hidden) . "', ";
        $sql .= "cache_control='" . DB::escapeValue($this->cache_control) . "' ";
        
        $sql .= " WHERE id = " . $this->id;
        
        $result = DB::query($sql) or die(DB::getLastError());
        
        $this->permissions->save($this->id);
        
        return $result;
    }

    public function delete()
    {
        if ($this->deleted_at === null) {
            $this->deleted_at = time();
        }
        $this->save();
    }

    public function undelete()
    {
        $this->deleted_at = null;
        $this->save();
    }

    public function containsModule($module = false)
    {
        $content = $this->content;
        $content = str_replace("&quot;", "\"", $content);
        if ($module) {
            return preg_match("/\[module=\"" . preg_quote($module) . "\"\]/", $content);
        } else {
            return preg_match("/\[module=\".+\"\]/", $content);
        }
    }

    public function getEmbeddedModules()
    {
        $result = array();
        $content = str_ireplace("&quot;", '"', $this->content);
        preg_match_all("/\[module=\"([a-z_\-0-9]+)\"]/i", $content, $match);
        if (count($match) > 0) {
            for ($i = 0; $i <= count($match); $i ++) {
                $id = unhtmlspecialchars($match[1][$i]);
                if (! faster_in_array($id, $result)) {
                    $result[] = $id;
                }
            }
        }
        return $result;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }
}
