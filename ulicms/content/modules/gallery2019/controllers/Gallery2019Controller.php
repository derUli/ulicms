<?php

class Gallery2019Controller extends Controller
{

    public const MODULE_NAME = "gallery2019";

    public function uninstall()
    {
        $migrator = new DBMigrator("module/{self::MODULE_NAME}", ModuleHelper::buildModuleRessourcePath(self::MODULE_NAME, "sql/down"));
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
        return Template::executeModuleTemplate(self::MODULE_NAME, "gallery/list.php");
    }
}