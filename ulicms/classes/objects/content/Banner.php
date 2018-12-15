<?php

class Banner
{

    public $id = null;

    public $name = "";

    public $link_url = "";

    public $image_url = "";

    public $category = 1;

    private $type = "gif";

    public $html = "";

    public $language = null;

    public $enabled = true;

    public function __construct($id = null)
    {
        if ($id) {
            $this->loadByID($id);
        }
    }

    public function loadByID($id)
    {
        $id = intval($id);
        $query = Database::query("SELECT * FROM `" . tbname("banner") . "` where id = $id");
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->fillVarsByResult($result);
        } else {
            throw new Exception("No banner with id $id");
        }
    }

    public function loadRandom()
    {
        $id = intval($id);
        $query = Database::query("SELECT * FROM `" . tbname("banner") . "` order by rand() LIMIT 1");
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->fillVarsByResult($result);
        }
    }

    private function fillVarsByResult($result)
    {
        $this->id = $result->id;
        $this->name = $result->name;
        $this->link_url = $result->link_url;
        $this->image_url = $result->image_url;
        $this->category = $result->category;
        $this->type = $result->type;
        $this->html = $result->html;
        $this->language = $result->language;
        $this->enabled = boolval($result->enabled);
    }

    public function setType($type)
    {
        $allowedTypes = array(
            "gif",
            "html"
        );
        if (faster_in_array($type, $allowedTypes)) {
            $this->type = $type;
            return true;
        }
        
        return false;
    }

    public function getType()
    {
        return $this->type;
    }

    public function save()
    {
        $retval = false;
        if ($this->id != null) {
            $retval = $this->update();
        }
        {
            $retval = $this->create();
        }
        return $retval;
    }

    public function create()
    {
        if ($this->id != null) {
            return $this->update();
        }
        $sql = "INSERT INTO " . tbname("banner") . "(name, link_url, image_url, category, type, html, language, enabled) values (";
        if ($this->name === null) {
            $sql .= "NULL, ";
        } else {
            $sql .= "'" . Database::escapeValue($this->name) . "',";
        }
        if ($this->link_url === null) {
            $sql .= "NULL, ";
        } else {
            $sql .= "'" . Database::escapeValue($this->link_url) . "',";
        }
        if ($this->image_url === null) {
            $sql .= "NULL, ";
        } else {
            $sql .= "'" . Database::escapeValue($this->image_url) . "',";
        }
        if ($this->category === null) {
            $sql .= "NULL, ";
        } else {
            $sql .= "'" . intval($this->category) . "',";
        }
        if ($this->type === null) {
            $sql .= "NULL, ";
        } else {
            $sql .= "'" . Database::escapeValue($this->type) . "',";
        }
        if ($this->html === null) {
            $sql .= "NULL, ";
        } else {
            $sql .= "'" . Database::escapeValue($this->html) . "',";
        }
        if ($this->language === null) {
            $sql .= "NULL, ";
        } else {
            $sql .= "'" . Database::escapeValue($this->language) . "',";
        }
        
        $sql .= intval($this->enabled);
        
        $sql .= ")";
        
        $result = Database::query($sql);
        $this->id = Database::getLastInsertID();
        
        return $result;
    }

    public function update()
    {
        if ($this->id === null) {
            return $this->create();
        }
        $sql = "UPDATE " . tbname("banner") . " set ";
        
        if ($this->name === null) {
            $sql .= "name=NULL, ";
        } else {
            $sql .= "name='" . Database::escapeValue($this->name) . "',";
        }
        if ($this->link_url === null) {
            $sql .= "link_url = NULL, ";
        } else {
            $sql .= "link_url='" . Database::escapeValue($this->link_url) . "',";
        }
        if ($this->image_url === null) {
            $sql .= "image_url=NULL, ";
        } else {
            $sql .= "image_url='" . Database::escapeValue($this->image_url) . "',";
        }
        if ($this->category === null) {
            $sql .= "category=NULL, ";
        } else {
            $sql .= "category='" . intval($this->category) . "',";
        }
        if ($this->type === null) {
            $sql .= "`type`=NULL, ";
        } else {
            $sql .= "`type`='" . Database::escapeValue($this->type) . "',";
        }
        if ($this->html === null) {
            $sql .= "html=NULL, ";
        } else {
            $sql .= "html='" . Database::escapeValue($this->html) . "',";
        }
        if ($this->language === null) {
            $sql .= "language=NULL, ";
        } else {
            $sql .= "language='" . Database::escapeValue($this->language) . "', ";
        }
        $sql .= "enabled = " . intval($this->enabled);
        
        $sql .= " where id = " . intval($this->id);
        return Database::query($sql);
    }

    public function delete()
    {
        $retval = false;
        if ($this->id !== null) {
            $sql = "DELETE from " . tbname("banner") . " where id = " . $this->id;
            $retval = Database::Query($sql);
            $this->id = null;
        }
        return $retval;
    }
}
	
