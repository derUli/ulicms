<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Utils\CacheUtil;
use UliCMS\Constants\HttpStatusCode;

class FrontPageSettingsController extends Controller {

    public function _savePost(): void {
        $languages = getAllLanguages();
        $languagesCount = count($languages);

        for ($i = 0; $i < $languagesCount; $i++) {
            $lang = $languages[$i];
            $varName = "frontpage_{$lang}";
            $page = Request::getVar($varName, "", "str");

            Settings::set($varName, $page);
            if ($lang === Settings::get("default_language")) {
                Settings::set("frontpage", $page);
            }
        }
        CacheUtil::clearPageCache();
    }

    public function savePost(): void {
        $this->_savePost();
        // if called by ajax return no content to improve performance
        Response::sendHttpStatusCodeResultIfAjax(
                HttpStatusCode::OK,
                ModuleHelper::buildActionURL("frontpage_settings")
        );
    }

}
