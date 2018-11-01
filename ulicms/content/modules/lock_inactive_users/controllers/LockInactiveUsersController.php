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
        
        // Save settings
        Settings::set("lock_inactive_users/enable", $enable);
        Settings::set("lock_inactive_users/days", $days);
        
        // Redirect to settings page and show success message.
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
        
        // get all not locked users
        $users = $userManager->getLockedUsers(false);
        foreach ($users as $user) {
            // lock all users where the time difference between now and the last login
            // is greater than X days
            if (time() - $user->getLastLogin() >= $daysInSeconds) {
                $user->setLocked(true);
                $user->save();
            }
        }
    }

    public function cron()
    {
        // If the function is enabled
        if (Settings::get("lock_inactive_users/enable")) {
            // Use better_cron if installed to run the cronjob in a regular interval
            if (class_exists("BetterCron")) {
                BetterCron::days("lock_inactive_users/cron", 1, function () {
                    $this->deleteExpiredUsers();
                });
            } else {
                // if better_cron is not installed run the cronjob on every page load
                // this may have a negative effect on site performance
                $this->deleteExpiredUsers();
            }
        }
    }
}