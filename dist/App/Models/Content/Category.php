<?php

declare(strict_types=1);

namespace App\Models\Content;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use Database;
use mysqli_result;

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
        $sql = 'select * from {prefix}categories where id = ?';
        $args = [
            (int)$id
        ];
        $result = Database::pQuery($sql, $args, true);
        $this->fillVars($result);
    }

    public function fillVars(?mysqli_result $result): void
    {
        $this->id = null;
        $this->name = null;
        $this->description = null;

        if ($result && Database::getNumRows($result) > 0) {
            $result = Database::fetchObject($result);
            $this->id = (int)$result->id;
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

    public function delete(): void
    {
        if ($this->id) {
            $sql = 'delete from {prefix}categories where id = ?';
            $args = [
                (int)$this->id
            ];
            Database::pQuery($sql, $args, true);
            $this->fillVars(null);
        }
    }

    public static function getAll(string $order = 'id'): array
    {
        $datasets = [];
        $sql = "select id from `{prefix}categories` order by $order";
        $result = Database::query($sql, true);
        while ($row = Database::fetchobject($result)) {
            $datasets[] = new Category((int)$row->id);
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
        $this->id = $val !== null ? (int)$val : null;
    }

    public function setName(?string $val): void
    {
        $this->name = $val !== null ? (string)$val : null;
    }

    public function setDescription(?string $val): void
    {
        $this->description = $val !== null ? (string)$val : null;
    }

    protected function insert(): void
    {
        $sql = 'INSERT INTO `{prefix}categories` (name, description) '
                . 'values (?, ?)';
        $args = [
            $this->getName(),
            $this->getDescription()
        ];
        Database::pQuery($sql, $args, true);
        $this->id = Database::getLastInsertID();
    }

    protected function update(): void
    {
        $sql = 'update `{prefix}categories` set name = ?, '
                . 'description = ? where id = ?';
        $args = [
            $this->getName(),
            $this->getDescription(),
            $this->id
        ];
        Database::pQuery($sql, $args, true);
    }
}
