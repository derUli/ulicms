<?php

declare(strict_types=1);

namespace UliCMS\Models\Content;

use function db_query;
use function db_fetch_object;
use function db_num_rows;
use function db_escape;

// Version Control System for pages
class VCS {

    public static function createRevision(int $content_id, string $content, $user_id) {
        $content_id = intval($content_id);
        $content = db_escape($content);
        $user_id = intval($user_id);
        return db_query("INSERT INTO `" . tbname("history") . "` (content_id, content, user_id) VALUES($content_id, '$content', $user_id)");
    }

    public static function getRevisionByID(int $history_id): ?object {
        $history_id = intval($history_id);
        $query = db_query("SELECT * FROM " . tbname("history") . " WHERE id = " . $history_id);
        if (db_num_rows($query) > 0) {
            return db_fetch_object($query);
        }
        return null;
    }

    public static function restoreRevision(int $history_id): ?bool {
        $history_id = intval($history_id);
        $query = db_query("SELECT * FROM " . tbname("history") . " WHERE id = " . $history_id);
        if (db_num_rows($query) > 0) {
            $row = db_fetch_object($query);
            $content_id = intval($row->content_id);
            $lastmodified = time();
            $content = db_escape($row->content);
            return db_query("UPDATE " . tbname("content") . " SET content='$content', lastmodified = $lastmodified where id = $content_id");
        }
        return null;
    }

    public static function getRevisionsByContentID(int $content_id, string $order = "date DESC"): array {
        $content_id = intval($content_id);
        $query = db_query("SELECT * FROM " . tbname("history") . " WHERE content_id = " . $content_id . " ORDER BY " . $order);
        $retval = [];
        while ($row = db_fetch_object($query)) {
            $retval[] = $row;
        }
        return $retval;
    }

}
