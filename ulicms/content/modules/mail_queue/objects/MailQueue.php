<?php
namespace MailQueue;

use Database;

class MailQueue
{

    private static $instance;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getAllMails()
    {
        $mails = array();
        
        $query = Database::query("select id from `{prefix}mail_queue` order by created asc");
        
        while ($row = Database::fetchObject($query)) {
            $mails[] = new Mail($row->id);
        }
        return $mails;
    }

    public function getNextMail()
    {
        $query = Database::query("select id from `{prefix}mail_queue` order by created asc limit 1");
        if (Database::getNumRows($query) == 0) {
            return null;
        }
        
        $result = Database::fetchObject($query);
        return new Mail($result->id);
    }

    public function flushMailQueue()
    {
        Database::truncateTable("mail_queue");
    }

    public function addMail($mail)
    {
        $mail->save();
    }

    public function removeMail($mail)
    {
        $mail->delete();
    }
}
