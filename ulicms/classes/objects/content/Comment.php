<?php

namespace UliCMS\Models\Content;

use Database;
use ContentFactory;
use UliCMS\Constants\CommentStatus;
use InvalidArgumentException;
use Model;
use StringHelper;
use UliCMS\Exceptions\FileNotFoundException;
use UliCMS\Security\SpamChecker\SpamFilterConfiguration;
use UliCMS\Security\SpamChecker\CommentSpamChecker;

// TODO: Comment public static functions
class Comment extends Model {

    private $content_id;
    private $author_name;
    private $author_email;
    private $author_url;
    private $date;
    private $text;
    private $status = CommentStatus::DEFAULT_STATUS;
    private $ip;
    private $useragent;
    private $read = false;

    const TABLE_NAME = "comments";

    public function loadByID($id) {
        $result = Database::selectAll("comments", [], "id=" . intval($id));
        if ($result == null or ! Database::any($result)) {
            throw new FileNotFoundException("no comment with id " . intval($id));
        }
        $this->fillVars($result);
    }

    public function fillVars($result = null) {
        $data = Database::fetchObject($result);
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
        $this->setRead($data->read);
    }

    protected function insert() {
        if (!$this->getDate()) {
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
             `useragent`,
             `read`
             )
VALUES      ( ?,
              ?,
              ?,
              ?,
              FROM_UNIXTIME(?),
              ?,
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
            $this->getUseragent(),
            $this->isRead()
                ), true);
        $this->setID(Database::getLastInsertID());
    }

    protected function update() {
        if (!$this->getDate()) {
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
                         `useragent` = ?,
                         `read` = ?
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
            $this->isRead(),
            $this->getID()
                ), true);
    }

    public function delete() {
        Database::deleteFrom("comments", "id = " . $this->getID());
        $this->setID(null);
    }

    // check if the comment is spam.
    // If spam is detected the function returns an array
    // of SpamDetectionResults
    // if the comment contains no spam the function
    // returns null
    public function isSpam() {
        $configuration = SpamFilterConfiguration::fromSettings();
        $checker = new CommentSpamChecker($this, $configuration);
        $result = null;
        if ($checker->doSpamCheck()) {
            $result = $checker->getErrors();
        }
        return $result;
    }

    public function getContentId() {
        return $this->content_id;
    }

    public function setContentId($val) {
        if (!is_numeric($val)) {
            throw new InvalidArgumentException("$val is not a number");
        }
        $this->content_id = intval($val);
    }

    public function getAuthorName() {
        return $this->author_name;
    }

    public function setAuthorName($val) {
        if (!is_string($val)) {
            throw new InvalidArgumentException("$val is not a string");
        }
        $this->author_name = StringHelper::isNotNullOrWhitespace($val) ? strval($val) : null;
    }

    public function getAuthorEmail() {
        return $this->author_email;
    }

    public function setAuthorEmail($val) {
        $this->author_email = StringHelper::isNotNullOrWhitespace($val) ? strval($val) : null;
    }

    public function getAuthorUrl() {
        return $this->author_url;
    }

    public function setAuthorUrl($val) {
        $this->author_url = is_url($val) ? strval($val) : null;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($val) {
        if (is_string($val)) {
            $val = strtotime($val);
        } else if (!is_numeric($val)) {
            throw new InvalidArgumentException("$val is not an integer timestamp");
        }
        $this->date = intval($val);
    }

    public function getText() {
        return $this->text;
    }

    public function setText($val) {
        $this->text = StringHelper::isNotNullOrWhitespace($val) ? strval($val) : null;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($val) {
        if (!is_string($val)) {
            throw new InvalidArgumentException("$val is not a status string");
        }
        $this->status = $val;
    }

    public function getIp() {
        return $this->ip;
    }

    public function setIp($val) {
        $this->ip = StringHelper::isNotNullOrWhitespace($val) ? strval($val) : null;
    }

    public function getUserAgent() {
        return $this->useragent;
    }

    public function setUserAgent($val) {
        $this->useragent = StringHelper::isNotNullOrWhitespace($val) ? strval($val) : null;
    }

    public function getContent() {
        if (!$this->getContentId()) {
            return null;
        }
        return ContentFactory::getByID($this->getContentId());
    }

    public static function getAllByContentId($content_id, $order_by = "date desc") {
        return self::getAllDatasets(self::TABLE_NAME, self::class, $order_by, "content_id = " . intval($content_id));
    }

    public static function getAllByStatus($status, $content_id = null, $order = "date desc") {
        $where = "status = '" . Database::escapeValue($status) . "'";
        if ($content_id) {
            $where .= " and content_id = " . intval($content_id);
        }
        return self::getAllDatasets(self::TABLE_NAME, self::class, $order, $where);
    }

    public static function getAll($order = "id desc") {
        return self::getAllDatasets(self::TABLE_NAME, self::class, $order);
    }

    public static function getUnreadCount() {
        $result = Database::pQuery("select count(id) as amount from {prefix}comments where `read` = ?",
                        [false], true);
        $dataset = Database::fetchObject($result);
        return $dataset->amount;
    }

    public static function getReadCount() {
        $result = Database::pQuery("select count(id) as amount from {prefix}comments where `read` = ?",
                        [true], true);
        $dataset = Database::fetchObject($result);
        return $dataset->amount;
    }

    public static function getAllCount() {
        $result = Database::pQuery("select count(id) as amount from {prefix}comments", [], true);
        $dataset = Database::fetchObject($result);
        return $dataset->amount;
    }

    public static function deleteIpsAfter48Hours($keepSpamIps = false) {
        $sql = "update {prefix}comments set ip = null WHERE date < FROM_UNIXTIME(UNIX_TIMESTAMP(NOW() - INTERVAL 2 DAY)) and ip is not null";
        if ($keepSpamIps) {
            $sql .= " and status <> 'spam'";
        }
        Database::query($sql, true);
        return Database::getAffectedRows();
    }

    public static function checkIfCommentWithIpExists($ip, $status = CommentStatus::SPAM) {
        $sql = "select ip from {prefix}comments where ip = ?";
        $args = array(
            strval($ip)
        );
        if ($status) {
            $sql .= " and status = ?";
            $args[] = strval($status);
        }
        $result = Database::pQuery($sql, $args, true);
        return Database::any($result);
    }

    public function isRead() {
        return boolval($this->read);
    }

    public function setRead($val) {
        $this->read = boolval($val);
    }

}
