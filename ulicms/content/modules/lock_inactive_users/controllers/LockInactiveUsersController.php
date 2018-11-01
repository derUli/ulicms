<?php

class LockInactiveUsersController extends MainClass
{

    private const MODULE_NAME = "lock_inactive_users";

    public function settings()
    {
        $userManager = new UserManager();
        return Template::executeModuleTemplate(self::MODULE_NAME, "settings.php");
    }

    public function savePost()
    {
        $enable = intval(Request::getVar("enable"));
        $days = Request::getVar("days", 30, "int");
        
        Settings::set("lock_inactive_users/enable", $enable);
        Settings::set("lock_inactive_users/days", $days);
        Response::redirect(ModuleHelper::buildAdminURL(self::MODULE_NAME, "save=1"));
    }

    public function getSettingsHeadline()
    {
        return get_translation("lock_inactive_users");
    }

    private function deleteExpiredUsers()
    {
        $days = Settings::get("lock_inactive_users/days");
        $daysInSeconds = $days * 60 * 60 * 24;
        
        $userManager = new UserManager();
        $users = $userManager->getLockedUsers(false);
        foreach ($users as $user) {
            var_dump($user);
            if (time() - $user->getLastLogin() >= $daysInSeconds) {
                $user->setLocked(true);
                $user->save();
            }
        }
    }

    public function cron()
    {
        if (Settings::get("lock_inactive_users/enable")) {
            if (class_exists("BetterCron")) {
                BetterCron::days("lock_inactive_users/cron", 1, function () {
                    $this->deleteExpiredUsers();
                });
            } else {
                $this->deleteExpiredUsers();
            }
        }
    }
}