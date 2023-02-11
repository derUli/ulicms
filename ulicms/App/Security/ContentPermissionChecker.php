<?php

declare(strict_types=1);

namespace App\Security;

use ContentFactory;
use User;
use Group;

// permission checks for read, write and delete content permissions
class ContentPermissionChecker implements IDatasetPermissionChecker
{
    private $user_id;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
    }

    public function canRead(int $contentId): bool
    {
        $content = ContentFactory::getByID($contentId);
        $access = $content->checkAccess($content);

        return $access !== null;
    }

    public function canWrite(int $contentId): bool
    {
        $content = ContentFactory::getByID($contentId);
        $permissions = $content->getPermissions();

        $contentOwner = $content->author_id;
        $contentGroup = $content->group_id;

        $user = new User($this->user_id);
        $permissionChecker = new PermissionChecker($this->user_id);
        $userGroups = [];
        $primaryGroup = $user->getGroupId();

        if ($primaryGroup) {
            $userGroups[] = new Group($primaryGroup);
        }

        // Is the user the owner of the content dataset?
        $isOwner = $user->getID() == $contentOwner;

        $userGroups = array_merge($userGroups, $user->getSecondaryGroups());

        $groupIds = [];
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
            } elseif ($adminsCanEdit and $user->isAdmin()) {
                $canEditThis = true;
            } elseif ($ownerCanEdit and $isOwner
                    and $permissionChecker->hasPermission("pages_edit_own")) {
                $canEditThis = true;
            } elseif ($othersCanEdit && !in_array($contentGroup, $groupIds) && !$user->isAdmin() && !$isOwner) {
                $canEditThis = true;
            }
        } else {
            if (!$isOwner
                    and $permissionChecker->hasPermission("pages_edit_others")) {
                $canEditThis = true;
            } elseif ($isOwner
                    and $permissionChecker->hasPermission("pages_edit_own")) {
                $canEditThis = true;
            }
        }

        // admins are gods
        if ($user->isAdmin()) {
            $canEditThis = true;
        }

        return $canEditThis;
    }

    public function canDelete($contentId): bool
    {
        return $this->canWrite($contentId);
    }
}
