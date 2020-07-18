<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class SiteSloganController extends Controller
{
    public function savePost(): void
    {
        $languages = getAllLanguages();

        if (isset($_POST["submit"])) {
            for ($i = 0; $i < count($languages); $i ++) {
                $lang = $languages[$i];
                if (isset($_POST["site_slogan_" . $lang])) {
                    $page = $_POST["site_slogan_" . $lang];
                    Settings::set("site_slogan_" . $lang, $page);
                    if ($lang == Settings::get("default_language")) {
                        Settings::set("site_slogan", $page);
                    }
                }
            }
        }

        CacheUtil::clearPageCache();

        // if called by ajax return no content to improve performance
        if (Request::isAjaxRequest()) {
            HTTPStatusCodeResult(HttpStatusCode::OK);
        }
        Request::redirect(ModuleHelper::buildActionURL("site_slogan"));
    }
}
