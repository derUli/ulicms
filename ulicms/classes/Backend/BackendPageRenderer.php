<?php

declare(strict_types=1);

namespace UliCMS\Backend;

use UliCMS\Backend\Utils\BrowserCompatiblityChecker;
use Template;
use UliCMS\Storages\Vars;
use Request;
use StringHelper;
use UliCMS\Registries\ActionRegistry;
use Settings;
use zz\Html\HTMLMinify;
use UliCMS\Security\PermissionChecker;
use UliCMS\HTML\Alert;

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

        $onlyContent = boolval(
                Request::getVar("only_content", false, 'bool')
        );

        if (!$onlyContent) {
            require "inc/header.php";
        }

        $isCompatible = true;
        if (Request::getUserAgent()) {
            $checker = new BrowserCompatiblityChecker(Request::getUserAgent());
            $isCompatible = $checker->isCompatible();
        }

        if (!$isCompatible) {
            $this->showUnsupportedBrowser($checker);
        } elseif (!is_logged_in()) {
            $this->handleNotLoggedIn($onlyContent);
        } else {
            $this->handleLoggedIn($onlyContent);
        }

        if (!$onlyContent) {
            do_event("admin_footer");
            require_once "inc/footer.php";
        }
        if (Settings::get("minify_html")) {
            $this->outputMinified();
        }

        $this->doCronEvents();
        exit();
    }

    public function showUnsupportedBrowser($checker) {
        $message = get_secure_translation(
                "unsupported_browser",
                [
                    "%browser%" => $checker->getUnsupportedBrowserName()
                ]
        );
        $message = nl2br($message);
        $message = make_links_clickable($message);

        echo Alert::danger($message, "", true);
    }

    // this method handles access to the features that are
    // accesible for non authenticated users
    protected function handleNotLoggedIn(bool $onlyContent = false): void {
        ActionRegistry::loadModuleActions();
        $actions = ActionRegistry::getActions();

        if ($this->getAction()) {
            $action_permission = ActionRegistry::getActionPermission(
                            $this->getAction()
            );
            if ($action_permission and $action_permission === "*") {
                Vars::set("action_filename", $actions[$this->getAction()]);
                echo Template::executeDefaultOrOwnTemplate(
                        "backend/container.php"
                );
            }
        }

        if (isset($_GET["register"])) {
            do_event("before_register_form");
            require_once "inc/registerform.php";
            do_event("after_register_form");
        } elseif (isset($_GET["reset_password"])) {
            do_event("before_reset_password_form");
            require_once "inc/reset_password.php";
            do_event("before_after_password_form");
        } else {
            do_event("before_login_form");
            require_once "inc/loginform.php";
            do_event("after_login_form");
        }
    }

    // this method handles all actions by authenticated users
    protected function handleLoggedIn(bool $onlyContent = false): void {
        $permissionChecker = new PermissionChecker(get_user_id());

        if (!$onlyContent) {
            require_once "inc/adminmenu.php";
        }

        ActionRegistry::loadModuleActions();
        $actions = ActionRegistry::getActions();

        do_event("register_actions");

        if ($_SESSION["require_password_change"]) {
            require_once "inc/change_password.php";
        } elseif (isset($actions[$this->getAction()])) {
            $requiredPermission = ActionRegistry::getActionPermission(
                            $this->getAction()
            );
            if (!$requiredPermission
                    or (
                    $requiredPermission
                    and $permissionChecker->hasPermission($requiredPermission))
            ) {
                Vars::set("action_filename", $actions[$this->getAction()]);
                echo Template::executeDefaultOrOwnTemplate(
                        "backend/container.php"
                );
            } else {
                noPerms();
            }
        } else {
            translate("action_not_found");
        }
    }

    // minify html output
    public function outputMinified(): void {
        $generatedHtml = ob_get_clean();
        $options = array(
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        );
        $HTMLMinify = new HTMLMinify($generatedHtml, $options);
        $generatedHtml = $HTMLMinify->process();
        $generatedHtml = StringHelper::removeEmptyLinesFromString(
                        $generatedHtml
        );

        echo $generatedHtml;
    }

    // run cron events of modules
    public function doCronEvents(): void {
        do_event("before_admin_cron");
        do_event("admin_cron");
        do_event("after_admin_cron");
    }

}
