<?php

declare(strict_types=1);

namespace App\Models\Content;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use App\Constants\CommentStatus;
use App\Exceptions\DatasetNotFoundException;
use App\Security\SpamChecker\CommentSpamChecker;
use App\Security\SpamChecker\SpamFilterConfiguration;
use ContentFactory;
use Database;
use InvalidArgumentException;
use Model;
use ModuleHelper;

// TODO: Comment public static functions
// This class is a comment model class
// Users can post comments to content types were comments are enabled
class Comment extends Model
{
    public const TABLE_NAME = 'comments';

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

    public function loadByID($id)
    {
        $result = Database::selectAll('comments', [], 'id=' . (int) $id);
        if ($result == null || ! Database::any($result)) {
            throw new DatasetNotFoundException('no comment with id ' .
                            (int) $id);
        }
        $this->fillVars($result);
    }

    public function fillVars($result = null)
    {
        $data = Database::fetchObject($result);
        $this->setID((int)$data->id);
        $this->setContentId((int)$data->content_id);
        $this->setAuthorName($data->author_name);
        $this->setAuthorUrl($data->author_url);
        $this->setAuthorEmail($data->author_email);
        $this->setDate($data->date);
        $this->setText($data->text);
        $this->setStatus($data->status);
        $this->setIp($data->ip);
        $this->setUserAgent($data->useragent);
        $this->setRead((bool) $data->read);
    }

    public function delete()
    {
        Database::deleteFrom('comments', 'id = ' . $this->getID());
        $this->setID(null);
    }

    // check if the comment is spam.
    // If spam is detected the function returns an array
    // of SpamDetectionResults
    // if the comment contains no spam the function
    // returns null
    public function isSpam(): ?array
    {
        $configuration = SpamFilterConfiguration::fromSettings();
        $checker = new CommentSpamChecker($this, $configuration);
        $result = null;
        if ($checker->doSpamCheck()) {
            $result = $checker->getErrors();
        }
        return $result;
    }

    public function getContentId(): ?int
    {
        return $this->content_id;
    }

    public function setContentId(int $val): void
    {
        $this->content_id = (int) $val;
    }

    public function getAuthorName(): ?string
    {
        return $this->author_name;
    }

    public function setAuthorName(?string $val): void
    {
        $this->author_name = ! empty($val) ?
                (string) $val : null;
    }

    public function getAuthorEmail(): ?string
    {
        return $this->author_email;
    }

    public function setAuthorEmail(?string $val): void
    {
        $this->author_email = ! empty($val) ?
                (string) $val : null;
    }

    public function getAuthorUrl(): ?string
    {
        return $this->author_url;
    }

    public function getCommentUrl(): ?string
    {
        return $this->content_id ?
                ModuleHelper::getFullPageURLByID($this->content_id)
                . "#comment-{$this->id}" : null;
    }

    public function setAuthorUrl(?string $val): void
    {
        $this->author_url = is_url($val) ? (string) $val : null;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($val): void
    {
        if (is_string($val)) {
            $val = strtotime($val);
        } elseif (! is_numeric($val)) {
            throw new InvalidArgumentException(
                var_dump_str($val) . ' is not an integer timestamp'
            );
        }
        $this->date = (int) $val;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $val): void
    {
        $this->text = ! empty($val) ?
                (string) $val : null;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $val): void
    {
        $this->status = $val;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $val): void
    {
        $this->ip = ! empty($val) ?
                (string) $val : null;
    }

    public function getUserAgent(): ?string
    {
        return $this->useragent;
    }

    public function setUserAgent(?string $val): void
    {
        $this->useragent = ! empty($val) ?
                (string) $val : null;
    }

    // returns the content where this comment is attached
    public function getContent()
    {
        if (! $this->getContentId()) {
            return null;
        }
        return ContentFactory::getByID($this->getContentId());
    }

    // returns all comments for a content by content_id
    public static function getAllByContentId(
        int $content_id,
        string $order_by = 'date desc'
    ): array {
        return self::getAllDatasets(
            self::TABLE_NAME,
            self::class,
            $order_by,
            'content_id = ' . (int) $content_id
        );
    }

    public static function getAllByStatus(
        string $status,
        ?int $content_id = null,
        string $order = 'date desc'
    ): array {
        $where = "status = '" . Database::escapeValue($status) . "'";
        if ($content_id) {
            $where .= ' and content_id = ' . (int) $content_id;
        }
        return self::getAllDatasets(
            self::TABLE_NAME,
            self::class,
            $order,
            $where
        );
    }

    public static function getAll(string $order = 'id desc'): array
    {
        return self::getAllDatasets(self::TABLE_NAME, self::class, $order);
    }

    // returns unread comments count to display at the comments icon
    // left to the hamburger menu
    public static function getUnreadCount(): int
    {
        $result = Database::pQuery(
            'select count(id) as amount from '
            . '{prefix}comments where `read` = ?',
            [false],
            true
        );
        $dataset = Database::fetchObject($result);
        return (int)$dataset->amount;
    }

    // returns the count of all read comments
    public static function getReadCount(): ?int
    {
        $result = Database::pQuery(
            'select count(id) as amount from '
            . '{prefix}comments where `read` = ?',
            [true],
            true
        );
        $dataset = Database::fetchObject($result);
        return (int) $dataset->amount;
    }

    // returns the count of all comments
    public static function getAllCount(): ?int
    {
        $result = Database::pQuery('select count(id) as amount from '
                        . '{prefix}comments', [], true);
        $dataset = Database::fetchObject($result);
        return (int)$dataset->amount;
    }

    // As enforces by the GDPR of the EU
    // it is not allowed to permanently save ips
    // however it may be required to save ips temporarly
    // to defend the system against bad bots
    // this method deletes ip addresses of comments after 48 hours
    public static function deleteIpsAfter48Hours(bool $keepSpamIps = false): int
    {
        $sql = 'update {prefix}comments set ip = null WHERE date < '
                . 'FROM_UNIXTIME(UNIX_TIMESTAMP(NOW() - INTERVAL 2 DAY)) '
                . 'and ip is not null';
        if ($keepSpamIps) {
            $sql .= " and status <> 'spam'";
        }
        Database::query($sql, true);
        return Database::getAffectedRows();
    }

    // check if a comment from this ip exists
    public static function checkIfCommentWithIpExists(
        ?string $ip,
        string $status = CommentStatus::SPAM
    ): bool {
        $sql = 'select ip from {prefix}comments where ip = ?';
        $args = [
            (string) $ip
        ];
        if ($status) {
            $sql .= ' and status = ?';
            $args[] = (string) $status;
        }
        $result = Database::pQuery($sql, $args, true);
        return Database::any($result);
    }

    // returns true if the comments was read by a backend user
    public function isRead(): bool
    {
        return (bool) $this->read;
    }

    public function setRead(?bool $val): void
    {
        $this->read = (bool) $val;
    }

    protected function insert()
    {
        if (! $this->getDate()) {
            $this->date = time();
        }
        Database::pQuery('INSERT INTO `{prefix}comments`
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
              ?) ', [
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
        ], true);
        $this->setID(Database::getLastInsertID());
    }

    protected function update()
    {
        Database::pQuery('UPDATE `{prefix}comments` set
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
                          where id = ?', [
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
        ], true);
    }
}
