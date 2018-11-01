<?php
use UliCMS\Exceptions\NotImplementedException;

class LockInactiveUsersController extends MainClass
{

    private const MODULE_NAME = "lock_inactive_users";

    public function settings()
    {
        return Template::executeModuleTemplate(self::MODULE_NAME, "settings.php");
    }

    public function savePost()
    {
        throw new NotImplementedException();
    }

    public function getSettingsHeadline()
    {
        return get_translation("Lock_inactive_users");
    }

    public function cron()
    {}
}