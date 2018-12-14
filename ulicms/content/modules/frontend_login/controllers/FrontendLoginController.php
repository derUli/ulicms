<?php
use UliCMS\Exceptions\NotImplementedException;

class FrontendLoginController extends MainClass
{

    const MODULE_NAME = "frontend_login";

    public function render()
    {
        if (! is_logged_in()) {
            return Template::executeModuleTemplate(self::MODULE_NAME, "form.php");
        }
        return Template::executeModuleTemplate(self::MODULE_NAME, "welcome.php");
    }

    public function doLogin()
    {
        $cfg = new CMSConfig();
        $firstPage = ModuleHelper::getFirstPageWithModule(self::MODULE_NAME, getCurrentLanguage(true));
        
        $user = Request::getVar("user");
        $password = Request::getVar("password");
        $login = validate_login($user, $password);
        if ($login) {
            register_session($login, false);
            $url = is_true($cfg->frontend_login_url) ? $cfg->frontend_login_url : buildSEOUrl($firstPage->systemname);
            Response::redirect($url);
        } else {
            $url = ModuleHelper::getFullPageURLByID($firstPage->id, "error=USER_OR_PASSWORD_INCORRECT");
            Response::redirect($url);
        }
    }

    public function doLogout()
    {
        @session_destroy();
        $firstPage = ModuleHelper::getFirstPageWithModule(self::MODULE_NAME, getCurrentLanguage(true));
        $url = ModuleHelper::getFullPageURLByID($firstPage->id);
        Response::redirect($url);
    }
}