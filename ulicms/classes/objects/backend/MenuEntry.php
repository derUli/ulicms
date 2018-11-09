<?php

class MenuEntry
{

    private $title;

    private $link;

    private $identifier;

    private $permissions;

    private $children = array();

    private $newWindow = false;

    public function __construct($title, $link, $identifier, $permissions = null, $children = array(), $newWindow = false)
    {
        $this->title = $title;
        $this->link = $link;
        $this->identifier = $identifier;
        $this->permissions = $permissions;
        $this->children = $children;
        $this->newWindow = $newWindow;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setTitle($value)
    {
        $this->title = $value;
    }

    public function setLink($value)
    {
        $this->link = $value;
    }

    public function setIdentifier($value)
    {
        $this->identifier = $value;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($value)
    {
        $this->children = $value;
    }

    public function hasChildren()
    {
        return (count($this->children) > 0);
    }

    public function addChild($children)
    {
        $this->children[] = $children;
    }

    public function getChildByID($identifier, $root = null)
    {
        $result = null;
        if (! $root) {
            $root = $this->children;
        }
        foreach ($this->children as $root) {
            if ($child->getIdentifier() == $identifier) {
                return $child;
            }
            if ($child->hasChildren()) {
                return $this->getChildByID($identifier, $child);
            }
        }
        return null;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    public function getNewWindow()
    {
        return $this->newWindow;
    }

    public function setNewWindow($val)
    {
        $this->newWindow = boolval($val);
    }

    public function userHasPermission()
    {
        $acl = new ACL();
        if (is_string($this->permissions) and ! empty($this->permissions)) {
            return $acl->hasPermission($this->permissions);
        } else if (is_array($this->permissions) and count($this->permissions) > 0) {
            $isPermitted = false;
            foreach ($this->permissions as $permission) {
                if (is_string($permission) and ! empty($permission) and $acl->hasPermission($permission)) {
                    $isPermitted = true;
                }
            }
            return $isPermitted;
        } else {
            return true;
        }
    }
}
