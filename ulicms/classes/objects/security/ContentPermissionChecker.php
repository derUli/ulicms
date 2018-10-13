<?php

use UliCMS\Exceptions\NotImplementedException;

class ContentPermissionChecker implements IDatasetPermissionChecker
{

    private $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function canRead($dataset)
    {
        return true;
    }

    public function canWrite($dataset)
    {
        $permissions = $this->getPermissionObject($dataset);
        $permissions->getEditRestriction("admins");
        $permissions->getEditRestriction("group");
        $permissions->getEditRestriction("owner");
        $permissions->getEditRestriction("others");
    }

    private function getPermissionObject($id)
    {
        $page = ContentFactory::getByID($id);
        return $page->getPermissions();
    }

    public function canDelete($dataset)
    {
        throw new NotImplementedException();
    }
}