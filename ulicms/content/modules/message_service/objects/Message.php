<?php
use UliCMS\Exceptions\NotImplementedException;

class Message extends Model
{

    private $message;

    private $receiver_id;

    private $sender_id;

    public function loadByID($id)
    {
        $query = Database::pQuery("select * from {prefix}messages where id = ?", array(
            intval($id)
        ), true);
        $this->fillVars($query);
    }

    public function fillVars($query = null)
    {
        if ($query == null || Database::getNumRows($query) == 0) {
            $this->setID(null);
            $this->message = null;
            $this->receiver_id = null;
            $this->sender_id = null;
            return;
        }
        
        $result = Database::fetchObject($query);
        $this->setID($result->id);
        $this->message = $result->message;
        $this->receiver_id = $result->receiver_id ? intval($result->receiver_id) : null;
        $this->sender_id = $result->sender_id ? intval($result->sender_id) : null;
    }

    public function getFormattedMessage()
    {
        // TODO: do format
        return $this->getMessage();
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getSenderId()
    {
        return $this->sender_id;
    }

    public function getSender()
    {
        if ($this->sender_id) {
            return new User($this->sender_id);
        }
        return null;
    }

    public function getReceiverId()
    {
        return $this->receiver_id;
    }

    public function getReceiver()
    {
        if ($this->receiver_id) {
            return new User($this->receiver_id);
        }
    }

    public function delete()
    {
        if ($this->getID()) {
            Database::deleteFrom("messages", "id = " . intval($this->getID()));
            $this->setID(null);
        }
    }

    public static function getAll()
    {
        throw new NotImplementedException();
    }

    public static function getAllWithReceiver($receiver_id)
    {
        $messages = array();
        $query = Database::pQuery("select id from {prefix}messages where receiver_id = ?", array(
            intval($receiver_id)
        ), true);
        while ($row = Database::fetchObject($query)) {
            $messages[] = new Message($row->id);
        }
        return $messages;
    }
}