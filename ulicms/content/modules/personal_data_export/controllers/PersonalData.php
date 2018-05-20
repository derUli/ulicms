<?php

class PersonalData extends MainClass
{

    const MODULE_NAME = "personal_data_export";

    public function settings()
    {
        return Template::executeModuleTemplate(self::MODULE_NAME, "list.php");
    }

    public function getSettingsLinkText()
    {
        return get_translation("open");
    }

    public function getSettingsHeadline()
    {
        return get_translation("personal_data");
    }
}