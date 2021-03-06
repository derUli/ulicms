<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class SiteSloganController extends Controller
{
    public function _savePost(): void
    {
        $languages = getAllLanguages();

        for ($i = 0; $i < count($languages); $i++) {
            $lang = $languages[$i];
            if (isset($_POST["site_slogan_" . $lang])) {
                $page = $_POST["site_slogan_" . $lang];
                Settings::set("site_slogan_" . $lang, $page);
                if ($lang === Settings::get("default_language")) {
                    Settings::set("site_slogan", $page);
                }
            }
        }

        CacheUtil::clearPageCache();
    }

    public function savePost(): void
    {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
            HttpStatusCode::OK,
            ModuleHelper::buildActionURL("site_slogan")
        );
    }
}
