<?php

declare(strict_types=1);

namespace UliCMS\Models\Content;

use function get_translation;
use function db_escape;
use UliCMS\Models\Content\Category;

// methods for manipulating categories
class Categories {

    public static function updateCategory(int $id, ?string $name, string $description = ''): ?int {
        $category = new Category($id);
        $category->setName($name);
        $category->setDescription($description);
        $category->save();
        return $category->getID();
    }

    public static function addCategory(?string $name = null, string $description = ""): ?int {
        $category = new Category();
        $category->setName($name);
        $category->setDescription($description);
        $category->save();
        return $category->getID();
    }

    // builds a html category select box
    public static function getHTMLSelect(int $default = 1, bool $allowNull = false, string $name = 'category_id'): string {
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

    public static function deleteCategory(int $id): void {
        $category = new Category($id);
        $category->delete();
    }

    public static function getCategoryDescriptionById(?int $id): ?string {
        $category = new Category($id);
        return $category->getDescription();
    }

    public static function getCategoryById(?int $id): ?string {
        $category = new Category($id);
        return $category->getName();
    }

    public static function getAllCategories(string $order = 'id'): array {
        return Category::getAll($order);
    }

}
