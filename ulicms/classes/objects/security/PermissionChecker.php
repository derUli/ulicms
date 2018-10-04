<?php
namespace UliCMS\Security;

use UliCMS\Exceptions\NotImplementedException;

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
       // TODO: get the primary and the secondary groups of the user
       // construct an associative permission array
       // and check permission
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