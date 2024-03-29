<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Utils\CacheUtil;

class MetaDescriptionController extends \App\Controllers\Controller {
    public function _savePost(): void {
        $languages = getAllLanguages();
        $languagesCount = count($languages);

        for ($i = 0; $i < $languagesCount; $i++) {
            $lang = $languages[$i];
            $key = 'meta_description_' . $lang;
            $meta_description = Request::getVar($key, '', 'str');

            Settings::set('meta_description_' . $lang, $meta_description);
            if ($lang === Settings::get('default_language')) {
                Settings::set('meta_description', $meta_description);
            }
        }

        CacheUtil::clearPageCache();
    }

    public function savePost(): void {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
            \App\Constants\HttpStatusCode::OK,
            \App\Helpers\ModuleHelper::buildActionURL('meta_description')
        );
    }
}
