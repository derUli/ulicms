<?php

declare(strict_types=1);

namespace UliCMS\Models\Content\Advertisement;

use Database;
use DB;
use function tbname;

class Banners {

    public static function getAll(string $order = "id"): array {
        $datasets = [];
        $sql = "SELECT id FROM " . tbname("banner") . " ORDER BY $order";
        $result = DB::query($sql);
        while ($row = DB::fetchObject($result)) {
            $banner = new Banner();
            $banner->loadByID($row->id);
            $datasets[] = $banner;
        }
        return $datasets;
    }

    public static function getByLanguage(string $language, string $order = "language"): array {
        $datasets = [];
        $language = DB::escapeValue($language);
        $sql = "SELECT id FROM " . tbname("banner") . " WHERE language = '$language' ORDER BY $order";
        $result = DB::query($sql);
        while ($row = DB::fetchObject($result)) {
            $banner = new Banner();
            $banner->loadByID($row->id);
            $datasets[] = $banner;
        }
        return $datasets;
    }

    public static function getByCategory(?int $category_id, string $order = "id"): array {
        $category_id = intval($category_id);
        $datasets = [];
        $sql = "SELECT id FROM " . tbname("banner") . " WHERE `category_id` = $category_id ORDER BY $order";
        $result = DB::query($sql);
        while ($row = DB::fetchObject($result)) {
            $banner = new Banner();
            $banner->loadByID($row->id);
            $datasets[] = $banner;
        }
        return $datasets;
    }

    public static function getByType(string $type = "gif", string $order = "language"): array {
        $type = DB::escapeValue($type);
        $datasets = [];
        $sql = "SELECT id FROM " . tbname("banner") . " WHERE `type` = '$type' ORDER BY $order";
        $result = DB::query($sql);
        while ($row = DB::fetchObject($result)) {
            $banner = new Banner();
            $banner->loadByID($row->id);
            $datasets[] = $banner;
        }
        return $datasets;
    }

    public static function getRandom(): ?Banner {
        $banner = null;

        $result = Database::pQuery("SELECT id FROM {prefix}banner
        WHERE enabled = 1 and
        (language IS NULL OR language = ? ) and
        (
        (
        date_from is not null and date_to is not null and
        CURRENT_DATE() >= date_from and CURRENT_DATE() <= date_to)
        or
        (date_from is not null and date_to is null and
        CURRENT_DATE() >= date_from )
        or
        (date_from is null and date_to is not null and
        CURRENT_DATE() <= date_to)
        or
        (date_from is null and date_to is null)
        )
        ORDER BY RAND() LIMIT 1", array(getCurrentLanguage()), true);

        if (Database::getNumRows($result)) {
            $data = Database::fetchObject($result);
            $banner = new Banner($data->id);
        }
        return $banner;
    }

}
