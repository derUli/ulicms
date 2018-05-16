<?php
namespace MailQueue;

use Database;
use Exception;
use NotImplementedException;

class Mail extends \Model
{

    private $recipient;

    private $headers;

    private $subject;

    private $content;

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
            $this->content = $result->content;
            $this->created = strtotime($result->created);
        } else {
            $this->recipient = null;
            $this->headers = null;
            $this->subject = null;
            $this->content = null;
            $this->created = null;
        }
    }

    public function insert()
    {
        $sql = "insert into `{prefix}mail_queue` (recipient, headers, subject,
                content, created) values (?, ?, ?, ?, from_unixtime(?))";
        $args = array(
            $this->recipient,
            $this->headers,
            $this->subject,
            $this->content,
            $this->created
        );
        Database::pQuery($sql, $args, true);
        $this->setID(Database::getLastInsertID());
    }

    public function update()
    {
        throw new NotImplementedException();
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
        throw new NotImplementedException();
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

    public function getContent()
    {
        return $this->content;
    }

    public function getCreated()
    {
        return $this->created;
    }
    // TODO: Setter implementieren
}