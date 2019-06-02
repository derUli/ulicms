<?php

namespace UliCMS\Models\Content\Advertisement;

use Database;
use DB;
use function tbname;

class Banners {

    public static function getAll($order = "id") {
        $result = array();
        $sql = "SELECT id FROM " . tbname("banner") . " ORDER BY $order";
        $query = DB::query($sql);
        while ($row = DB::fetchObject($query)) {
            $banner = new Banner();
            $banner->loadByID($row->id);
            $result[] = $banner;
        }
        return $result;
    }

    public static function getByLanguage($language, $order = "language") {
        $language = DB::escapeValue($language);
        $result = array();
        $sql = "SELECT id FROM " . tbname("banner") . " WHERE language = '$language' ORDER BY $order";
        $query = DB::query($sql);
        while ($row = DB::fetchObject($query)) {
            $banner = new Banner();
            $banner->loadByID($row->id);
            $result[] = $banner;
        }
        return $result;
    }

    public static function getByCategory($category_id, $order = "id") {
        $category_id = intval($category_id);
        $result = array();
        $sql = "SELECT id FROM " . tbname("banner") . " WHERE `category_id` = $category_id ORDER BY $order";
        $query = DB::query($sql);
        while ($row = DB::fetchObject($query)) {
            $banner = new Banner();
            $banner->loadByID($row->id);
            $result[] = $banner;
        }
        return $result;
    }

    public static function getByType($type = "gif", $order = "language") {
        $type = DB::escapeValue($type);
        $result = array();
        $sql = "SELECT id FROM " . tbname("banner") . " WHERE `type` = '$type' ORDER BY $order";
        $query = DB::query($sql);
        while ($row = DB::fetchObject($query)) {
            $banner = new Banner();
            $banner->loadByID($row->id);
            $result[] = $banner;
        }
        return $result;
    }

    public static function getRandom() {
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
