<?php

declare(strict_types=1);

namespace App\Models\Content\Advertisement;

use Template;
use Database;
use Model;
use InvalidArgumentException;
use App\Exceptions\DatasetNotFoundException;

// advertisement banners can be html codes or classic gif banners
class Banner extends Model {

    protected $id = null;
    private $name = null;
    private $link_url = null;
    private $image_url = null;
    private $category_id = 1;
    private $type = "gif";
    private $html = null;
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
        $id = (int)$id;
        $result = Database::query("SELECT * FROM `" . tbname("banner") .
                        "` where id = $id");
        if (Database::getNumRows($result) > 0) {
            $result = Database::fetchObject($result);
            $this->fillVars($result);
        } else {
            throw new DatasetNotFoundException("No banner with id $id");
        }
    }

    public function loadRandom(): void {
        $result = Database::query("SELECT * FROM `{prefix}banner` "
                        . "order by rand() LIMIT 1", true);
        if (Database::getNumRows($result) > 0) {
            $dataset = Database::fetchObject($result);
            $this->fillVars($dataset);
        }
    }

    protected function fillVars($result = null) {
        $this->id = (int)$result->id;
        $this->name = $result->name;
        $this->link_url = $result->link_url;
        $this->image_url = $result->image_url;
        $this->category_id = (int)$result->category_id;
        $this->type = $result->type;
        $this->html = $result->html;
        $this->language = $result->language;
        $this->enabled = (bool)$result->enabled;
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
        return $this->insert();
    }

    public function insert() {
        $sql = "INSERT INTO " . tbname("banner") . "(name, link_url, image_url, "
                . "category_id, type, html, language, date_from, date_to, "
                . "enabled) values (";
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

        $sql .= "'" . Database::escapeValue($this->type) . "',";

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
            $sql .= "link_url='" . Database::escapeValue($this->link_url) .
                    "',";
        }
        if ($this->image_url === null) {
            $sql .= "image_url=NULL, ";
        } else {
            $sql .= "image_url='" . Database::escapeValue($this->image_url) .
                    "',";
        }
        if ($this->category_id === null) {
            $sql .= "category_id=NULL, ";
        } else {
            $sql .= "category_id='" . intval($this->category_id) . "',";
        }

        $sql .= "`type`='" . Database::escapeValue($this->type) . "',";

        if ($this->html === null) {
            $sql .= "html=NULL, ";
        } else {
            $sql .= "html='" . Database::escapeValue($this->html) . "',";
        }
        if ($this->language === null) {
            $sql .= "language=NULL, ";
        } else {
            $sql .= "language='" . Database::escapeValue($this->language) .
                    "', ";
        }

        if ($this->date_from === null) {
            $sql .= "date_from=NULL, ";
        } else {
            $sql .= "date_from='" . Database::escapeValue($this->date_from) .
                    "', ";
        }
        if ($this->date_to === null) {
            $sql .= "date_to=NULL, ";
        } else {
            $sql .= "date_to='" . Database::escapeValue($this->date_to) .
                    "', ";
        }

        $sql .= "enabled = " . intval($this->enabled);

        $sql .= " where id = " . intval($this->id);
        return Database::query($sql);
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setDateFrom($val): void {
        if ($val === NULL or is_string($val)) {
            $this->date_from = $val;
        } elseif (is_numeric($val)) {
            $this->date_from = date("Y-m-d", $val);
        } else {
            throw new InvalidArgumentException(
                            "not a date and not a timestamp"
            );
        }
    }

    public function setDateTo($val): void {
        if ($val === NULL or is_string($val)) {
            $this->date_to = $val;
        } elseif (is_numeric($val)) {
            $this->date_to = date("Y-m-d", $val);
        } else {
            throw new InvalidArgumentException(
                            "not a date and not a timestamp"
            );
        }
    }

    public function setType(string $type): void {
        $allowedTypes = array(
            "gif",
            "html"
        );
        if (in_array($type, $allowedTypes)) {
            $this->type = $type;
        }
    }

    public function getType(): string {
        return $this->type;
    }

    public function getHtml(): ?string {
        return $this->html;
    }

    public function setHtml(?string $val): void {
        $this->html = $val !== NULL ? (string)$val : null;
    }

    public function getDateFrom(): ?string {
        return $this->date_from;
    }

    public function getDateTo(): ?string {
        return $this->date_to;
    }

    public function getCategoryId(): ?int {
        return $this->category_id;
    }

    public function setCategoryId(?int $val): void {
        $this->category_id = is_numeric($val) ? (int)$val : null;
    }

    public function getLanguage(): ?string {
        return $this->language;
    }

    public function setLanguage(?string $val): void {
        $this->language = $val !== NULL ? (string)$val : null;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName($val) {
        $this->name = $val !== NULL ? (string)$val : null;
    }

    public function getImageUrl(): ?string {
        return $this->image_url;
    }

    public function setImageUrl(?string $val): void {
        $this->image_url = $val !== NULL ? (string)$val : null;
    }

    public function getLinkUrl(): ?string {
        return $this->link_url;
    }

    public function setLinkUrl(?string $val): void {
        $this->link_url = $val !== NULL ? (string)$val : null;
    }

    public function getEnabled(): bool {
        return $this->enabled;
    }

    public function setEnabled(bool $val): void {
        $this->enabled = (bool)$val;
    }

    public function delete() {
        $retval = false;
        if ($this->id !== null) {
            $sql = "DELETE from " . tbname("banner") . " where id = " .
                    $this->id;
            $retval = Database::Query($sql);
            $this->id = null;
        }
        return $retval;
    }

    public function render(): string {
        $html = "";
        switch ($this->getType()) {
            case "gif":
                $title = Template::getEscape($this->getName());
                $link_url = Template::getEscape($this->getLinkUrl());
                $image_url = Template::getEscape($this->getImageUrl());
                $html = "<a href=\"$link_url\" target=\"_blank\">"
                        . "<img src=\"$image_url\" title=\"$title\" "
                        . "alt=\"$title\" border=\"0\"></a>";
                break;
            case "html":
                $html = $this->getHtml();

                break;
        }
        return $html;
    }

}
