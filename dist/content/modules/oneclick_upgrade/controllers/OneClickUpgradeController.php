<?php

use App\Controllers\MainClass;
use App\HTML\Alert;
use App\Storages\ViewBag;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class OneClickUpgradeController extends MainClass
{
    public const MODULE_NAME = 'oneclick_upgrade';

    public function accordionLayout(): void {
        echo Template::executeModuleTemplate(static::MODULE_NAME, 'Dashboard');
    }

    public function settings(): void {

        if (Request::isPost()) {
            Settings::set('oneclick_upgrade_channel', $_POST['oneclick_upgrade_channel']);
            echo Alert::success(get_translation('changes_were_saved'));
        }

        ViewBag::set('oneclick_upgrade_channel', Settings::get('oneclick_upgrade_channel'));
        ViewBag::set('channels', ['fast', 'slow']);

        echo Template::executeModuleTemplate(static::MODULE_NAME, 'admin');
    }

    public function getSettingsHeadline(): string {
        return get_translation(static::MODULE_NAME) . ' ' . get_translation('settings');
    }
}
