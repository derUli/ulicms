<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('no direct script access allowed');

class UserManager
{
    public function getUsersByGroupId(?int $gid, ?string $order = 'id'): array
    {
        $users = [];
        $sql = "select id from {prefix}users where `group_id` = ? order by $order";
        $args = [
            $gid
        ];
        $result = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchObject($result)) {
            $users[] = new User($row->id);
        }
        return $users;
    }

    public function getAllUsers(string $order = 'id'): array
    {
        $users = [];
        $sql = "select id from {prefix}users order by $order";
        $result = Database::Query($sql, true);
        while ($row = Database::fetchObject($result)) {
            $users[] = new User($row->id);
        }
        return $users;
    }

    public function getLockedUsers(
        bool $locked = true,
        string $order = 'id'
    ): array {
        $users = [];
        $sql = 'select id from {prefix}users where `locked` = ? '
                . "order by $order";
        $args = [
            (int)$locked
        ];
        $result = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchObject($result)) {
            $users[] = new User($row->id);
        }
        return $users;
    }
}
