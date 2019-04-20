<?php

class Categories {

    public static function updateCategory($id, $name, $description = '') {
        $sql = "UPDATE " . tbname("categories") . " SET name='" . db_escape($name) . "', description = '" . db_escape($description) . "' WHERE id=" . $id;
        $result = db_query($sql);
        if ($result) {
            Vars::delete("category_" . intval($id), null);
        }
        return $result;
    }

    public static function addCategory($name = null, $description = "") {
        if (is_null($name) or empty($name))
            return null;
        $sqlString = "INSERT INTO " . tbname("categories") . " (name, description) 
         VALUES('" . db_escape($name) . "', '" . db_escape($description) . "')";
        db_query($sqlString);
        return db_insert_id();
    }

    public static function getHTMLSelect($default = 1, $allowNull = false, $name = 'category_id') {
        $lst = self::getAllCategories("name");
        $html = "<select name='" . $name . "' id='$name' size='1'>";
        if ($allowNull) {
            if ($default == 0) {
                $html .= "<option value='0' selected='selected' >[" . get_translation("every") . "]</option>";
            } else {
                $html .= "<option value='0'>[" . get_translation("every") . "]</option>";
            }
        }
        foreach ($lst as $cat) {
            if ($cat["id"] == $default) {
                $html .= "<option value='" . $cat["id"] . "' selected='selected'>" . db_escape($cat["name"]) . "</option>";
            } else {
                $html .= "<option value='" . $cat["id"] . "'>" . db_escape($cat["name"]) . "</option>";
            }
        }

        $html .= "</select>";
        return $html;
    }

    public static function deleteCategory($id) {
        $sqlDeleteString = "DELETE FROM " . tbname("categories") . " WHERE id = " . $id;
        db_query($sqlDeleteString);

        $sqlMoveCategoryContentString = "UPDATE " . tbname("content") . " SET category_id=1 WHERE category_id = " . $id;
        db_query($sqlMoveCategoryContentString);

        $sqlMoveCategoryBannerString = "UPDATE " . tbname("banner") . " SET category_id=1 WHERE category_id = " . $id;
        db_query($sqlMoveCategoryBannerString);


        Vars::delete("category_" . intval($id), null);
    }

    public static function getCategoryDescriptionById($id) {
        $sqlString = "SELECT description FROM " . tbname("categories") . " WHERE id=" . $id;
        $result = db_query($sqlString);
        if (db_num_rows($result) > 0) {
            $row = db_fetch_assoc($result);

            return $row["description"];
        }

        return null;
    }

    public static function getCategoryById($id) {
        if (Vars::get("category_" . intval($id))) {
            return Vars::get("category_" . intval($id));
        }
        $sqlString = "SELECT name FROM " . tbname("categories") . " WHERE id=" . $id;
        $result = db_query($sqlString);
        if (db_num_rows($result) > 0) {
            $row = db_fetch_assoc($result);
            Vars::set("category_" . intval($id), $row["name"]);
            return $row["name"];
        }

        Vars::set("category_" . intval($id), null);
        return null;
    }

    public static function getAllCategories($order = 'id') {
        $sqlString = "SELECT * FROM " . tbname("categories") . " ORDER by " . $order;
        $result = db_query($sqlString);
        $arr = array();
        while ($row = db_fetch_assoc($result)) {
            array_push($arr, $row);
        }

        return $arr;
    }

}
