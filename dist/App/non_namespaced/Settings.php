<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\DefaultValues;
use App\Utils\MemstaticCached;

// class for handling system settings
class Settings extends MemstaticCached
{
    public static function register(string $key, $value, $type = 'str'): void
    {
        static::init($key, $value, $type);
    }

    public static function init(
        string $key,
        $value,
        ?string $type = 'str'
    ): bool {
        $success = false;
        if (! static::get($key)) {
            static::set($key, $value, $type);
            $success = true;
        }

        return $success;
    }

    // get a config variable
    public static function get(
        string $key,
        ?string $type = 'str'
    ) {
        $cachedValue = static::retrieveFromCache($key);

        // Is cached but null
        if ($cachedValue === DefaultValues::NULL_VALUE) {
            return null;
        }

        // Is cached and has a value
        if ($cachedValue !== null) {
            return static::convertVar($cachedValue, $type);
        }

        $value = null;
        $key = db_escape($key);
        $result = db_query('SELECT name, value FROM ' . tbname('settings') .
                " WHERE name='{$key}'");
        if (db_num_rows($result) > 0) {
            while ($row = db_fetch_object($result)) {
                static::storeInCache($row->name, $row->value);
                $value = static::convertVar($row->value, $type);
            }
        } else {
            static::storeInCache($key, DefaultValues::NULL_VALUE);
        }

        return $value;
    }

    public static function getLanguageSetting(
        string $name,
        ?string $language = null,
        ?string $type = 'str'
    ) {
        $retval = false;
        $settingsName = $language ? "{$name}_{$language}" : $name;

        $config = static::get($settingsName);
        if ($config) {
            $retval = $config;
        } else {
            $config = static::get($name, $type);
        }
        return $config;
    }

    public static function getLang(
        string $name,
        ?string $language = null,
        ?string $type = 'str'
    ) {
        return static::getLanguageSetting($name, $language, $type);
    }

    public static function setLanguageSetting(
        string $name,
        $value,
        ?string $language = null
    ): void {
        $settingsName = $language ? "{$name}_{$language}" : $name;

        if ($value) {
            Settings::set($settingsName, $value);
        } else {
            Settings::delete($settingsName);
        }
    }

    // Set a configuration Variable;
    public static function set(
        string $key,
        $value,
        ?string $type = 'str'
    ): void {
        static::storeInCache($key, $value);

        $key = db_escape($key);
        $originalValue = static::convertVar($value, $type);
        $value = db_escape($originalValue);
        $result = db_query('SELECT id FROM ' . tbname('settings') .
                " WHERE name='{$key}'");
        if (db_num_rows($result) > 0) {
            db_query('UPDATE ' . tbname('settings') . " SET value='{$value}' "
                    . "WHERE name='{$key}'");
        } else {
            db_query('INSERT INTO ' . tbname('settings') . ' (name, value) '
                    . "VALUES('{$key}', '{$value}')");
        }
    }

    // Remove an configuration variable
    public static function delete(string $key): bool
    {
        static::deleteInCache($key);
        $key = db_escape($key);
        db_query('DELETE FROM ' . tbname('settings') . " WHERE name='{$key}'");
        return Database::getAffectedRows() > 0;
    }

    public static function convertVar($value, ?string $type)
    {
        switch ($type) {
            case 'str':
                $value = (string)$value;
                break;
            case 'int':
                $value = (int)$value;
                break;
            case 'float':
                $value = (float)$value;
                break;
            case 'bool':

                if ($value === 'true') {
                    $value = true;
                } elseif ($value === 'false') {
                    $value = false;
                }

                $value = (int)(bool)$value;
                break;
        }
        return $value;
    }

    public static function getAll(string $order = 'name'): array
    {
        $datasets = [];
        $result = Database::query('SELECT * FROM `{prefix}settings` '
                        . "order by {$order}", true);
        while ($dataset = Database::fetchObject($result)) {
            $datasets[] = $dataset;

            static::storeInCache($dataset->name, $dataset->value);
        }

        return $datasets;
    }

    // converts a mapping string (such as domain2language mapping)
    // to an associative array
    // example mapping string
    // foo=>bar
    // hello=>world
    public static function mappingStringToArray(string $str): array
    {
        $str = trim($str);
        $str = normalizeLN($str, "\n");
        $lines = explode("\n", $str);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines, 'strlen');
        $result = [];
        foreach ($lines as $line) {
            // if a line starts with a hash skip it (comment)
            if (str_starts_with($line, '#')) {
                continue;
            }
            $splitted = explode('=>', $line);

            if (count($splitted) < 2) {
                continue;
            }

            $splitted = array_map('trim', $splitted);
            $key = $splitted[0];
            $value = $splitted[1];
            $result[$key] = $value;
        }
        return $result;
    }

     /**
      * Store setting in cache
      * @param string $key
      * @param type $value
      * @return bool
      */
    protected static function storeInCache(string $key, $value): bool
    {
        $valueToStore = $value !== null ? $value : DefaultValues::NULL_VALUE;
        return parent::storeInCache($key, $valueToStore);
    }
}
