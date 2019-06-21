<?php

namespace UliCMS\Security;

use User;

class PermissionChecker {

    private $user_id;

    public function __construct($user_id = null) {
        // if ($user_id == null) {
        // $user_id = get_user_id();
        // }
        $this->user_id = $user_id;
    }

    public function hasPermission($permission) {
        // If the user is not logged in he has no permissions on anything
        if (!$this->user_id) {
            return false;
        }
        $user = new User($this->user_id);
        // If the "Is Admin" flag is set the user has full access to the whole system
        if ($user->getAdmin()) {
            return true;
        }

        $groups = $this->getUserGroups($user);

        // if at least one group of the user has the required permission return true
        foreach ($groups as $group) {
            if ($group->hasPermission($permission)) {
                return true;
            }
        }

        // No group has the required permission, so return false
        return false;
    }

    private function getUserGroups($user) {
        // Collect primary group and secondary groups of the user
        $groups = [];
        if ($user->getGroup()) {
            $groups[] = $user->getGroup();
        }

        $secondaryGroups = $user->getSecondaryGroups();
        $groups = array_merge($groups, $secondaryGroups);
        return $groups;
    }

    public function getLanguages() {
        $user = new User($this->user_id);
        $groups = $this->getUserGroups($user);

        $languages = [];

        foreach ($groups as $group) {
            $languages = array_merge($languages, $group->getLanguages());
        }
        $languages = array_unique($languages);
        return $languages;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($val) {
        $this->user_id = is_numeric($val) ? intval($val) : null;
    }

}
