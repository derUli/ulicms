<?php

use UliCMS\Exceptions\NotImplementedException;

class FooterTextController extends Controller {

    public function savePost() {
        Settings::set("footer_text", Request::getVar("footer_text"));
        Response::sendHttpStatusCodeResultIfAjax(HttpStatusCode::OK, ModuleHelper::buildActionURL("design"));
    }

}
