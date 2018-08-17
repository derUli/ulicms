<?php

class Gallery2019Controller extends Controller
{

    private $moduleName = "gallery2019";

    public function uninstall()
    {
        $migrator = new DBMigrator("module/{$this->moduleName}", ModuleHelper::buildModuleRessourcePath($this->moduleName, "sql/down"));
        $migrator->migrate();
    }

    public function clearCache()
    {}
}