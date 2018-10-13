<?php
namespace UliCMS\Security;

use UliCMS\Exceptions\NotImplementedException;
use ContentFactory;
use User;

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
        $content = ContentFactory::getByID($id);
        $permissions = $content->getPermissions();
        
        $contentOwner = $content->autor;
		$contentGroup  = $content->group_id;

		$user = new User($this->user_id);
		$userGroups = array();
		$primaryGroup = $user->getGroupId();
		if($primaryGroup){
			$groups[] = new Group($primaryGroup);
		}
		
		$is_owner = $user->getID() ==contentOwner;
		
		$groups = array_merge($groups, $user->getSecondaryGroups());
				
        $adminsCanEdit = $permissions->getEditRestriction("admins");
        $groupCanEdit = $permissions->getEditRestriction("group");
        $ownerCanEdit = $permissions->getEditRestriction("owner");
        $othersCanEdit = $permissions->getEditRestriction("others");
		
		// if there are edit restrictions
		if($adminsCanEdit or $groupsCanEdit or $ownerCanEdit or $othersCanEdit){
			 // Work in Progress
		}
		
        $can_edit_this = false;

    }

    public function canDelete($dataset)
    {
        throw new NotImplementedException();
    }
}