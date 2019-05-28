<?php

namespace UliCMS\Models\Content;

use function get_translation;
use function db_escape;
use UliCMS\Models\Content\Category;

class Categories {

    public static function updateCategory($id, $name, $description = '') {
        $category = new Category($id);
        $category->setName($name);
        $category->setDescription($description);
        $category->save();
        return $category->getID();
    }

    public static function addCategory($name = null, $description = "") {
        $category = new Category();
        $category->setName($name);
        $category->setDescription($description);
        $category->save();
        return $category->getID();
    }

    public static function getHTMLSelect($default = 1, $allowNull = false, $name = 'category_id') {
        $lst = self::getAllCategories("name");
        $html = "<select name='" . $name . "' id='$name' size='1'>";
        if ($allowNull) {
            if (!$default) {
                $html .= "<option value='0' selected='selected'>[" . get_translation("every") . "]</option>";
            } else {
                $html .= "<option value='0'>[" . get_translation("every") . "]</option>";
            }
        }
        foreach ($lst as $cat) {
            if ($cat->getId() == $default) {
                $html .= "<option value='" . $cat->getId() . "' selected='selected'>" . db_escape($cat->getName()) . "</option>";
            } else {
                $html .= "<option value='" . $cat->getId() . "'>" . db_escape($cat->getName()) . "</option>";
            }
        }

        $html .= "</select>";
        return $html;
    }

    public static function deleteCategory($id) {
        $category = new Category($id);
        $category->delete();
    }

    public static function getCategoryDescriptionById($id) {
        $category = new Category($id);
        return $category->getDescription();
    }

    public static function getCategoryById($id) {
        $category = new Category($id);
        return $category->getName();
    }

    public static function getAllCategories($order = 'id') {
        return Category::getAll($order);
    }

}
