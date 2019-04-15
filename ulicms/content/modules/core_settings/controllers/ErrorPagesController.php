<?php

class ErrorPagesController {

    public function savePost() {
        $language = Request::getVar("language");

        $error403_page = Request::getVar("error404_page");
        $error404_page = Request::getVar("error404_page");

        Settings::setLanguageSetting($error403_page, $error403_page, $language);
        Settings::setLanguageSetting($error403_page, $error404_page, $language);

        Response::sendHttpStatusCodeResultIfAjax(HttpStatusCode::OK, ModuleHelper::buildActionURL("error_pages", "save=1"));
    }

}
