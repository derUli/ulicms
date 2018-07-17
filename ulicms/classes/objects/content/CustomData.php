<?php

class CustomData
{

    private static $defaults = array();

    public static function get($page = null)
    {
        if (! $page) {
            $page = get_requested_pagename();
        }
        $sql = "SELECT `custom_data` FROM " . tbname("content") . " WHERE systemname='" . Database::escapeValue($page) . "'  AND language='" . Database::escapeValue($_SESSION["language"]) . "'";
        $query = Database::query($sql);
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            return json_decode($result->custom_data, true);
        }
        return null;
    }

    public static function set($var, $value, $page = null)
    {
        if (! $page) {
            $page = get_requested_pagename();
        }
        $data = self::get($page);
        if (is_null($data)) {
            $data = array();
        }
        $data[$var] = $value;
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return Database::query("UPDATE " . tbname("content") . " SET custom_data = '" . Database::escapeValue($json) . "' WHERE systemname='" . Database::escapeValue($page) . "'");
    }

    public static function delete($var = null, $page = null)
    {
        if (! $page) {
            $page = get_requested_pagename();
        }
        $data = self::get($page);
        if (is_null($data)) {
            $data = array();
        }
        // Wenn $var gesetzt ist, nur $var aus custom_data löschen
        if ($var) {
            if (isset($data[$var])) {
                unset($data[$var]);
            }
        } else {
            // Wenn $var nicht gesetzt ist, alle Werte von custom_data löschen
            $data = array();
        }
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return Database::query("UPDATE " . tbname("content") . " SET custom_data = '" . Database::escapeValue($json) . "' WHERE systemname='" . Database::escapeValue($page) . "'");
    }

    public static function getCustomDataOrSetting($name)
    {
        $data = CustomData::get();
        if (! is_null($data) and is_array($data) and isset($data[$name])) {
            return $data[$name];
        }
        return Settings::get($name);
    }

    public static function setDefault($key, $value)
    {
        self::$defaults[$key] = $value;
    }

    public static function getDefault($key)
    {
        if (! isset(self::$defaults[$key])) {
            return null;
        }
        return self::$defaults[$key];
    }

    public static function getDefaultJSON()
    {
        return json_readable_encode(self::$defaults);
    }
}