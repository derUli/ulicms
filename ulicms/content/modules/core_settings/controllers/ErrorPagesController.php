<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class ErrorPagesController extends Controller
{
    public function _savePost(): void
    {
        $errorPages = $_POST["error_page"];
        if (is_array($errorPages)) {
            foreach ($errorPages as $code => $languages) {
                foreach ($languages as $language => $page_id) {
                    if ($page_id > 0) {
                        Settings::setLanguageSetting(
                            "error_page_{$code}",
                            $page_id,
                            $language
                        );
                    } else {
                        Settings::delete("error_page_{$code}_{$language}");
                    }
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
            ModuleHelper::buildActionURL("error_pages")
        );
    }
}
