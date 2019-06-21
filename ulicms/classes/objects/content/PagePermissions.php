<?php

namespace UliCMS\Security\Permissions;

use Database;

class PagePermissions {

    public function __construct($objects = []) {
        foreach ($objects as $object => $restriction) {
            $this->setEditRestriction($object, $restriction);
        }
    }

    private $only_admins_can_edit = false;
    private $only_group_can_edit = false;
    private $only_owner_can_edit = false;
    private $only_others_can_edit = false;

    public function getEditRestriction($object) {
        $varName = "only_{$object}_can_edit";
        if (!isset($this->$varName)) {
            return null;
        }
        return $this->$varName;
    }

    public function setEditRestriction($object, $restricted = false) {
        $varName = "only_{$object}_can_edit";
        if (!isset($this->$varName)) {
            return;
        }
        $this->$varName = boolval($restricted);
    }

    public function getAll() {
        $result = [];
        $classArray = (array) $this;
        foreach ($classArray as $key => $value) {
            preg_match("/only_([a-z]+)_can_edit/", $key, $matches);
            if (count($matches) >= 2) {
                $object = $matches[1];
                $result[$object] = $value;
            }
        }
        return $result;
    }

    public function save($id) {
        $all = $this->getAll();

        $sql = "update `{prefix}content` set ";
        $args = [];
        foreach ($all as $key => $value) {
            $sql .= " only_{$key}_can_edit = ?, ";
            $args[] = $value;
        }

        $sql .= " id = id ";
        $sql = trim($sql);

        $args[] = intval($id);
        $sql .= " where id = ?";
        Database::pQuery($sql, $args, true) or die(Database::getError());
    }

}
