<?php

declare(strict_types=1);

namespace App\Backend;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use App\Helpers\StringHelper;
use App\Registries\ActionRegistry;
use App\Security\PermissionChecker;
use Request;
use Settings;
use Template;
use Vars;
use zz\Html\HTMLMinify;

/**
 * This class renders a backend page
 */
class BackendPageRenderer
{
    private $action;
    private static $model;

    /**
     * Constructor
     * @param type $action
     * @param type $model
     */
    public function __construct(string $action, mixed $model = null)
    {
        $this->action = $action;

        self::$model = $model;
    }

    /**
     * Gets the action
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Sets the action
     * @param string $action
     * @return void
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * Gets the model
     * @return mixed
     */
    public static function getModel(): mixed
    {
        return self::$model;
    }

    /**
     * Sets the model
     * @param mixed $model
     * @return void`
     */
    public static function setModel(mixed $model): void
    {
        self::$model = $model;
    }

    /**
     * Renders a backend page outputs it and runs cron events
     * @return void
     */
    public function render(): void
    {
        if (Settings::get('minify_html')) {
            ob_start();
        }

        $onlyContent = (bool) Request::getVar('only_content', false, 'bool');

        if (! $onlyContent) {
            require 'inc/header.php';
        }

        if (! is_logged_in()) {
            $this->handleNotLoggedIn($onlyContent);
        } else {
            $this->handleLoggedIn($onlyContent);
        }

        if (! $onlyContent) {
            do_event('admin_footer');
            require 'inc/footer.php';
        }
        if (Settings::get('minify_html')) {
            $this->outputMinified();
        }

        $this->doCronEvents();
        exit();
    }

    /**
     * This method handles access to the features that are
     * accesible for non authenticated users
     * @param bool $onlyContent
     * @return void
     */
    protected function handleNotLoggedIn(bool $onlyContent = false): void
    {
        ActionRegistry::loadModuleActions();
        $actions = ActionRegistry::getActions();

        if ($this->getAction()) {
            $action_permission = ActionRegistry::getActionPermission(
                $this->getAction()
            );
            if ($action_permission && $action_permission === '*') {
                Vars::set('action_filename', $actions[$this->getAction()]);
                echo Template::executeDefaultOrOwnTemplate(
                    'backend/container.php'
                );
            }
        }

        if (isset($_GET['register'])) {
            do_event('before_register_form');
            require 'inc/registerform.php';
            do_event('after_register_form');
        } elseif (isset($_GET['reset_password'])) {
            do_event('before_reset_password_form');
            require 'inc/reset_password.php';
            do_event('before_after_password_form');
        } else {
            do_event('before_login_form');
            require 'inc/loginform.php';
            do_event('after_login_form');
        }
    }

    /**
     * This method handles all actions by authenticated users
     * @param bool $onlyContent
     * @return void
     */
    protected function handleLoggedIn(bool $onlyContent = false): void
    {
        $permissionChecker = new PermissionChecker(get_user_id());

        if (! $onlyContent) {
            require 'inc/adminmenu.php';
        }

        ActionRegistry::loadModuleActions();
        $actions = ActionRegistry::getActions();

        do_event('register_actions');

        if ($_SESSION['require_password_change']) {
            require 'inc/change_password.php';
        } elseif (isset($actions[$this->getAction()])) {
            $requiredPermission = ActionRegistry::getActionPermission(
                $this->getAction()
            );
            if (! $requiredPermission
                    || (
                        $requiredPermission
                        && $permissionChecker->hasPermission($requiredPermission))
            ) {
                Vars::set('action_filename', $actions[$this->getAction()]);
                echo Template::executeDefaultOrOwnTemplate(
                    'backend/container.php'
                );
            } else {
                noPerms();
            }
        } else {
            translate('action_not_found');
        }
    }

    /**
     * Outputs minified HTML
     * @return void
     */
    public function outputMinified(): void
    {
        $generatedHtml = ob_get_clean();
        $options = [
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED
        ];
        $HTMLMinify = new HTMLMinify($generatedHtml, $options);
        $generatedHtml = $HTMLMinify->process();
        $generatedHtml = StringHelper::removeEmptyLinesFromString(
            $generatedHtml
        );

        echo $generatedHtml;
    }

    /**
     * Run cron events of modules
     * @return void
     */
    public function doCronEvents(): void
    {
        do_event('before_admin_cron');
        do_event('admin_cron');
        do_event('after_admin_cron');
    }
}
