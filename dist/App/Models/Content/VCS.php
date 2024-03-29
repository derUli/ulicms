<?php

declare(strict_types=1);

namespace App\Models\Content;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use Database;

// Version Control System for pages
// tracks content changes
class VCS {
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

    public static function getRevisionByID(int $history_id): ?object {
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

    public static function restoreRevision(int $history_id): bool {
        $result = Database::query('SELECT * FROM ' . Database::tableName('history') .
                ' WHERE id = ' . $history_id);

        if (Database::getNumRows($result) > 0) {
            $row = Database::fetchObject($result);
            $content_id = (int)$row->content_id;
            $lastmodified = time();
            $content = Database::escapeValue($row->content);
            return Database::query('UPDATE ' . Database::tableName('content') .
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
        $result = Database::query('SELECT * FROM ' . Database::tableName('history')
                . ' WHERE content_id = ' . $content_id . ' ORDER BY ' . $order);
        $retval = [];
        while ($row = Database::fetchObject($result)) {
            $retval[] = $row;
        }
        return $retval;
    }
}
