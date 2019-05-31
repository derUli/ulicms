<?php

namespace UliCMS\Models\Content;

use Model;
use Database;
use Request;
use function getDomainByLanguage;
use Settings;

class Language extends Model {

    protected $id = null;
    private $name = null;
    private $language_code = null;

    public function __construct($id = null) {
        if (!is_null($id)) {
            $this->loadById($id);
        }
    }

    public function fillVars($query = null) {
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->id = $result->id;
            $this->name = $result->name;
            $this->language_code = $result->language_code;
        } else {
            $this->id = null;
            $this->name = null;
            $this->language_code = null;
        }
    }

    public function loadById($id) {
        $args = array(
            $id
        );
        $sql = "SELECT * FROM `{prefix}languages` where id = ?";
        $query = Database::pQuery($sql, $args, true);
        $this->fillVars($query);
    }

    public function loadByLanguageCode($language_code) {
        $args = array(
            strval($language_code)
        );
        $sql = "SELECT * FROM `{prefix}languages` where language_code = ?";
        $query = Database::pQuery($sql, $args, true);
        $this->fillVars($query);
    }

    public function getID() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getLanguageCode() {
        return $this->language_code;
    }

    public function setId($val) {
        $this->id = !is_null($val) ? intval($val) : null;
    }

    public function setName($val) {
        $this->name = !is_null($val) ? strval($val) : null;
    }

    public function setLanguageCode($val) {
        $this->language_code = !is_null($val) ? strval($val) : null;
    }

    public function save() {
        if (is_null($this->id)) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    protected function insert() {
        $sql = "INSERT INTO `{prefix}languages` (name, language_code) values (?,?)";
        $args = array(
            $this->name,
            $this->language_code
        );
        Database::pQuery($sql, $args, true);
        $this->id = Database::getLastInsertID();
    }

    protected function update() {
        $sql = "UPDATE `{prefix}languages` set name = ?, language_code = ? where id = ?";
        $args = array(
            $this->name,
            $this->language_code,
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function delete() {
        if (!is_null($this->id)) {
            $sql = "DELETE FROM `{prefix}languages` where id = ?";
            $args = array(
                $this->id
            );
            Database::pQuery($sql, $args, true);
            $this->id = null;
            $this->name = null;
            $this->language_code = null;
        }
    }

    public function makeDefaultLanguage() {
        if (!is_null($this->language_code)) {
            Settings::set("default_language", $this->language_code);
        }
    }

    public function isDefaultLanguage() {
        return $this->language_code == Settings::get("default_language");
    }

    public function isCurrentLanguage() {
        $current_language = is_admin_dir() ? getSystemLanguage() : getCurrentLanguage();
        return $this->language_code == $current_language;
    }

    public static function getAllLanguages($order = "id") {
        $result = array();
        $sql = "select id from `{prefix}languages` order by $order";
        $query = Database::query($sql, true);
        while ($row = Database::fetchObject($query)) {
            $result[] = new Language($row->id);
        }
        return $result;
    }

    public function __toString() {
        return $this->getLanguageCode();
    }

    public function getLanguageLink() {
        $domain = getDomainByLanguage($this->language_code);
        if ($domain) {
            $url = Request::getProtocol($domain);
        } else {
            $url = "./?language=" . $this->language_code;
        }
        return $url;
    }

}
