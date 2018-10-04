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

    public function hasPermission()
    {
        throw new NotImplementedException();
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