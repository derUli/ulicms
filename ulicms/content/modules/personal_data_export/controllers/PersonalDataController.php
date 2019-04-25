<?php

use GDPR\PersonalData\Query;
use UliCMS\Exceptions\NotImplementedException;

class PersonalDataController extends MainClass {

    const MODULE_NAME = "personal_data_export";

    public function settings() {
        return Template::executeModuleTemplate(self::MODULE_NAME, "list.php");
    }

    public function getSettingsLinkText() {
        return get_translation("open");
    }

    public function getSettingsHeadline() {
        return get_translation("personal_data");
    }

    public function deleteData() {
        $qString = Request::getVar("query", null, "str");
        $user = new User(get_user_id());
        if ($user->getEmail() == $qString) {
            ExceptionResult(get_translation("cant_delete_current_user"), HttpStatusCode::UNPROCESSABLE_ENTITY);
        }


        $query = new Query();
        $query->deleteData($qString);

        return ActionResult("personal_data_delete_success", $qString);
    }

    public function exportData() {
        $qString = Request::getVar("query", null, "str");
        if ($qString) {
            $query = new Query();
            ViewBag::set("person", $query->getData($qString));
            $escapedQString = str_replace("@", "_at_", trim($qString));
            $fileName = "data_export-" . date('Y-m-d_h-i') . "-" . $escapedQString . ".html";

            // HTMLResult(Template::executeModuleTemplate(self::MODULE_NAME, "export.php"));
            DownloadResultFromString(Template::executeModuleTemplate(self::MODULE_NAME, "export.php"), $fileName);
        }
    }

}
