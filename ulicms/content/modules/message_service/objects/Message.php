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

    protected function insert()
    {
        Database::pQuery("insert into {prefix}messages
                          (message, receiver_id, sender_id)
                          values
                          (?,?,?)", array(
            $this->message,
            $this->receiver_id,
            $this->sender_id
        ), true);
        $this->setID(Database::getLastInsertID());
    }

    protected function update()
    {
        Database::pQuery("update {prefix}messages set message = ?, receiver_id = ?, sender_id = ? where id = ?", array(
            $this->message,
            $this->receiver_id,
            $this->sender_id,
            $this->getID()
        ), true);
    }

    public function getFormattedMessage()
    {
        $message = "Absender: " . $this->getSender()->getUsername() . "\n\n";
        $message .= normalizeLN($this->getMessage(), "\n");
        $message = trim($message);
        return $message;
    }

    public function send()
    {
        $this->save();
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

    public function setMessage($val)
    {
        $this->message = ! is_null($val) ? strval($val) : null;
    }

    public function setSenderId($val)
    {
        $this->sender_id = ! is_null($val) ? intval($val) : null;
    }

    public function setSender($val)
    {
        $this->sender_id = $val instanceof User ? $val->getID() : null;
    }

    public function setReceiverId($val)
    {
        $this->receiver_id = ! is_null($val) ? intval($val) : null;
    }

    public function setReceiver($val)
    {
        $this->receiver_id = $val instanceof User ? $val->getID() : null;
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
        $messages = array();
        $query = Database::pQuery("select id from {prefix}messages order by id", array(), true);
        while ($row = Database::fetchObject($query)) {
            $messages[] = new Message($row->id);
        }
        return $messages;
    }

    public static function getAllWithReceiver($receiver_id)
    {
        $messages = array();
        $query = Database::pQuery("select id from {prefix}messages where receiver_id = ? order by id", array(
            intval($receiver_id)
        ), true);
        while ($row = Database::fetchObject($query)) {
            $messages[] = new Message($row->id);
        }
        return $messages;
    }
}