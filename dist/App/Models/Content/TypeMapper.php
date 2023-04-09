<?php

declare(strict_types=1);

// TODO: TypeMapper überflüssig machen
// Die Namen der Modelklassen sollten direkt gespeichert werden in der Tabelle
// content

namespace App\Models\Content;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use ModuleManager;

use function getModuleMeta;

// this class maps the values in the "type" column of the
// "content" table to the equally model class names
class TypeMapper
{
    private static $mapping = [
        'page' => 'Page',
        'snippet' => 'Snippet',
        'list' => 'Content_List',
        'node' => 'Node',
        'link' => 'Link',
        'module' => 'Module_Page',
        'video' => 'Video_Page',
        'audio' => 'Audio_Page',
        'image' => 'Image_Page',
        'article' => 'Article',
        'language_link' => 'Language_Link'
    ];

    public static function getMappings(): array
    {
        return self::$mapping;
    }

    public static function getModel($type): ?object
    {
        if (! (isset(self::$mapping[$type]) && class_exists(self::$mapping[$type]))) {
            return null;
        }
        return new self::$mapping[$type]();
    }

    // custom modules may load their own content type models
    public static function loadMapping(): void
    {
        $manager = new ModuleManager();
        $modules = $manager->getEnabledModuleNames();
        foreach ($modules as $module) {
            $mappings = getModuleMeta($module, 'type_classes');

            if (! $mappings) {
                continue;
            }

            foreach ($mappings as $key => $value) {
                if (empty($value)) {
                    unset(self::$mapping[$key]);
                } else {
                    self::$mapping[$key] = $value;
                }
            }
        }
    }
}
