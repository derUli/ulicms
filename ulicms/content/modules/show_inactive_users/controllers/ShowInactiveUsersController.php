<?php

class ShowInactiveUsersController extends Controller
{

    public const MODULE_NAME = "show_inactive_users";

    public function settings()
    {
        $daysSetting = Settings::get("show_inactive_users/days");
        $days = Request::getVar("days", $daysSetting, "int");
        if ($days != $daysSetting) {
            Settings::set("show_inactive_users/days", $days);
        }
        ViewBag::set("days", $days);
        ViewBag::set("users", $this->searchInactiveUsers($days));
        return Template::executeModuleTemplate(self::MODULE_NAME, "list.php");
    }

    public function getSettingsLinkText()
    {
        return get_translation("view");
    }

    public function getSettingsHeadline()
    {
        return get_translation("inactive_users");
    }

    protected function searchInactiveUsers($days)
    {
        $manager = new UserManager();
        $allUsers = $manager->getAllUsers("last_action");
        $inactiveUsers = array();
        foreach ($allUsers as $user) {
            if ($user->getLastLogin() and time() - $user->getLastLogin() >= ($days * 60 * 60 * 24)) {
                $inactiveUsers[] = $user;
            }
        }
        return $inactiveUsers;
    }

    public function deletePost()
    {
        if (! Request::getVar("confirm-delete")) {
            ExceptionResult(get_translation("fill_all_fields"));
        }
        $users = Request::getVar("users[]");
        if (is_array($users)) {
            foreach ($users as $user) {
                $dataset = new User($user);
                if ($dataset->getLastLogin()) {
                    $dataset->delete();
                }
            }
        }
        Response::redirect(ModuleHelper::buildAdminURL(self::MODULE_NAME));
    }

    public function uninstall()
    {
        Settings::delete("show_inactive_users/days");
    }
}