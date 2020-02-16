<?php

declare(strict_types=1);

use UliCMS\Exceptions\DatasetNotFoundException;
use UliCMS\Exceptions\UnknownContentTypeException;
use UliCMS\Models\Content\TypeMapper;

// this class contains methods to return one content model or an array of multiple content datasets
class ContentFactory {

    // this methods returns the model of the current page
    public static function getCurrentPage(): ?Content {
        return ContentFactory::getBySlugAndLanguage(
                        get_requested_pagename(),
                        getCurrentLanguage(true)
        );
    }

    public static function getByID(int $id): ?Content {
        $id = intval($id);
        $result = Database::query("SELECT `id`, `type` FROM `" .
                        tbname("content") . "` where id = " . $id);
        if (Database::getNumRows($result) > 0) {
            $dataset = Database::fetchObject($result);
            return self::getContentObjectByID($dataset);
        }
        throw new DatasetNotFoundException("No page with id $id");
    }

    public static function getBySlugAndLanguage(
            string $name,
            string $language
    ): ?Content {
        $name = Database::escapeValue($name);
        $language = Database::escapeValue($language);
        $result = Database::query("SELECT id, `type` FROM `" .
                        tbname("content") . "` where `slug` = '$name' "
                        . "and `language` = '$language'");
        if (Database::getNumRows($result) > 0) {
            $dataset = Database::fetchObject($result);
            return self::getContentObjectByID($dataset);
        }
        throw new DatasetNotFoundException("No page with this combination of "
                . "$name and $language");
    }

    private static function getContentObjectByID(object $row): ?Content {
        $retval = null;
        $type = $row->type;
        $mappings = TypeMapper::getMappings();
        if (isset($mappings[$type])
                and StringHelper::isNotNullOrEmpty($mappings[$type])
                and class_exists($mappings[$type])) {
            $retval = new $mappings[$type]();
            $retval->loadByID(intval($row->id));
        } else {
            $message = "Content with id={$row->id} has unknown content type "
                    . "\"{$type}\"";
            $logger = LoggerRegistry::get("exception_log");
            if ($logger) {
                $logger->error($message);
            }
            throw new UnknownContentTypeException(
                    $message
            );
        }

        return $retval;
    }

    public static function getAll(string $order = "id"): array {
        $datasets = [];
        $sql = "SELECT id, `type` FROM " . tbname("content") .
                " ORDER BY $order";
        $result = Database::query($sql);
        while ($row = Database::fetchObject($result)) {
            $datasets[] = self::getContentObjectByID($row);
        }
        return $datasets;
    }

    public static function getAllRegular(string $order = "id"): array {
        $datasets = [];
        $sql = "SELECT id, `type` FROM " . tbname("content") .
                " where type not in ('link', 'language_link', 'node') ORDER BY $order";
        $result = Database::query($sql);
        while ($row = Database::fetchObject($result)) {
            $datasets[] = self::getContentObjectByID($row);
        }
        return $datasets;
    }

    public static function getAllByLanguage(
            string $language,
            string $order = "id"
    ): array {
        $datasets = [];
        $language = Database::escapeValue($language);
        $sql = "SELECT id, `type` FROM " . tbname("content") .
                " where `language` = '$language' ORDER BY $order";
        $result = Database::query($sql);
        while ($row = Database::fetchObject($result)) {
            $datasets[] = self::getContentObjectByID($row);
        }
        return $datasets;
    }

    public static function getAllByParent(
            ?int $parent_id,
            string $order = "id"
    ): array {
        $contents = [];

        $sql = "SELECT id, type FROM {prefix}content ";

        $args = array(
            !is_null($parent_id) ? intval($parent_id) : null
        );

        $sql .= !is_null($parent_id) ? "where `parent_id` = ?" :
                "where `parent_id` IS ?";

        $sql .= " ORDER BY $order";

        $result = Database::pQuery($sql, $args, true);
        while ($row = Database::fetchObject($result)) {
            $contents[] = self::getContentObjectByID($row);
        }

        return $contents;
    }

    public static function getAllByMenu(
            string $menu,
            string $order = "id"):
    array {
        $menu = Database::escapeValue($menu);
        $datasets = [];
        $sql = "SELECT id, `type` FROM " . tbname("content") .
                " where `menu` = '$menu' ORDER BY $order";
        $result = Database::query($sql);
        while ($row = Database::fetchObject($result)) {
            $datasets[] = self::getContentObjectByID($row);
        }
        return $datasets;
    }

    public static function getAllByType(
            string $type,
            string $order = "id"):
    array {
        $type = Database::escapeValue($type);
        $datasets = [];
        $sql = "SELECT id, `type` FROM " . tbname("content")
                . " where `type` = '$type' ORDER BY $order";
        $result = Database::query($sql);
        while ($row = Database::fetchObject($result)) {
            $datasets[] = self::getContentObjectByID($row);
        }
        return $datasets;
    }

    public static function getAllWithComments(string $order = "title"): array {
        $datasets = [];
        $sql = "select type, a.id  from {prefix}content a inner join "
                . "{prefix}comments c on c.content_id = a.id group by "
                . "c.content_id order by a.{$order}";
        $result = Database::query($sql, true);

        while ($row = Database::fetchObject($result)) {
            $datasets[] = self::getContentObjectByID($row);
        }
        return $datasets;
    }

    public static function getForFilter(
            ?string $language = null,
            ?int $category_id = null,
            ?string $menu = null,
            ?int $parent_id = null,
            string $order_by = "title",
            string $order_direction = "asc",
            ?string $type = null,
            ?int $limit = null,
            ?int $offset = null
    ): array {
        $datasets = [];
        $sql = "select id, `type` from " . tbname("content") .
                " where active = 1 and deleted_at is null and ";
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
            $sql .= "parent_id = $parent_id and ";
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
        if (!is_null($offset)) {
            $sql .= " offset " . $offset;
        }

        $result = Database::query($sql) or die(Database::error());

        while ($row = Database::fetchObject($result)) {
            $datasets[] = self::getContentObjectByID($row);
        }
        return $datasets;
    }

    public static function getAllByMenuAndLanguage(
            string $menu,
            string $language,
            string $order = "id"
    ): array {
        $menu = Database::escapeValue($menu);
        $language = Database::escapeValue($language);
        $datasets = [];
        $sql = "SELECT id, `type` FROM " . tbname("content") .
                " where `menu` = '$menu' and language = '$language' "
                . "ORDER BY $order";
        $result = Database::query($sql);
        while ($row = Database::fetchObject($result)) {
            $datasets[] = self::getContentObjectByID($row);
        }
        return $datasets;
    }

    public static function filterByEnabled(
            array $elements,
            $enabled = 1
    ) {
        $result = [];
        foreach ($elements as $element) {
            if ($element->active == $enabled) {
                $result[] = $element;
            }
        }
        return $result;
    }

    public static function filterByCategory(
            array $elements,
            ?int $category_id = 1
    ): array {
        $result = [];
        foreach ($elements as $element) {
            if ($element->category_id == $category_id) {
                $result[] = $element;
            }
        }
        return $result;
    }

    public static function filterByAuthor(
            array $elements,
            ?int $author_id = 1
    ): array {
        $result = [];
        foreach ($elements as $element) {
            if ($element->author_id == $author_id) {
                $result[] = $element;
            }
        }
        return $result;
    }

    public static function filterByLastChangeBy(
            array $elements,
            ?int $lastchangeby = 1
    ): array {
        $result = [];
        foreach ($elements as $element) {
            if ($element->lastchangeby == $lastchangeby) {
                $result[] = $element;
            }
        }
        return $result;
    }

}
