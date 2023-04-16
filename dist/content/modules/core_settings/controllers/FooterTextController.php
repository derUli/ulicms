<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Utils\CacheUtil;

class FooterTextController extends \App\Controllers\Controller
{
    public function _savePost(): void
    {
        Settings::set('footer_text', Request::getVar('footer_text'));
        CacheUtil::clearPageCache();
    }

    public function savePost(): void
    {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
            HttpStatusCode::OK,
            ModuleHelper::buildActionURL('design')
        );
    }
}
