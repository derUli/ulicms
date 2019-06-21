<?php

class UserManager {

    public function getUsersByGroupId($gid, $order = "id") {
        $users = [];
        $sql = "select id from {prefix}users where `group_id` = ? order by $order";
        $args = array(
            intval($gid)
        );
        $query = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchObject($query)) {
            $users[] = new User($row->id);
        }
        return $users;
    }

    public function getAllUsers($order = "id") {
        $users = [];
        $sql = "select id from {prefix}users order by $order";
        $query = Database::Query($sql, true);
        while ($row = Database::fetchObject($query)) {
            $users[] = new User($row->id);
        }
        return $users;
    }

    public function getLockedUsers($locked = true, $order = "id") {
        $users = [];
        $sql = "select id from {prefix}users where `locked` = ? order by $order";
        $args = array(
            intval($locked)
        );
        $query = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchObject($query)) {
            $users[] = new User($row->id);
        }
        return $users;
    }

}
