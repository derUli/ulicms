<?php

class UserSettings
{

    public static function get($name, $type = null, $user_id = null)
    {
        if (! $user_id) {
            $user_id = get_user_id();
        }
        if (! $user_id) {
            return null;
        }
        $query = Database::pQuery("select value from `{prefix}user_settings` where user_id = ? and name = ?", array(
            intval($user_id),
            strval($name)
        ), true);
        if (! Database::any($query)) {
            return null;
        }
        $data = Database::fetchSingle($query);
        $value = Settings::convertVar($data->value, $type);
        return $value;
    }

    public static function set($name, $value, $type = null, $user_id = null)
    {
        if (! $user_id) {
            $user_id = get_user_id();
        }
        $query = Database::pQuery("select 'nothing' from `{prefix}user_settings` where user_id = ? and name = ?", array(
            intval($user_id),
            strval($name)
        ), true);
        if (Database::any($query)) {
            Database::pQuery("update `{prefix}user_settings` set value = ? where user_id = ? and name = ?", array(
                Settings::convertVar($value, $type),
                intval($user_id),
                strval($name)
            ), true);
        } else {
            Database::pQuery("insert into `{prefix}user_settings` (user_id, name, value) values (?, ?, ?)", array(
                intval($user_id),
                strval($name),
                Settings::convertVar($value, $type)
            ), true);
        }
    }

    public static function register($name, $value, $type = null, $user_id = null)
    {
        if (! $user_id) {
            $user_id = get_user_id();
        }
        if (! UserSettings::get($name, $type, $user_id)) {
            UserSettings::set($name, $value, $type, $user_id);
        }
    }

    public static function init($name, $value, $type = null, $user_id = null)
    {
        UserSettings::register($name, $value, $type, $user_id);
    }

    public static function delete($name, $user_id = null)
    {
        if (! $user_id) {
            $user_id = get_user_id();
        }
        Database::pQuery("delete from `{prefix}user_settings` where user_id = ? and name = ?", array(
            intval($user_id),
            strval($name)
        ), true);
    }
}