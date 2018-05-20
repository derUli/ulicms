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
            ob_start();
            var_dump($query->getData($qString));
            $data = ob_get_clean();
            // TODO: Export as HTML
            TextResult($data);
            exit();
        }
    }
}