<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Constants\DefaultValues;
use App\Storages\Cached\MemstaticCached;

// class for handling system settings
class Settings extends MemstaticCached {
    public static function register(string $key, $value, $type = 'str'): void {
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
        $cachedValue = static::getFromCache($key);

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
        $result = Database::query('SELECT name, value FROM ' . tbname('settings') .
                " WHERE name='{$key}'");
        if (Database::getNumRows($result) > 0) {
            while ($row = Database::fetchObject($result)) {
                static::setToCache($row->name, $row->value);
                $value = static::convertVar($row->value, $type);
            }
        } else {
            static::setToCache($key, DefaultValues::NULL_VALUE);
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
        static::setToCache($key, $value);

        $key = db_escape($key);
        $originalValue = static::convertVar($value, $type);
        $value = db_escape($originalValue);
        $result = Database::query('SELECT id FROM ' . tbname('settings') .
                " WHERE name='{$key}'");
        if (Database::getNumRows($result) > 0) {
            Database::query('UPDATE ' . tbname('settings') . " SET value='{$value}' "
                    . "WHERE name='{$key}'");
        } else {
            Database::query('INSERT INTO ' . tbname('settings') . ' (name, value) '
                    . "VALUES('{$key}', '{$value}')");
        }
    }

    // Remove an configuration variable
    public static function delete(string $key): bool {
        static::deleteFromCache($key);
        $key = db_escape($key);
        Database::query('DELETE FROM ' . tbname('settings') . " WHERE name='{$key}'");
        return Database::getAffectedRows() > 0;
    }

    public static function convertVar($value, ?string $type) {
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

    public static function getAll(string $order = 'name'): array {
        $datasets = [];
        $result = Database::query('SELECT * FROM `{prefix}settings` '
                        . "order by {$order}", true);
        while ($dataset = Database::fetchObject($result)) {
            $datasets[] = $dataset;

            static::setToCache($dataset->name, $dataset->value);
        }

        return $datasets;
    }

    // converts a mapping string (such as domain2language mapping)
    // to an associative array
    // example mapping string
    // foo=>bar
    // hello=>world
    public static function mappingStringToArray(string $str): array {
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
    protected static function setToCache(string $key, $value): bool {
        $valueToStore = $value !== null ? $value : DefaultValues::NULL_VALUE;
        return parent::setToCache($key, $valueToStore);
    }
}
