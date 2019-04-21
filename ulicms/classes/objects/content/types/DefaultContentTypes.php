<?php

class DefaultContentTypes
{

    private static $types = array();

    public static function initTypes()
    {
        self::$types = array();
        self::$types["page"] = new ContentType();
        self::$types["page"]->show = array(
            ".hide-on-non-regular",
            ".menu-stuff",
            "#hidden-attrib",
            "#tab-menu-image",
            "#tab-metadata",
            "#custom_fields_container",
            "#tab-target",
            "#tab-cache-control",
            "#tab-og",
            "#custom_data_json",
            "#content-editor",
            "#btn-view-page",
            "#tab-comments"
        
        );
        self::$types["article"] = clone self::$types["page"];
        self::$types["article"]->show[] = "#article-metadata";
        self::$types["article"]->show[] = "#article-image";
        
        self::$types["snippet"] = new ContentType();
        self::$types["snippet"]->show = array(
            ".show-on-snippet",
            "#content-editor"
        );
        
        self::$types["list"] = clone self::$types["page"];
        self::$types["list"]->show[] = ".list-show";
        self::$types["list"]->show[] = "#tab-list";
        self::$types["list"]->show[] = "#tab-text-position";
        
        self::$types["link"] = new ContentType();
        self::$types["link"]->show[] = "#tab-link";
        self::$types["link"]->show[] = "#tab-target";
        self::$types["link"]->show[] = ".menu-stuff";
        self::$types["link"]->show[] = "#hidden-attrib";
        self::$types["link"]->show[] = "#tab-menu-image";
        
        self::$types["language_link"] = new ContentType();
        self::$types["language_link"]->show[] = "#tab-language-link";
        self::$types["language_link"]->show[] = "#tab-target";
        self::$types["language_link"]->show[] = ".menu-stuff";
        self::$types["language_link"]->show[] = "#hidden-attrib";
        self::$types["language_link"]->show[] = "#tab-menu-image";
        
        self::$types["node"] = new ContentType();
        self::$types["node"]->show[] = ".menu-stuff";
        self::$types["node"]->show[] = "#hidden-attrib";
        
        self::$types["image"] = clone self::$types["page"];
        self::$types["image"]->show[] = "#tab-image";
        self::$types["image"]->show[] = "#tab-text-position";
        
        self::$types["module"] = clone self::$types["page"];
        self::$types["module"]->show[] = "#tab-module";
        self::$types["module"]->show[] = "#tab-text-position";
        
        self::$types["video"] = clone self::$types["page"];
        self::$types["video"]->show[] = "#tab-video";
        self::$types["video"]->show[] = "#tab-text-position";
        
        self::$types["audio"] = clone self::$types["page"];
        self::$types["audio"]->show[] = "#tab-audio";
        self::$types["audio"]->show[] = "#tab-text-position";
      
            self::$types = apply_filter(self::$types, "content_types");
        
    }

    public static function getAll()
    {
        return self::$types;
    }

    public static function get($name)
    {
        if (isset(self::$types[$name])) {
            return self::$types[$name];
        }
        return null;
    }

    public static function toJSON()
    {
        $result = array();
        foreach (self::$types as $key => $value) {
            $result[$key] = array(
                "show" => $value->show
            );
        }
        
        return $result;
    }
}