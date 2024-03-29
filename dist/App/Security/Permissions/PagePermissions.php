<?php

declare(strict_types=1);

namespace App\Security\Permissions;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use Database;

// This class is used to store the edit restrictions of content
// think of it as ACL like write permissions
class PagePermissions {
    private $only_admins_can_edit = false;

    private $only_group_can_edit = false;

    private $only_owner_can_edit = false;

    private $only_others_can_edit = false;

    public function __construct($objects = []) {
        foreach ($objects as $object => $restriction) {
            $this->setEditRestriction($object, $restriction);
        }
    }

    public function getEditRestriction(string $object): ?bool {
        $varName = "only_{$object}_can_edit";
        if (! isset($this->{$varName})) {
            return null;
        }
        return $this->{$varName};
    }

    public function setEditRestriction(
        string $object,
        bool $restricted = false
    ): void {
        $varName = "only_{$object}_can_edit";
        if (! isset($this->{$varName})) {
            return;
        }

        $this->{$varName} = $restricted;
    }

    public function getAll(): array {
        $result = [];
        $classArray = (array)$this;
        foreach ($classArray as $key => $value) {
            preg_match('/only_([a-z]+)_can_edit/', $key, $matches);
            if (count($matches) >= 2) {
                $object = $matches[1];
                $result[$object] = $value;
            }
        }
        return $result;
    }

    public function save(int $id): void {
        $all = $this->getAll();

        $sql = 'update `{prefix}content` set ';
        $args = [];
        foreach ($all as $key => $value) {
            $sql .= " only_{$key}_can_edit = ?, ";
            $args[] = $value;
        }

        $sql .= ' id = id ';
        $sql = trim($sql);

        $args[] = (int)$id;
        $sql .= ' where id = ?';
        Database::pQuery($sql, $args, true) || exit(Database::getError());
    }
}
