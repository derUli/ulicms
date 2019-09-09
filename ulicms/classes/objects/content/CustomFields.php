<?php

declare(strict_types=1);

// This class contains methods to manipulate CustomFields
// defined by modules
class CustomFields {

    public static function set(string $name, $value, ?int $content_id = null, $addPrefix = false): ?bool {
        if (is_null($content_id)) {
            $content_id = get_ID();
        }
        if ($addPrefix) {
            $page = ContentFactory::getByID($content_id);
            $name = "{$page->type}_{$name}";
        }
        // use two nullbytes as seperator for arrays
        if (is_array($value)) {
            $value = join("\0\0", $value);
        } else if (is_bool($value)) {
            $value = strval(intval($value));
        } else if (!is_null($value)) {
            $value = strval($value);
        }

        $content_id = intval($content_id);
        $args = array(
            $content_id,
            $name
        );
        $sql = "Select id from {prefix}custom_fields where content_id = ? and name = ?";
        $result = Database::pQuery($sql, $args, true);
        if (Database::getNumRows($result) > 0) {
            $result = Database::fetchObject($result);
            if (is_null($value)) {
                $args = array(
                    intval($result->id)
                );
                $sql = "DELETE FROM {prefix}custom_fields where id = ?";
                return Database::pQuery($sql, $args, true);
            } else {
                $args = array(
                    $value,
                    $name,
                    $content_id
                );
                $sql = "UPDATE {prefix}custom_fields set value = ? where name = ? and content_id = ?";
                return Database::pQuery($sql, $args, true);
            }
        } else if (!is_null($value)) {
            $args = array(
                $content_id,
                $name,
                $value
            );
            $sql = "INSERT INTO {prefix}custom_fields (content_id, name, value) VALUES(?, ?, ?)";
            return Database::pQuery($sql, $args, true);
        }
        return false;
    }

    public static function getAll(?int $content_id = null, bool $removePrefix = true): array {
        $fields = [];
        if (is_null($content_id)) {
            $content_id = get_ID();
        }

        $content_id = intval($content_id);
        $args = array(
            $content_id
        );
        $sql = "Select name, value from {prefix}custom_fields where content_id = ?";
        $result = Database::pQuery($sql, $args, true);

        while ($row = Database::fetchObject($result)) {
            $name = $row->name;

            if ($removePrefix) {
                $page = ContentFactory::getByID($content_id);
                $prefix = "{$page->type}_";
                $name = remove_prefix($name, $prefix);
            }
            $fields[$name] = $row->value;
        }
        return $fields;
    }

    public static function get(string $name, ?int $content_id = null, $addPrefix = true) {
        if (is_null($content_id)) {
            $content_id = get_ID();
        }
        if ($addPrefix) {
            $page = ContentFactory::getByID($content_id);
            $name = "{$page->type}_{$name}";
        }
        $content_id = intval($content_id);
        $args = array(
            $content_id,
            $name
        );
        $sql = "Select value from {prefix}custom_fields where content_id = ? and name = ?";
        $result = Database::pQuery($sql, $args, true);
        if (Database::getNumRows($result) > 0) {
            $dataset = Database::fetchObject($result);
            $value = $dataset->value;
            // if string contains double null bytes it is an array
            // FIXME: Use new boolean "array" Attribute
            if (str_contains("\0\0", $value)) {
                $value = explode("\0\0", $value);
            }
            return $value;
        }
        return null;
    }

}
