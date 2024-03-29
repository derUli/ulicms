<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Storages\Settings\MaintenanceMode;
use App\Utils\CacheUtil;
use jessedp\Timezones\Timezones;

class SimpleSettingsController extends \App\Controllers\Controller {
    public function _savePost(): void {

        do_event('before_safe_simple_settings');
        Settings::set('homepage_owner', $_POST['homepage_owner']);
        Settings::set('language', $_POST['language']);
        Settings::set('visitors_can_register', (int)isset($_POST['visitors_can_register']));

        // Maintenance Mode
        if(isset($_POST['maintenance_mode'])) {
            MaintenanceMode::getInstance()->enable();
        } else {
            MaintenanceMode::getInstance()->disable();
        }

        Settings::set('email', $_POST['email']);
        Settings::set('timezone', $_POST['timezone']);
        Settings::set('robots', $_POST['robots']);

        if (! isset($_POST['disable_password_reset'])) {
            Settings::set('disable_password_reset', 'disable_password_reset');
        } else {
            Settings::delete('disable_password_reset');
        }

        do_event('after_safe_simple_settings');

        CacheUtil::clearPageCache();
    }

    public function savePost(): void {
        $this->_savePost();

        Response::sendHttpStatusCodeResultIfAjax(
            \App\Constants\HttpStatusCode::OK,
            \App\Helpers\ModuleHelper::buildActionURL('settings_simple')
        );
    }

    public function getTimezones(): string {
        // TODO: Fork package and fix deprecation warning
        @$html = Timezones::create(
            'timezone',
            Settings::get('timezone'),
            [
                'attr' => [
                    'class' => 'form-control select2'
                ],
                'with_regions' => true
            ]
        );

        return str_replace(Timezones::WHITESPACE_SEP, ' ', $html);
    }
}
