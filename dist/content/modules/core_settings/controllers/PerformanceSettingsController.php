<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Utils\CacheUtil;

class PerformanceSettingsController extends \App\Controllers\Controller {
    public function _savePost(): void {
        if (isset($_POST['cache_enabled'])) {
            Settings::delete('cache_disabled');
        } else {
            Settings::set('cache_disabled', 'disabled');
        }
        if (isset($_POST['cache_period'])) {
            Settings::set('cache_period', $_POST['cache_period'] * 60);
        }

        $lazy_loading = $_POST['lazy_loading'] ?? [];

        $lazy_loading_img = (int)in_array('img', $lazy_loading);
        $lazy_loading_iframe = (int)in_array('iframe', $lazy_loading);

        Settings::set('lazy_loading_img', $lazy_loading_img);
        Settings::set('lazy_loading_iframe', $lazy_loading_iframe);
    }

    public function savePost(): void {
        $this->_savePost();

        Response::sendHttpStatusCodeResultIfAjax(
            HttpStatusCode::OK,
            \App\Helpers\ModuleHelper::buildActionUrl(
                'performance_settings',
                'save=1'
            )
        );
    }

    public function _clearCache(): void {
        CacheUtil::clearCache();
    }

    public function clearCache(): void {
        if (! is_logged_in()) {
            Response::redirect('index.php');
        }

        $this->_clearCache();
        Response::sendHttpStatusCodeResultIfAjax(
            HttpStatusCode::OK,
            \App\Helpers\ModuleHelper::buildActionURL('performance_settings')
        );
    }
}
