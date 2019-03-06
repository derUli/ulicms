<?php

class MetaKeywordsController extends Controller
{

    public function savePost()
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
        Request::redirect(ModuleHelper::buildActionURL("meta_keywords"));
    }
}