<?php

class EntityPermissions
{

    private $entity_name;

    private $entity_id;

    private $owner_user_id;

    private $owner_group_id;

    private $only_admins_can_edit = false;

    private $only_group_can_edit = false;

    private $only_owner_can_edit = false;

    private $only_others_can_edit = false;

    public function __construct($entityName = null, $entityId = null, $user_id = null, $group_id = null)
    {
        if ($entityName !== null and entityId !== null) {
            $this->loadByEntityNameAndId($entityName, $entityId);
            // if there is no dataset create a new one
            if (! $this->entity_id) {
                $this->entity_name = $entityName;
                $this->entity_id = intval($entityId);
                if ($user_id) {
                    $this->owner_user_id = $user_id;
                }
                if ($group_id) {
                    $this->owner_group_id = $group_id;
                }
            }
        }
    }

    public function fillVars($result = null)
    {
        if ($result === null) {
            $this->entity_name = null;
            $this->entity_id = null;
            
            $this->owner_user_id = null;
            $this->owner_group_id = null;
            
            $this->only_admins_can_edit = boolval(Settings::get("only_admins_can_edit", "bool"));
            $this->only_group_can_edit = boolval(Settings::get("only_group_can_edit", "bool"));
            $this->only_owner_can_edit = boolval(Settings::get("only_owner_can_edit", "bool"));
            $this->only_others_can_edit = boolval(Settings::get("only_others_can_edit", "bool"));
        } else {
            $data = Database::fetchObject($result);
            $this->entity_name = $data->entity_name;
            $this->entity_id = intval($data->entity_id);
            
            $this->owner_user_id = intval($data->owner_user_id);
            $this->owner_group_id = intval($data->owner_group_id);
            
            $this->only_admins_can_edit = boolval($data->only_admins_can_edit);
            $this->only_group_can_edit = boolval($data->only_group_can_edit);
            $this->only_owner_can_edit = boolval($data->only_owner_can_edit);
            $this->only_others_can_edit = boolval($data->only_others_can_edit);
        }
    }

    public function loadByEntityNameAndId($entityName, $entityId)
    {
        $query = Database::pQuery("select * from {prefix}entity_permissions where entity_name = ? and entity_id = ?", array(
            strval($entityName),
            intval($entityId)
        ), true);
        $query = Database::getNumRows($query) > 0 ? $query : null;
        $this->fillVars($query);
    }

    // Alias for loadByEntityNameAndId()
    public function load($entityName, $entityId)
    {
        $this->loadByEntityNameAndId($entityName, $entityId);
    }

    public function save()
    {
        Database::pQuery("REPLACE INTO `{prefix}entity_permissions`
                        (entity_name, entity_id, owner_user_id, 
                         owner_group_id, only_admins_can_edit, 
                         only_group_can_edit, only_owner_can_edit, 
                         only_others_can_edit) 
                        values(?, ?, ?, ?, ?, ?, ?, ?)", array(
            $this->entity_name,
            $this->entity_id,
            $this->owner_user_id,
            $this->owner_group_id,
            $this->only_admins_can_edit,
            $this->only_group_can_edit,
            $this->only_owner_can_edit,
            $this->only_others_can_edit), true);
    }

    public function delete()
    {
        if (is_null($this->entity_name) or is_null($this->entity_id)) {
            return;
        }
        Database::pQuery("DELETE FROM `{prefix}entity_permissions` where entity_name = ? and entity_id = ?", array(
            $this->entity_name,
            $this->entity_id
        ), true);
    }

    public function getEntityName()
    {
        return $this->entity_name;
    }

    public function getEntityId()
    {
        return $this->entity_id;
    }

    public function getOwnerUserId()
    {
        return $this->owner_user_id;
    }

    public function getOwnerGroupId()
    {
        return $this->owner_group_id;
    }

    public function getEditRestriction($object)
    {
        $varName = "only_{$object}_can_edit";
        return (isset($this->$varName) ? $this->$varName : false);
    }

    public function setEntityName($value)
    {
        $this->entity_name = strval($value);
    }

    public function setEntityId($value)
    {
        $this->entity_id = intval($value);
    }

    public function setOwnerUserId($value)
    {
        $this->owner_user_id = intval($value);
    }

    public function setOwnerGroupId($value)
    {
        $this->owner_group_id = intval($value);
    }

    public function setEditRestriction($object, $value)
    {
        $varName = "only_{$object}_can_edit";
        $this->$varName = boolval($value);
    }

    public function getAll()
    {
        return $this->getAllEditRestrictions();
    }

    public function getAllEditRestrictions()
    {
        $result = array();
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
}