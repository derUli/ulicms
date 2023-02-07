<?php

namespace UliCMS\Models\Content;

use Model;
use Database;
use Request;
use function getDomainByLanguage;
use Settings;

class Language extends Model
{
    protected $id = null;
    private $name = null;
    private $language_code = null;

    public function __construct($id = null)
    {
        if ($id !== null) {
            $this->loadById($id);
        }
    }

    public function fillVars($result = null)
    {
        if ($result and Database::getNumRows($result) > 0) {
            $result = Database::fetchObject($result);
            $this->id = $result->id;
            $this->name = $result->name;
            $this->language_code = $result->language_code;
        } else {
            $this->id = null;
            $this->name = null;
            $this->language_code = null;
        }
    }

    public function loadById($id)
    {
        $args = array(
            $id
        );
        $sql = "SELECT * FROM `{prefix}languages` where id = ?";
        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    public function loadByLanguageCode(string $language_code): void
    {
        $args = array(
            strval($language_code)
        );
        $sql = "SELECT * FROM `{prefix}languages` where language_code = ?";
        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLanguageCode(): ?string
    {
        return $this->language_code;
    }

    public function setName($val)
    {
        $this->name = !is_null($val) ? (string)$val : null;
    }

    public function setLanguageCode($val)
    {
        $this->language_code = !is_null($val) ? (string)$val : null;
    }

    public function save()
    {
        if (is_null($this->id)) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    protected function insert()
    {
        $sql = "INSERT INTO `{prefix}languages` (name, language_code) "
                . "values (?,?)";
        $args = array(
            $this->name,
            $this->language_code
        );
        Database::pQuery($sql, $args, true);
        $this->id = Database::getLastInsertID();
    }

    protected function update()
    {
        $sql = "UPDATE `{prefix}languages` set name = ?, language_code = ? "
                . "where id = ?";
        $args = array(
            $this->name,
            $this->language_code,
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function delete()
    {
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

    public function makeDefaultLanguage(): void
    {
        if (!is_null($this->language_code)) {
            Settings::set("default_language", $this->language_code);
        }
    }

    // returns true if this language is the default language
    public function isDefaultLanguage(): bool
    {
        return $this->language_code == Settings::get("default_language");
    }

    // returns true if this is the user's current language
    public function isCurrentLanguage(): bool
    {
        $current_language = is_admin_dir() ?
                getSystemLanguage() : getCurrentLanguage();
        return $this->language_code == $current_language;
    }

    // returns an array of all languages
    public static function getAllLanguages(string $order = "id"): array
    {
        $datasets = [];
        $sql = "select id from `{prefix}languages` order by $order";
        $result = Database::query($sql, true);
        while ($row = Database::fetchObject($result)) {
            $datasets[] = new Language($row->id);
        }
        return $datasets;
    }

    public function __toString(): string
    {
        return $this->getLanguageCode();
    }

    // returns a link to view the website in this language
    public function getLanguageLink(): string
    {
        $domain = getDomainByLanguage($this->language_code);
        if ($domain) {
            $url = Request::getProtocol($domain);
        } else {
            $url = "./?language=" . $this->language_code;
        }
        return $url;
    }
}
