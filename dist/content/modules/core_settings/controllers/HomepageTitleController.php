<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Utils\CacheUtil;

class HomepageTitleController extends \App\Controllers\Controller {
    public function _savePost(): void {
        $languages = getAllLanguages();

        $languagesCount = count($languages);
        for ($i = 0; $i < $languagesCount; $i++) {
            $lang = $languages[$i];
            $varName = "homepage_title_{$lang}";
            $page = Request::getVar($varName, '', 'str');

            Settings::set($varName, $page);
            if ($lang === Settings::get('default_language')) {
                Settings::set('homepage_title', $page);
            }
        }

        CacheUtil::clearPageCache();
    }

    public function savePost(): void {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
            \App\Constants\HttpStatusCode::OK,
            \App\Helpers\ModuleHelper::buildActionURL('homepage_title')
        );
    }
}
