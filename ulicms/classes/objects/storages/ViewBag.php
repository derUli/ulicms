<?php

class ViewBag
{

    private static $vars = array();

    public static function get($var)
    {
        if (isset(self::$vars[$var])) {
            return self::$vars[$var];
        }
        return null;
    }

    public static function set($var, $val)
    {
        self::$vars[$var] = $val;
    }

    public static function delete($var)
    {
        if (isset(self::$vars[$var])) {
            unset(self::$vars[$var]);
        }
    }

    public static function clear()
    {
        self::$vars = array();
    }

    public static function getAllVars()
    {
        return self::$vars;
    }
}