<?php

declare(strict_types=1);

namespace App\Models\Content;

use Database;

use function db_escape;
use function db_fetch_object;
use function db_num_rows;
use function db_query;

// Version Control System for pages
// tracks content changes
class VCS
{
    public static function createRevision(
        int $content_id,
        string $content,
        int $user_id
    ): bool {
        return Database::pQuery(
            'INSERT INTO `{prefix}history` (content_id, content, user_id) '
                        . 'VALUES(?, ?,?)',
            [$content_id, $content, $user_id],
            true
        );
    }

    public static function getRevisionByID(int $history_id): ?object
    {
        $history_id = $history_id;
        $result = Database::pQuery(
            'SELECT * FROM `{prefix}history` '
                        . 'WHERE id = ?',
            [$history_id],
            true
        );
        if (Database::getNumRows($result) > 0) {
            return Database::fetchObject($result);
        }
        return null;
    }

    public static function restoreRevision(int $history_id): bool
    {
        $result = db_query('SELECT * FROM ' . tbname('history') .
                ' WHERE id = ' . $history_id);
        if (db_num_rows($result) > 0) {
            $row = db_fetch_object($result);
            $content_id = (int)$row->content_id;
            $lastmodified = time();
            $content = db_escape($row->content);
            return db_query('UPDATE ' . tbname('content') .
                    " SET content='{$content}', lastmodified = {$lastmodified} "
                    . "where id = {$content_id}");
        }
        return false;
    }

    public static function getRevisionsByContentID(
        int $content_id,
        string $order = 'date DESC'
    ): array {
        $content_id = (int)$content_id;
        $result = db_query('SELECT * FROM ' . tbname('history')
                . ' WHERE content_id = ' . $content_id . ' ORDER BY ' . $order);
        $retval = [];
        while ($row = db_fetch_object($result)) {
            $retval[] = $row;
        }
        return $retval;
    }
}
