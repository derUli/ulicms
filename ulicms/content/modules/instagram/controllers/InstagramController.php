<?php

use InstagramAPI\Exception\InvalidUserException;
use InstagramAPI\Exception\IncorrectPasswordException;

// Damn I hate this Instagram / Social Media Crap
// But many corporations think they need this shit for marketing reasons
class InstagramController extends MainClass {

    public function __construct() {

        // Yes I wan't to use this API in a web application
        // and not in CLI environment
        \InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
    }

    const MODULE_NAME = "instagram";

    public function cron() {
        $username = Settings::get("instagram/username");
        $password = Settings::get("instagram/password");

        // If we have no login data abort
        if (!is_present($username) or ! is_present($password)) {
            return;
        }
        $this->postUnpostedImages($username, $password);
    }

    // post all unposted "image" type pages on instagram
    protected function postUnpostedImages($username, $password) {
        // ignore_user_abort to ensure that the connection is closed cleanly
        ignore_user_abort(true);
        set_time_limit(0);

        $images = $this->getNotPosted();

        // If we have no images then abort
        if (!count($images)) {
            return;
        }

        // Connect to that Instagram thing
        $instaCrap = new \InstagramAPI\Instagram();
        $instaCrap->login($username, $password);

        // iterate over all unposted images and post it
        foreach ($images as $image) {
            $image->postImage($instaCrap);
        }
        // logout
        $instaCrap->logout();
        ignore_user_abort(false);
    }

    public function getSettingsHeadline() {
        return '<i class="fab fa-instagram" '
                . 'style="font-size: 30px"></i> | Instagram';
    }

    // get all not posted images
    public function getNotPosted($order = "id") {
        $result = [];
        $sql = "SELECT id, `type` FROM " . tbname("content") . " where `type` = 'image' and active = 1 and posted2instagram = 0 ORDER BY $order";

        $query = Database::query($sql);
        while ($row = Database::fetchObject($query)) {
            $result[] = new InstagramImage($row->id);
        }
        return $result;
    }

    public function settings() {
        return Template::executeModuleTemplate(self::MODULE_NAME, "settings.php");
    }

    public function savePost() {
        // TODO: Test if login data is valid
        // else show error message
        Settings::set("instagram/username", Request::getVar("username"));
        Settings::set("instagram/password", Request::getVar("password"));

        $error = null;

        try {
            // Connect to that Instagram thing
            $instaCrap = new \InstagramAPI\Instagram();
            $instaCrap->login(Request::getVar("username"), Request::getVar("password"));
            $instaCrap->logout();
        } catch (IncorrectPasswordException $e) {
            $error = "invalid_password";
        } catch (InvalidUserException $e) {
            $error = "invalid_user";
        }

        if (!$error) {
            Response::redirect(ModuleHelper::buildAdminURL(self::MODULE_NAME, "save=1"));
        } else {
            Response::redirect(ModuleHelper::buildAdminURL(self::MODULE_NAME, "error={$error}"));
        }
    }

}
