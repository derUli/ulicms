<?php

namespace UliCMS\Models\Content;

use Database;

class Category {

    private $id = null;
    private $name = null;
    private $description = null;

    public function __construct($id = null) {
        if ($id) {
            $this->loadByID($id);
        }
    }

    public function loadByID($id) {
        $sql = "select * from {prefix}categories where id = ?";
        $args = array(
            intval($id)
        );
        $query = Database::pQuery($sql, $args, true);
        $this->fillVars($query);
    }

    public function fillVars($query) {
        $this->id = null;
        $this->name = null;
        $this->description = null;

        if ($query and Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->id = $result->id;
            $this->name = $result->name;
            $this->description = $result->description;
        }
    }

    public function save() {
        if ($this->id) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    protected function insert() {
        $sql = "INSERT INTO `{prefix}categories` (name, description) values (?, ?)";
        $args = array(
            $this->getName(),
            $this->getDescription()
        );
        Database::pQuery($sql, $args, true);
        $this->id = Database::getLastInsertID();
    }

    protected function update() {
        $sql = "update `{prefix}categories` set name = ?, description = ? where id = ?";
        $args = array(
            $this->getName(),
            $this->getDescription(),
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function delete() {
        if ($this->id) {
            $sql = "delete from {prefix}categories where id = ?";
            $args = array(
                intval($this->id)
            );
            Database::pQuery($sql, $args, true);
            $this->fillVars(null);
        }
    }

    public static function getAll($order = "id") {
        $sql = "select id from `{prefix}categories` order by $order";
        $query = Database::query($sql, true);
        $datasets = [];
        while ($row = Database::fetchobject($query)) {
            $datasets[] = new Category($row->id);
        }
        return $datasets;
    }

    public function getID() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setID($val) {
        $this->id = !is_null($val) ? intval($val) : null;
    }

    public function setName($val) {
        $this->name = !is_null($val) ? strval($val) : null;
    }

    public function setDescription($val) {
        $this->description = !is_null($val) ? strval($val) : null;
    }

}
