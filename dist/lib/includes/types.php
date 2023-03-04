<?php

declare(strict_types=1);

use App\Models\Content\Types\DefaultContentTypes;

function getFieldsForCustomType(string $type): array
{
    $fields = [];
    $modules = getAllModules();
    foreach ($modules as $module) {
        $custom_types = getModuleMeta($module, "custom_types");
        if (!$custom_types) {
            continue;
        }
        foreach ($custom_types as $key => $value) {
            if ($key === $type) {
                foreach ($value as $field) {
                    $fields[] = $field;
                }
            }
        }
    }
    return $fields;
}

function get_used_post_types(): array
{
    $result = Database::query("select `type` from {prefix}content "
                    . "group by `type`", true);
    $types = get_available_post_types();
    $used_types = [];
    $return_types = [];
    while ($row = Database::fetchObject($result)) {
        $used_types[] = $row->type;
    }
    foreach ($types as $type) {
        if (in_array($type, $used_types)) {
            $return_types[] = $type;
        }
    }
    return $return_types;
}

function get_available_post_types(): array
{
    $types = DefaultContentTypes::getAll();
    $types = array_keys($types);
    return $types;
}
