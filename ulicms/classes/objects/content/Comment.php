<?php
namespace UliCMS\Data\Content;

use CommentStatus;
use Model;
use InvalidArgumentException;
use UliCMS\Exceptions\NotImplementedException;
use AntiSpamHelper;

class Comment extends Model
{

    private $content_id;

    private $author_name;

    private $author_email;

    private $author_url;

    private $date;

    private $content;

    private $status = CommentStatus::PENDING;

    private $ip;

    private $useragent;

    public function isSpam()
    {
        $isSpam = false;
        // if spamfilter is disabled don't perform a spam check
        if (! AntiSpamHelper::isSpamFilterEnabled()) {
            return $isSpam;
        }
        
        // TODO: Do spam checks
        // implement ISpamChecker interface and
        // based on that a CommentSpamChecker class
        // which performs a spamcheck and if spam is detected
        // creates an array with the reasons why the comment
        // was detected as spam
        return $isSpam;
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
        return $this->author_name;
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
        if (! is_numeric($val)) {
            throw new InvalidArgumentException("$val is not an integer timestamp");
        }
        $this->date = intval($val);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($val)
    {
        $this->content = ! is_null($val) ? strval($val) : null;
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

    public function setIp()
    {
        $this->ip = ! is_null($ip) ? strval($ip) : null;
    }

    public function getUserAgent()
    {
        return $this->useragent;
    }

    public function setUserAgent()
    {
        $this->useragent = ! is_null($ip) ? strval($ip) : null;
    }
}
