<?php

declare(strict_types=1);

namespace UliCMS\Models\Content;

use mysqli_result;
use Database;

class Category
{
    private $id = null;
    private $name = null;
    private $description = null;

    public function __construct(?int $id = null)
    {
        if ($id) {
            $this->loadByID($id);
        }
    }

    public function loadByID(int $id): void
    {
        $sql = "select * from {prefix}categories where id = ?";
        $args = array(
            (int)$id
        );
        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    public function fillVars(?mysqli_result $result): void
    {
        $this->id = null;
        $this->name = null;
        $this->description = null;

        if ($result and Database::getNumRows($result) > 0) {
            $result = Database::fetchObject($result);
            $this->id = intval($result->id);
            $this->name = $result->name;
            $this->description = $result->description;
        }
    }

    public function save(): void
    {
        if ($this->id) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    protected function insert(): void
    {
        $sql = "INSERT INTO `{prefix}categories` (name, description) "
                . "values (?, ?)";
        $args = array(
            $this->getName(),
            $this->getDescription()
        );
        Database::pQuery($sql, $args, true);
        $this->id = Database::getLastInsertID();
    }

    protected function update(): void
    {
        $sql = "update `{prefix}categories` set name = ?, "
                . "description = ? where id = ?";
        $args = array(
            $this->getName(),
            $this->getDescription(),
            $this->id
        );
        Database::pQuery($sql, $args, true);
    }

    public function delete(): void
    {
        if ($this->id) {
            $sql = "delete from {prefix}categories where id = ?";
            $args = array(
                intval($this->id)
            );
            Database::pQuery($sql, $args, true);
            $this->fillVars(null);
        }
    }

    public static function getAll(string $order = "id"): array
    {
        $datasets = [];
        $sql = "select id from `{prefix}categories` order by $order";
        $result = Database::query($sql, true);
        while ($row = Database::fetchobject($result)) {
            $datasets[] = new Category(intval($row->id));
        }
        return $datasets;
    }

    public function getID(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setID(?int $val): void
    {
        $this->id = !is_null($val) ? (int)$val : null;
    }

    public function setName(?string $val): void
    {
        $this->name = !is_null($val) ? (string)$val : null;
    }

    public function setDescription(?string $val): void
    {
        $this->description = !is_null($val) ? (string)$val : null;
    }
}
