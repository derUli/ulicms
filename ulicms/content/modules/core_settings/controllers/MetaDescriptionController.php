<?php

class MetaDescriptionController extends Controller
{

    public function savePost()
    {
        $languages = getAllLanguages();
        for ($i = 0; $i < count($languages); $i ++) {
            $lang = $languages[$i];
            if (isset($_POST["meta_description_" . $lang])) {
                $page = $_POST["meta_description_" . $lang];
                Settings::set("meta_description_" . $lang, $page);
                if ($lang == Settings::get("default_language")) {
                    Settings::set("meta_description", $page);
                }
            }
        }
        // if called by ajax return no content to improve performance
        if (Request::isAjaxRequest()) {
            HTTPStatusCodeResult(HttpStatusCode::OK);
        }
        Request::redirect(ModuleHelper::buildActionURL("meta_description"));
    }
}