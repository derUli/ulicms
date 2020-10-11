<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class FooterTextController extends Controller {

    public function _savePost(): void {
        Settings::set("footer_text", Request::getVar("footer_text"));
    }

    public function savePost(): void {
        $this->_savePost();

        CacheUtil::clearPageCache();

        Response::sendHttpStatusCodeResultIfAjax(
                HttpStatusCode::OK,
                ModuleHelper::buildActionURL("design")
        );
    }

}
