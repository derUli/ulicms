<?php

class EntityPermissions
{

    private $entity_name;

    private $entity_id;

    private $owner_user_id;

    private $owner_group_id;

    private $only_admins_can_edit = false;

    private $only_groups_can_edit = false;

    private $only_owner_can_edit = false;

    private $only_others_can_edit = false;

    public function __construct($entityName = null, $entityId = null)
    {
        if ($entityName !== null and entityId !== null) {
            $this->loadByEntityNameAndId($entityName, $entityId);
        }
    }

    public function fillVars($result = null)
    {
        if ($result === null) {
            $this->entity_name = null;
            $this->entity_id = null;
            
            $this->owner_user_id = null;
            $this->owner_group_id = null;
            
            $this->only_admins_can_edit = false;
            $this->only_group_can_edit = false;
            $this->only_owner_can_edit = false;
            $this->only_others_can_edit = false;
        } else {
            $data = Database::fetchObject($result);
            $this->entity_name = $data->entity_name;
            $this->entity_id = $data->entity_id;
            
            $this->owner_user_id = $data->owner_user_id;
            $this->owner_group_id = $data->owner_group_id;
            
            $this->only_admins_can_edit = $data->only_admins_can_edit;
            $this->only_group_can_edit = $data->only_group_can_edit;
            $this->only_owner_can_edit = $data->only_owner_can_edit;
            $this->only_others_can_edit = $data->only_others_can_edit;
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
        throw new NotImplementedException("insert not implemented");
    }

    protected function update()
    {
        throw new NotImplementedException("update not implemented");
    }

    public function delete()
    {
        throw new NotImplementedException("delete not implemented");
    }
    
    // TODO: Implement Getter and Setter
}