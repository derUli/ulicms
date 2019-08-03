<?php

declare(strict_types=1);

namespace UliCMS\Backend;

use StringHelper;
use ActionRegistry;
use Settings;
use zz\Html\HTMLMinify;
use UliCMS\Security\PermissionChecker;
use Database;

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

    public function getAction(): string {
        return $this->action;
    }

    public function setAction(string $action): void {
        $this->action = $action;
    }

    public static function getModel() {
        return self::$model;
    }

    public static function setModel($model): void {
        self::$model = $model;
    }

    // renders a backend page, outputs it and do events
    public function render(): void {
        if (Settings::get("minify_html")) {
            ob_start();
        }

        require "inc/header.php";

        if (!is_logged_in()) {
            $this->handleNotLoggedIn();
        } else {
            $this->handleLoggedIn();
        }

        do_event("admin_footer");

        require_once "inc/footer.php";

        if (Settings::get("minify_html")) {
            $this->outputMinified();
        }

        $this->doCronEvents();
        exit();
    }

    protected function handleNotLoggedIn(): void {

        ActionRegistry::loadModuleActions();
        $actions = ActionRegistry::getActions();

        if ($this->getAction()) {
            $action_permission = ActionRegistry::getActionPermission($this->getAction());
            if ($action_permission and $action_permission === "*") {
                require_once $actions[$this->getAction()];
            }
        }

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
    }

    protected function handleLoggedIn(): void {
        $permissionChecker = new PermissionChecker(get_user_id());

        require_once "inc/adminmenu.php";

        ActionRegistry::loadModuleActions();
        $actions = ActionRegistry::getActions();

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

    protected function outputMinified(): void {
        $generatedHtml = ob_get_clean();
        $options = array(
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        );
        $HTMLMinify = new HTMLMinify($generatedHtml, $options);
        $generatedHtml = $HTMLMinify->process();
        $generatedHtml = StringHelper::removeEmptyLinesFromString($generatedHtml);

        echo $generatedHtml;
    }

    protected function doCronEvents(): void {
        do_event("before_admin_cron");
        do_event("admin_cron");
        do_event("after_admin_cron");
    }

}
