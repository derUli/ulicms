<?php

class MailQueueAdminController extends MainClass
{

    const MODULE_NAME = "mail_queue";

    public function settings()
    {
        return Template::executeModuleTemplate(self::MODULE_NAME, "admin.php");
    }

    public function doActionPost()
    {
        $action = Request::getVar("action");
        $ids = Request::getVar("ids");
        if (! $ids or ! is_array($ids) or StringHelper::isNullOrWhitespace($action)) {
            Response::redirect(ModuleHelper::buildAdminUrl(self::MODULE_NAME));
        }
        
        switch ($action) {
            case "delete":
                foreach ($ids as $id) {
                    $mail = new \MailQueue\Mail($id);
                    $mail->delete();
                }
                break;
            default:
                throw new BadMethodCallException("Action '$action' is not implemented");
                break;
        }
        Response::redirect(ModuleHelper::buildAdminUrl(self::MODULE_NAME));
    }

    public function getSettingsHeadline()
    {
        return get_translation("mail_queue");
    }

    public function getSettingsLinkText()
    {
        return get_translation("open");
    }

    public function cron()
    {
        $logger = LoggerRegistry::get("exception_log");
        
        $cfg = new CMSConfig();
        
        $mail_queue_interval = is_numeric($cfg->mail_queue_interval) ? intval($cfg->mail_queue_interval) : null;
        
        $mail_queue_limit = is_numeric($cfg->mail_queue_limit) ? intval($cfg->mail_queue_limit) : null;
        $error = false;
        if ($logger) {
            if (! $mail_queue_interval) {
                $logger->error('mail_queue: $mail_queue_interval is not set');
                $error = true;
            }
            if (! $mail_queue_limit) {
                $logger->error('mail_queue: $mail_queue_limit is not set');
                $error = true;
            }
            if ($error) {
                return;
            }
        }
        
        BetterCron::seconds("mail_queue/process", $mail_queue_interval, function () {
            $cfg = new CMSConfig();
            $mail_queue_limit = is_numeric($cfg->mail_queue_limit) ? intval($cfg->mail_queue_limit) : null;
            $queue = \MailQueue\MailQueue::getInstance();
            for ($i = 1; $i <= $mail_queue_limit; $i ++) {
                $mail = $queue->getNextMail();
                if (! $mail) {
                    return;
                }
                $mail->send();
            }
        });
    }

    public function uninstall()
    {
        $migrator = new DBMigrator("module/mail_queue", ModuleHelper::buildRessourcePath("mail_queue", "sql/down"));
        $migrator->rollback();
        
        $files = array(
            "ULICMS_ROOT/shell/mail_queue.php"
        );
        foreach ($files as $file) {
            $path = Path::resolve($file);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}