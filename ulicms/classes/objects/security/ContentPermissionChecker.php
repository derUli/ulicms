<?php
namespace UliCMS\Security;

use UliCMS\Exceptions\NotImplementedException;
use ContentFactory;
use User;
use Group;

class ContentPermissionChecker implements IDatasetPermissionChecker
{

    private $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function canRead($dataset)
    {
        throw new NotImplementedException();
    }

    public function canWrite($dataset)
    {
        $content = ContentFactory::getByID($id);
        $permissions = $content->getPermissions();
        
        $contentOwner = $content->autor;
        $contentGroup = $content->group_id;
        
        $user = new User($this->user_id);
        $permissionChecker = new PermissionChecker($user_id);
        $userGroups = array();
        $primaryGroup = $user->getGroupId();
        
        if ($primaryGroup) {
            $userGroups[] = new Group($primaryGroup);
        }
        
        // Is the user the owner of the content dataset?
        $isOwner = $user->getID() == $contentOwner;
        
        $userGroups = array_merge($userGroups, $user->getSecondaryGroups());
        
        $groupIds = array();
        foreach ($userGroups as $group) {
            $groupIds[] = $group->getID();
        }
        
        // page edit restrictions (booleans)
        $adminsCanEdit = $permissions->getEditRestriction("admins");
        $groupCanEdit = $permissions->getEditRestriction("group");
        $ownerCanEdit = $permissions->getEditRestriction("owner");
        $othersCanEdit = $permissions->getEditRestriction("others");
        
        $canEditThis = false;
        
        // if there are edit restrictions
        if ($groupCanEdit or $adminsCanEdit or $ownerCanEdit or $othersCanEdit) {
            if ($groupCanEdit and in_array($contentGroup, $groupIds)) {
                $canEditThis = true;
            } else if ($adminsCanEdit and $user->getAdmin()) {
                $canEditThis = true;
            } else if ($ownerCanEdit and $isOwner and $permissionChecker->hasPermission("pages_edit_own")) {
                $canEditThis = true;
            } else if ($othersCanEdit and ! in_array($contentGroup, $groupIds) and ! $user->getAdmin() and ! $isOwner) {
                $canEditThis = true;
            }
        } else {
            if (! $isOwner and $permissionChecker->hasPermission("pages_edit_others")) {
                $canEditThis = true;
            } else if ($isOwner and $permissionChecker->hasPermission("pages_edit_own")) {
                $canEditThis = true;
            }
        }
        
        // admins are gods
        if ($user->getAdmin()) {
            $canEditThis = true;
        }
        
        return $canEditThis;
    }

    public function canDelete($dataset)
    {
        return $this->canWrite($dataset);
    }
}