<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class HomepageTitleController extends Controller {

    public function _savePost(): void {
        $languages = getAllLanguages();
        for ($i = 0; $i < count($languages); $i++) {
            $lang = $languages[$i];
            if (isset($_POST["homepage_title_" . $lang])) {
                $page = $_POST["homepage_title_" . $lang];
                Settings::set("homepage_title_" . $lang, $page);
                if ($lang === Settings::get("default_language")) {
                    Settings::set("homepage_title", $page);
                }
            }
        }

        CacheUtil::clearPageCache();
    }

    public function savePost(): void {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
                HttpStatusCode::OK,
                ModuleHelper::buildActionURL("homepage_title")
        );
    }

}
