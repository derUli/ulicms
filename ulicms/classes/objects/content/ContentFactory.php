<?php

use UliCMS\Exceptions\FileNotFoundException;

class ContentFactory {

    public static function getCurrentPage() {
        return ContentFactory::getBySlugAndLanguage(get_requested_pagename(), getCurrentLanguage(true));
    }

    public static function getByID($id) {
        $id = intval($id);
        $query = Database::query("SELECT `id`, `type` FROM `" . tbname("content") . "` where id = " . $id);
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            return self::getContentObjectByID($result);
        } else {
            throw new FileNotFoundException("No page with id $id");
        }
    }

    public static function getBySlugAndLanguage($name, $language) {
        $name = Database::escapeValue($name);
        $language = Database::escapeValue($language);
        $query = Database::query("SELECT id, `type` FROM `" . tbname("content") . "` where `slug` = '$name' and `language` = '$language'");
        if (Database::getNumRows($query) > 0) {
            $result = Database::fetchObject($query);
            return self::getContentObjectByID($result);
        } else {
            throw new FileNotFoundException("No page with this combination of $name and $language");
        }
    }

    private static function getContentObjectByID($row) {
        $retval = null;
        $type = $row->type;
        $mappings = TypeMapper::getMappings();
        if (isset($mappings[$type]) and StringHelper::isNotNullOrEmpty($mappings[$type]) and class_exists($mappings[$type])) {
            $retval = new $mappings[$type]();
            $retval->loadByID($row->id);
        }

        return $retval;
    }

    public static function getAll($order = "id") {
        $result = array();
        $sql = "SELECT id, `type` FROM " . tbname("content") . " ORDER BY $order";
        $query = Database::query($sql);
        while ($row = Database::fetchObject($query)) {
            $result[] = self::getContentObjectByID($row);
        }
        return $result;
    }

    public static function getAllRegular($order = "id") {
        $result = array();
        $sql = "SELECT id, `type` FROM " . tbname("content") . " where type not in ('link', 'language_link', 'node') ORDER BY $order";
        $query = Database::query($sql);
        while ($row = Database::fetchObject($query)) {
            $result[] = self::getContentObjectByID($row);
        }
        return $result;
    }

    public static function getAllByLanguage($language, $order = "id") {
        $language = Database::escapeValue($language);
        $result = array();
        $sql = "SELECT id, `type` FROM " . tbname("content") . " where `language` = '$language' ORDER BY $order";
        $query = Database::query($sql);
        while ($row = Database::fetchObject($query)) {
            $result[] = self::getContentObjectByID($row);
        }
        return $result;
    }

    public static function getAllByMenu($menu, $order = "id") {
        $menu = Database::escapeValue($menu);
        $result = array();
        $sql = "SELECT id, `type` FROM " . tbname("content") . " where `menu` = '$menu' ORDER BY $order";
        $query = Database::query($sql);
        while ($row = Database::fetchObject($query)) {
            $result[] = self::getContentObjectByID($row);
        }
        return $result;
    }

    public static function getAllWithComments($order = "title") {
        $result = array();
        $sql = "select type, a.id  from {prefix}content a inner join {prefix}comments c on c.content_id = a.id group by c.content_id order by a.{$order}";
        $query = Database::query($sql, true) or die(Database::getError());

        while ($row = Database::fetchObject($query)) {
            $result[] = self::getContentObjectByID($row);
        }
        return $result;
    }

    public static function getForFilter($language = null, $category_id = null, $menu = null, $parent_id = null, $order_by = "title", $order_direction = "asc", $type = null, $limit = null) {
        $result = array();
        $sql = "select id, `type` from " . tbname("content") . " where active = 1 and deleted_at is null and ";
        if ($language !== null and $language !== "") {
            $language = Database::escapeValue($language);
            $sql .= "language = '$language' and ";
        }
        if ($category_id !== null and $category_id !== 0) {
            $category_id = intval($category_id);
            $sql .= "category_id = $category_id and ";
        }
        if ($menu !== null and $menu !== "") {
            $menu = Database::escapeValue($menu);
            $sql .= "menu = '$menu' and ";
        }

        if ($parent_id !== null and $parent_id !== 0) {
            $parent_id = intval($parent_id);
            $sql .= "parent = $parent_id and ";
        }

        if ($type !== null and $type !== "") {
            $type = Database::escapeValue($type);
            $sql .= "type = '$type' and ";
        }

        $sql .= "1=1 ";

        $order_by = Database::escapeName($order_by);

        if ($order_direction != "desc") {
            $order_direction = "asc";
        }
        $sql .= " order by $order_by $order_direction";

        if (!is_null($limit) and $limit > 0) {
            $sql .= " limit " . $limit;
        }

        $query = Database::query($sql) or die(Database::error());

        while ($row = Database::fetchObject($query)) {
            $result[] = self::getContentObjectByID($row);
        }
        return $result;
    }

    public static function getAllByMenuAndLanguage($menu, $language, $order = "id") {
        $menu = Database::escapeValue($menu);
        $language = Database::escapeValue($language);
        $result = array();
        $sql = "SELECT id, `type` FROM " . tbname("content") . " where `menu` = '$menu' and language = '$language' ORDER BY $order";
        $query = Database::query($sql);
        while ($row = Database::fetchObject($query)) {
            $result[] = self::getContentObjectByID($row);
        }
        return $result;
    }

    public static function filterByEnabled($elements, $enabled = 1) {
        $result = array();
        foreach ($elements as $element) {
            if ($element->active == $enabled) {
                $result[] = $element;
            }
        }
        return $result;
    }

    public static function filterByCategory($elements, $category_id = 1) {
        $result = array();
        foreach ($elements as $element) {
            if ($element->category_id == $category_id) {
                $result[] = $element;
            }
        }
        return $result;
    }

    public static function filterByAuthor($elements, $author_id = 1) {
        $result = array();
        foreach ($elements as $element) {
            if ($element->author_id == $author_id) {
                $result[] = $element;
            }
        }
        return $result;
    }

    public static function filterByLastChangeBy($elements, $lastchangeby = 1) {
        $result = array();
        foreach ($elements as $element) {
            if ($element->lastchangeby == $lastchangeby) {
                $result[] = $element;
            }
        }
        return $result;
    }

}
