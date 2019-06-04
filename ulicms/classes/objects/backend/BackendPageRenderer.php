<?php

namespace UliCMS\Backend;

use StringHelper;
use ActionRegistry;
use Settings;
use zz\Html\HTMLMinify;
use UliCMS\Security\PermissionChecker;

// This class renders a backend page
// if you set a model from a model
// you can get it with this code in your template
// $model = BackendPageRenderer::getModel();
class BackendPageRenderer {

    private $action;
    private static $model;

    public function __construct($action, $model = null) {
        $this->action = $action;

        self::$model = $model;
    }

    public function getAction() {
        return $this->action;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public static function getModel() {
        return self::$model;
    }

    public static function setModel($model) {
        self::$model = $model;
    }

    // renders a backend page, outputs it and do events
    public function render() {
        $permissionChecker = new PermissionChecker(get_user_id());
        if (Settings::get("minify_html")) {
            ob_start();
        }

        require "inc/header.php";

        if (!is_logged_in()) {
            if (isset($_GET["register"])) {
                do_event("before_register_form");
                require_once "inc/registerform.php";
                do_event("after_register_form");
            } else if (isset($_GET["reset_password"])) {
                do_event("before_reset_password_form");
                require_once "inc/reset_password.php";
                do_event("before_after_password_form");
            } else {
                do_event("before_login_form");
                require_once "inc/loginform.php";
                do_event("after_login_form");
            }
        } else {
            require_once "inc/adminmenu.php";
            // FIXME: don't use globals!
            global $actions;
            $actions = [];

            ActionRegistry::loadModuleActions();

            do_event("register_actions");

            if ($_SESSION["require_password_change"]) {
                require_once "inc/change_password.php";
            } else if (isset($actions[$this->getAction()])) {
                $requiredPermission = ActionRegistry::getActionPermission($this->getAction());
                if (!$requiredPermission or ( $requiredPermission and $permissionChecker->hasPermission($requiredPermission))) {
                    require_once $actions[$this->getAction()];
                } else {
                    noPerms();
                }
            } else {
                translate("action_not_found");
            }
        }

        do_event("admin_footer");

        require_once "inc/footer.php";

        if (Settings::get("minify_html")) {
            $generatedHtml = ob_get_clean();
            $options = array(
                'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
            );
            $HTMLMinify = new HTMLMinify($generatedHtml, $options);
            $generatedHtml = $HTMLMinify->process();
            $generatedHtml = StringHelper::removeEmptyLinesFromString($generatedHtml);

            echo $generatedHtml;
        }

        do_event("before_admin_cron");
        do_event("admin_cron");
        do_event("after_admin_cron");

        db_close($connection);
        exit();
    }

}
