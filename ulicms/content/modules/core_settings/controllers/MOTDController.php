<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class MOTDController extends Controller {

    public function _savePost(): void {
        if (StringHelper::isNullOrEmpty(Request::getVar("language"))) {
            Settings::set("motd", $_POST["motd"]);
        } else {
            Settings::set(
                    "motd_" . Request::getVar("language"),
                    Request::getVar("motd")
            );
        }

        CacheUtil::clearPageCache();
    }

    public function savePost(): void {
        $this->_savePost();
        Response::sendHttpStatusCodeResultIfAjax(
                HttpStatusCode::OK,
                ModuleHelper::buildActionURL(
                        "motd",
                        ModuleHelper::buildQueryString(
                                [
                                    "save" => "1",
                                    "language" => Request::getVar("language")
                                ],
                                false
                        )
                )
        );
    }

}
