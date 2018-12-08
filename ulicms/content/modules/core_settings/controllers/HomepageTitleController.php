<?php

class HomepageTitleController extends Controller
{

    public function savePost()
    {
        $languages = getAllLanguages();
        for ($i = 0; $i < count($languages); $i ++) {
            
            $lang = $languages[$i];
            if (isset($_POST["homepage_title_" . $lang])) {
                $page = db_escape($_POST["homepage_title_" . $lang]);
                setconfig("homepage_title_" . $lang, $page);
                if ($lang == Settings::get("default_language")) {
                    setconfig("homepage_title", $page);
                }
            }
        }
        Request::redirect(ModuleHelper::buildActionURL("homepage_title"));
    }
}