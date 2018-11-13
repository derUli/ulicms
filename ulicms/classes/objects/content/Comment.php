<?php
namespace UliCMS\Data\Content;

use Database;
use ContentFactory;
use CommentStatus;
use InvalidArgumentException;
use Model;
use UliCMS\Exceptions\FileNotFoundException;
use UliCMS\Security\SpamChecker\SpamFilterConfiguration;
use UliCMS\Security\SpamChecker\CommentSpamChecker;

class Comment extends Model
{

    private $content_id;

    private $author_name;

    private $author_email;

    private $author_url;

    private $date;

    private $text;

    private $status = CommentStatus::DEFAULT;

    private $ip;

    private $useragent;

    const TABLE_NAME = "comments";

    public function loadByID($id)
    {
        $query = Database::selectAll("comments", array(), "id=" . intval($id));
        if ($query == null or ! Database::any($query)) {
            throw new FileNotFoundException("no comment with id " . intval($id));
        }
        $this->fillVars($query);
    }

    public function fillVars($query = null)
    {
        $data = Database::fetchObject($query);
        $this->setID($data->id);
        $this->setContentId($data->content_id);
        $this->setAuthorName($data->author_name);
        $this->setAuthorUrl($data->author_url);
        $this->setAuthorEmail($data->author_email);
        $this->setDate($data->date);
        $this->setText($data->text);
        $this->setStatus($data->status);
        $this->setIp($data->ip);
        $this->setUserAgent($data->useragent);
    }

    protected function insert()
    {
        if (! $this->getDate()) {
            $this->date = time();
        }
        Database::pQuery("INSERT INTO `{prefix}comments` 
            (`content_id`, 
             `author_name`, 
             `author_email`, 
             `author_url`, 
             `date`, 
             `text`, 
             `status`, 
             `ip`, 
             `useragent`) 
VALUES      ( ?, 
              ?, 
              ?, 
              ?, 
              FROM_UNIXTIME(?), 
              ?, 
              ?, 
              ?, 
              ?) ", array(
            $this->getContentId(),
            $this->getAuthorName(),
            $this->getAuthorEmail(),
            $this->getAuthorUrl(),
            $this->getDate(),
            $this->getText(),
            $this->getStatus(),
            $this->getIp(),
            $this->getUseragent()
        ), true);
        $this->setID(Database::getLastInsertID());
    }

    protected function update()
    {
        if (! $this->getDate()) {
            $this->date = time();
        }
        Database::pQuery("UPDATE `{prefix}comments` set
                         `content_id` = ?,
                         `author_name` = ?,
                         `author_email` = ?,
                         `author_url` = ?,
                         `date` = FROM_UNIXTIME(?),
                         `text` = ?,
                         `status` = ?,
                         `ip` = ?,
                         `useragent` = ?
                          where id = ?", array(
            $this->getContentId(),
            $this->getAuthorName(),
            $this->getAuthorEmail(),
            $this->getAuthorUrl(),
            $this->getDate(),
            $this->getText(),
            $this->getStatus(),
            $this->getIp(),
            $this->getUseragent(),
            $this->getID()
        ), true);
    }

    public function delete()
    {
        Database::deleteFrom("comments", "id = " . $this->getID());
        $this->setID(null);
    }

    // check if the comment is spam.
    // If spam is detected the function returns an array
    // of SpamDetectionResults
    // if the comment contains no spam the function
    // returns null
    public function isSpam()
    {
        $configuration = SpamFilterConfiguration::fromSettings();
        $checker = new CommentSpamChecker($this, $configuration);
        $result = null;
        if ($checker->doSpamCheck()) {
            $result = $checker->getErrors();
        }
        return $result;
    }

    public function getContentId()
    {
        return $this->content_id;
    }

    public function setContentId($val)
    {
        if (! is_numeric($val)) {
            throw new InvalidArgumentException("$val is not a number");
        }
        $this->content_id = intval($val);
    }

    public function getAuthorName()
    {
        return $this->author_name;
    }

    public function setAuthorName($val)
    {
        if (! is_string($val)) {
            throw new InvalidArgumentException("$val is not a string");
        }
        $this->author_name = strval($val);
    }

    public function getAuthorEmail()
    {
        return $this->author_email;
    }

    public function setAuthorEmail($val)
    {
        $this->author_email = ! is_null($val) ? strval($val) : null;
    }

    public function getAuthorUrl()
    {
        return $this->author_url;
    }

    public function setAuthorUrl($val)
    {
        $this->author_url = ! is_null($val) ? strval($val) : null;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($val)
    {
        if (is_string($val)) {
            $val = strtotime($val);
        } else if (! is_numeric($val)) {
            throw new InvalidArgumentException("$val is not an integer timestamp");
        }
        $this->date = intval($val);
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($val)
    {
        $this->text = ! is_null($val) ? strval($val) : null;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($val)
    {
        if (! is_string($val)) {
            throw new InvalidArgumentException("$val is not a status string");
        }
        $this->status = $val;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($val)
    {
        $this->ip = ! is_null($val) ? strval($val) : null;
    }

    public function getUserAgent()
    {
        return $this->useragent;
    }

    public function setUserAgent($val)
    {
        $this->useragent = ! is_null($val) ? strval($val) : null;
    }

    public function getContent()
    {
        if (! $this->getContentId()) {
            return null;
        }
        return ContentFactory::getByID($this->getContentId());
    }

    public static function getAllByContentId($content_id)
    {
        return self::getAllDatasets(self::TABLE_NAME, self::class, "id", "content_id = " . intval($content_id));
    }

    public static function getAllByStatus($status, $content_id = null)
    {
        $where = "status = '" . Database::escapeValue($status) . "'";
        if ($content_id) {
            $where .= " and content_id = " . intval($content_id);
        }
        return self::getAllDatasets(self::TABLE_NAME, self::class, "id", $where);
    }
}
