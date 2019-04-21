<?php

class Banner {

    private $id = null;
    private $name = "";
    private $link_url = "";
    private $image_url = "";
    private $category_id = 1;
    private $type = "gif";
    private $html = "";
    private $language = null;
    private $enabled = true;
    private $date_from = null;
    private $date_to = null;

    public function __construct($id = null) {
        if ($id) {
            $this->loadByID($id);
        }
    }

    public function loadByID($id) {
        $id = intval($id);
        $query = Database::query("SELECT * FROM `" . tbname("banner") . "` where id = $id");
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->fillVarsByResult($result);
        } else {
            throw new Exception("No banner with id $id");
        }
    }

    public function loadRandom() {
        $id = intval($id);
        $query = Database::query("SELECT * FROM `" . tbname("banner") . "` order by rand() LIMIT 1");
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->fillVarsByResult($result);
        }
    }

    private function fillVarsByResult($result) {
        $this->id = $result->id;
        $this->name = $result->name;
        $this->link_url = $result->link_url;
        $this->image_url = $result->image_url;
        $this->category_id = $result->category_id;
        $this->type = $result->type;
        $this->html = $result->html;
        $this->language = $result->language;
        $this->enabled = boolval($result->enabled);
        $this->date_from = $result->date_from;
        $this->date_to = $result->date_to;
    }

    public function save() {
        $retval = false;
        if ($this->id != null) {
            $retval = $this->update();
        } {
            $retval = $this->create();
        }
        return $retval;
    }

    public function create() {
        if ($this->id != null) {
            return $this->update();
        }
        $sql = "INSERT INTO " . tbname("banner") . "(name, link_url, image_url, category_id, type, html, language, date_from, date_to, enabled) values (";
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
        if ($this->category_id === null) {
            $sql .= "NULL, ";
        } else {
            $sql .= "'" . intval($this->category_id) . "',";
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

        if ($this->date_from === null) {
            $sql .= "NULL, ";
        } else {
            $sql .= "'" . Database::escapeValue($this->date_from) . "',";
        }

        if ($this->date_to === null) {
            $sql .= "NULL, ";
        } else {
            $sql .= "'" . Database::escapeValue($this->date_to) . "',";
        }

        $sql .= intval($this->enabled);

        $sql .= ")";

        $result = Database::query($sql);
        $this->id = Database::getLastInsertID();

        return $result;
    }

    public function update() {
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
        if ($this->category_id === null) {
            $sql .= "category_id=NULL, ";
        } else {
            $sql .= "category_id='" . intval($this->category_id) . "',";
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

        if ($this->date_from === null) {
            $sql .= "date_from=NULL, ";
        } else {
            $sql .= "date_from='" . Database::escapeValue($this->date_from) . "', ";
        }
        if ($this->date_to === null) {
            $sql .= "date_to=NULL, ";
        } else {
            $sql .= "date_to='" . Database::escapeValue($this->date_to) . "', ";
        }

        $sql .= "enabled = " . intval($this->enabled);

        $sql .= " where id = " . intval($this->id);
        return Database::query($sql);
    }

    public function getId() {
        return $this->id;
    }

    public function setDateFrom($val) {
        if (is_null($val) or is_string($val)) {
            $this->date_from = $val;
        } else if (is_numeric($val)) {
            $this->date_from = date("Y-m-d", $val);
        } else {
            throw new InvalidArgumentException("not a date and not a timestamp");
        }
    }

    public function setDateTo($val) {
        if (is_null($val) or is_string($val)) {
            $this->date_to = $val;
        } else if (is_numeric($val)) {
            $this->date_to = date("Y-m-d", $val);
        } else {
            throw new InvalidArgumentException("not a date and not a timestamp");
        }
    }

    public function setType($type) {
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

    public function getType() {
        return $this->type;
    }

    public function getHtml() {
        return $this->html;
    }

    public function setHtml($val) {
        $this->html = !is_null($val) ? strval($val) : null;
    }

    public function getDateFrom() {
        return $this->date_from;
    }

    public function getDateTo() {
        return $this->date_to;
    }

    public function getCategoryId() {
        return $this->category_id;
    }

    public function setCategoryId($val) {
        $this->category_id = is_numeric($val) ? intval($val) : null;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($val) {
        $this->language = !is_null($val) ? strval($val) : null;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($val) {
        $this->name = !is_null($val) ? strval($val) : null;
    }

    public function getImageUrl() {
        return $this->image_url;
    }

    public function setImageUrl($val) {
        $this->image_url = !is_null($val) ? strval($val) : null;
    }

    public function getLinkUrl() {
        return $this->link_url;
    }

    public function setLinkUrl($val) {
        $this->link_url = !is_null($val) ? strval($val) : null;
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function setEnabled($val) {
        $this->enabled = boolval($val);
    }

    public function delete() {
        $retval = false;
        if ($this->id !== null) {
            $sql = "DELETE from " . tbname("banner") . " where id = " . $this->id;
            $retval = Database::Query($sql);
            $this->id = null;
        }
        return $retval;
    }

}
