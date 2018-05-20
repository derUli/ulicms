<?php
use GDPR\PersonalData\Query;

class PersonalDataController extends MainClass
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

    public function exportData()
    {
        $qString = Request::getVar("query", null, "str");
        if ($qString) {
            $query = new Query();
            ViewBag::set("person", $query->getData($qString));
            HTMLResult(Template::executeModuleTemplate(self::MODULE_NAME, "export.php"));
        }
    }
}