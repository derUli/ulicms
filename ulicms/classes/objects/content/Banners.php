<?php

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

}
