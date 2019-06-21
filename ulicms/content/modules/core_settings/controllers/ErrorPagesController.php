<?php

use UliCMS\Utils\CacheUtil;

class ErrorPagesController extends Controller {

    public function savePost() {
        $errorPages = $_POST["error_page"];
        if (is_array($errorPages)) {
            foreach ($errorPages as $code => $languages) {
                foreach ($languages as $language => $page_id) {
                    if ($page_id > 0) {
                        Settings::setLanguageSetting("error_page_{$code}",
                                $page_id, $language
                        );
                    } else {
                        Settings::delete("error_page_{$code}_{$language}");
                    }
                }
            }
        }

        CacheUtil::clearPageCache();

        Response::sendHttpStatusCodeResultIfAjax(HttpStatusCode::OK,
                ModuleHelper::buildActionURL("error_pages"));
    }

}
