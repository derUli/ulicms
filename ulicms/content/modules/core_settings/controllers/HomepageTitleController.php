<?php

class HomepageTitleController extends Controller
{

    public function savePost()
    {
        $languages = getAllLanguages();
        for ($i = 0; $i < count($languages); $i ++) {
            
            $lang = $languages[$i];
            if (isset($_POST["homepage_title_" . $lang])) {
                $page = $_POST["homepage_title_" . $lang];
                Settings::set("homepage_title_" . $lang, $page);
                if ($lang == Settings::get("default_language")) {
                    Settings::set("homepage_title", $page);
                }
            }
        }
        Request::redirect(ModuleHelper::buildActionURL("homepage_title"));
    }
}