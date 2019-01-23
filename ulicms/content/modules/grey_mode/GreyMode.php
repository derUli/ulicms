<?php
use UliCMS\Security\PermissionChecker;

class GreyMode extends MainClass
{

    const MODULE_NAME = "grey_mode";

    public function afterSessionStart()
    {
        $userId = get_user_id();
        if (! isset($_SESSION["grey_mode"]) && $userId > 0) {
            $_SESSION["grey_mode"] = boolval(Settings::get("enable_grey_mode/{$userId}"));
        }
    }

    public function toggleGreyModePost()
    {
        $userId = get_user_id();
        if ($_SESSION["grey_mode"]) {
            $_SESSION["grey_mode"] = false;
            Settings::delete("enable_grey_mode/{$userId}");
        } else {
            $_SESSION["grey_mode"] = true;
            Settings::set("enable_grey_mode/{$userId}", "1");
        }
        
        $referrer = Request::getVar("referrer", ModuleHelper::buildActionURL("home"), "str");
        Response::redirect($referrer);
    }

    public function adminHead()
    {
        if ($_SESSION["grey_mode"]) {
            $cssFile = ModuleHelper::buildRessourcePath(self::MODULE_NAME, "grey.css");
            enqueueStylesheet($cssFile);
            combinedStylesheetHtml();
        }
    }

    public function registerActions()
    {
        $checker = new PermissionChecker(get_user_id());
        if (! $checker->hasPermission("grey_mode")) {
            return "";
        }
        return Template::executeModuleTemplate(self::MODULE_NAME, "button.php");
    }
}