<?php

class SqlStudioController extends MainClass
{

    const MODULE_NAME = "sql_studio";

    public function settings()
    {
        return Template::executeModuleTemplate(self::MODULE_NAME, "form.php");
    }

    public function getSettingsHeadline()
    {
        return "SQL Studio";
    }

    public function getSettingsLinkText()
    {
        return get_translation("open");
    }
}