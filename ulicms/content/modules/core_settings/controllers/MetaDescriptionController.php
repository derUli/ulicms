<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class MetaDescriptionController extends Controller
{
    public function _savePost(): void
    {
        $languages = getAllLanguages();
        for ($i = 0; $i < count($languages); $i++) {
            $lang = $languages[$i];
            $key = "meta_description_" . $lang;
            $meta_description = Request::getVar($key, "", "str");
            
            Settings::set("meta_description_" . $lang, $meta_description);
            if ($lang === Settings::get("default_language")) {
                Settings::set("meta_description", $meta_description);
            }
        }

        CacheUtil::clearPageCache();
    }

    public function savePost(): void
    {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
            HttpStatusCode::OK,
            ModuleHelper::buildActionURL("meta_description")
        );
    }
}
