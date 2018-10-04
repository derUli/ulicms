<?php
namespace UliCMS\Security;

use UliCMS\Exceptions\NotImplementedException;
use User;

class PermissionChecker
{

    private $user_id;

    public function __construct($user_id = null)
    {
        $this->user_id = $user_id;
    }

    public function hasPermission($permission)
    {
        if(!$this->user_id){
            return false;
        }
       $user = new User($this->user_id);
       if($user->getAdmin()){
           return true;
       }
       $groups = array();
       if($user->getGroup()){
           $groups[] = $user->getGroup();
       }

       $secondaryGroups = $user->getSecondaryGroups();
       $groups = array_merge($groups, $secondaryGroups);

       foreach($groups as $group){
           if($group->hasPermission($permission)){
               return true;
           }
       }

       return false;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($val)
    {
        $this->user_id = is_numeric($val) ? intval($val) : null;
    }
}