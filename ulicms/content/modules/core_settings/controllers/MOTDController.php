<?php

class MOTDController extends Controller
{

    public function savePost()
    {
        if (StringHelper::isNullOrEmpty(Request::getVar("language"))) {
            Settings::set("motd", $_POST["motd"]);
        } else {
            Settings::set("motd_" . Request::getVar("language"), Request::getVar("motd"));
        }
        Request::redirect(ModuleHelper::buildActionURL("motd", ModuleHelper::buildQueryString(array(
            "save" => "1",
            "language" => Request::getVar("language")
        
        ), false)));
    }
}