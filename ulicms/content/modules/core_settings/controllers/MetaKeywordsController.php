<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class MetaKeywordsController extends Controller
{
    public function _savePost(): void
    {
        $languages = getAllLanguages();
        for ($i = 0; $i < count($languages); $i++) {
            $lang = $languages[$i];
            $key = "meta_keywords_" . $lang;
            $meta_keywords = Request::getVar($key, "", "str");
            
            Settings::set("meta_keywords_" . $lang, $meta_keywords);
            if ($lang === Settings::get("default_language")) {
                Settings::set("meta_keywords", $meta_keywords);
            }
        }

        CacheUtil::clearPageCache();
    }

    public function savePost(): void
    {
        $this->_savePost();

        Response::sendHttpStatusCodeResultIfAjax(
            HttpStatusCode::OK,
            ModuleHelper::buildActionURL("meta_keywords")
        );
    }
}
