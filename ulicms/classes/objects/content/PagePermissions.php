<?php

class PagePermissions
{

    public function __construct($objects = array())
    {
        foreach ($objects as $object => $restriction) {
            $this->setEditRestriction($object, $restriction);
        }
    }

    private $only_admins_can_edit = false;

    private $only_group_can_edit = false;

    private $only_owner_can_edit = false;

    private $only_others_can_edit = false;

    public function getEditRestriction($object)
    {
        $varName = "only_{$object}_can_edit";
        if (! isset($this->$varName)) {
            return null;
        }
        return $this->$varName;
    }

    public function setEditRestriction($object, $restricted = false)
    {
        $varName = "only_{$object}_can_edit";
        if (! isset($this->$varName)) {
            return;
        }
        $this->$varName = boolval($restricted);
    }
}