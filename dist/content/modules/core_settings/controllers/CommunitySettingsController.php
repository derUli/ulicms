<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Utils\CacheUtil;

class CommunitySettingsController extends \App\Controllers\Controller {
    public function _savePost(): void {
        if (Request::getVar('comments_enabled')) {
            Settings::set('comments_enabled', '1');
        } else {
            Settings::delete('comments_enabled');
        }

        if (Request::getVar('comments_must_be_approved')) {
            Settings::set('comments_must_be_approved', '1');
        } else {
            Settings::delete('comments_must_be_approved');
        }

        if (isset($_POST['commentable_content_types'])
                && is_array($_POST['commentable_content_types'])) {
            Settings::set(
                'commentable_content_types',
                implode(';', $_POST['commentable_content_types'])
            );
        } else {
            Settings::delete('commentable_content_types');
        }

        CacheUtil::clearPageCache();
    }

    public function savePost(): void {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
            \App\Constants\HttpStatusCode::OK,
            \App\Helpers\ModuleHelper::buildActionURL(
                'community_settings',
                'save=1'
            )
        );
    }
}
