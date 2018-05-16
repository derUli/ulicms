<?php
namespace MailQueue;

use Database;
use Exception;
use Mailer;

class Mail extends \Model
{

    private $recipient;

    private $headers;

    private $subject;

    private $message;

    private $created;

    public function loadByID($id)
    {
        $sql = "select * from `{prefix}mail_queue` where id = ?";
        $args = array(
            intval($id)
        );
        $query = Database::pQuery($sql, $args, true);
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            $this->fillVars($result);
        } else {
            throw new Exception("No mail with the id {$id}");
        }
    }

    public function fillVars($result = null)
    {
        if ($result) {
            $this->recipient = $result->recipient;
            ;
            $this->headers = $result->headers;
            $this->subject = $result->subject;
            $this->message = $result->message;
            $this->created = strtotime($result->created);
        } else {
            $this->recipient = null;
            $this->headers = null;
            $this->subject = null;
            $this->message = null;
            $this->created = null;
        }
    }

    public function insert()
    {
        $sql = "insert into `{prefix}mail_queue` (recipient, headers, subject,
                message, created) values (?, ?, ?, ?, from_unixtime(?))";
        $args = array(
            $this->recipient,
            $this->headers,
            $this->subject,
            $this->message,
            $this->created
        );
        Database::pQuery($sql, $args, true);
        $this->setID(Database::getLastInsertID());
    }

    public function update()
    {
        if ($this->getID()) {
            $sql = "update `{prefix}mail_queue` set recipient = ?, 
                     headers = ?, subject = ?, message = ?, created = ?
                     where id = ?";
            $args = array(
                $this->recipient,
                $this->headers,
                $this->subject,
                $this->message,
                $this->created,
                $this->getID()
            );
            Database::pQuery($sql, $args, true);
        }
    }

    public function delete()
    {
        if ($this->getID()) {
            Database::pQuery("delete from `{prefix}mail_queue` where id = ?", array(
                $this->getID()
            ), true);
            $this->fillVars(null);
        }
    }

    public function send()
    {
        if (Mailer::send($this->recipient, $this->subject, $this->message, $this->headers)) {
            $this->delete();
        }
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getCreated()
    {
        return $this->created;
    }
    // TODO: Setter implementieren
}
