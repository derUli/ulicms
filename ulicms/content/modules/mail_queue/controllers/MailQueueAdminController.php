<?php
class MailQueueAdminController extends MainClass{
    const MODULE_NAME = "mail_queue";
    public function settings(){
        return Template::executeModuleTemplate(self::MODULE_NAME, "admin.php");
    }
    public function getSettingsHeadline(){
        return get_translation("mail_queue");
    }
    public function getSettingsLinkText(){
        return get_translation("open");
    }
}