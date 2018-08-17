<?php

class Gallery2019Controller extends Controller
{

    private $moduleName = "gallery2019";

    public function uninstall()
    {
        $migrator = new DBMigrator("module/{$this->moduleName}", ModuleHelper::buildModuleRessourcePath($this->moduleName, "sql/down"));
        $migrator->rollback();
    }

    public function getSettingsLinkText()
    {
        return get_translation("edit");
    }

    public function getSettingsHeadline()
    {
        return get_translation("galleries");
    }

    public function settings()
    {
        return Template::executeModuleTemplate($this->moduleName, "list.php");
    }
}