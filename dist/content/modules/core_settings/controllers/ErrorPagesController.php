<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Utils\CacheUtil;

class ErrorPagesController extends \App\Controllers\Controller {
    public function _savePost(): void {
        $errorPages = $_POST['error_page'];
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

    public function savePost(): void {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
            HttpStatusCode::OK,
            \App\Helpers\ModuleHelper::buildActionURL('error_pages')
        );
    }
}
