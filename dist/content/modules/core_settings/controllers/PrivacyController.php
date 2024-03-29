<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Utils\CacheUtil;

class PrivacyController extends \App\Controllers\Controller {
    public function _savePost(): void {
        $language = basename(Request::getVar('language', null, 'str'));
        $varName = ! empty($language) ?
                "privacy_policy_checkbox_enable_{$language}" :
                'privacy_policy_checkbox_enable';

        if (Request::getVar('privacy_policy_checkbox_enable', 0, 'int')) {
            Settings::set($varName, 1);
        } else {
            Settings::delete($varName);
        }

        if (! isset($_POST['log_ip'])) {
            Settings::delete('log_ip');
        } else {
            Settings::set('log_ip', 'log_ip');
        }

        if (! isset($_POST['delete_ips_after_48_hours'])) {
            Settings::delete('delete_ips_after_48_hours');
        } else {
            Settings::set(
                'delete_ips_after_48_hours',
                'delete_ips_after_48_hours'
            );
        }

        if (isset($_POST['keep_spam_ips'])) {
            Settings::set('keep_spam_ips', '1');
        } else {
            Settings::delete('keep_spam_ips');
        }

        $varName = ! empty($language) ?
                "privacy_policy_checkbox_text_{$language}" :
                'privacy_policy_checkbox_text';

        $privacy_policy_checkbox_text = Request::getVar(
            'privacy_policy_checkbox_text',
            ''
        );
        Settings::set($varName, $privacy_policy_checkbox_text);

        CacheUtil::clearPageCache();
    }

    public function savePost(): void {
        $this->_savePost();

        $language = basename(Request::getVar('language', null, 'str'));

        Response::sendHttpStatusCodeResultIfAjax(
            \App\Constants\HttpStatusCode::OK,
            \App\Helpers\ModuleHelper::buildActionURL(
                'privacy_settings',
                "save=1&language={$language}"
            )
        );
    }
}
