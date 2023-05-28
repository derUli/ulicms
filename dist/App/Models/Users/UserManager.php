<?php

declare(strict_types=1);

namespace App\Models\Users;

use Database;
use User;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

class UserManager {
    /**
     * Get users by group id
     *
     * @param ?int $gid
     * @param ?string $order
     *
     * @return User[]
     */
    public function getUsersByGroupId(?int $gid, ?string $order = 'id'): array {
        $users = [];
        $sql = "select id from {prefix}users where `group_id` = ? order by {$order}";
        $args = [
            $gid
        ];
        $result = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchObject($result)) {
            $users[] = new User((int)$row->id);
        }
        return $users;
    }

    /**
     * Get all users
     *
     * @param ?string $order
     *
     * @return User[]
     */
    public function getAllUsers(string $order = 'id'): array {
        $users = [];
        $sql = "select id from {prefix}users order by {$order}";
        $result = Database::Query($sql, true);
        while ($row = Database::fetchObject($result)) {
            $users[] = new User((int)$row->id);
        }
        return $users;
    }

    /**
     * Get all users by lock status
     *
     * @param bool $locked
     * @param string $order
     *
     * @return User[]
     */
    public function getLockedUsers(
        bool $locked = true,
        string $order = 'id'
    ): array {
        $users = [];
        $sql = 'select id from {prefix}users where `locked` = ? '
                . "order by {$order}";
        $args = [
            (int)$locked
        ];
        $result = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchObject($result)) {
            $users[] = new User((int)$row->id);
        }
        return $users;
    }
}
