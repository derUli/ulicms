<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class FrontPageSettingsController extends Controller {

    public function _savePost(): void {
        $languages = getAllLanguages();
        for ($i = 0; $i < count($languages); $i++) {
            $lang = $languages[$i];
            if (isset($_POST["frontpage_" . $lang])) {
                $page = $_POST["frontpage_" . $lang];
                Settings::set("frontpage_" . $lang, $page);
                if ($lang === Settings::get("default_language")) {
                    Settings::set("frontpage", $page);
                }
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
