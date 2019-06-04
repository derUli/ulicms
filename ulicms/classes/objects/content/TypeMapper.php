<?php

// TODO: TypeMapper überflüssig machen
// Die Namen der Modelklassen sollten direkt gespeichert werden in der Tabelle
// content

namespace UliCMS\Models\Content;

use function getAllModules;
use function getModuleMeta;
use StringHelper;

class TypeMapper {

    private static $mapping = array(
        "page" => "Page",
        "snippet" => "Snippet",
        "list" => "Content_List",
        "node" => "Node",
        "link" => "Link",
        "module" => "Module_Page",
        "video" => "Video_Page",
        "audio" => "Audio_Page",
        "image" => "Image_Page",
        "article" => "Article",
        "language_link" => "Language_Link"
    );

    public static function getMappings() {
        return self::$mapping;
    }

    public static function getModel($type) {
        if (!(isset(self::$mapping[$type]) and class_exists(self::$mapping[$type]))) {
            return null;
        }
        return new self::$mapping[$type]();
    }

    public static function loadMapping() {
        $objectRegistry = [];
        $modules = getAllModules();
        foreach ($modules as $module) {
            $mappings = getModuleMeta($module, "type_classes");
            if ($mappings) {
                foreach ($mappings as $key => $value) {
                    if (StringHelper::isNullOrEmpty($value)) {
                        unset(self::$mapping[$key]);
                    } else {
                        self::$mapping[$key] = $value;
                    }
                }
            }
        }
    }

}
