<?php

class NewsletterController extends Controller
{

    private $moduleName = "newsletter2";

    public function render()
    {
        return Template::executeModuleTemplate($this->moduleName, "subscribe.php");
    }

    public function settings()
    {
        return Template::executeModuleTemplate($this->moduleName, "admin.php");
    }

    public function uninstall()
    {}
}