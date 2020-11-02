<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class MetaKeywordsController extends Controller {

    public function _savePost(): void {
        $languages = getAllLanguages();
        for ($i = 0; $i < count($languages); $i++) {
            $lang = $languages[$i];
            if (isset($_POST["meta_keywords_" . $lang])) {
                $page = $_POST["meta_keywords_" . $lang];
                Settings::set("meta_keywords_" . $lang, $page);
                if ($lang == Settings::get("default_language")) {
                    Settings::set("meta_keywords", $page);
                }
            }
        }

        CacheUtil::clearPageCache();
    }

    public function savePost(): void {
        $this->_savePost();
        
        Response::sendHttpStatusCodeResultIfAjax(
                HttpStatusCode::OK,
                ModuleHelper::buildActionURL("meta_keywords")
        );
    }

}
