<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class MetaKeywordsController extends Controller
{
    public function savePost(): void
    {
        $languages = getAllLanguages();
        for ($i = 0; $i < count($languages); $i ++) {
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

        // if called by ajax return no content to improve performance
        if (Request::isAjaxRequest()) {
            HTTPStatusCodeResult(HttpStatusCode::OK);
        }
        Request::redirect(ModuleHelper::buildActionURL("meta_keywords"));
    }
}
