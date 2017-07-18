<?php

class UpdateMyBrowser extends Controller
{

    public function frontendFooter()
    {
        return Template::executeModuleTemplate("updatemybrowser_org", "script");
    }
}