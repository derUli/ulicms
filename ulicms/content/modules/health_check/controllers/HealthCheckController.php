<?php

class HealthCheckController extends Controller
{

    private $moduleName = "health_check";

    public function getSettingsLinkText()
    {
        return get_translation("open");
    }

    public function getSettingsHeadline()
    {
        return "Health Check";
    }

    public function settings()
    {
        return Template::executeModuleTemplate($this->moduleName, "check.php");
    }

    public function getMySQLVersion()
    {
        $version = Database::getServerVersion();
        $version = preg_replace('/[^0-9.].*/', '', $version);
        return $version;
    }
}